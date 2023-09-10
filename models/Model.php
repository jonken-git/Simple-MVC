<?php
require_once(__DATABASE__ . "Database.php");
abstract class Model
{

    protected static Database $db;
    private static array $instances = [];
    protected static array $columns;

    public final function __construct()
    {
        self::$db = Database::getInstance(); 
    }

    public static function create(array $data): static
    {
        $model = new static();
        foreach($model::$columns as $column)
        {
            $model->$column = $data[$column];
        }
        return $model;
    }

    public static function getInstance(): static
    {
        $class = get_called_class();
        if(!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }

    public static function find(int $id): static
    {
        $class = get_called_class();
        $result = self::$db->find($class, $id);
        return $result;
    }
}



