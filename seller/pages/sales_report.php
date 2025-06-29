<?php
require_once './../controllers/AuthController.php';
require_once './../models/Order.php';
require_once './../models/Store.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: ../pages/login.php');
    exit;
}
use Models\Store;

$sellerId = $_SESSION['user_id'];
$orderModel = new Order();
$storeModel = new Store();

$monthlySales = [];

$store = $storeModel->getStoreByUserId($sellerId); 
if ($store) {
    $storeId = $store['id'];
    $monthlySales = $orderModel->getMonthlySalesByStore($storeId);
}

// Calculate totals for summary cards
$totalOrders = array_sum(array_column($monthlySales, 'total_orders'));
$totalRevenue = array_sum(array_column($monthlySales, 'total_sales'));
$averageMonthlyRevenue = !empty($monthlySales) ? $totalRevenue / count($monthlySales) : 0;
?>


    <style>
   
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 15px 15px;
        }
        .stats-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            margin-bottom: 1.5rem;
        }
        .stats-card:hover {
            transform: translateY(-2px);
        }
        .stats-card .card-body {
            padding: 1.5rem;
        }
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        .icon-orders { background: linear-gradient(45deg, #FF6B6B, #FF8E53); }
        .icon-revenue { background: linear-gradient(45deg, #4ECDC4, #44A08D); }
        .icon-average { background: linear-gradient(45deg, #A8E6CF, #7FCDCD); }
        
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            margin: 0;
        }
        .table thead th {
            border: none;
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }
        .table tbody tr {
            transition: background-color 0.2s ease;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        .no-data-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-bottom: 0;
        }
        .currency {
            color: #28a745;
            font-weight: 600;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }
        .breadcrumb-item a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        .breadcrumb-item.active {
            color: white;
        }
    </style>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="?page=analytics"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Sales Reports</li>
                </ol>
            </nav>
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2"><i class="fas fa-chart-line mr-3"></i>Monthly Sales Report</h1>
                    <p class="mb-0 opacity-75">Track your store's performance over time</p>
                </div>
                <div class="col-md-4 text-md-right">
                    <p class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Generated on <?= date('F j, Y') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($monthlySales)): ?>
            <!-- Summary Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon icon-orders mr-3">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <div class="stat-number"><?= number_format($totalOrders) ?></div>
                                    <p class="stat-label text-muted">Total Orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon icon-revenue mr-3">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div>
                                    <div class="stat-number currency">₱<?= number_format($totalRevenue, 2) ?></div>
                                    <p class="stat-label text-muted">Total Revenue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stats-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stats-icon icon-average mr-3">
                                    <i class="fas fa-chart-bar"></i>
                                </div>
                                <div>
                                    <div class="stat-number currency">₱<?= number_format($averageMonthlyRevenue, 2) ?></div>
                                    <p class="stat-label text-muted">Avg Monthly Revenue</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Data Table -->
            <div class="table-container">
                <div class="table-header">
                    <h4 class="mb-0"><i class="fas fa-table mr-2"></i>Monthly Breakdown</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <i class="fas fa-calendar mr-2"></i>Month
                                </th>
                                <th scope="col">
                                    <i class="fas fa-shopping-bag mr-2"></i>Total Orders
                                </th>
                                <th scope="col">
                                    <i class="fas fa-money-bill-wave mr-2"></i>Total Sales
                                </th>
                                <th scope="col">
                                    <i class="fas fa-calculator mr-2"></i>Avg Order Value
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlySales as $row): 
                                $avgOrderValue = $row['total_orders'] > 0 ? $row['total_sales'] / $row['total_orders'] : 0;
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= date('F Y', strtotime($row['month'])) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary badge-pill"><?= number_format($row['total_orders']) ?></span>
                                    </td>
                                    <td>
                                        <span class="currency">₱<?= number_format($row['total_sales'], 2) ?></span>
                                    </td>
                                    <td>
                                        <span class="text-info">₱<?= number_format($avgOrderValue, 2) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <!-- No Data State -->
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card no-data-card text-center">
                        <div class="card-body py-5">
                            <div class="mb-4">
                                <i class="fas fa-chart-line text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Sales Data Available</h4>
                            <p class="text-muted mb-4">You haven't made any sales yet. Once you start receiving orders, your sales data will appear here.</p>
                           
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

  