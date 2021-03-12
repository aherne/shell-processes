<?php
namespace Lucinda\Process;

abstract class Stream
{
    private $fileDescriptor;
    
    abstract public function getDescriptorSpecification();
    
    public function setFileDescriptor($fileDescriptor): void
    {
        $this->fileDescriptor = $fileDescriptor;
    }
    
    public function setBlocking(bool $blocking = false): bool
    {
        return stream_set_blocking($this->fileDescriptor, $blocking);
    }
    
    public function setTimeout(int $seconds, int $milliseconds = 0): bool
    {
        return stream_set_timeout($this->fileDescriptor, $seconds, $milliseconds);
    }
    
    public function getFileDescriptor()
    {
        return $this->fileDescriptor;
    }
    
    public function getContents(int $offset = 0, int $limit = 0): ?string
    {
        $response = stream_get_contents($this->fileDescriptor, ($limit>0?$limit:-1), ($offset>0?$offset:-1));
        return ($response!==false?$response:null);
    }
    
    public function read(int $length): string
    {
        return fread($this->fileDescriptor, $length);
    }
    
    public function write(string $data): void
    {
        fwrite($this->fileDescriptor, $data);
    }
    
    public function getStatus(): StreamStatus
    {
        return new StreamStatus($this->fileDescriptor);
    }
    
    public function close(): bool
    {
        return fclose($this->fileDescriptor);
    }
}
