<?php

declare(strict_types=1);

namespace Phalcon\Console\Commands;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Phalcon\Cli\Task;
use Phalcon\Console\PhalconConsoleCommand;

class BasicsTask extends Task
{
    #[PhalconConsoleCommand]
    public function testIntegerAction(int $value): void
    {
        echo "Value is: $value", PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testFloatAction(float $value): void
    {
        echo "Value is: $value", PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testBoolAction(bool $value): void
    {
        echo "Value is: ", $value ? 'true' : 'false', PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTimeAction(DateTime $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTimeImmutableAction(DateTimeImmutable $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand]
    public function testDateTimeInterfaceAction(DateTimeInterface $value): void
    {
        echo $value->format(DATE_ATOM), PHP_EOL;
    }

    #[PhalconConsoleCommand(alias: 'super-thing')]
    public function testWithAliasAction(): void
    {
        echo 'In testWithAlias', PHP_EOL;
    }

    #[PhalconConsoleCommand(description: 'This is a optional description of what the command does.')]
    public function testDescriptionAction(): void
    {
        echo 'In testWithAlias', PHP_EOL;
    }
}
