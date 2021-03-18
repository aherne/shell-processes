<?php
namespace Test\Lucinda\Process\Driver;

use Lucinda\Process\Driver\BasicRunnableProcess;
use Lucinda\Process\Process;
use Lucinda\UnitTest\Result;
use Lucinda\Process\Pool\Result\Status;

class BasicRunnableProcessTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new BasicRunnableProcess(new Process("php ".dirname(__DIR__).DIRECTORY_SEPARATOR."script.php"));
        $this->object->open();
    }
    
    public function __destruct()
    {
        $this->object->close();
    }

    public function handle()
    {
        $result = $this->object->handle();
        return new Result($result->getStatus()==Status::COMPLETED && $result->getPayload()=="OK");
    }
}
