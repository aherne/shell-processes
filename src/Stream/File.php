<?php
namespace Lucinda\Process\Stream;

class File extends \Lucinda\Process\Stream
{
    private $filePath;
    private $mode;
    
    public function __construct(string $filePath, string $fileMode)
    {
        if (!in_array($fileMode, ["r", "w", "a"])) {
            throw new Exception("Invalid file mode: ".$fileMode);
        }
        $this->filePath = $filePath;
        $this->mode = $fileMode;
    }
    
    public function getDescriptorSpecification()
    {
        return ["file", $this->filePath, $this->mode];
    }
}
