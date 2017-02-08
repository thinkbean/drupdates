#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use Drupdates\Command;
use Symfony\Component\Console\Application;

$foo = 'bar';

$application = new Application('Drupdates', '0.1-dev');
$application->add(new Command\DrupdatesCommand());
$application->setDefaultCommand('drupal:updates', true);
$application->run();
