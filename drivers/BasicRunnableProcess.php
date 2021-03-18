<?php
namespace Lucinda\Process\Driver;

use Lucinda\Process\Pool\Result;
use Lucinda\Process\Stream\Type;
use Lucinda\Process\Pool\Result\Status;
use Lucinda\Process\Stream\File\Mode;
use Lucinda\Process\Pool\Runnable;
use Lucinda\Process\Stream\Pipe;

/**
 * Simple runnable process using STDOUT+STDERR unnamed pipes what work with strings
 */
class BasicRunnableProcess extends Runnable
{
    /**
     * {@inheritDoc}
     * @see \Lucinda\Process\Pool\Runnable::getStreams()
     */
    protected function getStreams(): array
    {
        return [
            Type::STDOUT=>new Pipe(Mode::WRITE),
            Type::STDERR=>new Pipe(Mode::WRITE),
        ];
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Process\Pool\Runnable::handle()
     */
    public function handle(): Result
    {
        $stdout = $this->streams[Type::STDOUT];
        $stderr = $this->streams[Type::STDERR];
        $timeStarted = time();
        
        $read = [$stdout->getFileDescriptor(), $stderr->getFileDescriptor()];
        $write = null;
        $except = null;
        $result = stream_select($read, $write, $except, $this->timeout);
        if ($result===false) {
            // this can happen if the system call is interrupted by an incoming signal
            return $this->compileResult(Status::INTERRUPTED, "", $timeStarted);
        } elseif ($result===0) {
            // timeout expired
            $this->process->terminate();
            return $this->compileResult(Status::TERMINATED, "", $timeStarted);
        } else {
            // assumes stream is small enough to be retrieved in one iteration
            $output = $stdout->read();
            $error = $stderr->read();
            if ($error) {
                return $this->compileResult(Status::ERROR, $error, $timeStarted);
            } else {
                return $this->compileResult(Status::COMPLETED, $output, $timeStarted);
            }
        }
    }
    
    /**
     * Compiles a result object based on data supplied
     *
     * @param Status $status
     * @param string $payload
     * @param int $timeStarted
     * @return Result
     */
    protected function compileResult(int $status, string $payload, int $timeStarted)
    {
        $result = new Result();
        $result->setStatus($status);
        $result->setPayload($payload);
        $result->setDuration(time() - $timeStarted);
        return $result;
    }
}
