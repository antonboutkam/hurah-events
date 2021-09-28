#!/usr/bin/env php
<?php
// application.php

require dirname(__DIR__) . '/vendor/autoload.php';

use Hurah\Event\Commands\Runner;
use Symfony\Component\Console\Application;

$application = new Application('Hurah event handler');

$application->add(new Runner());


$application->run();