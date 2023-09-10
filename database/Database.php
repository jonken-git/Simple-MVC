<?php

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

    public function selectById(string $model, int $id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $model . " WHERE id = :id");
        $stmt->execute(["id" => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $user = $model::create($model, $res);
        return $user;
    }

    public PDO $conn;
}
