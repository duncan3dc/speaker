---
layout: default
title: Acapela Provider
permalink: /providers/acapela/
api: Providers.AcapelaProvider
---

The Acapela provider uses a paid service that has a free trial option.  

You must [register](http://www.acapela-vaas.com/index.html) for an account for the credentials that you pass to the constructor:

~~~php
$provider = new AcapelaProvider("EVAL_VAAS", "EVAL_2643150", "y6r2fjul");
~~~

## Voices

The voice used can be set via the constructor or the `withVoice()` method:

~~~php
$provider = new AcapelaProvider("EVAL_VAAS", "EVAL_2643150", "y6r2fjul", "Graham");

$provider = $provider->withVoice("Peter");
~~~

There is a [list of voices](http://www.acapela-vaas.com/ReleasedDocumentation/voices_list.php) available from Acapela.

## Speed

The speed that the text is read can be set via the constructor or the `withSpeed()` method:

~~~php
$provider = new AcapelaProvider("EVAL_VAAS", "EVAL_2643150", "y6r2fjul", 200);

$provider = $provider->withSpeed(360);
~~~

The speed is an integer between 60 and 360
