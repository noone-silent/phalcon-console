<?php

$baseDir = dirname(__DIR__);

require $baseDir . '/vendor/autoload.php';

use Phalcon\Di\FactoryDefault\Cli;

$di = new Cli();

$loader = new Phalcon\Autoload\Loader();
$loader->addNamespace('Phalcon', [$baseDir . '/tests/Phalcon/', $baseDir . '/src/Phalcon/']);
$loader->register();
