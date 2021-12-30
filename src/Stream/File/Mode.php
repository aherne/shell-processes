<?php
namespace Lucinda\Shell\Stream\File;

/**
 * Enum collecting allowable file access modes (available if stream is of file/pipe type)
 */
enum Mode: string
{
    case READ = "r";
    case WRITE = "w";
    case APPEND = "a";
}
