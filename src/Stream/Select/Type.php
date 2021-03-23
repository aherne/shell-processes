<?php
namespace Lucinda\Process\Stream\Select;

/**
 * Enum of possible SELECT file descriptor set types
 */
interface Type
{
    const READ = 1;
    const WRITE = 2;
    const EXCEPT = 3;
}
