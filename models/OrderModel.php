<?php

require_once __DIR__ . '/../config/database.php';

class OrderModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function create(array $orderData): int
    {
        $this->conn->beginTransaction();

        try {
            // Tạo đơn hàng
            $sql = "INSERT INTO orders (user_id, customer_name, customer_phone, customer_email, 
                    shipping_address, shipping_lat, shipping_lng, total_amount, status, notes, 
                    created_at, updated_at)
                    VALUES (:user_id, :customer_name, :customer_phone, :customer_email, 
                    :shipping_address, :shipping_lat, :shipping_lng, :total_amount, :status, :notes, 
                    NOW(), NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':user_id' => $orderData['user_id'],
                ':customer_name' => $orderData['customer_name'],
                ':customer_phone' => $orderData['customer_phone'],
                ':customer_email' => $orderData['customer_email'],
                ':shipping_address' => $orderData['shipping_address'],
                ':shipping_lat' => $orderData['shipping_lat'] ?? null,
                ':shipping_lng' => $orderData['shipping_lng'] ?? null,
                ':total_amount' => $orderData['total_amount'],
                ':status' => $orderData['status'] ?? 'pending',
                ':notes' => $orderData['notes'] ?? null,
            ]);

            $orderId = (int) $this->conn->lastInsertId();

            // Thêm chi tiết đơn hàng
            foreach ($orderData['items'] as $item) {
                $sql = "INSERT INTO order_items (order_id, product_id, product_name, product_price, 
                        quantity, subtotal, created_at)
                        VALUES (:order_id, :product_id, :product_name, :product_price, 
                        :quantity, :subtotal, NOW())";
                
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':product_name' => $item['product_name'],
                    ':product_price' => $item['product_price'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $item['subtotal'],
                ]);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM orders WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        
        if ($order) {
            $order['items'] = $this->getOrderItems($id);
        }
        
        return $order ?: null;
    }

    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT * FROM order_items WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getByUserId(int $userId): array
    {
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}

