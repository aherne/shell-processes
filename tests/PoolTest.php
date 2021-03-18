<?php
namespace Test\Lucinda\Process;

use Lucinda\Process\Pool;
use Lucinda\Process\Driver\BasicRunnableProcess;
use Lucinda\Process\Process;
use Lucinda\UnitTest\Result;
use Lucinda\Process\Pool\Result\Status;

class PoolTest
{
    const POOL_SIZE = 3;
    const PROCESS_NUMBER = 4;
    
    private $object;
    
    public function __construct()
    {
        $this->object = new Pool(self::POOL_SIZE);
    }

    public function submit()
    {
        for ($i=0; $i<self::PROCESS_NUMBER; $i++) {
            $this->object->submit(new BasicRunnableProcess(new Process("php ".dirname(__DIR__).DIRECTORY_SEPARATOR."script.php")));
        }
        return new Result(true, "tested via shutdown()");
    }
        

    public function shutdown()
    {
        $start = microtime(true);
        $results = $this->object->shutdown();
        $status = true;
        foreach ($results as $result) {
            if ($result->getStatus()!=Status::COMPLETED || $result->getPayload()!="OK") {
                $status = false;
                break;
            }
        }
        return new Result($status && round(microtime(true)-$start)==2);
    }
}
