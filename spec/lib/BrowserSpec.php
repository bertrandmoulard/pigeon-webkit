<?php
use PigeonWebkit\Browser;

describe("Browser", function() {
  describe("generateServerPath", function() {
    it("returns the server path", function() {
      $browser = new Browser();
      $path = $browser->generateServerPath("- INSTALLATION DIRECTORY: /Library/Ruby/Gems/2.0.0");
      expect($path)->toBe("/Library/Ruby/Gems/2.0.0/gems/capybara-webkit-1.4.1/bin/webkit_server");
    });
  });
});
