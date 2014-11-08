<?php

namespace PigeonWebkit;

use Symfony\Component\CssSelector\CssSelector,
  Behat\Mink\Session;

class FunctionalPigeon extends CapybaraWebkitDriver {

  public function __construct() {
    $browser = new PigeonBrowser();
    parent::__construct($browser);
    $this->setSession(new Session($this));
  }
}

class PigeonBrowser extends Browser {
  public function find($query) {
    return parent::find(CssSelector::toXPath($query));
  }
}
