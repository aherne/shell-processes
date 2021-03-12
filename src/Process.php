<?php
namespace Lucinda\Process;

/**

    I would advice not using mkfifo-pipes, because filesystem fifo-pipe (mkfifo) blocks open/fopen call (!!!) until somebody opens other side (unix-related behavior). In case the pipe is opened not by shell and the command is crashed or is not exists you will be blocked forever.

 */
class Process
{
    private $command;
    private $workingDirectory;
    private $environmentVariables = [];
    
    private $fileDescriptor;
    private $streams = [];
    
    public function __construct(string $command, bool $autoEscape = true)
    {
        $this->command = ($autoEscape?escapeshellcmd($command):$command);
    }
    
    public function setWorkingDirectory(string $workingDirectory): void
    {
        if (!is_dir($workingDirectory)) {
            throw new Exception("Invalid directory: ".$workingDirectory);
        }
        $this->workingDirectory = $workingDirectory;
    }
    
    public function addEnvironmentVariable(string $key, string $value): void
    {
        $this->environmentVariables[$key] = $value;
    }
    
    public function addStream(int $fileDescriptorNumber, Stream $stream): void
    {
        $this->streams[$fileDescriptorNumber] = $stream;
    }
        
    public function open(): void
    {
        $descriptors = [];
        foreach ($this->streams as $fileDescriptorNumber=>$object) {
            $descriptors[$fileDescriptorNumber] = $object->getDescriptorData();
        }
        
        $pipes = [];
        $resource = proc_open($this->command, $descriptors, $pipes, $this->workingDirectory, $this->environmentVariables);
        if ($resource === false || !is_resource($resource)) {
            throw new Exception("Process could not be created");
        }
        $this->fileDescriptor = $resource;
                
        foreach ($pipes as $fileDescriptorNumber=>$childFileDescriptor) {
            $this->streams[$fileDescriptorNumber]->setFileDescriptor($childFileDescriptor);
        }
    }
    
    public function isOpen(): bool
    {
        return $this->fileDescriptor!==null;
    }
    
    public function getStream(int $fileDescriptorNumber): ?Stream
    {
        return (isset($this->streams[$fileDescriptorNumber])?$this->streams[$fileDescriptorNumber]:null);
    }
    
    public function getStatus(): ProcessStatus
    {
        return new ProcessStatus($this->fileDescriptor);
    }
    
    public function terminate(): bool
    {
        return proc_terminate($this->fileDescriptor);
    }
    
    public function close(): bool
    {
        return proc_close($this->fileDescriptor)!==-1;
    }
}
// https://gist.github.com/sergant210/ca6e67889f892974a37b211f24cfd125
