<?php
namespace Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\File\Mode;

/**
 * Encapsulates a stream that uses a file underneath
 */
class File extends \Lucinda\Shell\Stream
{
    private $filePath;
    private $mode;
    
    /**
     * Sets location of file data will be streamed into
     *
     * @param string $filePath Absolute location of file data will be streamed into
     * @param Mode $fileMode One of enum values, identifying file access mode.
     * @throws Exception If invalid file mode is supplied
     */
    public function __construct(string $filePath, string $fileMode)
    {
        if (!in_array($fileMode, ["r", "w", "a"])) {
            throw new Exception("Invalid file mode: ".$fileMode);
        }
        $this->filePath = $filePath;
        $this->mode = $fileMode;
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification()
    {
        return ["file", $this->filePath, $this->mode];
    }
}
