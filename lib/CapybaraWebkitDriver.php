<?php

namespace PigeonWebkit;

use Behat\Mink\Session,
  Behat\Mink\Element\NodeElement,
  Behat\Mink\Exception\DriverException,
  Behat\Mink\Exception\UnsupportedDriverActionException;

class CapybaraWebkitDriver implements \Behat\Mink\Driver\DriverInterface {
  private $started;
  private $browser;
  private $session;

  public function __construct(Browser $browser) {
    $this->browser = $browser;
  }

  public function getBrowser() {
    return $this->browser;
  }

  public function setSession(Session $session) {
    $this->session = $session;
  }

  public function getSession() {
    return $this->session;
  }

  public function start() {
    $this->started = true;
    $this->browser->start();
  }

  public function isStarted() {
    return $this->started;
  }

  public function stop() {
    $this->started = false;
    $this->browser->stop();
  }

  public function reset() {
    $this->browser->reset();
  }

  public function visit($url) {
    $this->browser->visit($url);
  }

  public function getCurrentUrl() {
    return $this->browser->currentUrl();
  }

  public function reload() {
    $this->browser->executeScript('location.reload()');
  }

  public function forward() {
    $this->browser->executeScript('history.forward()');
  }

  public function back() {
    $this->browser->executeScript('history.back()');
  }

  public function setBasicAuth($user, $password) {
    throw new UnsupportedDriverActionException('Basic auth is not supported by this %s', $this);
  }

  public function switchToWindow($name = null) {
    throw new UnsupportedDriverActionException('Window management is not supported by %s', $this);
  }

  public function switchToIFrame($name = null) {
    throw new UnsupportedDriverActionException('iFrame management is not supported by %s', $this);
  }

  public function setRequestHeader($name, $value) {
    $this->browser->setHeader($name, $value);
  }

  public function getResponseHeaders() {
    return $this->browser->responseHeader();
  }

  public function setCookie($name, $value = null) {
    $url_bits = parse_url($this->getCurrentUrl());
    $this->browser->setCookie("$name=$value; domain={$url_bits['host']}; path={$url_bits['path']}");
  }

  public function getCookie($name) {
    foreach($this->browser->getCookies() as $cookie_line) {
      $elements = explode(";", $cookie_line);
      $cookie_bits = explode("=", $elements[0]);
      if ($cookie_bits[0] == $name) {
        return $cookie_bits[1];
      }
    }
    return null;
  }

  public function getStatusCode() {
    return $this->browser->statusCode();
  }

  public function getContent() {
    return $this->browser->body();
  }

  public function find($xpath) {
    $nodes = $this->browser->find($xpath);

    $elements = [];
    foreach ($nodes as $offset => $node_id) {
      $elements[] = new NodeElement(sprintf('(%s)[%d]', $xpath, $offset + 1), $this->session);
    }

    return $elements;
  }

  public function getTagName($xpath) {
    return $this->browser->invoke("tagName", $this->browser->findOne($xpath));
  }

  public function getText($xpath) {
    return $this->browser->invoke("text", $this->browser->findOne($xpath));
  }

  public function getHtml($xpath) {
    return $this->browser->invoke("getInnerHTML", $this->browser->findOne($xpath));
  }

  public function getOuterHtml($xpath) {
    throw new UnsupportedDriverActionException('iFrame management is not supported by %s', $this);
  }

  public function getAttribute($xpath, $attr) {
    return $this->browser->invoke("attribute", $this->browser->findOne($xpath), $attr);
  }

  public function getValue($xpath) {
    return $this->browser->invoke("value", $this->browser->findOne($xpath));
  }

  public function setValue($xpath, $value) {
    $this->browser->setValue($xpath, $value);
  }

  public function check($xpath) {
    $node = $this->browser->findOne($xpath);
    return $this->browser->invoke("set", $node, "true");
  }

  public function uncheck($xpath) {
    $node = $this->browser->findOne($xpath);
    return $this->browser->invoke("set", $node, "false");
  }

  public function isChecked($xpath) {
    $node = $this->browser->findOne($xpath);
    return $this->browser->invoke("attribute", $node, "checked");
  }

