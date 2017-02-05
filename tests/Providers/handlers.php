<?php


namespace duncan3dc\Speaker\Providers
{
    use duncan3dc\Speaker\Test\Providers\Handlers;

    function exec($command, &$output = [], &$return = 0)
    {
        return Handlers::call("exec", $command);
    }
}
