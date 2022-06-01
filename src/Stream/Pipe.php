<?php

namespace Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\File\Mode;

/**
 * Encapsulates a stream that uses a un-named pipe underneath
 */
class Pipe extends \Lucinda\Shell\Stream
{
    private Mode $mode;

    /**
     * Sets location of file data will be streamed into
     *
     * @param Mode $accessMode One of enum values, identifying pipe access mode.
     * @throws Exception If invalid file mode is supplied
     */
    public function __construct(Mode $accessMode)
    {
        $this->mode = $accessMode;
    }

    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification(): mixed
    {
        return ["pipe", $this->mode->value];
    }
}
