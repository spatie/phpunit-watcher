# Changelog

All notable changes to `phpunit-watcher` will be documented in this file

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
