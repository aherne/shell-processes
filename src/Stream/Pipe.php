<?php
namespace Lucinda\Process\Stream;

class Pipe extends \Lucinda\Process\Stream
{
    private $mode;
    
    public function __construct(string $accessMode)
    {
        if (!in_array($accessMode, ["r", "w", "a"])) {
            throw new Exception("Invalid file mode: ".$accessMode);
        }
        $this->mode = $accessMode;
    }
    
    public function getDescriptorSpecification()
    {
        return ["pipe", $this->mode];
    }
}
