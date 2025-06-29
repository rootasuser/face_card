<?php

namespace Models;

require_once __DIR__ . '/../database/connection.php';
use PDO;

class Product {
    // Add Product 
    public static function addProduct($store_id, $name, $price, $description, $quantity, $imagePath) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO products (store_id, name, price, description, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$store_id, $name, $price, $description, $quantity, $imagePath]);
    }

    // Ge that product by store
    public static function getProductsByStore($store_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM products WHERE store_id = ?");
        $stmt->execute([$store_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all product of that store
    public static function getAllProducts() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT p.*, s.store_name, s.store_profile 
            FROM products p 
            JOIN stores s ON p.store_id = s.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Edit Product Uploaded/Added
     public static function editProduct($product_id, $name, $price, $description, $quantity, $imagePath) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, description = ?, quantity = ?, image = ? WHERE id = ?");
        return $stmt->execute([$name, $price, $description, $quantity, $imagePath, $product_id]);
    }

    // Delete Product
    public static function deleteProduct($product_id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$product_id]);
    }
    // Get Product for some purpose
    public static function getProduct($product_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

  // Get all products with their store names
public static function getAllPublicProducts($search = '', $limit = 10, $offset = 0)
{
    global $pdo;

    $sql = "SELECT 
                products.*, 
                stores.store_name 
            FROM products
            JOIN stores ON products.store_id = stores.id
            WHERE products.name LIKE :search OR products.description LIKE :search
            ORDER BY products.created_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Count all matching products
public static function countAllPublicProducts($search = '')
{
    global $pdo;

    $sql = "SELECT COUNT(*) 
            FROM products
            WHERE name LIKE :search OR description LIKE :search";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->execute();

    return $stmt->fetchColumn();
}

// Get product by id for add to cart
public static function getProductById($productId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $productId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


}
