<?php

abstract class Model
{
    protected static Database $db;
    private static array $instances = [];

    public static function getInstance(): static
    {
        $class = get_called_class();
        if(!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }
}



