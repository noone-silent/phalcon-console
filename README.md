# Phalcon Console

The **Phalcon Console** package is a robust command-line interface (CLI) framework designed to simplify the creation
and management of console Commands in Phalcon applications. Built with the Phalcon framework ecosystem in mind, this
package provides developers with an elegant, attribute-driven approach to defining and executing command-line tasks
with minimal boilerplate code.

## Core Functionality

At its heart, the Phalcon Console package automates the discovery, registration, and execution of console Commands
from your application's codebase. Rather than manually maintaining a command registry, the package uses PHP's
reflection capabilities to dynamically scan specified directories for command classes, automatically extracting
metadata about available Commands and their parameters through attributes and method signatures.

The package implements a command discovery system that scans PHP files in designated locations, parses class
definitions to identify Commands, and extracts method-level metadata through the `#[PhalconConsoleCommand]` attribute.
This allows developers to define new Commands by simply creating appropriately named classes with decorated methods.
No complex configuration is required.

# Installation

```bash
composer require noone-silent/phalcon-console
```

# Configuration

For the discovery of Commands, add a list of all folders that you wish to scan for PHP-Classes. Modify your project
composer.json and add the following configuration, adjusted to your needs.

```json
{
  "extras": {
    "phalcon-console": {
      "locations": [
        ".",
        "src/",
        "app/",
        "cli/"
      ]
    }
  }
}
```

Now add the `#[PhalconConsoleCommand]` attribute to any method you want to be usable as command line command.

```php
// Before

class MyCliTask extends Task
{
    public function mainAction(string $date): void
    {
        // Do something
    }
    
    public function doSomethingAction(int $a, int $b = 2): void
    {
        // Do something
    }
}

// After

use Phalcon\Console\PhalconConsoleCommand;

class MyCliTask extends Task
{
    #[PhalconConsoleCommand]
    public function mainAction(string $date): void
    {
        // Do something
    }
    #[PhalconConsoleCommand]
    public function doSomethingAction(int $a, int $b = 2): void
    {
        // Do something
    }
}
```

In the project root, where your `vendor/` folder is located, you can run now the following script:

```bash
vendor/bin/phalcon-console
```

The output should have a listing looking like this:

```bash
mycli:
  mycli:main <date:string>
  mycli:dosomething <a:int> [b:int=2]
```

Parameters that are required are wrapped in `<` and `>`. Optional parameters are wrapped in `[` and `]`.

Now you can execute a command:

```bash
vendor/bin/phalcon-console mycli:main date=2025-12-13

# or

vendor/bin/phalcon-console mycli:dosomething a=2 b=3
```

# Advanced configuration

There are a few other options you can use. Below is the list with their default values.

```json
{
  "extras": {
    "phalcon-console": {
      "colored": true,
      "locations": [],
      "options": {
        "suffixes": [
          "Command",
          "Task",
          "Action",
          "Controller"
        ]
      }
    }
  }
}
```

The option **colored** is used to enable or disable colored output.

With the **locations** option, you define which folders need to be scanned. It uses the project root as a starting point.

If your command has a suffix that you want to remove, you can add it to the **suffixes** option.

# Testing

```bash
# start docker environment
docker compose up -d
```

```bash
# jump into the phalcon container
docker compose exec phalcon bash
```

```bash
# install composer requirements
composer install
```

```bash
# get a complete list of all available Commands
bin/phalcon-console
```

```bash
# execute a single command with some arguments
bin/phalcon-console dummy:say name=Phalcon
```
