<?php

namespace PhpCapybaraWebkit;

class Browser {

  function __construct() {
    $this->capybara_js_contents = file_get_contents("/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/src/capybara.js");
    $this->setupWebkitServerAndPort();
    $this->client = stream_socket_client("tcp://localhost:" . $this->port,$errno, $errstr,5);
    $this->evaluateScript($this->capybara_js_contents);
  }

  public function visit($url) {
    return $this->command('Visit', [$url]);
  }

  public function body() {
    return $this->command('Body');
  }

  public function evaluateScript($content) {
    $this->command("Evaluate", [$content]);
  }

  public function execute($js) {
    $this->command("Execute", [$js]);
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
