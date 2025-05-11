<?php

declare(strict_types=1);

namespace Phalcon\Console;

use ReflectionClass;
use ReflectionException;
use SplFileInfo;

use function array_shift;
use function count;
use function explode;
use function implode;
use function is_array;
use function uksort;
use function rtrim;
use function str_replace;
use function strtolower;
use function substr;

class Console
{
    /** @var array<int, string> */
    private static array $suffixes = ['Command', 'Task', 'Action', 'Controller'];

    /** @var array<string, array<Location>> */
    private array $locations;

    /**
     * @param array<int, Location>                 $locations
     * @param array{suffixes?: array<int, string>} $options
     */
    public function __construct(array $locations, array $options = [])
    {
        if (isset($options['suffixes']) === true && is_array($options['suffixes']) === true) {
            self::$suffixes = [...self::$suffixes, ...$options['suffixes']];
        }

        foreach ($locations as $location) {
            if (isset($this->locations[$location->getNamespace()]) === false) {
                $this->locations[$location->getNamespace()] = [];
            }
            $this->locations[$location->getNamespace()][] = $location;
        }

        // Add internal Phalcon commands
        $this->locations['Phalcon\\Console\\Commands'] = [
            new Location(
                'Phalcon\\Console\\Commands',
                __DIR__ . DIRECTORY_SEPARATOR . 'Commands' . DIRECTORY_SEPARATOR
            ),
        ];
    }

    /**
     * @param array<int, string> $args
     *
     * @throws ReflectionException
     */
    public function run(array $args): void
    {
        /** @var array<Command> $cmdList */
        $cmdList = [];
        foreach ($this->locations as $namespace => $locations) {
            foreach ($locations as $location) {
                foreach ($location->getList() as $file) {
                    if ($file instanceof SplFileInfo === false || $file->isDir() === true) {
                        continue;
                    }

                    $cmdList = [...$cmdList, ...$this->addCommand($namespace, $file)];
                }
            }
        }

        usort($cmdList, static fn (Command $a, Command $b): int => $a->getCommand() <=> $b->getCommand());

        $hasArgs = count($args) > 1;
        if ($hasArgs === false) {
            $this->listCommands($cmdList);

            exit(0);
        }

        array_shift($args);
        $requested = array_shift($args);

        foreach ($cmdList as $cmd) {
            if ($cmd->getCommand() !== $requested) {
                continue;
            }
            $invokeArgs = [];
            foreach ($args as $arg) {
                $parts                           = explode('=', $arg);
                $invokeArgs[array_shift($parts)] = implode('=', $parts);
            }
            $class = $cmd->getClass();
            $cmd->getMethod()->invokeArgs(new $class(), $invokeArgs);
        }
    }

    /**
     * @param string      $namespace
     * @param SplFileInfo $file
     *
     * @throws ReflectionException
     * @return array<int, Command>
     */
    private function addCommand(string $namespace, SplFileInfo $file): array
    {
        $commands = [];

        $clean   = substr(rtrim($file->getFilename(), DIRECTORY_SEPARATOR), 0, -4);
        $class   = '\\' . $namespace . '\\' . $clean;
        $command = strtolower(str_replace(self::$suffixes, '', $clean));

        $ref = new ReflectionClass($class);
        foreach ($ref->getMethods() as $method) {
            $invokeMethod = $method->getAttributes(ConsoleCommand::class);
            $params       = $method->getParameters();
            $task         = strtolower(str_replace(self::$suffixes, '', $method->getName()));

            foreach ($invokeMethod as $attr) {
                $real = $attr->newInstance();

                $commands[] = new Command(
                    $real->getName() ?? "$command:$task",
                    $class,
                    $method,
                    $params
                );
            }
        }

        return $commands;
    }

    /**
     * @param array<Command> $commands
     *
     * @return void
     */
    private function listCommands(array $commands): void
    {
        $currentGroup = null;
        foreach ($commands as $cmd) {
            $parts = explode(':', $cmd->getCommand());
            $group = array_shift($parts);
            if ($currentGroup !== $group) {
                $currentGroup = $group;
                echo PHP_EOL . $currentGroup . ':' . PHP_EOL;
            }

            echo str_repeat(' ', 4) . $cmd->getCommand();
            foreach ($cmd->getRequiredParams() as $param) {
                echo " <{$param->getName()}:{$param->getType()}>";
            }
            foreach ($cmd->getOptionalParams() as $param) {
                echo " [{$param->getName()}:{$param->getType()}={$this->defaultToString($param->getDefault())}]";
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;
    }

    private function defaultToString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_int($value)) {
            return (string)$value;
        }
        if (is_float($value)) {
            return (string)$value;
        }
        if (is_string($value) === false) {
            return '';
        }

        return $value;
    }
}