<?php
namespace Lucinda\Process\Stream;

use Lucinda\Process\Stream\File\Mode;

/**
 * Encapsulates a stream that uses a un-named pipe underneath
 */
class Pipe extends \Lucinda\Process\Stream
{
    private $mode;
    
    /**
     * Sets location of file data will be streamed into
     *
     * @param Mode $fileMode One of enum values, identifying pipe access mode.
     * @throws Exception If invalid file mode is supplied
     */
    public function __construct(string $accessMode)
    {
        if (!in_array($accessMode, ["r", "w", "a"])) {
            throw new Exception("Invalid file mode: ".$accessMode);
        }
        $this->mode = $accessMode;
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Process\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification()
    {
        return ["pipe", $this->mode];
    }
}
