<?php

namespace Models;

require_once __DIR__ . '/../database/connection.php';
use PDO;

class Store {

    // Create a new store
    public static function createStore($user_id, $store_name, $store_description, $store_profile) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO stores (user_id, store_name, store_description, store_profile) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $store_name, $store_description, $store_profile]);
    }

    // Get store data by user ID
    public static function getStoreByUserId($user_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM stores WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Optional: Get store by store ID
    public static function getStoreById($store_id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM stores WHERE id = ?");
        $stmt->execute([$store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
