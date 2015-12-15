#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Commands\ImportCommand;

$application = new Application();
$application->add(new ImportCommand());
$application->run();