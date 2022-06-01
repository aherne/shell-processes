<?php

namespace Lucinda\Shell\Stream;

/**
 * Enum collecting standard stream types
 */
enum Type: int
{
    case STDIN = 0;
    case STDOUT = 1;
    case STDERR = 2;
}
