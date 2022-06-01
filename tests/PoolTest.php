<?php

namespace Test\Lucinda\Shell;

use Lucinda\Shell\Pool;
use Lucinda\Shell\Process;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Process\Result\Status;
use Lucinda\Shell\Driver\MultiCommandRunner;

class PoolTest
{
    public const POOL_SIZE = 3;
    public const PROCESS_NUMBER = 4;

    private $object;

    public function __construct()
    {
        $this->object = new Pool(self::POOL_SIZE);
    }

    public function submit()
    {
        for ($i=0; $i<self::PROCESS_NUMBER; $i++) {
            $this->object->submit(new Process("php ".dirname(__DIR__).DIRECTORY_SEPARATOR."script.php"));
        }
        return new Result(true, "tested via shutdown()");
    }


    public function shutdown()
    {
        $start = microtime(true);
        $results = $this->object->shutdown(new MultiCommandRunner(60));
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
