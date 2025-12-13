<?php

declare(strict_types=1);

namespace Phalcon\Console;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class PhalconConsoleCommand
{
    public function __construct(private ?string $alias = null, private ?string $description = null)
    {
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
