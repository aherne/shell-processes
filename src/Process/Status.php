<?php

namespace Lucinda\Shell\Process;

/**
 * Encapsulates running process status information handling on top of proc_get_status function
 */
class Status
{
    private mixed $fileDescriptor;

    /**
     * Constructs class by process file descriptor
     *
     * @param resource $fileDescriptor
     */
    public function __construct(mixed $fileDescriptor)
    {
        $this->fileDescriptor = $fileDescriptor;
    }

    /**
     * Gets PID of running process
     *
     * @return int
     */
    public function getProcessID(): int
    {
        return proc_get_status($this->fileDescriptor)["pid"];
    }

    /**
     * Checks if process is still running
     *
     * @return bool
     */
    public function isRunning(): bool
    {
        return proc_get_status($this->fileDescriptor)["running"];
    }

    /**
     * Checks if process was terminated
     *
     * @return bool
     */
    public function isTerminated(): bool
    {
        return proc_get_status($this->fileDescriptor)["signaled"];
    }

    /**
     * Checks if process was stopped
     *
     * @return bool
     */
    public function isStopped(): bool
    {
        return proc_get_status($this->fileDescriptor)["stopped"];
    }
}
