<?php

declare(strict_types=1);

namespace Phalcon\Console;

use DateTime;
use Phalcon\Cli\Console as PhalconConsole;
use Phalcon\Console\ColorInterface as CI;
use Phalcon\Di\DiInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use SplFileInfo;
use Throwable;

use function array_shift;
use function count;
use function end;
use function explode;
use function get_declared_classes;
use function implode;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_string;
use function method_exists;
use function rtrim;
use function str_repeat;
use function str_replace;
use function str_starts_with;
use function strtolower;
use function substr;
use function usort;

use const DIRECTORY_SEPARATOR;
use const PHP_EOL;

class Console
{
    /** @var array<int, string> */
    private static array $suffixes = ['Action', 'Command', 'Controller', 'Task'];

    /** @var array<int, Location> */
    private array $locations;

    private string $bootstrapFile;

    private ?ColorInterface $color = null;

    private int $loadedClasses = 0;

    private string $containerName = 'di';

    /**
     * @param array<int, Location>                                                                 $locations
     * @param array{bootstrap: string, di?: string, colored?: bool, suffixes?: array<int, string>} $options
     */
    public function __construct(array $locations, array $options)
    {
        if (isset($options['suffixes']) === true && is_array($options['suffixes']) === true) {
            self::$suffixes = [...self::$suffixes, ...$options['suffixes']];
        }
        if (isset($options['di']) === true && $options['di'] !== '') {
            $this->containerName = $options['di'];
        }

        $this->bootstrapFile = $options['bootstrap'];

        $this->locations = $locations;
    }

    public function setColor(ColorInterface $color): void
    {
        $this->color = $color;
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
        foreach ($this->locations as $location) {
            foreach ($location->getList() as $file) {
                if ($file instanceof SplFileInfo === false || $file->isDir() === true) {
                    continue;
                }

                $cmdList = [...$cmdList, ...$this->addCommand($file)];
            }
        }

        usort($cmdList, static fn (Command $a, Command $b): int => $a->getCommand() <=> $b->getCommand());

        $hasArgs = count($args) > 1;
        if ($hasArgs === false) {
            $this->listCommands($cmdList);

            exit(0);
        }

        $this->executeCommand($args, $cmdList);
    }

