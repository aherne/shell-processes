<?php
namespace Lucinda\Process;

class BasicRunnableProcess extends RunnableProcess
{
    public function service(): Result
    {
        $stdout = $this->process->getStream(Stream\Type::STDOUT);
        $stderr = $this->process->getStream(Stream\Type::STDERR);
        $timeStarted = microtime(true);
        
        $read = [$stdout->getFileDescriptor(), $stderr->getFileDescriptor()];
        $write = null;
        $except = null;
        $result = stream_select($read, $write, $except, 0, $this->timeout);
        if ($result===false) {
            // this can happen if the system call is interrupted by an incoming signal
            $this->process->terminate();
            return $this->compileResult(ResultStatus::INTERRUPTED, "", $timeStarted);
        } elseif ($result===0) {
            // timeout expired
            $this->process->terminate();
            return $this->compileResult(ResultStatus::TERMINATED, "", $timeStarted);
        } else {
            // assumes stream is small enough to be retrieved in one iteration
            $output = $stdout->getContents();
            $error = $stderr->getContents();
            if ($error) {
                return $this->compileResult(ResultStatus::ERROR, $error, $timeStarted);
            } else {
                return $this->compileResult(ResultStatus::COMPLETED, $output, $timeStarted);
            }
        }
    }
    
    protected function compileResult(int $status, string $payload, int $timeStarted)
    {
        $result = new Result();
        $result->setStatus($status);
        $result->setPayload($payload);
        $result->setDuration(round((microtime(true) - $timeStarted)*1000));
        return $result;
    }
}
