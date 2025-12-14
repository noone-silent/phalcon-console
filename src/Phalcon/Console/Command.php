<?php

declare(strict_types=1);

namespace Phalcon\Console;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

use function strtolower;

final class Command
{
    /** @var array<string, array<Param>> */
    private array $params = [
        'required' => [],
        'optional' => [],
    ];

    private int $paramCount = 0;

    /**
     * @param string                     $command
     * @param ReflectionMethod           $method
     * @param array<ReflectionParameter> $params
     * @param string|null                $description
     */
    public function __construct(
        private readonly string $command,
        private readonly ReflectionMethod $method,
        array $params = [],
        private readonly ?string $description = null
    ) {
        foreach ($params as $param) {
            $this->paramCount++;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getClass(): ReflectionClass
    {
        return $this->getMethod()->getDeclaringClass();
    }

    public function getClassName(): string
    {
        return $this->method->getDeclaringClass()->getShortName();
    }

    public function getMethod(): ReflectionMethod
    {
        return $this->method;
    }

    public function getMethodName(): string
    {
        return $this->method->getName();
    }

    public function hasParams(): bool
    {
        return $this->paramCount !== 0;
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

    public function getParamByName(string $name): ?Param
    {
        $checkName = strtolower($name);

        foreach ($this->params['required'] as $param) {
            if ($param->getName(true) === $checkName) {
                return $param;
            }
        }

        foreach ($this->params['optional'] as $param) {
            if ($param->getName(true) === $checkName) {
                return $param;
            }
        }

        return null;
    }
}