<?php

require 'lib/Browser.php';

$browser = new PhpCapybaraWebkit\Browser();

$browser->command("Visit", ["https://google.com"]);
