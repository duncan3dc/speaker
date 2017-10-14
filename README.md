# speaker
A PHP library to convert text to speech using various services

Full documentation is available at http://duncan3dc.github.io/speaker/  
PHPDoc API documentation is also available at [http://duncan3dc.github.io/speaker/api/](http://duncan3dc.github.io/speaker/api/namespaces/duncan3dc.Speaker.html)  

[![Latest Stable Version](https://poser.pugx.org/duncan3dc/speaker/version.svg)](https://packagist.org/packages/duncan3dc/speaker)
[![Build Status](https://travis-ci.org/duncan3dc/speaker.svg?branch=master)](https://travis-ci.org/duncan3dc/speaker)
[![Coverage Status](https://coveralls.io/repos/github/duncan3dc/speaker/badge.svg)](https://coveralls.io/github/duncan3dc/speaker)

## Quick Example

```php
$google = new \duncan3dc\Speaker\Providers\GoogleProvider;
$tts = new \duncan3dc\Speaker\TextToSpeech("Hello World", $google);
file_put_contents("/tmp/hello.mp3", $tts->getAudioData());
```

_Read more at http://duncan3dc.github.io/speaker/_  


## Services
* __Acapela__ - _Paid voice as a service_
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
