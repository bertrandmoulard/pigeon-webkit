<?php

require_once __DIR__ . "/../spec_helper.php";

use PigeonWebkit\FunctionalPigeon;

describe("FunctionalPigeon", function() {

  $this->foo_fixture_url = "http://localhost:8419/foo.html";

  before(function() {
    $this->test_http_server = startTestHttpServer();
    $this->pigeon = new FunctionalPigeon();
    $this->pigeon->start();
  });

  after(function() {
    $this->pigeon = null;
    proc_terminate($this->test_http_server);
  });

  beforeEach(function() {
    $this->pigeon->reset();
    $this->pigeon->visit($this->foo_fixture_url);
  });

  describe("#find", function() {
    it("finds elements by css selector", function() {
      expect(count($this->pigeon->find(".test-css-class")))->toBe(1);
    });

    it("supports complex selectors", function() {
      expect($this->pigeon->getText("div:nth-child(2)"))->toBe("find me: body");
      expect($this->pigeon->getText("div:contains('body')"))->toBe("find me: body");
      expect($this->pigeon->getText("body > div:contains('body')"))->toBe("find me: body");
      expect($this->pigeon->getText("div:contains('body') + div:contains('class')"))->toBe("find me: css class");
      expect($this->pigeon->getTagName("div:contains('find me: css id') + *"))->toBe("a");
    });
  });

  describe("#getTagName", function() {
    it("returns the tag name of the element found by the css query", function() {
      expect($this->pigeon->getTagName(".find-me-tag-name"))->toBe("span");
    });
  });

  describe("#setXPathMode", function() {
    it("makes the driver find elements by xpath rather than css queries", function() {
      $this->pigeon->visit($this->foo_fixture_url);
      $query = "//*[@class='test-css-class']";
      $callable = function() use ($query) {
        $this->pigeon->find($query);
      };
      expect($callable)->toThrow('Symfony\Component\CssSelector\Exception\SyntaxErrorException');

      $this->pigeon->setXPathMode(true);
      expect($callable)->notToThrow('Symfony\Component\CssSelector\Exception\SyntaxErrorException');
      expect(count($this->pigeon->find($query)))->toBe(1);

      $this->pigeon->setXPathMode(false);
      expect($callable)->toThrow('Symfony\Component\CssSelector\Exception\SyntaxErrorException');
      expect(count($this->pigeon->find(".test-css-class")))->toBe(1);

    });
  });

  describe("#saveScreenShot", function() {
    it("takes a screen shot and saves it to the specified location", function() {
      $this->pigeon->visit($this->foo_fixture_url);
      $this->pigeon->saveScreenShot("/tmp/tmp.png");
      expect(exec('file /tmp/tmp.png'))->toContain("PNG");
      exec('rm /tmp/tmp.png');
    });
  });
});
