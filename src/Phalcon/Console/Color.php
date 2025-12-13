<?php

declare(strict_types=1);

namespace Phalcon\Console;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use Throwable;

final readonly class Color implements ColorInterface
{
    public function __construct(private ConsoleColor $color = new ConsoleColor())
    {
    }

    public function group(string $text): string
    {
        return $this->wrap(['bold', 'yellow'], $text);
    }

    public function command(string $text): string
    {
        return $this->wrap(['green'], $text);
    }

    public function value(string $text): string
    {
        return $this->wrap('bold', $text);
    }

    public function type(string $text): string
    {
        return $this->wrap('italic', $text);
    }

    public function default(string $text): string
    {
        return $this->wrap('green', $text);
    }

    public function desc(string $text): string
    {
        return $text;
    }

    /**
     * @param string|array<int, string> $styles
     * @param string                    $text
     *
     * @return string
     */
    private function wrap(string | array $styles, string $text): string
    {
        try {
            return $this->color->apply($styles, $text);
        } catch (Throwable) {
            return $text;
        }
    }
}
