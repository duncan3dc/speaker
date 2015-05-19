<?php

namespace duncan3dc\Speaker\Test\Providers;

use duncan3dc\Speaker\Providers\AbstractProvider;

class ExampleProvider extends AbstractProvider
{
    public function textToSpeech($text)
    {
        return $this->sendRequest("http://example.com/", [
            "text"  =>  $text,
        ]);
    }
}
