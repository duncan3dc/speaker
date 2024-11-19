Changelog
=========

## x.y.z - UNRELEASED

--------

## 1.5.0 - 2024-01-19

### Added

* [Providers] Added support for a different voice in VoiceRSS. ([#25](https://github.com/duncan3dc/speaker/issues/25))

--------

## 1.4.1 - 2024-11-18

### Fixed

* [Providers] Ensure the local filename is unique per text/language. ([#23](https://github.com/duncan3dc/speaker/issues/23))

### Changed

* [Support] Added support for PHP 8.2, and 8.3.

--------

## 1.4.0 - 2022-04-14

### Changed

* [Support] Added support for PHP 8.0, and 8.1.
* [Support] Dropped support for PHP 7.2.

--------

## 1.3.1 - 2020-10-15

### Changed

* [Support] Added support for Guzzle 7.

--------

## 1.3.0 - 2019-04-06

### Changed

* [Providers] Use duncan3dc/exec in the Picotts provider for shell commands.
* [Support] Dropped support for PHP 7.1.

--------

## 1.2.0 - 2019-01-13

### Changed

* [Support] Added support for PHP 7.3.
* [Support] Dropped support for PHP 7.0.
* [Support] Added support for Symfony 3.

--------

## 1.1.0 - 2018-03-09

### Added

* [Providers] AmazonPolly provider.

--------

## 1.0.0 - 2017-10-14

### Added

* [Providers] ResponsiveVoice provider.
* [TextToSpeech] Added a TextToSpeechInterface.

### Changed

* [Support] Added support for PHP 7.
* [Support] Dropped support for PHP 5.
* [Support] Dropped support for HHVM.
* [Providers] Dropped the Voxygen provider as the service is no longer available.
* [Providers] All providers are now immutable.
* [Exceptions] All exceptions thrown are now library specific.

### Fixed

* [Providers] Improve the error message for a pico unknown language.

--------

## 0.7.3 - 2017-04-07

### Fixed

* [Providers] Don't use a forward slash in the client name.

--------

## 0.7.2 - 2017-03-01

### Fixed

* [Providers] Use symfony/process within the Picotts provider for shell commands.

--------

## 0.7.1 - 2017-02-05

### Fixed

* [Providers] Fixed a bug with setLanguage() for the Picotts provider.

--------

## 0.7.0 - 2016-09-12

### Changed

* [TextToSpeech] Made generateFilename() public.
* [Providers] Picotts provider.
* [Support] Drop support for php5.5

--------

## 0.6.0 - 2015-08-29

### Changed

* [Google] Added the new "client" parameter
* [Providers] Acapela provider.

--------

## 0.5.1 - 2015-08-22

### Changed

* [Dependencies] Added PHPUnit to the dev dependencies.

--------

## 0.5.0 - 2015-06-13

### Changed

* [Dependencies] Use Guzzle 6.
* [Support] Drop support for php5.4

--------

## 0.2.0 - 2015-05-20

### Added

* [TextToSpeech] getFile() method to cache webservice calls.
* [TextToSpeech] save() method to store the audio on the local filesystem.

--------

## 0.1.0 - 2015-05-19

### Added

* [Providers] Google provider.
* [Providers] Voxygen provider.
* [Providers] Voice RSS provider.
