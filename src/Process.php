<?php

namespace Lucinda\Shell;

use Lucinda\Shell\Process\Exception;
use Lucinda\Shell\Process\Status;

/**
 * Encapsulates process handling on top of proc_open/proc_close functions
 */
class Process
{
    private string $command;
    private string $workingDirectory = "";
    /**
     * @var array<string,string>
     */
    private array $environmentVariables = [];

    private mixed $fileDescriptor; // resource @ proc_open
    /**
     * @var array<int,Stream>
     */
    private array $streams = [];

    /**
     * Constructs a process by shell command
     *
     * @param string $command Shell command to execute.
     * @param bool $autoEscape Whether command should be escaped using escapeshellcmd
     */
    public function __construct(string $command, bool $autoEscape = true)
    {
        $this->command = ($autoEscape ? escapeshellcmd($command) : $command);
    }

    /**
     * Sets working directory command should be executed from
     *
     * @param string $workingDirectory Absolute path to to working directory
     * @throws Exception If directory doesn't exist
     */
    public function setWorkingDirectory(string $workingDirectory): void
    {
        if (!is_dir($workingDirectory)) {
            throw new Exception("Invalid directory: ".$workingDirectory);
        }
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * Adds environment variable to be made available in process to execute
     *
     * @param string $key
     * @param string $value
     */
    public function addEnvironmentVariable(string $key, string $value): void
    {
        $this->environmentVariables[$key] = $value;
    }

    /**
     * Adds a stream to be tracked for process to execute
     *
     * @param int $fileDescriptorNumber One of \Lucinda\Shell\Stream\Type enum values or a custom number greater than 2
     * @param Stream $stream Stream to be tracked
     */
    public function addStream(int $fileDescriptorNumber, Stream $stream): void
    {
        $this->streams[$fileDescriptorNumber] = $stream;
    }

    /**
     * Starts process
     *
     * @return bool Whether operation was successful or not
     */
    public function open(): bool
    {
        $descriptors = [];
        foreach ($this->streams as $fileDescriptorNumber=>$object) {
            $descriptors[$fileDescriptorNumber] = $object->getDescriptorSpecification();
        }

        $pipes = [];
        $resource = proc_open(
            $this->command,
            $descriptors,
            $pipes,
            $this->workingDirectory,
            $this->environmentVariables
        );
        if ($resource === false || !is_resource($resource)) {
            return false;
        }
        $this->fileDescriptor = $resource;

        foreach ($pipes as $fileDescriptorNumber=>$childFileDescriptor) {
            $this->streams[$fileDescriptorNumber]->setFileDescriptor($childFileDescriptor);
        }

        return true;
    }

    /**
     * Checks if process still exists
     *
     * @return bool
     */
    public function isOpen(): bool
    {
        return is_resource($this->fileDescriptor);
    }

    /**
     * Gets process status information
     *
     * @return Status
     */
    public function getStatus(): Status
    {
        return new Status($this->fileDescriptor);
    }

    /**
     * Gets stream of running process
     *
     * @param int $fileDescriptorNumber One of \Lucinda\Shell\Stream\Type enum values or a custom number greater than 2
     * @return Stream|NULL Corresponding stream or NULL if not found.
     */
    public function getStream(int $fileDescriptorNumber): ?Stream
    {
        return ($this->streams[$fileDescriptorNumber] ?? null);
    }

    /**
     * Terminates (kills) process
     *
     * @return bool Whether operation was successful or not
     */
    public function terminate(): bool
    {
        return proc_terminate($this->fileDescriptor, SIGKILL);
    }

    /**
     * Closes process gracefully
     *
     * @return bool Whether operation was successful or not
     */
    public function close(): bool
    {
        return proc_close($this->fileDescriptor)!==-1;
    }
}
