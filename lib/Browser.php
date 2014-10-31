<?php

namespace PhpCapybaraWebkit;

class Browser {

  function __construct() {
    $this->setupWebkitServerAndPort();
    $this->client = stream_socket_client("tcp://localhost:" . $this->port,$errno, $errstr,5);
  }

  public function visit($url) {
    return $this->command('Visit', [$url]);
  }

  public function body() {
    return $this->command('Body');
  }

  private function command($name, $args=array()) {
    fwrite($this->client, $name . "\n");
    fwrite($this->client, count($args) . "\n");
    foreach($args as $arg) {
      fwrite($this->client, strlen($arg) . "\n");
      fwrite($this->client, $arg);
    }
    $this->check();
    return $this->readResponse($this->client);
  }

  private function check() {
    $status = trim(fgets($this->client));
    if ($status != "ok") {
      throw new \Exception("non ok response from webkit server: $status");
    }
  }

  private function readResponse() {
    $response = "";
    $length = intval(trim(fgets($this->client)));
    if ($length == 0) {
      return $response;
    }
    $read = 0;
    while ($read < $length) {
      $tmp   = fread($this->client, $length);
      $read += strlen($tmp);
      $response .= $tmp;
    }
    return $response;
  }

  private function setupWebkitServerAndPort() {
    $this->pipes = [];
    $server_path = "/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/bin/webkit_server";
    $descriptorspec = [0 => array("pipe", "r"), 1 => array("pipe", "w")];
    $this->server_process = proc_open($server_path, $descriptorspec, $this->pipes);
    $process_output = fgets($this->pipes[1]);
    preg_match('/listening on port: (\d+)/', $process_output, $matches);
    $this->port = $matches[1];
  }

  private function shutdown() {
    fclose($this->client);
    proc_terminate($this->server_process);
  }

  function __destruct() {
    $this->shutdown();
  }
}
