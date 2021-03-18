<?php
namespace Lucinda\Process\Stream;

/**
 * Encapsulates a stream that uses a resource underneath (eg: socket)
 */
class Resource extends \Lucinda\Process\Stream
{
    private $resource;
    private $mode;
    
    /**
     * Sets resource data will be streamed into
     *
     * @param resource $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Process\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification()
    {
        return $this->resource;
    }
}
