<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands\Utils;

use Phalcon\Cli\Task;
use Phalcon\Console\PhalconConsoleCommand;

use const PHP_EOL;

class ZipperTask extends Task
{
    #[PhalconConsoleCommand]
    public function createAction(string $name, string $directory): void
    {
        echo "I am creating a zip file named $name.zip from directory $directory.", PHP_EOL;
    }
}