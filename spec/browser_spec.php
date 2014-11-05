<?php

require __DIR__ . "/spec_helper.php";

use PhpCapybaraWebkit\Browser;
use PhpCapybaraWebkit\CapybaraWebkitDriver;

describe("Browser", function() {

  $this->fixture_url = "file:///". __DIR__ ."/fixtures/foo.html";

  before(function() {
    var_dump("starting it");
    $this->test_http_server = startTestHttpServer();
    var_dump($this->test_http_server);
  });

  after(function() {
    var_dump("killing it");
    var_dump($this->test_http_server);
    proc_terminate($this->test_http_server);
  });

  beforeEach(function() {
    $b = new Browser('/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.3.1/bin/webkit_server');
    $this->driver = new CapybaraWebkitDriver($b);
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

  describe("#reload", function() {});

  describe("#forward", function() {});

  describe("#back", function() {});

  describe("#setBasicAuth", function() {});

  describe("#switchToWindow", function() {});

  describe("#switchToIFrame", function() {});

  describe("#setRequestHeader", function() {});

  describe("#getResponseHeaders", function() {});

  describe("#setCookie", function() {});

  describe("#getCookie", function() {});

  describe("#getStatusCode", function() {
    context("when the request is successful", function() {
      it("is 200", function() {
        $this->driver->visit("https://etsy.com");
        expect($this->driver->getStatusCode())->toBe(200);
      });
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

  describe("#click", function() {});

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
