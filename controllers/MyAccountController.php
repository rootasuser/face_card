<?php
require_once './../models/User.php';
// I used this because I already have session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Fetch current user info
$user = User::getUserById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_profile') {
    $user_id = $_POST['user_id'];
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Invalid email format.'];
        header('Location: my_account.php');
        exit;
    }

    $updated = User::editUserSellerInformation($user_id, $email, $password ?: null);

    if ($updated) {
        $_SESSION['email'] = $email;
        $_SESSION['toast'] = ['type' => 'success', 'message' => 'Profile updated successfully.'];
    } else {
        $_SESSION['toast'] = ['type' => 'danger', 'message' => 'Failed to update profile.'];
    }

}
