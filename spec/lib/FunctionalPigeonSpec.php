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
  });

  describe("#getTagName", function() {
    it("returns the tag name of the element found by the css query", function() {
      expect($this->pigeon->getTagName(".find-me-tag-name"))->toBe("span");
    });
  });
});
