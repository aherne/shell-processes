<?php
namespace Lucinda\Shell;

use Lucinda\Shell\Process\Result;
use Lucinda\Shell\Process\Multiplexer;

/**
 * Handles a pool of processes
 */
class Pool
{
    private int $capacity;
    private array $processes = [];

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
     * @param Process $process
     */
    public function submit(Process $process): void
    {
        $this->processes[] = $process;
    }
    
    /**
     * Executes processes in pool using capacity-sized batches, returning results
     *
     * @return Result[] List of encapsulated processes' results
     */
    public function shutdown(Multiplexer $multiplexer): array
    {
        $results = [];
        $batches = array_chunk($this->processes, $this->capacity);
        foreach ($batches as $batch) {
            $tmp = $multiplexer->run($batch);
            foreach ($tmp as $result) {
                $results[] = $result;
            }
        }
        return $results;
    }
}
