<?php

namespace duncan3dc\Speaker\Test\Providers;

require __DIR__ . "/handlers.php";

class Handlers
{
    private static $handlers = [];


    public static function clear()
    {
        self::$handlers = [];
    }


    public static function handle($name, callable $handler)
    {
        self::$handlers[$name] = $handler;
    }


    public static function call($name, ...$arguments)
    {
        if (array_key_exists($name, self::$handlers)) {
            $function = self::$handlers[$name];
        } else {
            $function = $name;
        }

        return call_user_func_array($function, $arguments);
    }
}
