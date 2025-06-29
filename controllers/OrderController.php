<?php
require_once './../database/connection.php'; 

class OrderController {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    }

 public function placeOrder($userId, $productId, $productImage, $productName, $quantity, $price, $paymentMethod, $address, $fullname, $contact) {
    $sql = "INSERT INTO orders_tbl 
            (user_id, product_id, product_image, product_name, quantity, product_price, payment_method, address, fullname, contact, created_at)
            VALUES 
            (:user_id, :product_id, :product_image, :product_name, :quantity, :product_price, :payment_method, :address, :fullname, :contact, NOW())";
    
    $stmt = $this->pdo->prepare($sql); 
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':product_image' => $productImage,
        ':product_name' => $productName,
        ':quantity' => $quantity,
        ':product_price' => $price,
        ':payment_method' => $paymentMethod,
        ':address' => $address,
        ':fullname' => $fullname,
        ':contact' => $contact
    ]);
}

    public function getOrdersByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders_tbl WHERE user_id = :user_id ORDER BY id DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearCart($userId) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    return $stmt->execute([$userId]);
}

}
