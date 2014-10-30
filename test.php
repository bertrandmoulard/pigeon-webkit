<?php

class Browser {

  function __construct($client) {
    $this->client = $client;
  }

  function command($name, $args=array()) {
    fwrite($this->client, $name . "\n");
    fwrite($this->client, count($args) . "\n");
    foreach($args as $arg) {
      fwrite($this->client, strlen($arg) . "\n");
      fwrite($this->client, $arg);
    }
    $this->check();
    return $this->readResponse();
  }

  protected function check() {
    $status = trim(fgets($this->client));
    if ($status != "ok") {
      throw new \Exception($this->readResponse($this->client));
    }
  }

  function readResponse() {
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


  function debug_str($str) {
    echo $str . "\n";
    echo "-----------\n";
  }
}

$browser = new Browser(stream_socket_client("tcp://localhost:59570",$errno, $errstr,5));

$browser->command("Visit", ["https://google.com"]);
echo $browser->command("Body");
