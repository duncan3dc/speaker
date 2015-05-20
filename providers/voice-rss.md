---
layout: default
title: Voice RSS Provider
permalink: /providers/voice-rss/
api: Providers.VoiceRssProvider
---

The Voice RSS provider uses a paid service that has a free limited option.  

You must [register](http://www.voicerss.org/personel/) for an apikey that you pass to the constructor:

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs");
~~~

The language used can be set via the constructor or the `setLanguage()` method:

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs", "en-gb");

$provider->setLanguage("fr-fr");
~~~

The speed that the text is read can be set via the constructor or the `setSpeed()` method:

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs", "en-gb", -10);

$provider->setSpeed(10);
~~~