  public function selectOption($xpath, $value, $multiple = false) {
    $path = $xpath . "/option[(text()='$value' or @value='$value')]";

    $node = $this->browser->findOne($path);
    $this->browser->invoke("selectOption", $node);
  }

  public function click($xpath) {
    $this->browser->click($xpath);
  }

  public function doubleClick($xpath) {
    $this->browser->trigger($xpath, "dblclick");
  }

  public function rightClick($xpath) {
    $this->browser->trigger($xpath, "contextmenu");
  }

  public function attachFile($xpath, $path) {
    $this->browser->setValue($xpath, $path);
  }

  public function isVisible($xpath) {
    $node = $this->browser->findOne($xpath);
    return $this->browser->invoke("visible", $node);
  }

  public function mouseOver($xpath) {
    $this->browser->trigger($xpath, "mouseover");
  }

  public function focus($xpath) {
    $this->browser->trigger($xpath, "focus");
  }

  public function blur($xpath) {
    $this->browser->trigger($xpath, "blur");
  }

  public function keyPress($xpath, $char, $modifier = null) {
    $node = $this->browser->findOne($xpath);
    $alt = ($modifier == "alt") ? "true" : "false";
    $ctrl = ($modifier == "ctrl") ? "true" : "false";
    $meta = ($modifier == "meta") ? "true" : "false";
    $shift = ($modifier == "shift") ? "true" : "false";
    $charCode = ord($char);

    $this->getBrowser()->invoke("keypress", $node, $alt, $ctrl, $shift, $meta, 0, $charCode);
  }

  public function keyDown($xpath, $char, $modifier = null) {
    $this->keyImpl('keydown', $xpath, $char, $modifier);
  }

  public function keyUp($xpath, $char, $modifier = null) {
    $this->keyImpl('keyup', $xpath, $char, $modifier);
  }

  protected function keyImpl($type, $xpath, $char, $modifier = null) {
    $altKey = ($modifier == 'alt') ? 'true' : 'false';
    $ctrlKey = ($modifier == 'ctrl') ? 'true' : 'false';
    $shiftKey = ($modifier == 'shift') ? 'true' : 'false';
    $metaKey = ($modifier == 'meta') ? 'true' : 'false';
    $charCode = ord($char);

    $script = <<<JS
(function(){
    var itr = document.evaluate("$xpath", document, null, XPathResult.ORDERED_NODE_ITERATOR_TYPE, null);
    var node;
    var results = [];

    while (node = itr.iterateNext()) {
        results.push(node);
    }

    if (results.length > 0) {
        var target = results[0];
        var eventObject = document.createEvent("Events");
        eventObject.initEvent('$type', true, true);

        eventObject.window   = window;
        eventObject.altKey   = $altKey;
        eventObject.ctrlKey  = $ctrlKey;
        eventObject.shiftKey = $shiftKey;
        eventObject.metaKey  = $metaKey;
        eventObject.keyCode  = 0;
        eventObject.charCode = $charCode;
        target.dispatchEvent(eventObject);
    }

})();
JS;
    $this->executeScript($script);
  }

  public function dragTo($sourceXpath, $destinationXpath) {
    $source = $this->browser->findOne($sourceXpath);
    $dest = $this->browser->findOne($destinationXpath);

    $this->browser->invoke("dragTo", $source, $dest);
  }

  public function executeScript($script) {
    $this->browser->executeScript($script);
  }

  public function evaluateScript($script) {
    return $this->browser->evaluateScript(preg_replace('/^\s*return\s*/msU', '', $script));
  }

  public function wait($time, $condition) {
    $script = "$condition";
    $start = 1000 * microtime(true);
    $end = $start + $time;

    while (1000 * microtime(true) < $end && !$this->browser->evaluateScript($script)) {
      sleep(0.1);
    }

  }

  public function getScreenshot() {
    $file = 'tmp-screenshot.png';
    $dimensions = $this->browser->evaluateScript('document.body.offsetWidth + "," + document.body.offsetHeight');
    list($width, $height) = explode(',', $dimensions, 2);
    $this->browser->render($file, $width, $height);
    $png_data = file_get_contents($file);
    unlink($file);
    return $png_data;
  }

  public function resizeWindow($width, $height, $name = null) {
    return $this->browser->resizeWindow($width, $height, $name);
  }
}
