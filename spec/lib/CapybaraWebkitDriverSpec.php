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
    $this->unsupported_driver_exception = new \Behat\Mink\Exception\UnsupportedDriverActionException("", $this->driver);
  });

  after(function() {
    $this->driver = null;
    proc_terminate($this->test_http_server);
  });

  beforeEach(function() {
    $this->driver->reset();
  });

  describe("#start", function() {
    context("when the driver is stopped", function() {
      before(function(){ $this->driver->stop(); });
      it("should start", function() {
        $this->driver->start();
        expect(is_resource($this->driver->getBrowser()->getClient()))->toBe(true);
        expect(is_resource($this->driver->getBrowser()->getProcess()))->toBe(true);
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

  describe("#isStarted", function() {
    context("when the driver is started", function(){
      it("is true", function() {
        expect($this->driver->isStarted())->toBe(true);
      });
    });

    context("when the driver is stopped", function(){
      before(function() { $this->driver->stop(); });
      after(function() { $this->driver->start(); });

      it("is false", function() {
        expect($this->driver->isStarted())->toBe(false);
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
        expect($this->driver->getContent())->not->toContain('HTTP_X_TESTING');
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
        expect($this->driver->getContent())->not->toContain('reset_me');
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
      expect($this->driver->getContent())->not->toContain("new content");
    });
  });

  describe("#forward", function() {
    it("goes forward", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->click("//a[@id='link-to-bar']");
      expect($this->driver->getContent())->toContain("bar page");
      $this->driver->back();
      expect($this->driver->getContent())->not->toContain("bar page");
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
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#switchToWindow", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->switchToWindow();
      };
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#switchToIFrame", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->switchToIFrame();
      };
      expect($callable)->toThrow($this->unsupported_driver_exception);
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

  describe("#getScreenshot", function() {
    it("return the data for the screenshot", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getScreenShot())->not->toBeNull();;
    });
  });

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
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#getAttribute", function() {
    it("returns the value of the attributes", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getAttribute("//div[@class='find-me-attribute']", "foo"))->toBe("bar");
    });
  });

  describe("#getValue", function() {
    it("returns the value of the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->getValue("//input[@class='find-me-value']"))->toBe("foo");
    });
  });

  describe("#setValue", function() {
    it("sets the value of the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->setValue("//input[@class='find-me-value']", "bar");
      expect($this->driver->getValue("//input[@class='find-me-value']"))->toBe("bar");
    });
  });

  describe("#check", function() {
    it("checks the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->check("//input[@class='find-me-unchecked']");
      expect($this->driver->isChecked("//input[@class='find-me-unchecked']"))->toBe(true);
    });
  });

  describe("#uncheck", function() {
    it("unchecks the matched element", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->check("//input[@class='find-me-unchecked']");
      expect($this->driver->isChecked("//input[@class='find-me-unchecked']"))->toBe(true);
      $this->driver->uncheck("//input[@class='find-me-unchecked']");
      expect($this->driver->isChecked("//input[@class='find-me-unchecked']"))->toBe(false);
    });
  });

  describe("#isChecked", function() {
    beforeEach(function() {
      $this->driver->visit($this->foo_fixture_url);
    });

    context("when the matching element is checked", function() {
      it("returns true", function() {
        $this->driver->check("//input[@class='find-me-unchecked']");
        expect($this->driver->isChecked("//input[@class='find-me-unchecked']"))->toBe(true);
      });
    });

    context("when the matching element is not checked", function() {
      it("returns true", function() {
        expect($this->driver->isChecked("//input[@class='find-me-unchecked']"))->toBe(false);
      });
    });
  });

  describe("#selectOption", function() {
    it("selects the option", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->isSelected("//option[@value='val_1']"))->toBe(true);
      expect($this->driver->isSelected("//option[@value='val_2']"))->toBe(false);
      $this->driver->selectOption("//select[@name='dropdown']", "val_2");
      expect($this->driver->isSelected("//option[@value='val_1']"))->toBe(false);
      expect($this->driver->isSelected("//option[@value='val_2']"))->toBe(true);
    });
  });

  describe("#isSelected", function() {
    it("returns true is the element is selected, false otherwise", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->isSelected("//option[@value='val_1']"))->toBe(true);
      expect($this->driver->isSelected("//option[@value='val_2']"))->toBe(false);
    });
  });

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

  describe("#isVisible", function() {
    beforeEach(function() {
      $this->driver->visit($this->foo_fixture_url);
    });

    context("when the node is visible", function() {
      it("is true", function() {
        expect($this->driver->isVisible("//div[@class='visible']"))->toBe(true);
      });
    });

    context("when the node is invisible", function() {
      it("is false", function() {
        expect($this->driver->isVisible("//div[@class='invisible']"))->toBe(false);
      });
    });
  });

  describe("#mouseOver", function() {});

  describe("#focus", function() {});

  describe("#blur", function() {});

  describe("#keyPress", function() {});

  describe("#keyDown", function() {});

  describe("#keyUp", function() {});

  describe("#dragTo", function() {});

  describe("#executeScript", function() {
    it("executes the script", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->executeScript("document.write('content from executed script')");
      expect($this->driver->getContent())->toContain("content from executed script");
    });
  });

  describe("#evaluateScript", function() {
    it("evaluates the script", function() {
      $this->driver->visit($this->foo_fixture_url);
      expect($this->driver->evaluateScript("'test' == 'test' ? 'yes' : 'no'"))->toBe("yes");
    });
  });

  describe("#wait", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->wait(2, "");
      };
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#resizeWindow", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->resizeWindow(10, 10);
      };
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#maximizeWindow", function() {
    it("throws an UnsupportedDriverActionException", function() {
      $callable = function() {
        $this->driver->maximizeWindow();
      };
      expect($callable)->toThrow($this->unsupported_driver_exception);
    });
  });

  describe("#submitForm", function() {
    it("submits the form", function() {
      $this->driver->visit($this->foo_fixture_url);
      $this->driver->submitForm("//form");
      expect($this->driver->getContent())->toContain("bar page");
    });
  });
});
