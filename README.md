![pigeon](https://cloud.githubusercontent.com/assets/247901/6005761/db2e982e-aada-11e4-9836-f0a659ba3512.png) Pigeon Webkit
=============

Functional testing tool for PHP built on top of [capybara-webkit](https://github.com/thoughtbot/capybara-webkit)

 * [Installation](#installation)
 * [How it works](#how-it-works)
 * [Usage](#usage)

## Installation

The following instructions outline installation using Composer. If you don't
have Composer, you can download it from [https://getcomposer.org/](https://getcomposer.org/)

 * Install QT following these [instructions] (https://github.com/thoughtbot/capybara-webkit/wiki/Installing-Qt-and-compiling-capybara-webkit).
 * Install capybara-webkit

```
$ gem install capybara-webkit -v "1.3.1"
```

or

```
$ sudo gem install capybara-webkit -v "1.3.1"
```

 * Run the following command:

```
$ php composer.phar global require etsy/pigeon-webkit:dev-master
```

## How it works

capybara-webkit provides a headless web browser (built on QT Webkit). When the browser starts, it opens up a port to accept commands. Pigeon Webkit starts the browser, opens a TCP connection to that port and sends commands to the browser to "drive" it and run assertions.

FunctionalPigeon is extending CapybaraWebkitDriver, which is an implementation of Behat Mink's [DriverInterface] (https://github.com/minkphp/Mink/blob/master/src/Behat/Mink/Driver/DriverInterface.php). As a result, most of the methods defined by the interface are available. The ones that are not implemented throw an UnsupportedException.

The css to xpath translation is handled by [the Symfony CssSelector component] (http://symfony.com/doc/current/components/css_selector.html)

## Usage

```php
$pigeon = new PigeonWebkit\FunctionalPigeon();
$pigeon->visit("https://etsy.com");
print_r($pigeon->body()); // HTML content of etsy.com
$pigeon->click("a#sign-in"); // opens the sign in modal
// ...etc
```

By default, Pigeon Webkit accepts css selectors. But it also has an xpath mode.

```php
$pigeon->setXPathMode(true);
$pigeon->visit("https://etsy.com");
$pigeon->click("//a[@id='sign-in']");
```

Example usage with the [pho] (https://github.com/danielstjules/pho) testing framework

```php
<?php
 
use PigeonWebkit\FunctionalPigeon;
 
describe("Visiting a URL", function() {
  before(function() {
    $this->p = new FunctionalPigeon();
  });
  
  it("loads the page", function() {
    $this->p->visit("https://etsy.com");
    expect($this->p->body())->toContain("Shop directly from people around the world.");
  });
});
```

For more examples, take a look at the specs for [FunctionalPigeon](spec/lib/FunctionalPigeonSpec.php) and its base class, [CapybaraWebkiDriver](spec/lib/CapybaraWebkitDriverSpec.php).

Some of the available methods

```
  reset
  visit
  getCurrentUrl
  reload
  forward
  back
  setRequestHeader
  getResponseHeaders
  setCookie
  getCookie
  getStatusCode
  getContent
  find
  getTagName
  getText
  getHtml
  getOuterHtml
  getAttribute
  getValue
  setValue
  check
  uncheck
  isChecked
  selectOption
  isSelected
  submitForm
  click
  isVisible
  executeScript
  evaluateScript
  getScreenshot
```
