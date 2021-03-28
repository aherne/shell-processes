<?php
namespace Lucinda\Shell\Process;

use Lucinda\Shell\Process\Result\Status;

/**
 * Encapsulates results of streams handling for a pool process
 */
class Result
{
    private $status;
    private $payload;
        
    /**
     * Sets stream handling outcome status for pool process
     *
     * @param Status $status One of enum values
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
    
    /**
     * Gets stream handling outcome status of executed pool process
     *
     * @return Status One of enum values
     */
    public function getStatus(): int
    {
        return $this->status;
    }
    
    /**
     * Sets payload produced by pool process execution
     *
     * @param mixed $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }
    
    /**
     * Gets payload produced by pool process execution
     *
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
