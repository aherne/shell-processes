<?php
namespace Lucinda\Process;

abstract class RunnableProcess
{
    protected $process;
    protected $timeout;
    
    public function __construct(string $command, bool $autoEscape = true, int $timeout = 100)
    {
        $this->process = new Process($command, $autoEscape);
        $this->process->addStream(Stream\Type::STDOUT, new Stream\Pipe(Stream\FileMode::WRITE));
        $this->process->addStream(Stream\Type::STDERR, new Stream\Pipe(Stream\FileMode::WRITE));
        $this->timeout = $timeout;
    }
    
    public function init(): void
    {
        $this->process->open();
        $this->process->getStream(Stream\Type::STDOUT)->setBlocking(false);
        $this->process->getStream(Stream\Type::STDERR)->setBlocking(false);
    }
    
    abstract public function service(): Result;
    
    public function destroy(): void
    {
        $this->process->getStream(Stream\Type::STDOUT)->close();
        $this->process->getStream(Stream\Type::STDERR)->close();
        $this->process->close();
    }
}
