<?php
require_once __DIR__ . '/../database/connection.php';

class CartController {

    public function getCartCountByUser($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getCartItemsByUser($userId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // if add to cart remove return back to products the quantity
    public function removeCartItem($cartItemId, $userId) {
        global $pdo;

        $stmt = $pdo->prepare("SELECT product_id, quantity FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cartItemId, $userId]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cartItem) {
            $productId = $cartItem['product_id'];
            $cartQty = $cartItem['quantity'];

            $updateStmt = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
            $updateStmt->execute([$cartQty, $productId]);

            $deleteStmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $deleteStmt->execute([$cartItemId, $userId]);

            return true;
        }

        return false;
    }

    public function clearCart($userId) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }
}
