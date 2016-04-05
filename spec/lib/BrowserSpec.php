<?php

require_once __DIR__ . "/../spec_helper.php";

use PigeonWebkit\Browser;
use Kahlan\Plugin\Monkey;

describe("Browser", function() {

    describe("#findServerPath", function() {
        context("when there is a server", function() {
            it("should find the webkit server", function() {
                Monkey::patch('exec', function($command, &$output=[], &$return_var=null) {
                    array_push($output, "A line of output");
                    array_push($output, "    - GEM PATHS:  ");
                    array_push($output, "Another line of output");
                    array_push($output, "valid path");
                });

                Monkey::patch('file_exists', function($path) {
                    if($path == "valid path/gems/capybara-webkit-1.8.0/bin/webkit_server") {
                        return true;
                    }
                    return false;
                });
                expect(PigeonWebkit\Browser::findServerPath())->toBe("valid path/gems/capybara-webkit-1.8.0/bin/webkit_server");
            });
        });

        context("when there isn't a server", function() {
            it("should be null", function() {
                Monkey::patch('exec', function($command, &$output=[], &$return_var=null) {
                    array_push($output, "A line of output");
                    array_push($output, "    - GEM PATHS:  ");
                    array_push($output, "Another line of output");
                });

                Monkey::patch('file_exists', function($path) {
                    return false;
                });
                expect(PigeonWebkit\Browser::findServerPath())->toBe(null);
            });
        });

    });
});
