<?php
namespace Test\Lucinda\Process;

use Lucinda\Process\Process;
use Lucinda\Process\Stream\Pipe;
use Lucinda\Process\Stream\File\Mode;
use Lucinda\Process\Stream\Type;
use Lucinda\UnitTest\Result;

class ProcessTest
{
    private $process;
    
    public function __construct()
    {
        $this->process = new Process("php script.php");
    }

    public function setWorkingDirectory()
    {
        $this->process->setWorkingDirectory(dirname(__DIR__));
        return new Result(true, "tested via open()");
    }
        

    public function addEnvironmentVariable()
    {
        $this->process->addEnvironmentVariable("test", "me");
        return new Result(true, "tested via open()");
    }
        

    public function addStream()
    {
        $this->process->addStream(Type::STDOUT, new Pipe(Mode::WRITE));
        return new Result(true, "tested via open()");
    }
        

    public function open()
    {
        return new Result($this->process->open());
    }
        

    public function isOpen()
    {
        return new Result($this->process->isOpen());
    }
    
    
    public function getStatus()
    {
        return new Result($this->process->getStatus()->getProcessID()>0);
    }
        

    public function getStream()
    {
        $stream = $this->process->getStream(Type::STDOUT);
        $content = $stream->read();
        return new Result($content=="OK");
    }
        

    public function terminate()
    {
        return new Result($this->process->terminate());
    }
        

    public function close()
    {
        $this->process->getStream(Type::STDOUT)->close();
        return new Result($this->process->close());
    }
}
