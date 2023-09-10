<?php
require_once(__CONFIG__ . "database.php");

class Database
{
    use Singleton;

    public function __construct()
    {
        $hostName = DatabaseConf::HOST->value;
        $dbName   = DatabaseConf::DATABASE_NAME->value;
        $username = DatabaseConf::USERNAME->value;
        $password = DatabaseConf::PASSWORD->value; // TODO: cannot use, becuase enum cant have cases with same value i.e. root root

        $this->conn = new PDO("mysql:host={$hostName};dbname={$dbName}", $username, $username);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function find(string $model, int $id): ?Model
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $model . " WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if($res) {
            $res = $model::create($res);
        }
        return $res;
    }

    public function all(string $model): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $model);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($res as $user) {
            $users[] = $model::create($user);
        }
        return $users;
    }

    public PDO $conn;
}