    /**
     * @param SplFileInfo $file
     *
     * @throws ReflectionException
     * @return array<int, Command>
     */
    private function addCommand(SplFileInfo $file): array
    {
        require_once $file->getPathname();
        $classes    = get_declared_classes();
        $newClasses = count($classes);
        if ($newClasses === $this->loadedClasses) {
            return [];
        }

        $this->loadedClasses = $newClasses;
        $loadClass           = end($classes);

        if ($loadClass === false) {
            return [];
        }

        $ref       = new ReflectionClass($loadClass);
        $namespace = $ref->getNamespaceName();

        $clean   = str_replace(
            self::$suffixes,
            '',
            substr(rtrim($file->getFilename(), DIRECTORY_SEPARATOR), 0, -4)
        );
        $class   = '\\' . $namespace . '\\' . $clean;
        $command = strtolower(str_replace(self::$suffixes, '', $clean));

        $commands = [];
        foreach ($ref->getMethods() as $method) {
            $invokeMethod = $method->getAttributes(PhalconConsoleCommand::class);
            $params       = $method->getParameters();
            $task         = strtolower(str_replace(self::$suffixes, '', $method->getName()));

            foreach ($invokeMethod as $attr) {
                $real       = $attr->newInstance();
                $commands[] = new Command(
                    "$command:$task",
                    $class,
                    $method,
                    $params,
                    $real->getDescription()
                );

                if ($real->getAlias() === null) {
                    continue;
                }
                $commands[] = new Command(
                    $real->getAlias(),
                    $class,
                    $method,
                    $params,
                    $real->getDescription() ?? "Alias of $command:$task"
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
        $space        = str_repeat(' ', 2);
        $dspace       = $space . $space;

        echo 'Phalcon Console', PHP_EOL, PHP_EOL;
        echo $this->colored(CI::GROUP, 'Usage:'), PHP_EOL;
        echo $space, 'command arg=value arg2=value2', PHP_EOL, PHP_EOL;
        echo $this->colored(CI::GROUP, 'Available commands:'), PHP_EOL;
        foreach ($commands as $cmd) {
            $parts = explode(':', $cmd->getCommand());
            $group = array_shift($parts);
            if ($currentGroup !== $group) {
                $currentGroup = $group;
                echo PHP_EOL, $space, $this->colored(CI::GROUP, $currentGroup . ':'), PHP_EOL;
            }

            echo $this->colored(CI::COMMAND, $dspace . $cmd->getCommand());
            if ($cmd->hasParams() === true) {
                $params = [];
                foreach ($cmd->getRequiredParams() as $param) {
                    $params[] = "<{$this->format($param)}>";
                }
                foreach ($cmd->getOptionalParams() as $param) {
                    $params[] = "[{$this->format($param)}]";
                }
                echo ' ', implode(' ', $params);
            }
            echo PHP_EOL;

            $description = $cmd->getDescription();
            if ($description !== null) {
                echo $dspace, $this->colored(CI::DESC, $description), PHP_EOL;
            }
        }
        echo PHP_EOL;
    }

    /**
     * @param array<int, string> $args
     * @param array<Command>     $cmdList
     *
     * @return void
     */
    private function executeCommand(array $args, array $cmdList): void
    {
        array_shift($args);
        $requested = array_shift($args);

        $matched = null;
        foreach ($cmdList as $cmd) {
            if ($cmd->getCommand() === $requested) {
                $matched = $cmd;
                break;
            }
        }

        if ($matched === null) {
            echo "Command \"$requested\" not found", PHP_EOL;
            return;
        }

        $invokeArgs = [];
        foreach ($args as $arg) {
            $parts                = explode('=', $arg);
            $argName              = array_shift($parts);
            $argValue             = implode('=', $parts);
            $invokeArgs[$argName] = $this->transformValues(
                $matched->getParamByName($argName),
                $argValue
            );
        }
        $class = $matched->getClass();

        include $this->bootstrapFile;

        if (
            isset(${$this->containerName}) === false ||
            ${$this->containerName} instanceof DiInterface === false
        ) {
            throw new RuntimeException('Phalcon Console needs $di to be defined!');
        }

        /** @var DiInterface $di */
        $di = ${$this->containerName};

        $arguments           = [];
        $arguments['task']   = $matched->getClass();
        $arguments['action'] = str_replace(self::$suffixes, '', $matched->getMethod()->getName());
        $arguments['params'] = $invokeArgs;

        $cli = new PhalconConsole($di);
        $cli->handle($arguments);
    }

    private function colored(string $type, string $text): string
    {
        if ($this->color === null || method_exists($this->color, $type) === false) {
            return $text;
        }

        $colored = $this->color->$type($text);

        return is_string($colored) ? $colored : $text;
    }

    private function format(Param $param): string
    {
        return $this->colored(CI::VALUE, $param->getName()) .
            ':' .
            $this->colored(CI::TYPE, $param->getType()) .
            ($param->getDefault() !== null
                ? $this->colored(CI::DEFAULT, '=' . $this->defaultToString($param->getDefault()))
                : '');
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

    private function transformValues(?Param $param, mixed $value): mixed
    {
        if ($value === 'true' && $param?->getType() === 'bool') {
            return true;
        }
        if ($value === 'false' && $param?->getType() === 'bool') {
            return false;
        }
        if ($param === null) {
            return $value;
        }

        if (str_starts_with($param->getType(), 'DateTime') === true) {
            $type = $param->getType();
            if (is_string($value) === false) {
                return $value;
            }
            try {
                return match (strtolower($type)) {
                    'datetimeinterface', 'datetime' => new DateTime($value),
                    default => new $type($value)
                };
            } catch (Throwable) {
                return $value;
            }
        }

        return $value;
    }
}