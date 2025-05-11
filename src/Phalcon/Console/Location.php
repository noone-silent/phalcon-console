<?php

declare(strict_types=1);

namespace Phalcon\Console;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

use function trim;

final class Location
{
    public function __construct(private string $namespace, private readonly string $location)
    {
        $this->namespace = trim($this->namespace, '\\');
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return RecursiveIteratorIterator<RecursiveDirectoryIterator>
     */
    public function getList(): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->getLocation(),
                FilesystemIterator::SKIP_DOTS
            ),
            RecursiveIteratorIterator::CHILD_FIRST
        );
    }
}
