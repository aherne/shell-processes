<?php
namespace Lucinda\Process\Stream;

/**
 * Enum collecting standard stream types
 */
interface Type
{
    const STDIN = 0;
    const STDOUT = 1;
    const STDERR = 2;
}
