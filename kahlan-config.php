<?php
use filter\Filter;

// Switch `src` to `lib` by default for sources location.
$args = $this->args();
$args->argument('src', 'default', ['lib']);

function startTestHttpServer() {
  $command = sprintf('php -S %s:%d -t %s',
                              'localhost',
                              '8419',
                              __DIR__ . "/spec/fixtures");
  $pipes = [];

  $descriptorspec = [
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w"),
  ];
  return proc_open($command, $descriptorspec, $pipes);
  $resource;
}

// Starts the server & injects the variable up to the root suite scope.
$root = $this->suite();
$root->test_http_server = startTestHttpServer();
sleep(2);

// Shutdowns the server
Filter::register('stop.httpserver', function($chain) {
  $root = $this->suite();
  proc_terminate($root->test_http_server);
  return $chain->next();
});
Filter::apply($this, 'stop', 'stop.httpserver'); // Attach `'stop.httpserver'` to the `'stop'` entry point
