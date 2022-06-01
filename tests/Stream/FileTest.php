<?php

namespace Test\Lucinda\Shell\Stream;

use Lucinda\Shell\Stream\File;
use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Stream\Type;
use Lucinda\UnitTest\Result;
use Lucinda\Shell\Stream\Pipe;

class FileTest
{
    private $path;
    private $process;
    private $stdin;
    private $stdout;

    public function __construct()
    {
        $this->path = dirname(__DIR__, 2)."/test.txt";
        $this->stdin = new File($this->path, Mode::READ);
        $this->stdout = new Pipe(Mode::WRITE);
    }

    public function __destruct()
    {
        proc_close($this->process);
    }

    public function getDescriptorSpecification()
    {
        $pipes = [];
        $this->process = proc_open("php ".dirname(__DIR__, 2).DIRECTORY_SEPARATOR."script.php", [
            Type::STDIN->value=>$this->stdin->getDescriptorSpecification(),
            Type::STDOUT->value=>$this->stdout->getDescriptorSpecification()
        ], $pipes);
        $this->stdout->setFileDescriptor($pipes[Type::STDOUT->value]);
        return new Result(is_resource($this->process));
    }


    public function setFileDescriptor()
    {
        return new Result(true, "tested via getDescriptorSpecification()");
    }


    public function getFileDescriptor()
    {
        return new Result(true, "operation not yet supported by PHP");
    }


    public function setTimeout()
    {
        return new Result(true, "operation not yet supported by PHP");
    }


    public function setBlocking()
    {
        return new Result(true, "operation not yet supported by PHP");
    }


    public function write()
    {
        return new Result(true, "operation not yet supported by PHP");
    }


    public function read()
    {
        return new Result($this->stdout->read()=="OK", "operation tested indirectly");
    }


    public function getStatus()
    {
        return new Result(true, "operation not yet supported by PHP");
    }


    public function close()
    {
        return new Result($this->stdout->close(), "operation tested indirectly");
    }
}
