<?php

namespace PigeonWebkit;

class Browser {
  protected $client;
  protected $port;
  protected $process;
  protected $server_path;

  public function __construct($server_path = null) {
    if($server_path) {
      $this->server_path = $server_path;
    } else {
      $this->server_path = $this->generateServerPath(exec('gem environment | grep INSTALLATION'));
    }
    $this->start();
  }

  public function start() {
    if (!is_resource($this->process)) {
      $this->startServer();
      $this->connect();
    }
  }

  public function stop() {
    $this->killServer();
    $this->disconnect();
  }

  public function getPort() {
    return $this->port;
  }

  public function getProcess() {
    return $this->process;
  }

  public function getClient() {
    return $this->client;
  }

  public function getServerPath() {
    return $this->server_path;
  }

  public function generateServerPath($output_text) {
    return trim(explode(":", $output_text)[1]) . "/gems/capybara-webkit-1.3.1/bin/webkit_server";
  }

  public function getGemCommandOuput() {
    return exec('gem environment | grep INSTALLATION');
  }

  public function ignoreSslErrors() {
    $this->command("IgnoreSslErrors");
}

  public function currentUrl() {
    return $this->command("CurrentUrl");
  }

  public function getTagName($xpath) {
    $one = $this->findOne($xpath);
    $this->invoke("tagName", $one);
  }

  public function setValue($xpath, $value) {
    $node = $this->findOne($xpath);
    $this->invoke("set", $node, $value);
  }

  public function render($path, $width = 1024, $height = 680) {
    $this->command("Render", [$path, intval($width), intval($height)]);
  }

  public function visit($url) {
    return $this->command("Visit", [$url]);
  }

  public function consoleMessage() {
    return json_decode($this->command("ConsoleMessages"), true);
  }

  public function responseHeader() {
    $result = [];
    foreach (explode("\n", $this->command("Headers")) as $line) {
      list($key, $value) = explode(": ", $line);
      $result[$key] = $value;
    }

    return $result;
  }

  public function setHeader($key, $value) {
    $this->command("Header", [$key, $value]);
  }

  public function startServer() {
    $this->pipes = [];

    if (!file_exists($this->server_path)) {
      throw new \RuntimeException("webkit_server not found");
    }

    $descriptorspec = [
      0 => ["pipe", "r"],
      1 => ["pipe", "w"],
      2 => ["pipe", "w"],
    ];

    $process = proc_open($this->server_path, $descriptorspec, $this->pipes);
    if (is_resource($process)) {
      $data = fgets($this->pipes[1]);
      $this->port = $this->discoverServerPort($data);
      $this->process = $process;
    } else {
      throw new \RuntimeException("Couldn't launch webkit_server");
    }

    register_shutdown_function([$this, "registerShutdownHook"]);
  }

  public function findOne($xpath) {
    $nodes = $this->find($xpath);
    if (count($nodes)) {
      return array_shift($nodes);
    } else {
      throw new \Exception(
        "element not found"
      );
    }
  }

  public function invoke() {
    $arguments = func_get_args();
    $invokeCommand = array_shift($arguments);
    array_unshift($arguments, "true");
    array_unshift($arguments, $invokeCommand);
    return $this->command("Node", $arguments);
  }

  public function statusCode() {
    return (int)$this->command("Status");
  }

  public function source() {
    return $this->command("Source");
  }

  public function trigger($xpath, $event) {
    $nodes = $this->find($xpath);
    $node = array_shift($nodes);
    if (!empty($node)) {
      $this->invoke("trigger", $node, $event);
      return true;
    } else {
      return false;
    }
  }

  public function reset() {
    $this->command("Reset");
  }

  public function find($query) {
    $ret = $this->command("FindXpath", [$query]);
    if (empty($ret)) {
      return [];
    }
    return explode(",", $ret);
  }

  public function body() {
    return $this->command("Body");
  }

  public function evaluateScript($script) {
    $this->updateConsoleLog();
    $json = $this->command("Evaluate", [$script]);
    $this->updateConsoleLog(true);
    $result = json_decode("[{$json}]", true);
    return $result[0];
  }

  public function executeScript($script) {
    $this->updateConsoleLog();
    $result = $this->command("Execute", [$script]);
    $this->updateConsoleLog(true);
    return $result;
  }

  protected function connect() {
    $server = stream_socket_client("tcp://localhost:{$this->port}", $errno, $errstr, 5);
    if (is_resource($server)) {
      $this->client = $server;
    } else {
      throw new \RuntimeException("could not connect to webkit_server");
    }
  }

  public function command($command, $args = []) {
    fwrite($this->client, $command . "\n");
    fwrite($this->client, count($args) . "\n");

    foreach ($args as $arg) {
      fwrite($this->client, strlen($arg) . "\n");
      fwrite($this->client, $arg);
    }
    $this->check();
    return $this->readResponse();
  }

  public function clearCookies() {
    $this->command("ClearCookies");
  }

  public function click($xpath) {
    $this->invoke("leftClick", $this->findOne($xpath));
  }

  public function mouseup($xpath) {
    $this->invoke("mouseup", $this->findOne($xpath));
  }

  public function mousedown($xpath) {
    $this->invoke("mousedown", $this->findOne($xpath));
  }

  public function visible($xpath) {
    return (bool)$this->invoke("visible", $this->findOne($xpath));
  }

  public function setCookie($cookie) {
    $this->command("SetCookie", [$cookie]);
  }

  public function getCookies() {
    $result = [];
    foreach (explode("\n", $this->command("GetCookies")) as $line) {
      $line = trim($line);
      if (!empty($line)) {
        $result[] = $line;
      }
    }
    return $result;
  }

  protected function updateConsoleLog($throwExceptionOnJSError = false) {
    if ($throwExceptionOnJSError) {
      $throwException = false;
      $newLog = $this->consoleMessage();
      $logDiff = [];
      for ($i = count($this->consoleLog); $i < count($newLog); $i++) {
        $logDiff[] = $newLog[$i];
        if ($newLog != null && is_array($newLog) && array_key_exists('line_number', $newLog[$i]) && array_key_exists('message', $newLog[$i]) && strpos($newLog[$i]['message'], 'Error:') !== FALSE) {
          $throwException = true;
        }
      }
      if ($throwException) {
        throw new \Behat\Mink\Exception\DriverException('JavaScript error occured: ' . print_r($logDiff, 1));
      }
    }

    $this->consoleLog = $this->consoleMessage();
  }

  public function __destruct() {
    $this->killServer();
  }

  protected function killServer() {
    if (is_resource($this->process)) {
      proc_terminate($this->process);
    }
    $this->process = null;
  }

  protected function disconnect() {
    if (is_resource($this->client)) {
      fclose($this->client);
    }
    $this->client = null;
  }

  public function registerShutdownHook() {
    $this->killServer();
  }

  protected function check() {
    $status = trim(fgets($this->client));
    if ($status != "ok") {
      throw new \Exception($this->readResponse($this->client));
    }
  }

  protected function readResponse() {
    $data = "";
    $length = trim(fgets($this->client));

    if ($length == 0) {
      return $data;
    }

    $read = 0;
    while ($read < $length) {
      $tmp = fread($this->client, $length);
      $read += strlen($tmp);
      $data .= $tmp;
    }
    return $data;
  }

  protected function discoverServerPort($line) {
    if (preg_match('/listening on port: (\d+)/', $line, $matches)) {
      return (int)$matches[1];
    } else {
      throw new \RuntimeException("couldn't find server port");
    }
  }
}
