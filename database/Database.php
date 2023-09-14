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

    // Returns bool === false on failure
    public function find(string $model, int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $model . " WHERE id = :id");
        $stmt->execute(["id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all(string $model): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $model);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public PDO $conn;
}
