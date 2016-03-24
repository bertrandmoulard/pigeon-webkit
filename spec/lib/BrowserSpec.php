<?php

require_once __DIR__ . "/../spec_helper.php";

use PigeonWebkit\Browser;

describe("Browser", function() {
  describe("generateServerPath", function() {
    it("returns the server path", function() {
      $browser = new Browser();
      $path = $browser->generateServerPath("- INSTALLATION DIRECTORY: /Library/Ruby/Gems/someversion");
      expect($path)->toBe("/Library/Ruby/Gems/someversion/gems/capybara-webkit-1.8.0/bin/webkit_server");
    });
  });
});
