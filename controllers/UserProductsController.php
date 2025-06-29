<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';

use Models\Product;
use Models\Cart;

// GET parameters
$search = $_GET['search'] ?? '';
$productPage = isset($_GET['product_page']) ? max(1, intval($_GET['product_page'])) : 1;

$limit = 4;
$offset = ($productPage - 1) * $limit;

// Fetch products and pagination info
$products = Product::getAllPublicProducts($search, $limit, $offset);
$totalProducts = Product::countAllPublicProducts($search);
$totalPages = max(1, ceil($totalProducts / $limit));

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_add_to_cart'])) {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
    $image = $_POST['product_image'] ?? '';
    $name = $_POST['product_name'] ?? '';
    $price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0.0;

    $userId = $_SESSION['user_id'] ?? null;

    if ($userId && $productId) {
        $product = Product::getProductById($productId);
        if ($product && $product['quantity'] >= 1) {
            $cart = new Cart(); 
            $cart->addToCart($userId, $productId, $name, $image, $price); 
        }
    }
}
