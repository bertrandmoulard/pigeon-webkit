<?php

require_once __DIR__ . "/../spec_helper.php";

use PigeonWebkit\Browser;
use Kahlan\Plugin\Monkey;

describe("Browser", function() {
    describe("__construct", function() {

        context("when the server path is specified", function() {
            context("and the path is valid", function() {
                beforeEach(function(){
                });

                it("doesn't throw an exception", function() {

                });
            });
            context("and the path is not valid", function() {
                it("throws an exception", function() {

                });
            });
        });

        context("when the server path is not specified", function() {
            context("and the webkit server can be found", function() {

                it("doesn't throw an exception", function() {

                });
            });
            context("and the webkit server cannot be found", function() {
                it("throws an exception", function() {

                });
            });
        });

    });

    fdescribe("#findServerPath", function() {
        it("should find the webkit server", function() {
            Monkey::patch('exec', function($command, &$output=[], &$return_var=null) {
                echo "inside the fake function\n";
                array_push($output, "A line of output");
            });
            PigeonWebkit\Browser::findServerPath();
        });
    });
});
