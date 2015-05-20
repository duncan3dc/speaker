---
layout: default
title: Voxygen Provider
permalink: /providers/voxygen/
api: Providers.VoxygenProvider
---

The Voxygen provider uses a specialist text to speech conversion website.  

The voice used can be set via the constructor or the `setVoice()` method:

~~~php
$provider = new VoxygenProvider("Yeti");

$provider->setVoice("Robot");
~~~
