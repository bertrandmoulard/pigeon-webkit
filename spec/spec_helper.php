<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . "/../lib/Browser.php";
require __DIR__ . "/../lib/CapybaraWebkitDriver.php";
require __DIR__ . "/../lib/FunctionalPigeon.php";

function startTestHttpServer() {
  $command = sprintf('php -S %s:%d -t %s',
                              'localhost',
                              '84819',
                              __DIR__ . "/fixtures");
  $pipes = [];

  $descriptorspec = [
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w"),
  ];

  return proc_open($command, $descriptorspec, $pipes);
}
