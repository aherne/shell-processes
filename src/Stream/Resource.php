<?php
namespace Lucinda\Process\Stream;

class Resource extends \Lucinda\Process\Stream
{
    private $resource;
    private $mode;
    
    public function __construct($resource)
    {
        $this->resource = $resource;
    }
    
    public function getDescriptorSpecification()
    {
        return $this->resource;
    }
}
