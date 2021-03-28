<?php
namespace Lucinda\Shell\Stream\File;

/**
 * Enum collecting allowable file access modes (available if stream is of file/pipe type)
 */
interface Mode
{
    const READ = "r";
    const WRITE = "w";
    const APPEND = "a";
}
