<?php

declare(strict_types=1);

namespace Phalcon\Console;

use ReflectionMethod;
use ReflectionParameter;

final class Command
{
    /** @var array<string, array<Param>> */
    private array $params = [
        'required' => [],
        'optional' => [],
    ];

    /**
     * @param string                     $command
     * @param string                     $class
     * @param ReflectionMethod           $method
     * @param array<ReflectionParameter> $params
     */
    public function __construct(
        private readonly string $command,
        private readonly string $class,
        private readonly ReflectionMethod $method,
        array $params = []
    ) {
        foreach ($params as $param) {
            if ($param->isOptional() === true) {
                $this->params['optional'][] = new Param(
                    $param->getName(),
                    $param->getType()?->__toString() ?? 'string',
                    $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
                );
                continue;
            }

            $this->params['required'][] = new Param(
                $param->getName(),
                $param->getType()?->__toString() ?? 'string',
            );
        }
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): ReflectionMethod
    {
        return $this->method;
    }

    /**
     * @return array<Param>
     */
    public function getRequiredParams(): array
    {
        return $this->params['required'];
    }

    /**
     * @return array<Param>
     */
    public function getOptionalParams(): array
    {
        return $this->params['optional'];
    }
}