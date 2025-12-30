# Changelog

All notable changes to `phpunit-watcher` will be documented in this file

## 1.24.3 - 2025-12-30

### What's Changed

* Support php new versions and higher and `symfony/console@8` by @KentarouTakeda in https://github.com/spatie/phpunit-watcher/pull/178

**Full Changelog**: https://github.com/spatie/phpunit-watcher/compare/1.24.2...1.24.3

## 1.24.2 - 2025-12-22

### What's Changed

* Make it work properly with Symfony 8 by @KentarouTakeda in https://github.com/spatie/phpunit-watcher/pull/177
* Bump actions/checkout from 4 to 6 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/175

### New Contributors

* @KentarouTakeda made their first contribution in https://github.com/spatie/phpunit-watcher/pull/177

**Full Changelog**: https://github.com/spatie/phpunit-watcher/compare/1.24.1...1.24.2

## 1.24.1 - 2025-11-03

### What's Changed

* Bump stefanzweifel/git-auto-commit-action from 4 to 5 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/163
* Bump actions/checkout from 2 to 4 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/162
* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/165
* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/166
* Update issue template by @AlexVanderbist in https://github.com/spatie/phpunit-watcher/pull/170
* Added Symfony 8 support to all symfony/* packages. by @thecaliskan in https://github.com/spatie/phpunit-watcher/pull/174
* Bump stefanzweifel/git-auto-commit-action from 5 to 7 by @dependabot[bot] in https://github.com/spatie/phpunit-watcher/pull/171

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/spatie/phpunit-watcher/pull/163
* @AlexVanderbist made their first contribution in https://github.com/spatie/phpunit-watcher/pull/170
* @thecaliskan made their first contribution in https://github.com/spatie/phpunit-watcher/pull/174

**Full Changelog**: https://github.com/spatie/phpunit-watcher/compare/1.24.0...1.24.1

## 1.23.2 - 2021-02-26

- replace passthru with echo (#133)

## 1.23.0 - 2020-10-27

- add support for `exclude` and `ignore` options

## 1.22.1 - 2020-10-21

- merge options recursively in `WatcherFactory` to preserve nested values (#115)

## 1.22.0 - 2020-01-04

- add ability to control timeout for PHPUnit process (#104)

## 1.21.4 - 2019-12-04

- fix compatibility with yosymfony/resource-watcher (#101)

## 1.12 - 1.21

- some version numbers were skipped due to mistagging

## 1.12.3 - 2019-12-02

- update dependencies

## 1.12.2 - 2019-01-15

- improve windows compatibility (#98)

## 1.12.1 - 2019-10-16

- update version number

## 1.12.0 - 2019-09-09

- new random seed feature. Run tests in random order.

## 1.11.2 - 2019-09-09

- Remove `deleteChar` call

## 1.11.1 - 2019-08-23

- update version number

## 1.11.0 - 2019-07-24

- drop support for older symfony versions
- drop support for PHP 7.1

## 1.10.1 - 2019-07-24

- fix compatibly with newer symfony versions

## 1.10.0 - 2019-07-19

- fix for deprecated symfony/process string construction
- removed support for PHP 7.0

## 1.9.0 - 2019-03-25

- add `hideManual` option

## 1.8.3 - 2019-02-09

- update to version 2 of react-stdio and use new methods

## 1.8.2 - 2019-01-10

- reverts 1.8.1

## 1.8.1 - 2019-01-09

**THIS VERSION DOES NOT WORK**

- allow new versions of deps

## 1.8.0 - 2018-11-12

- add `binaryPath` to configuration file

## 1.7.0 - 2018-10-18

- support for alternative config file names

## 1.6.0 - 2018-07-28

- add autocomplete when filtering on file names

## 1.5.1 - 2018-05-15

- fix a bug around screen switching

## 1.5.0 - 2018-02-27

- add filter for groups

## 1.4.0 - 2018-02-26

- add filter on testsuite

## 1.3.9 - 2018-02-17

- use Jolinotif 2

## 1.3.8 - 2018-02-02

- allow PHPUnit 7

## 1.3.7 - 2018-01-10

- allow symfony 4

## 1.3.6 - 2017-08-29

- fix typing in interactive mode

## 1.3.5 - 2017-08-29

- add back version number

## 1.3.4 - 2017-08-23

- fix for tests being run too many times

## 1.3.3 - 2017-08-09

- fix tool becoming unresponsive after pressing a key with no action

## 1.3.2 - 2017-08-05

- fix filters

## 1.3.1 - 2017-08-05

- improved readability of manual

## 1.3.0 - 2017-08-05

- added desktop notifications
- display used config file and application version
- allow initial arguments for PHPUnit to be set in the config file

## 1.2.1 - 2017-08-05

- throw exception if config file does not contain yml

## 1.2.0 - 2017-08-05

- check parent directories for config file

## 1.1.0 - 2017-08-02

- add interactive commands

## 1.0.4 - 2017-08-01

- do not halt when watching a non existing directory

## 1.0.3 - 2017-08-01

- switch to in memory cache

## 1.0.2 - 2017-07-31

- fix performance problems

## 1.0.1 - 2017-07-31

- scan for changes every quarter of a second instead of an entire second

## 1.0.0 - 2017-07-31

- initial release
