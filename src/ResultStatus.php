<?php
namespace Lucinda\Process;

interface ResultStatus
{
    const ERROR = 0;
    const COMPLETED = 1;
    const TERMINATED = 2;
    const INTERRUPTED = 3;
}
