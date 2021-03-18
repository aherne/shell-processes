<?php
namespace Test\Lucinda\Process\Pool;

use Lucinda\Process\Pool\Result\Status;
use Lucinda\UnitTest\Result;

class ResultTest
{
    private $object;
    
    public function __construct()
    {
        $this->object = new \Lucinda\Process\Pool\Result();
    }

    public function setStatus()
    {
        $this->object->setStatus(Status::COMPLETED);
        return new Result(true, "tested via getStatus()");
    }
        

    public function getStatus()
    {
        return new Result($this->object->getStatus()==Status::COMPLETED);
    }
        

    public function setPayload()
    {
        $this->object->setPayload("asd");
        return new Result(true, "tested via getPayload()");
    }
        

    public function getPayload()
    {
        return new Result($this->object->getPayload()=="asd");
    }
        

    public function setDuration()
    {
        $this->object->setDuration(10);
        return new Result(true, "tested via getDuration()");
    }
        

    public function getDuration()
    {
        return new Result($this->object->getDuration()==10);
    }
}
