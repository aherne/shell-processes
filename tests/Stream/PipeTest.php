<?php

namespace Test\Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Stream\Type;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Stream\Pipe;

class PipeTest
{
    private $process;
    private $stdin;
    private $stdout;

    public function __construct()
    {
        $this->stdin = new Pipe(Mode::READ);
        $this->stdout = new Pipe(Mode::WRITE);
    }

    public function __destruct()
    {
        proc_close($this->process);
    }

    public function getDescriptorSpecification()
    {
        $pipes = [];
        $this->process = proc_open(
            "php ".dirname(__DIR__, 2).DIRECTORY_SEPARATOR."script.php",
            [
            Type::STDIN->value=>$this->stdin->getDescriptorSpecification(),
            Type::STDOUT->value=>$this->stdout->getDescriptorSpecification()
            ],
            $pipes
        );
        $this->stdin->setFileDescriptor($pipes[Type::STDIN->value]);
        $this->stdout->setFileDescriptor($pipes[Type::STDOUT->value]);
        return new Result(is_resource($this->process));
    }


    public function setFileDescriptor()
    {
        return new Result(true, "tested via getDescriptorSpecification()");
    }


    public function getFileDescriptor()
    {
        return new Result($this->stdout->getFileDescriptor()!=null);
    }


    public function setBlocking()
    {
        return new Result($this->stdout->setBlocking(true));
    }


    public function setTimeout()
    {
        return new Result($this->stdout->setTimeout(0));
    }


    public function write()
    {
        return new Result($this->stdin->write("OK"));
    }

    public function read()
    {
        return new Result($this->stdout->read() == "OK");
    }


    public function getStatus()
    {
        return new Result($this->stdout->getStatus()->isBlocked());
    }


    public function close()
    {
        $this->stdin->close();
        return new Result($this->stdout->close());
    }
}
