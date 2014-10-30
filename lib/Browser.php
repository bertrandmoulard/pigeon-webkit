<?php

namespace PhpCapybaraWebkit;

class Browser {

  function __construct() {
    $this->port = 59654;
    $this->startProcess();
    $this->client = stream_socket_client("tcp://localhost:" . 59734,$errno, $errstr,5);
    sleep(120);
  }

  public function visit($url) {
    return $this->command("Visit", [$url]);
  }

  private function command($name, $args=array()) {
    fwrite($this->client, $name . "\n");
    fwrite($this->client, count($args) . "\n");
    foreach($args as $arg) {
      fwrite($this->client, strlen($arg) . "\n");
      fwrite($this->client, $arg);
    }
    $this->check();
    return $this->readResponse();
  }

  private function check() {
    $status = trim(fgets($this->client));
    if ($status != "ok") {
      throw new \Exception($this->readResponse($this->client));
    }
  }

  private function readResponse() {
    $data = "";
    $nread = trim(fgets($this->client));

    if ($nread == 0) {
      return $data;
    }

    $read = 0;
    while ($read < $nread) {
      $tmp   = fread($this->client, $nread);
      $read += strlen($tmp);
      $data .= $tmp;
    }
    return $data;
  }

  private function startProcess() {
    $pipes = [];
    // TODO discover path in code
    $server_path = "/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/bin/webkit_server";
    $descriptorspec = [
      0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        ];
    $server_process = proc_open($server_path, $descriptorspec, $pipes);
    var_dump($server_process);
    if (is_resource($server_process)) {
      $data = fgets($pipes[1]);
      $this->port = $this->discoverProcessPort($data);
      var_dump($this->port);
      $this->server_process = $server_process;
    } else {
      throw new \RuntimeException("coudn't lunch webkit_server_process");
    }
    //register_shutdown_function(array($this,"registerShutdownHook"));
  }

  private function discoverProcessPort($line) {
    if (preg_match('/listening on port: (\d+)/',$line,$matches)) {
      return $matches[1];
    } else {
      throw \RuntimeException("couldn't find process port");
    }
  }
}
// class Browser {

  // function __construct(){
    // $this->port = 0;
    // $this->startProcess();
    // $this->client = stream_socket_client("tcp://localhost:{$this->port}", $errno, $errstr, 5);
    // $this->deb($this->port);
    // var_dump($this->client);
  // }

  // public function visit($url) {
    // return $this->command("Visit", [$url]);
  // }

  // public function body() {
    // return  $this->command("Body");
  // }

  // function command($name, $args=array()) {
    // $this->deb($name);
    // fwrite($this->client, $name . "\n");
    // fwrite($this->client, count($args) . "\n");
    // foreach($args as $arg) {
      // $this->deb($arg);
      // fwrite($this->client, strlen($arg) . "\n");
      // fwrite($this->client, $arg);
    // }
    // var_dump("command sent");
    // $this->check();
    // var_dump("checking");
    // return $this->readResponse();
  // }

  // protected function check() {
    // $status = trim(fgets($this->client));
    // if ($status != "ok") {
      // throw new \Exception($this->readResponse($this->client));
    // }
  // }

  // function readResponse() {
    // $data = "";
    // $nread = trim(fgets($this->client));

    // if ($nread == 0) {
      // return $data;
    // }

    // $read = 0;
    // while ($read < $nread) {
      // $tmp   = fread($this->client, $nread);
      // $read += strlen($tmp);
      // $data .= $tmp;
    // }
    // return $data;
  // }

  // private function startProcess() {
    // $pipes = [];
    // // TODO discover path in code
    // $server_path = "/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/bin/webkit_server";
    // $descriptorspec = [
      // 0 => array("pipe", "r"),
        // 1 => array("pipe", "w"),
        // ];
    // $server_process = proc_open($server_path, $descriptorspec, $pipes);
    // if (is_resource($server_process)) {
      // $data = fgets($pipes[1]);
      // $this->port = $this->discoverProcessPort($data);
      // $this->server_process = $server_process;
    // } else {
      // throw new \RuntimeException("coudn't lunch webkit_server_process");
    // }
    // //register_shutdown_function(array($this,"registerShutdownHook"));
  // }

  // private function discoverProcessPort($line) {
    // if (preg_match('/listening on port: (\d+)/',$line,$matches)) {
      // return $matches[1];
    // } else {
      // throw \RuntimeException("couldn't find process port");
    // }
  // }

  // private function deb($str) {
    // echo $str . "\n";
    // echo "-----------\n";
  // }

// }
