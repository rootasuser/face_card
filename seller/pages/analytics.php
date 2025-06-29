<?php
require_once './../models/Order.php';
require_once './../models/Store.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


use Models\Store;

$monthlyIncome = 0;

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'seller') {
    $storeModel = new Store();
    $orderModel = new Order();
    $store = $storeModel->getStoreByUserId($_SESSION['user_id']);

    if ($store) {
        $storeId = $store['id'];
        $allOrders = $orderModel->getOrdersByStoreId($storeId);

        $currentMonth = date('Y-m');

        foreach ($allOrders as $order) {
            if (strpos($order['created_at'], $currentMonth) === 0) {
                $monthlyIncome += $order['product_price'] * $order['quantity'];
            }
        }
    }
}
?>


<div class="row">

    <!-- Sales Report Card for monthly Income -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Sales Report</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">₱<?= number_format($monthlyIncome, 2) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
