<?php

namespace Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\File\Mode;

/**
 * Encapsulates a stream that uses a file underneath
 */
class File extends \Lucinda\Shell\Stream
{
    private string $filePath;
    private Mode $mode;

    /**
     * Sets location of file data will be streamed into
     *
     * @param string $filePath Absolute location of file data will be streamed into
     * @param Mode $fileMode One of enum values, identifying file access mode.
     */
    public function __construct(string $filePath, Mode $fileMode)
    {
        $this->filePath = $filePath;
        $this->mode = $fileMode;
    }

    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification(): mixed
    {
        return ["file", $this->filePath, $this->mode->value];
    }
}
