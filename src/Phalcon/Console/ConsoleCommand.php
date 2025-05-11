<?php

declare(strict_types=1);

namespace Phalcon\Console;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class ConsoleCommand
{
    public function __construct(private ?string $name = null)
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}