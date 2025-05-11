<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use Phalcon\Console\ConsoleCommand;

class Console
{
    #[ConsoleCommand]
    public function build(bool $debug = false, bool $cache = true): void
    {
    }

    #[ConsoleCommand]
    public function clear(): void
    {
    }
}