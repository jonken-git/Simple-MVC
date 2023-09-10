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

    private static ?self $instance = null;
}