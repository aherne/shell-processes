<?php
namespace Test\Lucinda\Shell\Process;

use Lucinda\Shell\Process\Status;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Stream\Type;

class StatusTest
{
    private $process;
    
    public function __construct()
    {
        $pipes = [];
        $this->process = proc_open("php ".dirname(__DIR__, 2).DIRECTORY_SEPARATOR."script.php", [Type::STDOUT=>["pipe","w"]], $pipes);
    }
    
    public function __destruct()
    {
        proc_close($this->process);
    }
    

    public function getProcessID()
    {
        $status  = new Status($this->process);
        return new Result($status->getProcessID()>0);
    }
        

    public function isRunning()
    {
        $status  = new Status($this->process);
        return new Result($status->isRunning()==true);
    }
        

    public function isTerminated()
    {
        $status  = new Status($this->process);
        return new Result($status->isTerminated()==false);
    }
        

    public function isStopped()
    {
        $status  = new Status($this->process);
        return new Result($status->isStopped()==false);
    }
}
