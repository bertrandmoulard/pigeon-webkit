<?php

namespace PigeonWebkit;

use Symfony\Component\CssSelector\CssSelector,
  Behat\Mink\Session;

class FunctionalPigeon extends CapybaraWebkitDriver {

  protected $browser;

  public function __construct() {
    $this->browser = new PigeonBrowser();
    parent::__construct($this->browser);
    $this->setSession(new Session($this));
  }

  public function setXPathMode($bool) {
    $mode = $bool ? "xpath" : "css";
    $this->browser->setMode($mode);
  }

  public function saveScreenShot($path) {
    file_put_contents($path, $this->getScreenShot());
  }
}

class PigeonBrowser extends Browser {

  protected $mode = "css";

  public function find($query) {
    $query = $this->mode == "xpath" ? $query : CssSelector::toXPath($query);
    return parent::find($query);
  }

  public function setMode($mode) {
    $this->mode = $mode;
  }
}
