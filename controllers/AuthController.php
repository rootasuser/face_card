<?php
require_once __DIR__ . '/../models/User.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Toast structure for feedback messages
$toast = ['type' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizing inputs
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $action   = isset($_POST['login']) ? 'login' : (isset($_POST['register']) ? 'register' : '');

    // ----------------------------
    // Handle Login
    // ----------------------------
    if ($action === 'login') {
        $user = User::findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // If seller, status must be approved
            if ($user['role'] === 'seller' && $user['status'] !== 'approved') {
                $toast = [
                    'type'    => 'danger',
                    'message' => ($user['status'] === 'pending')
                        ? 'Seller account pending approval.'
                        : 'Seller account was rejected.'
                ];
            } else {
                // Set session and redirect based on role
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];
                $_SESSION['email']    = $user['email'];
                
               $redirect = match ($user['role']) {
                    'admin'  => 'admin/dashboard.php',
                    'seller' => 'seller/dashboard.php',
                    'user'   => 'user/dashboard.php',
                    default  => 'pages/login.php',
                };
                header("Location: $redirect");
                exit;

            }
        } else {
            $toast = ['type' => 'danger', 'message' => 'Invalid email or password.'];
        }
    }

    // ----------------------------
    // Handle Register
    // ----------------------------
    if ($action === 'register') {
        $role   = $_POST['role'] ?? 'user';
        $status = ($role === 'seller') ? 'pending' : 'approved';

        if (User::findByEmail($email)) {
            $toast = ['type' => 'danger', 'message' => 'Email is already registered.'];
        } else {
            if (User::create($email, $password, $role, $status)) {
                $toast = [
                    'type'    => 'success',
                    'message' => ($role === 'seller')
                        ? 'Seller registration submitted. Awaiting approval.'
                        : 'Account created successfully. You can now log in.'
                ];
            } else {
                $toast = ['type' => 'danger', 'message' => 'Failed to register. Try Again.'];
            }
        }
    }
}
