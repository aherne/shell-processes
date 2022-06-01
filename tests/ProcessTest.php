<?php

namespace Test\Lucinda\Shell;

use Lucinda\Shell\Process;
use Lucinda\Shell\Stream\Pipe;
use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Stream\Type;
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
        $this->process->addStream(Type::STDOUT->value, new Pipe(Mode::WRITE));
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
        $stream = $this->process->getStream(Type::STDOUT->value);
        $content = $stream->read();
        return new Result($content=="OK");
    }


    public function terminate()
    {
        return new Result($this->process->terminate());
    }


    public function close()
    {
        $this->process->getStream(Type::STDOUT->value)->close();
        return new Result($this->process->close());
    }
}
