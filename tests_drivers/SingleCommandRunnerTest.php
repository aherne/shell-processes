<?php
namespace Test\Lucinda\Shell\Driver;

use Lucinda\Shell\Driver\SingleCommandRunner;
use Lucinda\Shell\Process\Result\Status;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Process;

class SingleCommandRunnerTest
{
    public function run()
    {
        $start = microtime(true);
        
        $command = new SingleCommandRunner(10);
        $result = $command->run(new Process("php ".dirname(__DIR__).DIRECTORY_SEPARATOR."script.php"));
        
        return new Result($result->getStatus()==Status::COMPLETED && $result->getPayload()=="OK" && round(microtime(true)-$start)==1);
    }
}
