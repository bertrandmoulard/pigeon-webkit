<?php

namespace PigeonWebkit;

use Behat\Mink\Session;

class FunctionalPigeon extends CapybaraWebkitDriver {

  protected $browser;

  public function __construct() {
    $this->browser = new PigeonBrowser();
    parent::__construct($this->browser);
    $this->setSession(new Session($this));
    $this->start();
  }

  public function setXPathMode($bool) {
    $mode = $bool ? "xpath" : "css";
    $this->browser->setMode($mode);
  }

  public function saveScreenShot($path) {
    file_put_contents($path, $this->getScreenShot());
  }

  public function body() {
    return $this->getContent();
  }

  public function ignoreSslErrors() {
    $this->browser->ignoreSslErrors();
  }
}

class PigeonBrowser extends Browser {

  protected $mode = "css";

  public function find($query) {
    $css_selector = new \Symfony\Component\CssSelector\CssSelectorConverter();
    $query = $this->mode == "xpath" ? $query : $css_selector->toXPath($query);
    return parent::find($query);
  }

  public function setMode($mode) {
    $this->mode = $mode;
  }
}
