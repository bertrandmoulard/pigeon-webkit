<?php

require_once __DIR__ . "/../spec_helper.php";

use PigeonWebkit\Browser;
use PigeonWebkit\CapybaraWebkitDriver;

describe("CapybaraWebkitDriver", function() {

  $this->foo_fixture_url = "http://localhost:8419/foo.html";
  $this->php_foo_fixture_url = "http://localhost:8419/fixture.php";

  before(function() {
    $this->test_http_server = startTestHttpServer();
    $browser = new Browser();
    $this->driver = new CapybaraWebkitDriver($browser);
    $this->session = new Behat\Mink\Session($this->driver);
    $this->driver->setSession($this->session);
    $this->driver->start();
  });

  after(function() {
    $this->driver = null;
    proc_terminate($this->test_http_server);
  });

  beforeEach(function() {
    $this->driver->reset();
  });

  describe("#setSession", function() {
    after(function(){
      $this->driver->setSession($this->session);
    });

    it("sets the session", function() {
      $new_session = new Behat\Mink\Session($this->driver);
      $this->driver->setSession($new_session);
      expect($this->driver->getSession())->toBe($new_session);
    });
  });

  describe("#getSession", function() {
    it("gets the session", function() {
      expect($this->driver->getSession())->toBe($this->session);
    });
  });

  describe("#start", function() {
    context("when the driver is stopped", function() {
      before(function(){ $this->driver->stop(); });
      it("should start", function() {
        $this->driver->start();
        expect(is_resource($this->driver->getBrowser()->getClient()))->toBeTrue();
        expect(is_resource($this->driver->getBrowser()->getProcess()))->toBeTrue();
      });
    });

    context("when the driver is started", function() {
      it("should have no effect", function() {
        $client = $this->driver->getBrowser()->getClient();
        $process = $this->driver->getBrowser()->getProcess();
        $this->driver->start();
        expect($this->driver->getBrowser()->getClient())->toBe($client);
        expect($this->driver->getBrowser()->getProcess())->toBe($process);
      });
    });
  });

  describe("#isStarted", function() {
    context("when the driver is started", function(){
      it("is true", function() {
        expect($this->driver->isStarted())->toBeTrue();
      });
    });

    context("when the driver is stopped", function(){
      before(function() { $this->driver->stop(); });
      after(function() { $this->driver->start(); });

      it("is false", function() {
        expect($this->driver->isStarted())->toBeFalse();
      });
    });
  });

  describe("#stop", function() {
    before(function() {
      $this->driver->stop();
    });

    after(function() {
      $this->driver->start();
    });

    it("stop the driver", function() {
      expect($this->driver->getBrowser()->getClient())->toBeNull();
      expect($this->driver->getBrowser()->getProcess())->toBeNull();
    });
  });

  describe("#reset", function() {
    describe("request headers", function() {
      it("get reset", function() {
        $this->driver->setRequestHeader('X-Testing', 'testing');
        $this->driver->visit($this->php_foo_fixture_url);
        expect($this->driver->getContent())->toContain('HTTP_X_TESTING');
        $this->driver->reset();
        $this->driver->visit($this->php_foo_fixture_url);
        expect($this->driver->getContent())->notToContain('HTTP_X_TESTING');
      });
    });

    describe("cookies", function() {
      it("get reset", function() {
        $this->driver->visit($this->php_foo_fixture_url);
        $this->driver->setCookie('reset_me', 'please');
        $this->driver->visit($this->php_foo_fixture_url);
        expect($this->driver->getContent())->toContain('reset_me');
        $this->driver->reset();
        $this->driver->visit($this->php_foo_fixture_url);
        expect($this->driver->getContent())->notToContain('reset_me');
      });
    });
  });

  describe("#visit", function() {
    it("returns an empty string", function() {
      $response = $this->driver->visit($this->foo_fixture_url);
      expect($response)->toBeNull();
    });
  });

  describe("#getCurrentUrl", function() {
    it("returns the current url", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getCurrentUrl())->toContain("foo.html");
    });
  });

  describe("#reload", function() {
    it("reloads the page", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->executeScript("document.write('new content');");
      expect($this->driver->getContent())->toContain("new content");
      $this->driver->reload();
      expect($this->driver->getContent())->notToContain("new content");
    });
  });

  describe("#forward", function() {
    it("goes forward", function() {
      $this->driver->visit($this->foo_fixture_url);
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
      $this->driver->visit($this->foo_fixture_url);
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

  describe("#setRequestHeader", function() {
    it("sets a request header", function() {
      $this->driver->setRequestHeader('X-Testing', 'testing!!!');
      $this->driver->visit($this->php_foo_fixture_url);
      expect($this->driver->getContent())->toContain("X_TESTING");
      expect($this->driver->getContent())->toContain("testing!!!");
    });
  });

  describe("#getResponseHeaders", function() {
    it("returns the response headers", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getResponseHeaders()['Content-Type'])->toBe("text/html; charset=UTF-8");
    });
  });

  describe("#setCookie", function() {
    it("sets a cookie", function() {
      $this->driver->visit($this->php_foo_fixture_url);
      $this->driver->setCookie("client_cookie", "I_am_browser_cookie");
      $this->driver->visit($this->php_foo_fixture_url);
      expect($this->driver->getContent())->toContain("I_am_browser_cookie");
    });
  });

  describe("#getCookie", function() {
    it("get the cookie", function() {
      $this->driver->visit($this->php_foo_fixture_url);
      expect($this->driver->getCookie('server_cookie_1'))->toBe("one");
      expect($this->driver->getCookie('server_cookie_2'))->toBe("two");
      expect($this->driver->getCookie('server_cookie_3'))->toBe("three");

    });
  });

  describe("#getStatusCode", function() {
    it("returns the status code of the request", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getStatusCode())->toBe(200);
    });
  });

  describe("#getContent", function() {
    context("when the driver is visiting a page", function() {
      it("returns the html source of the page", function() {
        $this->driver->visit($this->foo_fixture_url);
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

  describe("#find", function() {
    it("finds elements with specified XPath query and returns an array of NodeElements", function() {
      $this->driver->visit($this->foo_fixture_url);
      $find_me_divs = $this->driver->find("//div[@class='find-me']");
      expect(count($find_me_divs))->toBe(2);;
      expect(get_class($find_me_divs[0]))->toBe("Behat\Mink\Element\NodeElement");
    });
  });

  describe("#getTagName", function() {
    it("returns the tag name of the element found by the query", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getTagName("//*[@class='find-me-tag-name']"))->toBe("span");
    });
  });

  describe("#getText", function() {
    it("returns the text content of the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getText("//div[@class='find-me-text']"))->toBe("the text");
    });
  });

  describe("#getHtml", function() {
    it("returns the hmtl content of the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getHtml("//div[@class='find-me-html']"))->toBe("<span>html content</span>");
    });
  });

  describe("#getOuterHtml", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->getOuterHtml("anything");
      };
      expect($callable)->toThrow('Behat\Mink\Exception\UnsupportedDriverActionException');
    });
  });

  describe("#getAttribute", function() {
    it("returns the value of the attributes", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getAttribute("//div[@class='find-me-attribute']", "foo"))->toBe("bar");
    });
  });

  describe("#getValue", function() {});

  describe("#setValue", function() {});

  describe("#check", function() {});

  describe("#uncheck", function() {});

  describe("#isChecked", function() {});

  describe("#selectOption", function() {});

  describe("#isSelected", function() {});

  describe("#click", function() {
    it("clicks on the element", function() {
      $this->driver->visit($this->foo_fixture_url);
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
