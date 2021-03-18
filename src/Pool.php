<?php
namespace Lucinda\Process;

use Lucinda\Process\Pool\Runnable;
use Lucinda\Process\Pool\Result;

/**
 * Handles a pool of processes
 */
class Pool
{
    private $capacity;
    
    private $runnables = [];
    private $results = [];
    
    /**
     * Instances pool by maximum number of processes allowed to work in paralel
     *
     * @param int $capacity Max number of parallel processes.
     */
    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
    }
    
    /**
     * Adds a runnable process to pool
     *
     * @param Runnable $runnable
     */
    public function submit(Runnable $runnable): void
    {
        $this->runnables[] = $runnable;
    }
    
    /**
     * Executes processes in pool using capacity-sized batches, returning results
     *
     * @return Result[] List of encapsulated processes' results
     */
    public function shutdown(): array
    {
        $results = [];
        $batches = array_chunk($this->runnables, $this->capacity);
        foreach ($batches as $batch) {
            // start processes in parallel
            foreach ($batch as $runnable) {
                $runnable->open();
            }
            
            // processes streams
            foreach ($batch as $runnable) {
                $results[] = $runnable->handle();
            }
            
            // closes processes and strems
            foreach ($batch as $runnable) {
                $runnable->close();
            }
        }
        return $results;
    }
}
