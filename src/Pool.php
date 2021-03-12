<?php
namespace Lucinda\Process;

class Pool
{
    private $capacity;
    
    private $runnables = [];
    private $results = [];
    
    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
    }
    
    public function submit(RunnableProcess $runnable): void
    {
        $this->runnables[] = $runnable;
    }
    // TODO: handle exceptions
    public function run(): array
    {
        $results = [];
        $batches = array_chunk($this->runnables, $this->capacity);
        foreach ($batches as $batch) {
            // start processes in parallel
            foreach ($batch as $runnable) {
                $runnable->init();
            }
            
            // processes streams
            foreach ($batch as $runnable) {
                $results[] = $runnable->service();
            }
            
            // closes processes and strems
            foreach ($batch as $runnable) {
                $runnable->destroy();
            }
        }
        return $results;
    }
}

// https://gist.github.com/Arbow/982320
// https://jrsinclair.com/articles/2012/simple-parallel-processing-with-php-and-proc-open/
// https://gist.github.com/overtrue/8d53d18d9e2cda518c8993d2670257a9
// https://riptutorial.com/php/example/23677/spawning-non-blocking-runnables-with-proc-open--
