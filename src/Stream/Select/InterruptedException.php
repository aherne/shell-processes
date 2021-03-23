<?php
namespace Lucinda\Process\Stream\Select;

/**
 * Exception thrown when SELECT operation failed (invalid streams supplied or interrupted by signal)
 */
class InterruptedException extends \Exception
{
}
