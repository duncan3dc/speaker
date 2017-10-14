---
layout: default
title: Google Provider
permalink: /providers/google/
api: Providers.GoogleProvider
---

The Google provider uses an unadvertised service from Google's translation subdomain.  

<p class="message-warning">This provider is restricted to messages up to a maximum of 100 characters</p>

The language used can be set via the constructor or the `withLanguage()` method:

~~~php
$provider = new GoogleProvider("fr");

$provider = $provider->withLanguage("en");
~~~
