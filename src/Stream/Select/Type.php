<?php

namespace Lucinda\Shell\Stream\Select;

/**
 * Enum of possible SELECT file descriptor set types
 */
enum Type: int
{
    case READ = 1;
    case WRITE = 2;
    case EXCEPT = 3;
}
