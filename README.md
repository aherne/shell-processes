# Lucinda Shell Process & Pooling API

This API is a light weight wrapper over PHP proc_* functions, able to execute shell processes individually, pool them or handle streams that traverse them. Its design goals were:

- **platform agnosticity**: API doesn't take any assumptions on the operating system process runs into
- **least assumption principle**: API doesn't take any assumption how you will handle process streams, so provides a skeleton instead
- **elegance and simplicity**: API is written on *less is more* principle so it's easy to understand and flexible to extend

API is 100% unit tested, fully PSR-4 compliant and only requiring PHP7.1+ interpreter. For installation you just need to write this in console:

```console
composer require lucinda/shell
```

Then use one of main classes provided (using use **Lucinda\Shell** namespace):

- [Stream](https://github.com/aherne/shell-processes/blob/master/src/Stream.php): encapsulates an abstract data stream to be used by process (eg: STDIN/STDOUT/STDER). Extended by:
    - [Stream\Pipe](https://github.com/aherne/shell-processes/blob/master/src/Stream/Pipe.php): encapsulates a stream of un-named pipes to be processed immediately
    - [Stream\File](https://github.com/aherne/shell-processes/blob/master/src/Stream/File.php): encapsulates a stream of that delegates to a file on disk
    - [Stream\Resource](https://github.com/aherne/shell-processes/blob/master/src/Stream/Resource.php): encapsulates a stream of that delegates to a *resource* (eg: socket)
- [Process](https://github.com/aherne/shell-processes/blob/master/src/Process.php): encapsulates a single process using [Stream](https://github.com/aherne/shell-processes/blob/master/src/Stream.php) instances above
- [Pool](https://github.com/aherne/shell-processes/blob/master/src/Pool.php): encapsulates a pool of processes to be executed in paralel queue-ing [Process](https://github.com/aherne/shell-processes/blob/master/src/Process.php) instances above and using [Process\Multiplexer](https://github.com/aherne/shell-processes/blob/master/src/Process/Multiplexer.php) implementation for processing

Each of above classes branches through its methods to deeper classes that become relevant depending on the complexity of process execution logic. To make things simple following drivers were provided (using **Lucinda\Shell\Driver** namespace):

- [SingleCommandRunner](https://github.com/aherne/shell-processes/blob/master/drivers/SingleCommandRunner.php): executes a single [Process](https://github.com/aherne/shell-processes/blob/master/src/Process.php) and returns [Process\Result](https://github.com/aherne/shell-processes/blob/master/src/Process/Result.php)
- [MultiCommandRunner](https://github.com/aherne/shell-processes/blob/master/drivers/MultiCommandRunner.php): executes list of [Process](https://github.com/aherne/shell-processes/blob/master/src/Process.php)-es and returns list of [Process\Result](https://github.com/aherne/shell-processes/blob/master/src/Process/Result.php)-s

Both classes make use of I/O multiplexing that uses SELECT command underneath.

## Executing Single Processes

To execute a single shell command you can use [Lucinda\Shell\Process](https://github.com/aherne/shell-processes/blob/master/src/Process.php) for minute control or [Lucinda\Shell\Driver\SingleCommandRunner](https://github.com/aherne/shell-processes/blob/master/drivers/SingleCommandRunner.php) driver provided:

```php
use Lucinda\Shell\Driver\SingleCommandRunner;
use Lucinda\Shell\Process;

$object = new SingleCommandRunner(5);
$result = $object->run(new Process("YOUR_SHELL_COMMAND"));
```

This is superior to **shell_exec** because it will:
- terminate if shell command execution exceeds 5 seconds
- process STDOUT/STDERR streams separately using IO multiplexing
- read streams in parallel using chunks
- automatically escape shell command

## Executing Multiple Processes

To execute multiple shell commands at once you can implement your own [Lucinda\Shell\Process\Multiplexer](https://github.com/aherne/shell-processes/blob/master/src/Process\Multiplexer.php) for minute control or [Lucinda\Shell\Driver\MultiCommandRunner](https://github.com/aherne/shell-processes/blob/master/drivers/MultiCommandRunner.php) driver provided:

```php
use Lucinda\Shell\Driver\MultiCommandRunner;
use Lucinda\Shell\Process;

$object = new MultiCommandRunner(5);
$results = $object->run([
  new Process("YOUR_SHELL_COMMAND1"),
  new Process("YOUR_SHELL_COMMAND2"),
  ...
]);
```

This will:
- open processes simultaneously
- terminate if ANY of processes exceeds 5 seconds
- use non-blocking approach, allowing streams processing to be done in parallel
- automatically escape shell commands

### Pooling Multiplexed Processes

To run multiplexed processes in pools of fixed size (5 in this example):

```php
use Lucinda\Shell\Driver\MultiCommandRunner;
use Lucinda\Shell\Process;
use Lucinda\Shell\Pool;

# defines a pool of max 5 capacity
$pool = new Pool(5);
# adds processes to pool
$pool->submit(new Process("YOUR_SHELL_COMMAND1"));
$pool->submit(new Process("YOUR_SHELL_COMMAND2"));
...
# executes processes in batches, delegating to Multiplexer instances
$results = $pool->shutdown(new MultiCommandRunner(5));
```

This will:
- execute N processes at same time (5 in above example)
- when they all end, proceed to next batch
