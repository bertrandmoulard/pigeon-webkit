<?php

require __DIR__ . "/spec_helper.php";

use PhpCapybaraWebkit\Browser;
use PhpCapybaraWebkit\CapybaraWebkitDriver;

describe("CapybaraWebkitDriver", function() {

  $this->fixture_url = "http://localhost:8000/foo.html";

  before(function() {
    $this->test_http_server = startTestHttpServer();
  });

  after(function() {
    proc_terminate($this->test_http_server);
  });

  beforeEach(function() {
    $browser = new Browser('/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/bin/webkit_server');
    $this->driver = new CapybaraWebkitDriver($browser);
    $session = new Behat\Mink\Session($this->driver);
    $this->driver->setSession($session);
    $this->driver->start();
  });

  afterEach(function() {
    $this->driver = null;
  });

  describe("#setSession", function() {});

  describe("#start", function() {});

  describe("#isStarted", function() {});

  describe("#stop", function() {});

  describe("#reset", function() {});

  describe("#visit", function() {
    it("returns an empty string", function() {
      $response = $this->driver->visit($this->fixture_url);
      expect($response)->toBeNull();
    });
  });

  describe("#getCurrentUrl", function() {
    it("returns the current url", function() {
      $this->driver->visit($this->fixture_url);
      expect($this->driver->getCurrentUrl())->toContain("foo.html");
    });
  });

  describe("#reload", function() {
    it("reloads the page", function() {
      $this->driver->visit($this->fixture_url);
      $this->driver->executeScript("document.write('new content');");
      expect($this->driver->getContent())->toContain("new content");
      $this->driver->reload();
      expect($this->driver->getContent())->notToContain("new content");
    });
  });

  describe("#forward", function() {
    it("goes forward", function() {
      $this->driver->visit($this->fixture_url);
      $this->driver->click("//a[@id='link-to-bar']");
      expect($this->driver->getContent())->toContain("bar page");
      $this->driver->back();
      expect($this->driver->getContent())->notToContain("bar page");
      $this->driver->forward();
      expect($this->driver->getContent())->toContain("bar page");
    });
  });

  describe("#back", function() {
    it("goes back", function() {
      $this->driver->visit($this->fixture_url);
      $this->driver->click("//a[@id='link-to-bar']");
      expect($this->driver->getContent())->toContain("bar page");
      $this->driver->back();
      expect($this->driver->getContent())->toContain("foo page");
    });
  });

  describe("#setBasicAuth", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->setBasicAuth('good', 'morning');
      };
      expect($callable)->toThrow('Behat\Mink\Exception\UnsupportedDriverActionException');
    });
  });

  describe("#switchToWindow", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->switchToWindow();
      };
      expect($callable)->toThrow('Behat\Mink\Exception\UnsupportedDriverActionException');
    });
  });

  describe("#switchToIFrame", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->switchToIFrame();
      };
      expect($callable)->toThrow('Behat\Mink\Exception\UnsupportedDriverActionException');
    });
  });

  describe("#setRequestHeader", function() {});

  describe("#getResponseHeaders", function() {});

  describe("#setCookie", function() {});

  describe("#getCookie", function() {});

  describe("#getStatusCode", function() {
    it("returns the status code of the request", function() {
      $this->driver->visit("http://localhost:8000/foo.html");
      expect($this->driver->getStatusCode())->toBe(200);
    });
  });

  describe("#getContent", function() {
    context("when the driver is visiting a page", function() {
      it("returns the html source of the page", function() {
        $this->driver->visit($this->fixture_url);
        expect($this->driver->getContent())->toContain("find me: body");
      });

    });

    context("when the driver is not visiting a page", function() {
      it("returns an empty string", function() {
        expect($this->driver->getContent())->toBe("");
      });
    });
  });

  describe("#getScreenshot", function() {});

  describe("#getWindowNames", function() {});

  describe("#getWindowName", function() {});

  describe("#find", function() {});

  describe("#getTagName", function() {});

  describe("#getText", function() {});

  describe("#getHtml", function() {});

  describe("#getOuterHtml", function() {});

  describe("#getAttribute", function() {});

  describe("#getValue", function() {});

  describe("#setValue", function() {});

  describe("#check", function() {});

  describe("#uncheck", function() {});

  describe("#isChecked", function() {});

  describe("#selectOption", function() {});

  describe("#isSelected", function() {});

  describe("#click", function() {
    it("clicks on the element", function() {
      $this->driver->visit($this->fixture_url);
      $this->driver->click("//a[@id='link-to-bar']");
      expect($this->driver->getCurrentUrl())->toContain("bar.html");
    });
  });

  describe("#doubleClick", function() {});

  describe("#rightClick", function() {});

  describe("#attachFile", function() {});

  describe("#isVisible", function() {});

  describe("#mouseOver", function() {});

  describe("#focus", function() {});

  describe("#blur", function() {});

  describe("#keyPress", function() {});

  describe("#keyDown", function() {});

  describe("#keyUp", function() {});

  describe("#dragTo", function() {});

  describe("#executeScript", function() {});

  describe("#evaluateScript", function() {});

  describe("#wait", function() {});

  describe("#resizeWindow", function() {});

  describe("#maximizeWindow", function() {});

  describe("#submitForm", function() {});

  describe("it works", function() {
    it("overall", function() {
      $this->driver->visit("https://etsy.com");
      $one = $this->driver->click("//button[@value='Search']");
      expect($this->driver->getContent())->toContain("Accessories");
    });
  });
});
