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

The language used can be set via the constructor or the `withLanguage()` method:

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs", "en-gb");

$provider = $provider->withLanguage("fr-fr");
~~~

The speed that the text is read can be set via the constructor or the `withSpeed()` method:

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs", "en-gb", -10);

$provider = $provider->withSpeed(10);
~~~

The voice that the text is read with can be set via the constructor or the `withVoice()` method:
<p class="message-api">Note that the voice must be <a href='https://www.voicerss.org/api/'>compatible</a> with the language.</p>

~~~php
$provider = new VoiceRssProvider("sp39483478dhshdfs", "en-gb", 0, "Harry");

$provider = $provider->withVoice("Harry");
~~~
