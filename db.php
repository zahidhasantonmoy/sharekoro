<?php
// db.php - Database connection class

require_once 'config.php';

class Database {
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $pdo;

    public function __construct() {
        $this->host = DB_HOST;
        $this->port = DB_PORT;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        
        $this->connect();
    }

    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->database};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>