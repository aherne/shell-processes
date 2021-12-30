<?php
namespace Lucinda\Shell\Process;

use Lucinda\Shell\Process\Result\Status;

/**
 * Encapsulates results of streams handling for a pool process
 */
class Result
{
    private Status $status;
    private mixed $payload;
        
    /**
     * Sets stream handling outcome status for pool process
     *
     * @param Status $status One of enum values
     */
    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }
    
    /**
     * Gets stream handling outcome status of executed pool process
     *
     * @return Status One of enum values
     */
    public function getStatus(): Status
    {
        return $this->status;
    }
    
    /**
     * Sets payload produced by pool process execution
     *
     * @param mixed $payload
     */
    public function setPayload(mixed $payload): void
    {
        $this->payload = $payload;
    }
    
    /**
     * Gets payload produced by pool process execution
     *
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
