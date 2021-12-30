<?php
namespace Test\Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\Pipe;
use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Process;
use Lucinda\Shell\Stream\Type;
use Lucinda\Shell\Stream\Select;
use Lucinda\UnitTest\Result;

class SelectTest
{
    private $process;
    private $object;
    
    public function __construct()
    {
        $this->process = new Process("php ".dirname(__DIR__, 2)."/script.php");
        $this->process->addStream(Type::STDOUT->value, new Pipe(Mode::WRITE));
        $this->process->addStream(Type::STDERR->value, new Pipe(Mode::WRITE));
        $this->process->open();
        
        $this->object = new Select(60);
    }
    
    public function __destruct()
    {
        $this->process->getStream(Type::STDOUT->value)->close();
        $this->process->getStream(Type::STDERR->value)->close();
        $this->process->close();
    }
    
    public function addStream()
    {
        $this->object->addStream($this->process->getStream(Type::STDOUT->value));
        $this->object->addStream($this->process->getStream(Type::STDERR->value));
        return new Result(true, "tested via run()");
    }

    public function run()
    {
        return new Result($this->object->run()==1);
    }
}
