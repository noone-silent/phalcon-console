<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use DateTimeInterface;
use Phalcon\Console\PhalconConsoleCommand;

class Console
{
    #[PhalconConsoleCommand]
    public function build(bool $debug = false, bool $cache = true): void
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
    public function clear(): void
    {
        echo "I cleared the cache!", PHP_EOL;
    }
}