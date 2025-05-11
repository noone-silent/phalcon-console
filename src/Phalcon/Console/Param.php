<?php

declare(strict_types=1);

namespace Phalcon\Console;

final readonly class Param
{
    public function __construct(
        private string $name,
        private string $type,
        private mixed $default = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
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