# speaker
A PHP library to convert text to speech using various services

Full documentation is available at http://duncan3dc.github.io/speaker/  
PHPDoc API documentation is also available at [http://duncan3dc.github.io/speaker/api/](http://duncan3dc.github.io/speaker/api/namespaces/duncan3dc.Speaker.html)  

[![release](https://poser.pugx.org/duncan3dc/speaker/version.svg)](https://packagist.org/packages/duncan3dc/speaker)
[![build](https://github.com/duncan3dc/speaker/workflows/.github/workflows/buildcheck.yml/badge.svg?branch=master)](https://github.com/duncan3dc/speaker/actions?query=branch%3Amaster+workflow%3A.github%2Fworkflows%2Fbuildcheck.yml)
[![coverage](https://codecov.io/gh/duncan3dc/speaker/graph/badge.svg)](https://codecov.io/gh/duncan3dc/speaker)

## Quick Example

```php
$google = new \duncan3dc\Speaker\Providers\GoogleProvider;
$tts = new \duncan3dc\Speaker\TextToSpeech("Hello World", $google);
file_put_contents("/tmp/hello.mp3", $tts->getAudioData());
```

_Read more at http://duncan3dc.github.io/speaker/_  


## Services
* __Acapela__ - _Paid voice as a service_
* __AmazonPolly__ - _AWS service with a 12 month free tier_
* __Google__ - _Unadvertised service with 100 character limit_
* __Picotts__ - _An offline command line version_
* __ResponsiveVoice__ - _Unadvertised service running over a javascript engine_
* __Voice RSS__ - _Free/paid service requires [registration](http://www.voicerss.org/personel/)_


## Changelog
A [Changelog](CHANGELOG.md) has been available since the beginning of time


## Where to get help
Found a bug? Got a question? Just not sure how something works?  
Please [create an issue](//github.com/duncan3dc/speaker/issues) and I'll do my best to help out.  
Alternatively you can catch me on [Twitter](https://twitter.com/duncan3dc)


## duncan3dc/speaker for enterprise

Available as part of the Tidelift Subscription

The maintainers of duncan3dc/speaker and thousands of other packages are working with Tidelift to deliver commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use. [Learn more.](https://tidelift.com/subscription/pkg/packagist-duncan3dc-speaker?utm_source=packagist-duncan3dc-speaker&utm_medium=referral&utm_campaign=readme)
