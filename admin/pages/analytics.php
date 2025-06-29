<?php
require_once './../database/connection.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../pages/login.php");
    exit;
}

// Count users by role
$stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
$counts = ['admin' => 0, 'seller' => 0, 'user' => 0];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $counts[$row['role']] = $row['count'];
}
$totalAccounts = array_sum($counts);

// Total sales income
$sales = $pdo->query("SELECT SUM(product_price * quantity) FROM orders_tbl")->fetchColumn();
$sales = $sales ? number_format($sales, 2) : '0.00';

// Pending requests (optional - update table/column if needed)
$pending = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending'")->fetchColumn();
?>


<div class="row">
  <!-- Registered Accounts Card -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-dark shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
              Registered Accounts</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalAccounts ?></div>
            <div class="small text-muted mt-1">
              Admin: <?= $counts['admin'] ?> | Sellers: <?= $counts['seller'] ?> | Users: <?= $counts['user'] ?>
            </div>
          </div>
          <div class="col-auto">
            <i class="fas fa-users fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Sales Report Card -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-dark shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Sales Report</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?= $sales ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Pending Requests Card -->
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card border-left-dark shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
              Pending Requests</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $pending ?></div>
          </div>
          <div class="col-auto">
            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
