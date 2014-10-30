<?php

require __DIR__ . "/../lib/Browser.php";

use PhpCapybaraWebkit\Browser;

describe("Browser", function() {

  $this->browser = new Browser();

  describe("visit", function() {
    it("returns an empty string", function() {
      $response = $this->browser->visit("https://google.com");
      expect($response)->toBe("");
    });
  });

  // describe("body", function() {
    // context("when the browser is visiting a page", function() {
      // it("should return the html source of the page", function() {
        // $this->browser->visit("https://google.com");
        // expect($this->browser->body())->toContain("<html>");
      // });

    // });

    // context("when the browser is not visiting a page", function() {
      // it("should return an empter string", function() {
        // expect($this->browser->body())->toBe("");
      // });
    // });
  // });

});
