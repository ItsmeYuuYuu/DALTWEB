<?php

require_once __DIR__ . '/../config/database.php';

class ProductModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($limit = 20, $offset = 0, $categoryId = null, $sortBy = 'created_at', $sortOrder = 'DESC')
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.status = 1";
        
        $params = [];
        
        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = (int) $categoryId;
        }
        
        // Validate sortBy và sortOrder để tránh SQL injection
        $allowedSorts = ['created_at', 'price', 'name'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'created_at';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY p.{$sortBy} {$sortOrder}";
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAllActive($categoryId = null)
    {
        $sql = "SELECT COUNT(*) AS total FROM products WHERE status = 1";
        $params = [];
        
        if ($categoryId !== null && $categoryId > 0) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = (int) $categoryId;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch();
        return (int) $row['total'];
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM products
                WHERE status = 1 AND name LIKE :kw
                ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':kw', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO products (name, slug, category_id, price, old_price, thumbnail, description, status, created_at, updated_at)
                VALUES (:name, :slug, :category_id, :price, :old_price, :thumbnail, :description, :status, NOW(), NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':category_id' => $data['category_id'],
            ':price' => $data['price'],
            ':old_price' => $data['old_price'],
            ':thumbnail' => $data['thumbnail'],
            ':description' => $data['description'],
            ':status' => $data['status'] ?? 1,
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE products SET
                    name = :name,
                    slug = :slug,
                    category_id = :category_id,
                    price = :price,
                    old_price = :old_price,
                    thumbnail = :thumbnail,
                    description = :description,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':category_id' => $data['category_id'],
            ':price' => $data['price'],
            ':old_price' => $data['old_price'],
            ':thumbnail' => $data['thumbnail'],
            ':description' => $data['description'],
            ':status' => $data['status'],
            ':id' => $id,
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}


