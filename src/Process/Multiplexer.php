<?php
namespace Lucinda\Shell\Process;

use Lucinda\Shell\Process;

/**
 * Defines blueprints for a process multiplexer
 */
interface Multiplexer
{
    /**
     * Runs multiple processes and returns their results
     *
     * @param Process[] $processes
     * @return Result[]
     */
    public function run(array $processes): array;
}
