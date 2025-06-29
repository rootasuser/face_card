<?php
require_once './../models/Store.php';
require_once './../models/Product.php';

use Models\Store;
use Models\Product;

// Start output buffering to prevent stray output
ob_start();

// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to handle file uploads
function uploadFile($fileInputName, $uploadDir) {

    if (!empty($_FILES[$fileInputName]['name'])) {
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = time() . '_' . basename($_FILES[$fileInputName]['name']);
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetPath)) {
            $dirName = ($fileInputName === 'store_profile') ? 'stores' : 'products';
            return 'Uploads/' . $dirName . '/' . $filename;
        } else {
            $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Failed to upload ' . $fileInputName . ' image.'];
            return null;
        }
    }
    return null;
}

// Handle getting Product Data
if (isset($_GET['action']) && $_GET['action'] === 'get_product') {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }

    header('Content-Type: application/json');

    $productId = intval($_GET['product_id']);
    $product = Product::getProduct($productId);

    if ($product) {
        echo json_encode([
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'description' => $product['description'],
            'quantity' => $product['quantity'],
            'image' => $product['image']
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
    }
    exit;
}

// Handle Store Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_store') {

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'User not logged in'];
        header('Location: ../login.php');
        exit;
    }

    $store_name = trim($_POST['store_name']);
    $store_description = trim($_POST['store_description']);
    $profile_image = uploadFile('store_profile', './../Uploads/stores/');

    if (Store::createStore($user_id, $store_name, $store_description, $profile_image)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Store created successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Store creation failed.'];
    }
   
}

// Handle Product Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'User not logged in'];
        header('Location: ../login.php');
        exit;
    }

    $store = Store::getStoreByUserId($user_id);

    if ($store) {
        $imagePath = uploadFile('image', './../Uploads/products/');
        $success = Product::addProduct(
            $store['id'],
            trim($_POST['name']),
            floatval($_POST['price']),
            trim($_POST['description']),
            intval($_POST['quantity']),
            $imagePath
        );

        $_SESSION['toast'] = [
            'type' => $success ? 'success' : 'danger',
            'message' => $success ? 'Product added successfully!' : 'Failed to add product.'
        ];
    } else {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Store not found.'];
    }
 
}

// Handle Product Editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product') {

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'User not logged in'];
        header('Location: ../login.php');
        exit;
    }

    $product_id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $imagePath = uploadFile('image', './../Uploads/products/');

    // If new image uploaded, use it; otherwise keep current image
    $currentProduct = Product::getProduct($product_id);
    if (!$currentProduct) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Product not found.'];
        header('Location: ../dashboard.php?page=store');
        exit;
    }

    if ($imagePath === null) {
        $imagePath = $currentProduct['image']; // keep existing image if none uploaded
    }

    $success = Product::editProduct(
        $product_id,
        $name,
        $price,
        $description,
        $quantity,
        $imagePath
    );

    $_SESSION['toast'] = [
        'type' => $success ? 'success' : 'danger',
        'message' => $success ? 'Product updated successfully!' : 'Failed to update product.'
    ];
    
   
}


// Handle Product Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'User not logged in'];
        header('Location: ../login.php');
        exit;
    }

    $product_id = intval($_POST['product_id']);

    if (Product::deleteProduct($product_id)) {
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Product deleted successfully!'];
    } else {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Failed to delete product.'];
    }
 
}

// Load Store and Products
$store = Store::getStoreByUserId($_SESSION['user_id'] ?? null);
$products = $store ? Product::getProductsByStore($store['id']) : [];

// Pagination and Search
$search = $_GET['search'] ?? '';
$productPage = isset($_GET['product_page']) ? max(1, intval($_GET['product_page'])) : 1;
$perPage = 8;

$filtered = array_filter($products, function ($p) use ($search) {
    return stripos($p['name'], $search) !== false;
});

$total = count($filtered);
$pages = ceil($total / $perPage);
$offset = ($productPage - 1) * $perPage;
$displayedProducts = array_slice($filtered, $offset, $perPage);

// End output buffering
ob_end_flush();
?>