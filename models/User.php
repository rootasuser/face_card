<?php
require_once __DIR__ . '/../database/connection.php';

class User {
    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|false
     */
    public static function findByEmail($email) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new user.
     * For sellers, status should be 'pending'. For users, status should be 'approved'.
     *
     * @param string $email
     * @param string $password
     * @param string $role
     * @param string $status
     * @return bool
     */
    public static function create($email, $password, $role = 'user', $status = 'approved') {
        global $pdo;
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$email, $hashedPassword, $role, $status]);
    }

    // Display the data of this into admin/pages/accounts.php
    public static function getAllAccounts() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approved or Reject Account
   public static function accountStatusChecking($userId, $status) {
    global $pdo;

    // Only allow 'approved' or 'rejected'
    if (!in_array($status, ['approved', 'rejected'])) {
        return false;
    }

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $userId]);
    }

    // Search Accounts for search input
    public static function searchAccounts($search = '', $limit = 10, $offset = 0) {
        global $pdo;
        $sql = "SELECT * FROM users WHERE email LIKE :search OR role LIKE :search ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Counting # of accounts for pagination
    public static function countAccounts($search = '') {
        global $pdo;
        $sql = "SELECT COUNT(*) FROM users WHERE email LIKE :search OR role LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
    // Update role in admin accounts section
    public static function updateRole($id, $role) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    // Delete account in admin accounts section
    public static function deleteAccount($userId) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }


    // Get User by id for seller/user updating account
    public static function getUserById($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Edit User / Seller Account Info
    public static function editUserSellerInformation($user_id, $email, $password = null) {
    global $pdo;

    try {
        if ($password) {
            // Update email and password
            $stmt = $pdo->prepare("UPDATE users SET email = :email, password = :password WHERE id = :id");
            return $stmt->execute([
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':id' => $user_id
            ]);
        } else {
            // Update only email
            $stmt = $pdo->prepare("UPDATE users SET email = :email WHERE id = :id");
            return $stmt->execute([
                ':email' => $email,
                ':id' => $user_id
            ]);
        }
    } catch (Exception $e) {
        return false;
    }
}




}
