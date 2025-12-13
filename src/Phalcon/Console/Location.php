<?php

declare(strict_types=1);

namespace Phalcon\Console;

use CachingIterator;
use FilesystemIterator;
use Phalcon\Console\Iterators\PhpFileFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

final readonly class Location
{
    public function __construct(private string $location)
    {
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @return CachingIterator<mixed, mixed, RecursiveIteratorIterator<PhpFileFilterIterator>>
     */
    public function getList(): CachingIterator
    {
        return new CachingIterator(
            new RecursiveIteratorIterator(
                new PhpFileFilterIterator(
                    new RecursiveDirectoryIterator(
                        $this->getLocation(),
                        FilesystemIterator::SKIP_DOTS
                    ),
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            )
        );
    }
}
