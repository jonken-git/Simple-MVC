<?php

trait Singleton
{

    private static function createInstance(): static
    {
        if(self::$instance == null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getInstance(): static
    {
        return self::createInstance();
    }

    private static ?Server $instance = null;
}