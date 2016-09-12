---
layout: default
title: Picotts Provider
permalink: /providers/picotts/
api: Providers.PicottsProvider
---

The Picotts provider uses a local command line tool for text to speech.  

~~~php
$provider = new PicottsProvider;
~~~

---

It can be installed in Debian/Ubuntu like so:

~~~sh
sudo apt-get install libttspico-utils
~~~
