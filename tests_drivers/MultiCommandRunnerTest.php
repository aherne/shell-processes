<?php

namespace Test\Lucinda\Shell\Driver;

use Lucinda\Shell\Process;
use Lucinda\Shell\Process\Result\Status;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Driver\MultiCommandRunner;

class MultiCommandRunnerTest
{
    public function run()
    {
        $start = microtime(true);
        $processes = [];
        for ($i=0; $i<3; $i++) {
            $processes[] = new Process("php ".dirname(__DIR__).DIRECTORY_SEPARATOR."script.php");
        }
        $simpleMultiplexer = new MultiCommandRunner(10);
        $results = $simpleMultiplexer->run($processes);

        $status = true;
        foreach ($results as $result) {
            if ($result->getStatus()!=Status::COMPLETED || $result->getPayload()!="OK") {
                $status = false;
                break;
            }
        }

        return new Result($status && round(microtime(true)-$start)==1);
    }
}
