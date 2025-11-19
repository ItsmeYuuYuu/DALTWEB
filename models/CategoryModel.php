<?php

require_once __DIR__ . '/../config/database.php';

class CategoryModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllActive()
    {
        $sql = "SELECT * FROM categories WHERE status = 1 ORDER BY name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


