<?php
namespace Lucinda\Shell\Driver;

use Lucinda\Shell\Process\Multiplexer;
use Lucinda\Shell\Stream\Pipe;
use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Stream\Type;
use Lucinda\Shell\Stream\Select;
use Lucinda\Shell\Process\Result\Status;
use Lucinda\Shell\Process\Result;
use Lucinda\Shell\Stream\Select\InterruptedException;
use Lucinda\Shell\Stream\Select\TimeoutException;

/**
 * Runs processes in paralel, reading their STDOUT/STDERR streams by 1024 byte chunks
 */
class MultiCommandRunner extends CommandRunner implements Multiplexer
{
    const CHUNK_SIZE = 1024;
    
    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Process\Multiplexer::run()
     */
    public function run(array $processes): array
    {
        $types = [Type::STDOUT, Type::STDERR];
        
        // adds STDIN/STDOUT streams, opens process and sets streams as non-blocking
        foreach ($processes as $i=>$process) {
            foreach ($types as $type) {
                $process->addStream($type, new Pipe(Mode::WRITE));
            }
            $process->open();
            foreach ($types as $type) {
                $process->getStream($type)->setBlocking(false);
            }
        }
        
        // performs multiplexing
        $results = [];
        try {
            // initializes streams and results
            $streams = [];
            foreach ($processes as $i=>$process) {
                foreach ($types as $type) {
                    $streams[$i][$type] = $process->getStream($type);
                    $results[$i][$type] = "";
                }
            }
            
            // consumes streams
            while (!empty($streams)) {
                // runs SELECT
                $select = new Select($this->timeout);
                foreach ($streams as $i=>$list) {
                    foreach ($list as $stream) {
                        $select->addStream($stream);
                    }
                }
                $select->run();
                
                // reads streams in 1024 bytes chunks
                foreach ($streams as $i=>$list) {
                    foreach ($list as $type=>$stream) {
                        // reads a 1024 byte chunk of streams' payload and appends it to response
                        $results[$i][$type] .= $stream->read(self::CHUNK_SIZE);
                        
                        // if nothing more to process, mark stream as done and close it
                        if ($stream->getStatus()->isEndOfFile()) {
                            unset($streams[$i][$type]);
                            $stream->close();
                            if (empty($streams[$i])) {
                                unset($streams[$i]);
                                $processes[$i]->close();
                            }
                        }
                    }
                }
            }
            
            // prepares results
            foreach ($results as $i=>$info) {
                if (!empty($info[Type::STDERR])) {
                    $results[$i] = $this->compileResult(Status::ERROR, $info[Type::STDERR]);
                } else {
                    $results[$i] = $this->compileResult(Status::COMPLETED, $info[Type::STDOUT]);
                }
            }
        } catch (InterruptedException $e) {
            foreach ($processes as $i=>$process) {
                if (isset($streams[$i])) {
                    $process->close();
                    $results[$i] = $this->compileResult(Status::INTERRUPTED);
                }
            }
        } catch (TimeoutException $e) {
            foreach ($processes as $i=>$process) {
                if (isset($streams[$i])) {
                    $process->terminate();
                    $results[$i] = $this->compileResult(Status::TERMINATED);
                }
            }
        }
        
        return $results;
    }
}
