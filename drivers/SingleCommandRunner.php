<?php
namespace Lucinda\Shell\Driver;

use Lucinda\Shell\Process;
use Lucinda\Shell\Process\Result;
use Lucinda\Shell\Stream\Pipe;
use Lucinda\Shell\Stream\File\Mode;
use Lucinda\Shell\Stream\Type;
use Lucinda\Shell\Stream\Select;
use Lucinda\Shell\Process\Result\Status;
use Lucinda\Shell\Stream\Select\InterruptedException;
use Lucinda\Shell\Stream\Select\TimeoutException;

/**
 * Run a single process reading its STDOUT/STDERR streams by 1024 byte chunks
 */
class SingleCommandRunner extends CommandRunner
{
    const CHUNK_SIZE = 1024;
        
    /**
     * Executes process and returns result
     *
     * @param Process $process
     * @return Result
     */
    public function run(Process $process): Result
    {
        $types = [Type::STDOUT->value, Type::STDERR->value];
        
        // adds STDIN/STDOUT streams, opens process and sets streams as non-blocking
        foreach ($types as $type) {
            $process->addStream($type, new Pipe(Mode::WRITE));
        }
        $process->open();
        foreach ($types as $type) {
            $process->getStream($type)->setBlocking(false);
        }
        
        // performs multiplexing
        try {
            // initializes streams and results
            $streams = [];
            foreach ($types as $type) {
                $streams[$type] = $process->getStream($type);
                $results[$type] = "";
            }
            
            // consumes streams
            while (!empty($streams)) {
                // runs SELECT
                $select = new Select($this->timeout);
                foreach ($streams as $type=>$stream) {
                    $select->addStream($stream);
                }
                $select->run();
                
                // reads streams in 1024 bytes chunks
                foreach ($streams as $type=>$stream) {
                    // reads a 1024 byte chunk of streams' payload and appends it to response
                    $results[$type] .= $stream->read(self::CHUNK_SIZE);
                    
                    // if nothing more to process, mark stream as done and close it
                    if ($stream->getStatus()->isEndOfFile()) {
                        unset($streams[$type]);
                        $stream->close();
                        if (empty($streams)) {
                            $process->close();
                        }
                    }
                }
            }
            
            // prepares results
            if (!empty($results[Type::STDERR->value])) {
                return $this->compileResult(Status::ERROR, $results[Type::STDERR->value]);
            } else {
                return $this->compileResult(Status::COMPLETED, $results[Type::STDOUT->value]);
            }
        } catch (InterruptedException $e) {
            $process->close();
            return $this->compileResult(Status::INTERRUPTED);
        } catch (TimeoutException $e) {
            $process->terminate();
            return $this->compileResult(Status::TERMINATED);
        }
        
        return $results;
    }
}
