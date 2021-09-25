#!/usr/bin/env php
<?php
// application.php

require dirname(__DIR__) . '/vendor/autoload.php';

use Hurah\Event\Commands\Generator\CreateInotifyWait;
use Hurah\Event\Commands\Generator\CreateListener;
use Hurah\Event\Commands\Generator\CreateSupervisorConfig;
use Hurah\Event\Commands\Worker\Runner;
use Hurah\Event\Receiver;
use Symfony\Component\Console\Application;

$application = new Application('Hurah event handler');

// ... register commands
$application->add(new CreateListener());
$application->add(new CreateInotifyWait());
$application->add(new CreateSupervisorConfig());

$application->add(new Runner());


$application->run();