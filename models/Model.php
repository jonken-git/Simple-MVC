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
        if($model::$columns == null || empty($model::$columns) || !is_array($model::$columns))
        {
            throw new Exception("$model::\$columns is not set");
        }
        foreach($model::$columns as $column)
        {
            $model->$column = $data[$column];
        }
        return $model;
    }

    private static function getInstance(): static
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
        $model = self::getInstance();
        $result = self::$db->find($model::class, $id);
        return $result;
    }

    public static function all(): array
    {
        $model = self::getInstance();
        $result = self::$db->all($model::class);
        return $result;
    }
}



