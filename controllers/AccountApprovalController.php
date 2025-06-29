<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';

// Only process POST if admin and required fields exist
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_SESSION['role']) && $_SESSION['role'] === 'admin' &&
    isset($_POST['action'], $_POST['user_id'])
) {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    switch ($action) {
        case 'approve':
        case 'reject':
            $newStatus = $action === 'approve' ? 'approved' : 'rejected';
            $success = User::accountStatusChecking($userId, $newStatus);

            $_SESSION['toast'] = [
                'type' => $success ? 'success' : 'danger',
                'message' => $success ? "User account has been {$newStatus}." : "Failed to update user status."
            ];
            break;

        case 'edit':
            if (isset($_POST['role'])) {
                $role = $_POST['role'];
                $success = User::updateRole($userId, $role);

                $_SESSION['toast'] = [
                    'type' => $success ? 'success' : 'danger',
                    'message' => $success ? 'Account updated successfully.' : 'Failed to update account.'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => 'Missing role for editing.'
                ];
            }
            break;

        case 'delete':
            $success = User::deleteAccount($userId);

            $_SESSION['toast'] = [
                'type' => $success ? 'success' : 'danger',
                'message' => $success ? 'Account deleted successfully.' : 'Failed to delete account.'
            ];
            break;

        default:
            $_SESSION['toast'] = [
                'type' => 'warning',
                'message' => 'Invalid action.'
            ];
            break;
    }

 
}
?>
