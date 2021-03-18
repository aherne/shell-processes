<?php
namespace Lucinda\Process;

use Lucinda\Process\Stream\Status;

/**
 * Encapsulates a stream to read from or write to on top of a file descriptor
 */
abstract class Stream
{
    private $fileDescriptor;
    
    /**
     * Gets descriptor specification for proc_open specific to stream type
     *
     * @return array|resource
     */
    abstract public function getDescriptorSpecification();
    
    /**
     * Sets stream file descriptor of open process
     *
     * @param resource $fileDescriptor
     */
    public function setFileDescriptor($fileDescriptor): void
    {
        $this->fileDescriptor = $fileDescriptor;
    }
    
    /**
     * Gets file descriptor of running stream. Use only if you need special processing not covered by methods in class!
     *
     * @return resource
     */
    public function getFileDescriptor()
    {
        return $this->fileDescriptor;
    }
    
    /**
     * Sets stream timeout. To check if exceeded, use StreamStatus::isTimedOut!
     *
     * @param int $seconds Number of seconds to time out stream
     * @return bool Whether or not operation was successful
     */
    public function setTimeout(int $seconds): bool
    {
        return stream_set_timeout($this->fileDescriptor, $seconds, 0);
    }
    
    /**
     * Sets current stream as non-blocking (forcing usage of stream_select to handle)
     *
     * @param bool $isBlocking
     * @return bool Whether or not operation was successful
     */
    public function setBlocking(bool $isBlocking): bool
    {
        return stream_set_blocking($this->fileDescriptor, $isBlocking);
    }
    
    /**
     * Writes section of string into stream
     *
     * @param string $data
     * @param int $length (optional) Number of bytes to read
     * @return bool Whether or not operation was successful
     */
    public function write(string $data, int $length=0): bool
    {
        $response = null;
        if ($length) {
            $response = fwrite($this->fileDescriptor, $data, $length);
        } else {
            $response = fwrite($this->fileDescriptor, $data);
        }
        return $response!==false;
    }
    
    /**
     * Reads section of stream into string (or entire stream if length is zero)
     *
     * @param int $length Number of bytes to read
     * @return string String
     */
    public function read(int $length = 0): string
    {
        $response = null;
        if ($length==0) {
            $response = stream_get_contents($this->fileDescriptor);
        } else {
            $response = fread($this->fileDescriptor, $length);
        }
        return ($response!==false?$response:null);
    }
    
    /**
     * Gets running status of stream
     *
     * @return Status
     */
    public function getStatus(): Status
    {
        return new Status($this->fileDescriptor);
    }
    
    /**
     * Closes stream of running process
     *
     * @return bool Whether or not operation was successful
     */
    public function close(): bool
    {
        return fclose($this->fileDescriptor);
    }
}
