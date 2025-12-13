<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use Phalcon\Cli\Task;
use Phalcon\Console\PhalconConsoleCommand;

class DummyTask extends Task
{
    #[PhalconConsoleCommand]
    public function sayAction(string $name, string $from = 'me'): void
    {
        echo "Hello $name! From $from" . PHP_EOL;
    }
}