<?php
namespace Lucinda\Process;

class ProcessStatus
{
    private $fileDescriptor;
    
    public function __construct($fileDescriptor)
    {
        $this->fileDescriptor = $fileDescriptor;
    }
    
    public function getProcessID(): int
    {
        return proc_get_status($this->fileDescriptor)["pid"];
    }
    
    public function isRunning(): bool
    {
        return proc_get_status($this->fileDescriptor)["running"];
    }
    
    public function isTerminated(): bool
    {
        return proc_get_status($this->fileDescriptor)["signaled"];
    }
    
    public function isStopped(): bool
    {
        return proc_get_status($this->fileDescriptor)["stopped"];
    }
}
