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

    /**
     * @var array The children to select
     */
    private array $with = [];
    /**
     * @var bool Whether the query is joining tables
     */
    private bool  $isJoining = false;
    /**
     * @var bool Whether the query has a where clause
     */
    private bool  $isWhere = false;
    /**
     * @var bool Whether the query has a children
     */
    private bool  $isWith = false;

    /**
     * @var bool Whether the query is getting one or many results
     */
    private bool $isGetOne = false;


    private final function __construct()
    {
        self::$db = Database::getInstance(); 
    }

    /**
     * Create single model from data
     * @param array $data The data to create the model from
     * @return static The model instance
     */
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
            $instance::$name = self::$name;
            self::$instances[$class] = $instance;
        }
        return self::$instances[$class];
    }

    /**
     * @param string $column The column to search
     * @param string $condition The condition to search for
     * @param string $operator The operator to use (default: "=")
     * @return static The model instance for chaining
     */
    public static function where(string $column, string $condition, string $operator = "="): static
    {
        $model = self::getInstance();
        $model->isWhere = true;
        $condition = htmlspecialchars($condition);
        $model->where[] = "{$column} {$operator} '{$condition}'";
        return $model;
    }

    /**
     * @param string $table_1 The table to join
     * @param string $table_1_column The column to join on
     * @param string|null $table_2 The table to join on (default: Callers table)
     * @param string|null $table_2_column The column to join on (default: "id")
     * @param string|null $joinKind The kind of join (default: "LEFT JOIN")
     * @return static The model instance for chaining
     */
    public static function join(
        string $table_1,
        string $table_1_column,
        ?string $table_2 = null,
        ?string $table_2_column = null,
        ?string $joinKind = null
    ): static {
        $instance = self::getInstance();
        $instance->isJoining = true;
        $join = "{$table_1}.{$table_1_column}";

        $on = match([$table_2, $table_2_column]) {
            [null, null] => $instance::$name . ".id",
            default => "$table_2.{$table_2_column}"
        };

        $instance->join[] = [
            "table" => $table_1, 
            "join" => $join,
            "on" => $on,
            "kind" => $joinKind ?? "LEFT JOIN"
        ];
        return $instance;
    }



    /**
     * @param bool $isGetOne Whether to get one or many results
     * @return static[]|static result of the query
     */
    public static function get(bool $isGetOne = false)
    {
        $instance = self::getInstance();
        $instance->isGetOne = $isGetOne;
        if($instance->isJoining)
        {
            [$tables, $columns] = $instance::createSelectFieldsWithJoin();
            $query = "SELECT {$columns} FROM {$tables}";
        }
        else
        {
            $query = "SELECT * FROM " . $instance::$name . " ";
        }

        if($instance->isWhere)
        {
            $query .= $instance::createWhere();
        }
        $res = self::$db->query($query);
        $res = $instance->createFromResult($res);
        // Resets the instances used
        self::$instances = [];
        return $isGetOne ? $res[0] : $res;
    }

    /**
     * Heavy on the database, use with caution. 
     * @param Model $object The object to add the children to
     * @return Model The object with the children added
     */
    private static function addWith(Model $object)
    {
        $instance = self::getInstance();
        foreach($instance->with as $with)
        {
            /** @var static $model */
            $model = ucfirst($with["table"]);
            // If not specified, the where clause is the id of the object
            $with["where"] ??= $object->id;
            $rows = $model::where($with["on"], $with["where"])::get();
            $as = strtolower($with["table"]) . "s";
            $object->$as = $rows;
        }
        return $object;
    }

    /**
     * @param array $result The result from the query
     * @return static[]|static result of the query. Array of models if many, otherwise a single model
     */
    private static function createFromResult(array $result)
    {
        $instance = self::getInstance();
        if($instance->isJoining)
        {
            $res = array_map(fn($res) => self::createWithJoinedFields($res), $result);
        }
        else
        {
            $res = array_map(fn($res) => self::create($res), $result);
            if($instance->isGetOne || $instance->isWith)  
            {
                foreach($res as &$object)
                {
                    $object = self::addWith($object);
                }
            }
            return $res;
        }
    }

    /**
     * @param string $withModel The child with relation to model
     * @param string $on The column to join on
     * @param string|null $where The where clause to use (default: id of model)
     * @return static The model instance for chaining
     */
    public static function with(string $withModel, string $on, string $where = null): static
    {
        $instance = self::getInstance();
        $instance->isWith = true;
        $instance->isGetOne = true;
        $instance->with[] = ["table" => $withModel, "on" => $on, "where" => $where];
        return $instance;
    }

    /**
     * @param int $id The id of the model to find
     * @return static The model instance
     */
    public static function find(int $id): static
    {
        $model = self::getInstance();
        $result = self::$db->find($model::class, $id);
        return $model::create($result);
    }

    /**
     * Returns all rows in the table as models
     * @return static[]
     */
    public static function all(): array
    {
        $model = self::getInstance();
        $result = self::$db->all($model::class);
        return array_map(fn($data) => $model::create($data), $result);
    }

    /**
     * @param string $table The table to join
     * @return string The table with the joined columns aliased as "table_column"
     */
    private static function createAliasForJoinedColumns(string $table): string
    {
        $columns = [...$table::$required, ...$table::$optional];
        $columns = array_map(fn($column) => $table . "." . $column . " AS " . $table . "_" . $column, $columns);
        return implode(", ", $columns);
    }

    /**
     * @return array<string> The tables and columns to select as comma separated strings
     */
    private static function createSelectFieldsWithJoin(): array
    {
        $instance = self::getInstance();

        $selectColumns = [];
        foreach($instance->join as $join)
        {
            $joinFields[] = "{$join["kind"]} {$join["table"]} ON {$join["join"]} = {$join["on"]}";
            $selectColumns[] = self::createAliasForJoinedColumns($join['table']);
        }
        // Prepends the main table to the select columns and join fields
        array_unshift($selectColumns, $instance::$name . ".*");
        array_unshift($joinFields, $instance::$name);
        return [implode(" ", $joinFields), implode(", ", $selectColumns)];
    }

    /**
     * Creates a model from the result of a query with joined tables.
     * The joined tables are aliased as "{table_name}_{column_name}"
     * @param array $data The data to create the model from
     * @return static The model instance
     */
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



