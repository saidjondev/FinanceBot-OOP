<?php

class User {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function registerIfNotExists($telegramId, $firstName) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE telegram_id = ?");
        $stmt->execute([$telegramId]);
        
        if (!$stmt->fetch()) {
            $insertStmt = $this->db->prepare("INSERT INTO users (telegram_id, first_name) VALUES (?, ?)");
            $insertStmt->execute([$telegramId, $firstName]);
        }
    }
}