<?php

declare(strict_types=1);

namespace Phalcon\Console;

use function strtolower;

final readonly class Param
{
    private string $lowerName;

    public function __construct(
        private string $name,
        private string $type,
        private mixed $default = null
    ) {
        $this->lowerName = strtolower($name);
    }

    public function getName(bool $lower = false): string
    {
        return $lower ? $this->lowerName : $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }
}