<?php
namespace Models;

require_once __DIR__ . '/../database/connection.php';

use PDO;
use PDOException;

class Cart
{
    private $pdo;

    public function __construct()
    {
        global $pdo;         // Access the global $pdo
        $this->pdo = $pdo;   // Assign it to $this->pdo
    }

   public function addToCart($userId, $productId, $productName, $productImage, $productPrice)
{
    try {
        $this->pdo->beginTransaction();

        // Insert add to cart
        $stmt = $this->pdo->prepare("INSERT INTO cart (user_id, product_id, product_name, product_image, product_price, quantity) 
                                     VALUES (?, ?, ?, ?, ?, 1)
                                     ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        $stmt->execute([$userId, $productId, $productName, $productImage, $productPrice]);

        // Deduct 1 from product quantity after inserting added cart into cart table
        $updateStmt = $this->pdo->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ? AND quantity >= 1");
        $updateStmt->execute([$productId]);

        $this->pdo->commit();
        return true;
    } catch (PDOException $e) {
        $this->pdo->rollBack();
        // error_log($e->getMessage()); UNcommENt this if need
        return false;
    }
}

}
