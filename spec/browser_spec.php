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

  describe("#visit", function() {
    it("returns an empty string", function() {
      $response = $this->browser->visit($this->local_url);
      expect($response)->toBe("");
    });
  });

  describe("#body", function() {
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

  describe("#evaluateScript", function() {
    beforeEach(function() {
      $this->browser->visit("https://google.com");
    });

    context("when a script is valid", function() {
      it("runs", function() {
        $res = $this->browser->evaluateScript("Capybara.findCss('span');");
        var_dump($res);
      });
    });
  });

  // describe("#findCss", function() {
    // beforeEach(function() {
      // $this->browser->visit($this->local_url);
    // });

    // context("when no elements match given css", function() {
      // it("returns null", function() {
        // expect($this->browser->findCss(".not-exists"))->toBeNull();
      // });
    // });
    // context("when one element matches given css", function() {
      // it("returns null", function() {
        // expect($this->browser->findCss(".test-css-class"))->not()->toBeNull();
      // });
    // });

  // });

});
