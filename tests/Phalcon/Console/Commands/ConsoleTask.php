<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use DateTimeInterface;
use Phalcon\Cli\Task;
use Phalcon\Console\PhalconConsoleCommand;

class ConsoleTask extends Task
{
    #[PhalconConsoleCommand]
    public function buildAction(bool $debug = false, bool $cache = true): void
    {
        echo "Debug enabled: ", $debug === true ? 'true' : 'false', PHP_EOL;
        echo "Cache enabled: ", $cache === true ? 'true' : 'false', PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function dateTestAction(DateTimeInterface $test): void
    {
        var_dump($test);
    }

    #[PhalconConsoleCommand]
    public function clearAction(): void
    {
        echo "I cleared the cache!", PHP_EOL;
    }
}