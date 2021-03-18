<?php
namespace Lucinda\Process\Pool\Result;

/**
 * Enum encapsulating outcome statuses of executed pool process
 */
interface Status
{
    const ERROR = 0;
    const COMPLETED = 1;
    const TERMINATED = 2;
    const INTERRUPTED = 3;
}
