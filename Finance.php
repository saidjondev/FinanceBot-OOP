<?php

class Finance {
    private $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function addTransaction($userId, $amount, $category, $type) {
        $sql = "INSERT INTO transactions (user_id, amount, category, type) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $amount, $category, $type]);
    }

    public function getBalance($userId) {
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN type = 'daromad' THEN amount ELSE 0 END), 0) - 
                    COALESCE(SUM(CASE WHEN type = 'xarajat' THEN amount ELSE 0 END), 0) as balance
                FROM transactions WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();        
    }
}