<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use Phalcon\Console\ConsoleCommand;

class Dummy
{
    #[ConsoleCommand]
    public function say(string $name): void
    {
        echo "Hello $name!" . PHP_EOL;
    }
}