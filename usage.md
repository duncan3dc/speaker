---
layout: default
title: Usage
permalink: /usage/
api: TextToSpeech
---

Here's a full example, converting the text `"Hello World"` to speech and saving it an mp3 file at `/tmp/hello.mp3`

~~~php
require_once __DIR__ . "vendor/autoload.php";

use duncan3dc\Speaker\TextToSpeech;
use duncan3dc\Speaker\Providers\GoogleProvider;

$provider = new GoogleProvider;

$tts = new TextToSpeech("Hello World", $provider);
$tts->save("/tmp/hello.mp3");
~~~

# Caching
You can take advantage of caching by using the `getFile()` method:

~~~php
$tts = new TextToSpeech("Hello World", $provider);

# This will call the provider's web service
$filename = $tts->getFile("/var/tts");

# This will just return the previously generated file
$filename = $tts->getFile("/var/tts");
~~~

In the above examples `$filename` includes the full path and file name.
