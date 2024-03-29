<?php

namespace Test\Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\Type;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Stream\Status;

class StatusTest
{
    private $process;
    private $pipe;

    public function __construct()
    {
        $pipes = [];
        $this->process = proc_open("php ".dirname(__DIR__, 2).DIRECTORY_SEPARATOR."script.php", [Type::STDOUT->value=>["pipe","w"]], $pipes);
        $this->pipe = $pipes[Type::STDOUT->value];
    }

    public function __destruct()
    {
        fclose($this->pipe);
        proc_close($this->process);
    }

    public function isTimedOut()
    {
        $status  = new Status($this->pipe);
        return new Result(!$status->isTimedOut());
    }


    public function isBlocked()
    {
        $status  = new Status($this->pipe);
        return new Result($status->isBlocked());
    }


    public function isEndOfFile()
    {
        $status  = new Status($this->pipe);
        return new Result(!$status->isEndOfFile());
    }


    public function isSeekable()
    {
        $status  = new Status($this->pipe);
        return new Result(!$status->isSeekable());
    }
}
