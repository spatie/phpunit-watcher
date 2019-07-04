# Automatically rerun PHPUnit tests when source code changes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/phpunit-watcher.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-watcher)
[![Build Status](https://img.shields.io/travis/spatie/phpunit-watcher/master.svg?style=flat-square)](https://travis-ci.org/spatie/phpunit-watcher)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/phpunit-watcher.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/phpunit-watcher)
[![StyleCI](https://styleci.io/repos/98163923/shield?branch=master)](https://styleci.io/repos/98163923)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/phpunit-watcher.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-watcher)

Wouldn't it be great if your PHPUnit tests would be automatically rerun whenever you change some code? This package can do exactly that.

With the package installed you can do this:

```bash
phpunit-watcher watch
```

Here's how it looks like:

![watcher screenshot](https://spatie.github.io/phpunit-watcher/screenshots/watcher.jpg)

This will run the tests and rerun them whenever a file in the `app`, `src` or `tests` directory is modified.

Want to pass some arguments to PHPUnit? No problem, just tack them on:

```bash
phpunit-watcher watch --filter=it_can_run_a_single_test
```

In his excellent talk at Laracon EU 2017 [Amo Chohan](https://github.com/amochohan) shows our phpunit-watcher in action.

[![Amo Chohan demo](https://spatie.github.io/phpunit-watcher/videothumb.png)](https://youtu.be/CF1UhUj9LG0?t=26m13s)

## Installation

You can install this package globally like this

```bash
composer global require spatie/phpunit-watcher
```

After that `phpunit-watcher watch` can be run in any directory on your system.

Alternatively you can install the package locally as a dev dependency in your project

```bash
composer require spatie/phpunit-watcher --dev
```

Locally installed you can run it with `vendor/bin/phpunit-watcher watch`


## Usage

All the examples assume you've installed the package globally. If you opted for the local installation prepend `vendor/bin/` everywhere where `phpunit-watcher` is mentioned.

You can start the watcher with:

```bash
phpunit-watcher watch
```

This will run the tests and rerun them whenever a file in the `src` or `tests` directory is modified.

Want to pass some arguments to PHPUnit? No problem, just tack them on:

```bash
phpunit-watcher watch --filter=it_can_run_a_single_test
```

### Auto-filter mode
This package has a secondary mode that automatically runs only the tests related to changed files:

```bash
phpunit-watcher --auto-filter watch
```

In this mode, making a change to SomeClass.php will run the tests in `SomeClassTest`. It also works when editing tests.

Note: The `--auto-filter` (or `-a`) option must come **before** the `watch` command, or else it will be passed to PHPUnit.

## Customization

Certain aspects of the behaviour of the tool can be modified. The file for options may be named `.phpunit-watcher.yml`, `phpunit-watcher.yml` or `phpunit-watcher.yml.dist`. The tool will look for a file in that order.

If a config file does not exist in the project directory, the tool will check if a file exists in any of the parent directories of the project directory.

Here's some example content. Read on for a more detailed explanation of all the options.

```yaml
watch:
  directories:
    - src
    - tests
  fileMask: '*.php'
notifications:
  passingTests: false
  failingTests: false
phpunit:
  binaryPath: vendor/bin/phpunit
  arguments: '--stop-on-failure'
```

### Customize watched directories and files

You can customize the directories being watched by creating a file named `.phpunit-watcher.yml` in your project directory. Here's some example content:

```yaml
watch:
  directories:
    - src
    - tests
  fileMask: '*.php'
```

### Desktop notifications

By default the tool will display desktop notifications whenever the tests pass or fail. If you want to disable certain desktop notifications update `.phpunit-watcher.yml` by adding a `notifications` key.

```yaml
notifications:
  passingTests: false
  failingTests: false
```

### Help messages
By default the tool will display a helper for keyboard actions after each run. You can hide these help messages by adding a `hideManual` key in the `.phpunit-watcher.yml`.

```yaml
hideManual: true
```

### Customize PHPUnit

#### Binary

By default the tool use `vendor/bin/phpunit` as default PHPUnit binary file, however, it may be useful to be able to customize this value for people who have a binary file in a different location.

You can specificy it in the `.phpunit-watcher.yml` config file. Here's an example:

```yaml
phpunit:
  binaryPath: ./vendor/phpunit/phpunit/phpunit
```

#### Initial arguments

If you want to use pass the same arguments to PHPUnit everytime to watcher starts, you can specificy those in the `.phpunit-watcher.yml` config file. Here's an example:

```yaml
phpunit:
  arguments: '--stop-on-failure'
```

When starting the tool with some arguments (eg `phpunit-watcher watch --filter=my_favourite_test`) those arguments will get used instead of the ones specified in the config file.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if you use it often we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

We started creating this package after reading [this excellent article](https://www.sitepoint.com/write-javascript-style-test-watchers-php/) by [Christoper Pitt](https://twitter.com/assertchris)

Interactive commands were inspired by [Jest](https://facebook.github.io/jest/).

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie).
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
