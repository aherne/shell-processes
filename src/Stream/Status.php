<?php

namespace Lucinda\Shell\Stream;

/**
 * Encapsulates running process stream status information handling on top of stream_get_meta_data function
 */
class Status
{
    private mixed $fileDescriptor;

    /**
     * Constructs class by stream file descriptor
     *
     * @param resource $fileDescriptor
     */
    public function __construct(mixed $fileDescriptor)
    {
        $this->fileDescriptor = $fileDescriptor;
    }

    /**
     * Checks if stream has timed out (available if stream_set_timeout was used)
     *
     * @return bool
     */
    public function isTimedOut(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["timed_out"];
    }

    /**
     * Checks if stream is in blocking I/0 mode
     *
     * @return bool
     */
    public function isBlocked(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["blocked"];
    }

    /**
     * Check if stream has reached end-of-file
     *
     * @return bool
     */
    public function isEndOfFile(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["eof"];
    }

    /**
     * Checks if current stream can be seeked into.
     *
     * @return bool
     */
    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->fileDescriptor)["seekable"];
    }
}
