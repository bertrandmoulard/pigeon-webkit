Pigeon Webkit
=============

Functional testing tool for PHP built on top of [https://github.com/thoughtbot/capybara-webkit](capybara-webkit)

 * [Installation](#installation)
 * [How it works](#how-it-works)
 * [Usage](#usage)

## Installation

The following instructions outline installation using Composer. If you don't
have Composer, you can download it from [http://getcomposer.org/](http://getcomposer.org/)

 * Install QT following these [instructions] (https://github.com/thoughtbot/capybara-webkit/wiki/Installing-Qt-and-compiling-capybara-webkit).
 * Install capybara-webkit

```
$ gem install capybara-webkit -v "1.3.1"
```

or

```
$ sudo gem install capybara-webkit -v "1.3.1"
```

 * Run either of the following commands, depending on your environment:

```
$ composer global require etsy/pigeon-webkit:dev-master
$ php composer.phar global require etsy/pigeon-webkit:dev-master
```

* Edit your `~/.bash_profile` or `~/.profile` and add:

```
export PATH=$HOME/.composer/vendor/bin:$PATH
```

## How it works

capybara-webkit provides a headless web browser (built on QT Webkit). When the browser starts, it opens up a port to accept commands. Pigeon Webkit starts the browser, opens a TCP connection to that port and sends commands to the browser to "drive" it and run assertions.

FunctionalPigeon is extending CapybaraWebkitDriver, which is an implementation of Behat Mink's [DriverInterface] (https://github.com/Behat/Mink/blob/master/src/Behat/Mink/Driver/DriverInterface.php). As a result, most of the methods defined by the interface are available. The ones that are not implemented throw an UnsupportedException.

The css to xpath translation is handled by [the symphony CssSelector component] (http://symfony.com/doc/current/components/css_selector.html)

## Usage

```php
$pigeon = new PigeonWebkit\FunctionalPigeon();
$pigeon->visit("https://etsy.com");
print_r($pigeon->body()); // HTML content of etsy.com
$pigeon->click("a#sign-in");
```

By default, Pigeon Webkit accepts css selector. But it also has a xpath mode.

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



