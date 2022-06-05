<?php

namespace Lucinda\Shell\Stream;

use Lucinda\Shell\Stream;
use Lucinda\Shell\Stream\Select\Type;
use Lucinda\Shell\Stream\Select\InterruptedException;
use Lucinda\Shell\Stream\Select\TimeoutException;

/**
 * Monitors streams (file descriptors) until they are ready for I/O
 */
class Select
{
    private int $timeout;
    /**
     * @var array<int,Stream[]>
     */
    private array $streams = [];

    /**
     * Sets timeout (in seconds) we should block waiting for a file descriptor to become ready
     *
     * @param int $timeout
     */
    public function __construct(int $timeout = 0)
    {
        $this->timeout = $timeout;
    }

    /**
     * Adds stream to pool by file descriptor set type
     *
     * @param Stream $stream Stream to be multiplexed
     * @param Type   $type   One of enum values corresponding to a a file descriptor set type
     */
    public function addStream(Stream $stream, Type $type = Type::READ): void
    {
        $stream->setBlocking(false);
        $this->streams[$type->value][] = $stream;
    }

    /**
     * Monitors streams added to pool
     *
     * @throws InterruptedException If operation was cancelled (invalid streams supplied or interrupted by signal)
     * @throws TimeoutException If blocking timeout was exceeded
     * @return int Number of streams modified.
     */
    public function run(): int
    {
        // populates read file descriptor set
        $read = $this->populateFileDescriptorSet(Type::READ);

        // populates write file descriptor set
        $write = $this->populateFileDescriptorSet(Type::WRITE);

        // populates except file descriptor set
        $except = $this->populateFileDescriptorSet(Type::EXCEPT);

        // executes select call
        $result = stream_select($read, $write, $except, $this->timeout);

        if ($result === false) {
            throw new InterruptedException();
        }
        if ($result === 0) {
            throw new TimeoutException();
        }

        return $result;
    }

    /**
     * Populates file descriptor set for given FD type
     *
     * @param  Type $type
     * @return array<int,mixed>
     */
    private function populateFileDescriptorSet(Type $type): array
    {
        $output = [];
        $value = $type->value;
        if (isset($this->streams[$value])) {
            foreach ($this->streams[$value] as $stream) {
                $output[] = $stream->getFileDescriptor();
            }
        }
        return $output;
    }
}
