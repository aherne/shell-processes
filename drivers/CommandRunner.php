<?php
namespace Lucinda\Shell\Driver;

use Lucinda\Shell\Process\Result;
use Lucinda\Shell\Process\Result\Status;

/**
 * Implements an abstract command runner
 */
abstract class CommandRunner
{
    protected $timeout;
    
    /**
     * Sets timeout (in seconds) we should block waiting for a file descriptor to become ready
     *
     * @param int $timeout
     */
    public function __construct(int $timeout)
    {
        $this->timeout = $timeout;
    }
    
    /**
     * Compiles a result object based on data supplied
     *
     * @param Status $status
     * @param string $payload
     * @return Result
     */
    protected function compileResult(int $status, string $payload = "")
    {
        $result = new Result();
        $result->setStatus($status);
        $result->setPayload($payload);
        return $result;
    }
}
