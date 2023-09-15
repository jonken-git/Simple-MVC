<?php
require_once(__DATABASE__ . "Database.php");
#[\AllowDynamicProperties]
abstract class Model
{

    protected static Database $db;
    private static array $instances = [];
    protected static array $required;
    protected static array $optional;
    private static string $name;

    private array $where = [];
    private array $join = [];
    private array $joinTables = [];
    private bool  $isJoining = false;
    private bool  $isWhere = false;


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
            $instance = new $class();
            self::$name = strtolower($instance::class);
            self::$instances[$class] = $instance;
        }
        return self::$instances[$class];
    }

    /**
     * @param string $where The where clause (excluding "WHERE")
     * @return static The model instance for chaining
     */
    public static function where(string $where): static
    {
        $model = self::getInstance();
        $model->isWhere = true;
        $model->where[] = $where;
        return $model;
    }

    /**
     * @param string $table_1 The table to join
     * @param string $table_1_column The column to join on
     * @param string|null $table_2 The table to join on (default: Callers table)
     * @param string|null $table_2_column The column to join on (default: "id")
     * @return static The model instance for chaining
     */
    public static function join(string $table_1, string $table_1_column, ?string $table_2 = null, ?string $table_2_column = null): static
    {
        $model = self::getInstance();
        $model->isJoining = true;
        $table_2 = match($table_2) {
            null => $model::$name . ".id",
            default => "$table_2.$table_2_column"
        };
        $model->joinTables[] = $table_1;
        $model->join[] = "JOIN $table_1 ON $table_1.$table_1_column = $table_2";
        return $model;
    }



    /**
     * @param bool $isGetOne Whether to get one or many results
     * @return static[]|static result of the query
     */
    public static function get(bool $isGetOne = false)
    {
        $model = self::getInstance();
        if($model->isJoining)
        {
            $columns = $model::createSelectFieldsWithJoin();
            $tables = $model::createTablesWithJoin();
            $query = "SELECT {$columns} FROM {$tables}";
        }
        else
        {
            $query = "SELECT * FROM " . $model::$name . " ";
        }

        if($model->isWhere)
        {
            $query .= $model::createWhere();
        }

        $res = self::$db->query($query);

        if($model->isJoining)
        {
            return array_map(fn($data) => self::createWithJoinedFields($data), $res);
        }
        else
        {
            return array_map(fn($data) => self::create($data), $res);
        }
    }

    public static function find(int $id): static
    {
        $model = self::getInstance();
        $result = self::$db->find($model::class, $id);
        return $model::create($result);
    }

    /**
     * @return static[]
     */
    public static function all(): array
    {
        $model = self::getInstance();
        $result = self::$db->all($model::class);
        return array_map(fn($data) => $model::create($data), $result);
    }

    private static function createTablesWithJoin()
    {
        $instance = self::getInstance();
        return $instance::$name . " " . implode(" ", $instance->join);
    }

    private static function createSelectFieldsWithJoin(): string
    {
        $instance = self::getInstance();

        $selectColumns = [];
        foreach($instance->joinTables as $table)
        {
            $columns = [...$table::$required, ...$table::$optional];

            $withUnderscore = array_map(fn($column) => $table . "." . $column . " AS " . $table . "_" . $column, $columns);
            foreach($withUnderscore as $column)
            {
                $selectColumns[] = $column;
            }

        }
        $mainColumns = [...$instance::$required, ...$instance::$optional];
        $mainColumns = array_map(fn($column) => $instance::$name . "." . $column . " AS " . $column, $mainColumns);
        $columns = implode(", ", [...$mainColumns, ...$selectColumns]);
        return $columns;
    }

    private static function createWithJoinedFields(array $data): static
    {
        $model = new static();
        if($model::$required == null || empty($model::$required) || !is_array($model::$required))
        {
            throw new Exception("$model::\$columns is not set");
        }
        $instance = self::getInstance();
        $models = [$model::class, ...$instance->joinTables];
        foreach($models as $_model)
        {
            $_model = ucfirst($_model);
            $isMainTable = $_model == $model::class;
            $prefix = $isMainTable ? "" : strtolower($_model) . "_";
            foreach($_model::$required as $column)
            {
                $model->$column = $data[$prefix . $column];
            }
            foreach($_model::$optional as $column)
            {
                $model->$column = $data[$prefix . $column] ?? null;
            }
        }
        return $model;
    }

    private static function createWhere()
    {
        $instance = self::getInstance();
        $where = implode(" AND ", $instance->where);
        return " WHERE " . $where;
    }

}



