<?php

namespace Lucinda\Shell\Driver;

use Lucinda\Shell\Process;
use Lucinda\Shell\Process\Multiplexer;
use Lucinda\Shell\Stream;
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
    public const CHUNK_SIZE = 1024;

    /**
     * {@inheritDoc}
     * @see \Lucinda\Shell\Process\Multiplexer::run()
     */
    public function run(array $processes): array
    {
        $types = [Type::STDOUT->value, Type::STDERR->value];

        // adds STDIN/STDOUT streams, opens process and sets streams as non-blocking
        $this->openProcesses($processes, $types);
        $streams = $this->getStreams($processes, $types);

        // performs multiplexing
        $results = [];
        try {
            // prepares results
            $results = $this->multiplex($processes, $types, $streams);
            foreach ($results as $i=>$info) {
                if (!empty($info[Type::STDERR->value])) {
                    $results[$i] = $this->compileResult(Status::ERROR, $info[Type::STDERR->value]);
                } else {
                    $results[$i] = $this->compileResult(Status::COMPLETED, $info[Type::STDOUT->value]);
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

    /**
     * Adds STDIN/STDOUT streams, opens process and sets streams as non-blocking
     *
     * @param Process[] $processes
     * @param int[] $types
     * @return void
     */
    private function openProcesses(array &$processes, array $types): void
    {
        foreach ($processes as $process) {
            foreach ($types as $type) {
                $process->addStream($type, new Pipe(Mode::WRITE));
            }
            $process->open();
            foreach ($types as $type) {
                $process->getStream($type)->setBlocking(false);
            }
        }
    }

    /**
     * Gets streams to use
     *
     * @param Process[] $processes
     * @param int[] $types
     * @return array<int,array<int,Stream>>
     */
    private function getStreams(array $processes, array $types): array
    {
        $streams = [];
        foreach ($processes as $i=>$process) {
            foreach ($types as $type) {
                $streams[$i][$type] = $process->getStream($type);
            }
        }
        return $streams;
    }

    /**
     * Multiplexes commands and returns results
     *
     * @param Process[] $processes
     * @param int[] $types
     * @param array<int,array<int,Stream>> $streams
     * @return array<int,array<int,string>>
     * @throws InterruptedException
     * @throws TimeoutException
     */
    private function multiplex(array &$processes, array $types, array $streams): array
    {
        $results = [];

        // initializes streams and results
        foreach ($processes as $i=>$process) {
            foreach ($types as $type) {
                $results[$i][$type] = "";
            }
        }

        // consumes streams
        while (!empty($streams)) {
            // runs SELECT
            $select = new Select($this->timeout);
            foreach ($streams as $list) {
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

        return $results;
    }
}
