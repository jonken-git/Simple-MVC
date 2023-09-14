<?php
require_once(__DATABASE__ . "Database.php");
abstract class Model
{

    protected static Database $db;
    private static array $instances = [];
    protected static array $required;
    protected static array $optional;

    private final function __construct()
    {
        self::$db = Database::getInstance(); 
    }

    private static function create(array $data): static
    {
        $model = new static();
        if($model::$required == null || empty($model::$required) || !is_array($model::$required))
        {
            throw new Exception("$model::\$columns is not set");
        }
        foreach($model::$required as $column)
        {
            $model->$column = $data[$column];
        }
        foreach($model::$optional as $column)
        {
            if(isset($data[$column]))
            {
                $model->$column = $data[$column];
            }
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
        return $model::create($result);
    }

    public static function all(): array
    {
        $model = self::getInstance();
        $result = self::$db->all($model::class);
        return array_map(fn($data) => $model::create($data), $result);
    }

    public static function join(string $join, string $on): ?array
    {
        die(__CLASS__ . "::" . __FUNCTION__ . " not implemented");
    }
}



