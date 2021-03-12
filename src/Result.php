<?php
namespace Lucinda\Process;

class Result
{
    private $status;
    private $payload;
    private $duration;
        
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }
    
    public function getStatus(): int
    {
        return $this->status;
    }
    
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }
    
    public function getPayload()
    {
        return $this->payload;
    }
    
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }
    
    public function getDuration(): int
    {
        return $this->duration;
    }
}
