<?php
require_once __DIR__ . '/../database/connection.php';

class Order {
    private $pdo;

    public function __construct() {
        global $pdo;
        if (!$pdo instanceof PDO) {
            error_log("PDO connection is not initialized in Order model.");
            throw new Exception("Database connection not available.");
        }
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function placeOrder($userId, $productId, $productImage, $productName, $quantity, $price, $paymentMethod, $address, $fullname, $contact) {
        // Validate inputs
        if (empty($userId) || empty($productId) || empty($productName) || empty($quantity) || empty($price) || 
            empty($paymentMethod) || empty($address) || empty($fullname) || empty($contact)) {
            error_log("Invalid input data for placeOrder: " . json_encode(compact('userId', 'productId', 'productName', 'quantity', 'price', 'paymentMethod', 'address', 'fullname', 'contact')));
            throw new InvalidArgumentException("All order fields are required.");
        }

        if (!is_numeric($userId) || !is_numeric($productId) || !is_numeric($quantity) || !is_numeric($price) || $quantity < 1 || $price < 0) {
            error_log("Invalid numeric input for placeOrder: userId=$userId, productId=$productId, quantity=$quantity, price=$price");
            throw new InvalidArgumentException("Invalid numeric values for order.");
        }

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO orders_tbl (
                    user_id, product_id, product_image, product_name, quantity, product_price,
                    payment_method, address, fullname, contact, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userId,
                $productId,
                $productImage ?? '',
                $productName,
                $quantity,
                $price,
                $paymentMethod,
                $address,
                $fullname,
                $contact
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Order placement failed: " . $e->getMessage());
            throw $e; // Re-throw to allow caller to handle
        }
    }

    public function getOrdersByUser($userId) {
        if (!is_numeric($userId)) {
            error_log("Invalid userId for getOrdersByUser: $userId");
            throw new InvalidArgumentException("Invalid user ID.");
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM orders_tbl WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch orders for user $userId: " . $e->getMessage());
            throw $e;
        }
    }

    public function getOrdersByStoreId($storeId) {
        if (!is_numeric($storeId)) {
            error_log("Invalid storeId for getOrdersByStoreId: $storeId");
            throw new InvalidArgumentException("Invalid store ID.");
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT o.* FROM orders_tbl o
                INNER JOIN products p ON o.product_id = p.id
                WHERE p.store_id = ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$storeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch orders for store $storeId: " . $e->getMessage());
            throw $e;
        }
    }

    public function getMonthlySalesByStore($storeId) {
        if (!is_numeric($storeId)) {
            error_log("Invalid storeId for getMonthlySalesByStore: $storeId");
            throw new InvalidArgumentException("Invalid store ID.");
        }

        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE_FORMAT(o.created_at, '%Y-%m') AS month,
                    SUM(o.product_price * o.quantity) AS total_sales,
                    COUNT(*) AS total_orders
                FROM orders_tbl o
                INNER JOIN products p ON o.product_id = p.id
                WHERE p.store_id = ?
                GROUP BY month
                ORDER BY month DESC
            ");
            $stmt->execute([$storeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch monthly sales for store $storeId: " . $e->getMessage());
            throw $e;
        }
    }
}