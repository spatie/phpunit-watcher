# Automatically rerun PHPUnit tests when source code changes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/phpunit-watcher.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-watcher)
[![Build Status](https://img.shields.io/travis/spatie/phpunit-watcher/master.svg?style=flat-square)](https://travis-ci.org/spatie/phpunit-watcher)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/7b6b575b-81b1-4fac-826a-9257d46c5c6c.svg?style=flat-square)](https://insight.sensiolabs.com/projects/7b6b575b-81b1-4fac-826a-9257d46c5c6c)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/phpunit-watcher.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/phpunit-watcher)
[![StyleCI](https://styleci.io/repos/98163923/shield?branch=master)](https://styleci.io/repos/98163923)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/phpunit-watcher.svg?style=flat-square)](https://packagist.org/packages/spatie/phpunit-watcher)

Wouldn't it be great if your PHPUnit tests would be automatically rerun whenever you change some code? This package can do exactly that.

With the package installed you can do this:

```bash
phpunit-watcher
```

This will run the tests and rerun them whenever a file in the `app`, `src` or `tests` directory is modified.

Want to pass some arguments to PHPUnit? No problem, just tack them on:

```bash
phpunit-watcher --filter=it_can_run_a_single_test
```

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Installation

You can install this package globally like this

```bash
composer global require spatie/phpunit-watcher
```

After that `phpunit-watcher` can be run in any directory on your system.

Alternatively you can install the package locally as a dev dependency in your project

```bash
composer require spatie/phpunit-watcher --dev
```

Locally installed you can run it with `vendor/bin/phpunit-watcher`


## Usage

All the examples assume you've installed the package globally. If you opted for the local installation prepend `vendor/bin/` everywhere where `phpunit-watcher` is mentioned.

You can start the watcher with:

```bash
phpunit-watcher
```

This will run the tests and rerun them whenever a file in the `src` or `tests` directory is modified.

You can customize the directories being watched by creating a file named `.phpunit-watcher.yml` in your project directory. Here's some example content:

```yaml
watch:
  directories:
    - src
    - tests
  fileMask: '*.php'
```

Want to pass some arguments to PHPUnit no problem, just tack them on:

```bash
phpunit-watcher --filter=it_can_run_a_single_test
```

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

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
