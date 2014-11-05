<?php

require 'vendor/autoload.php';
require 'lib/Browser.php';
require 'lib/CapybaraWebkitDriver.php';

$command = sprintf('php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
                            'localhost',
                            '8000',
                            __DIR__ . "/spec/fixtures");
$pipes = [];

$descriptorspec = [
  0 => array("pipe", "r"),
  1 => array("pipe", "w"),
  2 => array("pipe", "w"),
];

$process = proc_open($command, $descriptorspec, $pipes);
$data = fgets($pipes[1]);
