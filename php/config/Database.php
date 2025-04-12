<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_WARNING);

class Database {
    private $hostname = 'mysql';
    private $dbname = 'pkrim-art-gallery';
    private $username = 'root';
    private $password = 'root';
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=". $this->hostname .";dbname=". $this->dbname .";charset=utf8mb4",
                $this->username, $this->password);

        }catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

}