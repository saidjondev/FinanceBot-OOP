<?php

class Database {

    private static $instance = null;
    private $pdo;

    private function __construct($host, $port, $dbname, $user, $password) {
        echo "DIQQAT: PostgreSQL eshigi hozir ochildi!\n";
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        try {
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->createTables();
        } catch(PDOException $e) {
            die("Bazaga ulanib bo'lmadi: " . $e->getMessage());
        }
    }

    private function __clone() { }      

    public static function getInstance($host, $port, $dbname, $user, $password) {
        if (self::$instance === null) {
            self::$instance = new self($host, $port, $dbname, $user, $password);
        }
        return self::$instance;
    }

    private function createTables() {
        $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            telegram_id BIGINT UNIQUE NOT NULL,
            first_name VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sqlUsers);
    
        $sqlTransaction = "CREATE TABLE IF NOT EXISTS transactions (
            id SERIAL PRIMARY KEY,
            user_id BIGINT NOT NULL,
            amount NUMERIC(10, 2) NOT NULL,
            category VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->pdo->exec($sqlTransaction);
    }

    public function getConnection() {
        return $this->pdo;
    }     
}