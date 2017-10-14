---
layout: default
title: ResponsiveVoicer Provider
permalink: /providers/responsive-voice/
api: Providers.ResponsiveVoiceProvider
---

The ResponsiveVoice provider uses a specialist text to speech conversion website.  

The language used can be set via the constructor or the `withLanguage()` method:

~~~php
$provider = new ResponsiveVoiceProvider("de");

$provider = $provider->withVoice("fr");
~~~
