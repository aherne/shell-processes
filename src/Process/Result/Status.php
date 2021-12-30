<?php
namespace Lucinda\Shell\Process\Result;

/**
 * Enum encapsulating outcome statuses of executed pool process
 */
enum Status: int
{
    case ERROR = 0;
    case COMPLETED = 1;
    case TERMINATED = 2;
    case INTERRUPTED = 3;
}
