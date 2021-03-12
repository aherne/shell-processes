<?php
namespace Lucinda\Process;

class StreamStatus
{
    private $fileDescriptor;
    
    public function __construct($fileDescriptor)
    {
        $this->fileDescriptor = $fileDescriptor;
    }
    
    public function isTimedOut(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["timed_out"];
    }
    
    public function isBlocked(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["blocked"];
    }
    
    public function isEndOfFile(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["eof"];
    }
    
    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["seekable"];
    }
    
    public function getUnreadBytes(): int
    {
        return stream_get_meta_data($this->fileDescriptor)["unread_bytes"];
    }
}
