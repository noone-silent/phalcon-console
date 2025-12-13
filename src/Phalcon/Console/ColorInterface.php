<?php

namespace Phalcon\Console;

interface ColorInterface
{
    public const GROUP   = 'group';
    public const COMMAND = 'command';
    public const VALUE   = 'value';
    public const DEFAULT = 'default';
    public const TYPE    = 'TYPE';
    public const DESC    = 'DESC';

    public function group(string $text): string;

    public function command(string $text): string;

    public function value(string $text): string;

    public function default(string $text): string;

    public function type(string $text): string;

    public function desc(string $text): string;
}
