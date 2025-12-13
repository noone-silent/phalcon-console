<?php

declare(strict_types=1);

namespace Phalcon\Console\Iterators;

use RecursiveFilterIterator;
use SplFileInfo;

use function str_ends_with;

class PhpFileFilterIterator extends RecursiveFilterIterator
{
    public function accept(): bool
    {
        $current = $this->current();
        if ($current instanceof SplFileInfo === false) {
            return false;
        }

        // Leave the isDir() check else the recursive functionality is gone.
        return $current->isDir() || str_ends_with($current->getFilename(), '.php') === true;
    }
}
