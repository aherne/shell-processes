<?php
namespace Lucinda\Shell\Stream;

/**
 * Encapsulates a stream that uses a resource underneath (eg: socket)
 */
class Resource extends \Lucinda\Shell\Stream
{
    private mixed $resource;

    /**
     * Sets resource data will be streamed into
     *
     * @param resource $resource
     */
    public function __construct(mixed $resource)
    {
        $this->resource = $resource;
    }
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Stream::getDescriptorSpecification()
     */
    public function getDescriptorSpecification(): mixed
    {
        return $this->resource;
    }
}
