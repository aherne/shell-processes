<?php
namespace Lucinda\Process\Pool;

use Lucinda\Process\Process;
use Lucinda\Process\Stream;
use Lucinda\Process\Stream\Type;
use Lucinda\Process\Stream\Pipe;

/**
 * Defines blueprints of a process to be handled in pool
 */
abstract class Runnable
{
    protected $process;
    protected $timeout;
    protected $streams = [];
    
    /**
     * Initializes a process and set up streams
     *
     * @param Process $process Process to execute
     * @param int $timeout Maximum amount of seconds command is allowed to run into
     */
    public function __construct(Process $process, int $timeout = 60)
    {
        $this->process = $process;
        $this->timeout = $timeout;
        $this->streams = $this->getStreams();
    }
    
    /**
     * Gets streams to be handled by process
     *
     * @return Stream[Type] Array where key is stream type and value is stream object
     */
    abstract protected function getStreams(): array;
    
    /**
     * Opens process underneath, setting streams also
     */
    public function open(): void
    {
        foreach ($this->streams as $type=>$stream) {
            $this->process->addStream($type, $stream);
        }
        $this->process->open();
        foreach ($this->streams as $stream) {
            if ($stream instanceof Pipe) {
                $stream->setBlocking(false);
            }
        }
    }
    
    /**
     * Handles streams inside opened process and returns result
     *
     * @return Result
     */
    abstract public function handle(): Result;
    
    /**
     * Closes process underneath
     */
    public function close(): void
    {
        foreach ($this->streams as $stream) {
            if ($stream instanceof Pipe) {
                $stream->close();
            }
        }
        $this->process->close();
    }
}
