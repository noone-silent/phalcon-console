<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use Phalcon\Console\PhalconConsoleCommand;

class Dummy
{
    #[PhalconConsoleCommand]
    public function say(string $name, string $from = 'me'): void
    {
        echo "Hello $name! From $from" . PHP_EOL;
    }
}