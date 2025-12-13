<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phalcon\Console\PhalconConsoleCommand;

class Basics
{
    #[PhalconConsoleCommand]
    public function testInteger(int $value): void
    {
        echo "Value is: $value", PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testFloat(float $value): void
    {
        echo "Value is: $value", PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testBool(bool $value): void
    {
        echo "Value is: ", $value ? 'true' : 'false', PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTime(DateTime $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTimeImmutable(DateTimeImmutable $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTimeInterface(DateTimeInterface $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand(alias: 'super-thing')]
    public function testWithAlias(): void
    {
        echo 'In testWithAlias', PHP_EOL;
    }

    #[PhalconConsoleCommand(description: 'This is a optional description of what the command does.')]
    public function testDescription(): void
    {
        echo 'In testWithAlias', PHP_EOL;
    }
}
