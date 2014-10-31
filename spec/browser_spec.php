<?php

require __DIR__ . "/../lib/Browser.php";

use PhpCapybaraWebkit\Browser;

describe("Browser", function() {

  $this->local_url = "file:///". __DIR__ ."/fixtures/foo.html";

  beforeEach(function() {
    $this->browser = new Browser();
  });

  afterEach(function() {
    $this->browser = null;
  });

  describe(".visit", function() {
    it("returns an empty string", function() {
      $response = $this->browser->visit($this->local_url);
      expect($response)->toBe("");
    });
  });

  describe(".body", function() {
    context("when the browser is visiting a page", function() {
      it("returns the html source of the page", function() {
        $this->browser->visit($this->local_url);
        expect($this->browser->body())->toContain("find me: body");
      });

    });

    context("when the browser is not visiting a page", function() {
      it("returns an empty string", function() {
        expect($this->browser->body())->toBe("");
      });
    });
  });

});
