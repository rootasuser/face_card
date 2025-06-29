<?php
require_once './../controllers/AuthController.php';
require_once './../models/Order.php';
require_once './../models/Store.php';
require_once './../models/Product.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: ../pages/login.php');
    exit;
}

use Models\Store;

// Get seller user id from session
$sellerId = $_SESSION['user_id'] ?? null;

$orderModel = new Order();
$orders = [];
$storeId = null;

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['order_page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;

if ($sellerId) {

    $store = (new Store())->getStoreByUserId($sellerId);

    if ($store) {

        $storeId = $store['id'];
        $allOrders = $orderModel->getOrdersByStoreId($storeId);

        $today = date('Y-m-d');
        $groupedOrders = [];

       foreach ($allOrders as $order) {


        $userId = $order['user_id'];
        $match = true;

    if (!empty($search)) {

        $term = strtolower($search);
        $match = strpos(strtolower($order['product_name']), $term) !== false ||
                 strpos(strtolower($order['fullname']), $term) !== false ||
                 strpos(strtolower($order['payment_method']), $term) !== false;
    }

    if ($match) {

        if (!isset($groupedOrders[$userId])) {
            $groupedOrders[$userId] = [
                'user_id'        => $userId,
                'fullname'       => $order['fullname'],
                'contact'        => $order['contact'],
                'address'        => $order['address'],
                'orders'         => [],
                'total_quantity' => 0,
                'total_price'    => 0,
                'latest_date'    => $order['created_at'],
                'payment_method' => $order['payment_method'],
            ];
        }

        $groupedOrders[$userId]['orders'][] = $order;
        $groupedOrders[$userId]['total_quantity'] += $order['quantity'];
        $groupedOrders[$userId]['total_price'] += $order['product_price'] * $order['quantity'];

        if (strtotime($order['created_at']) > strtotime($groupedOrders[$userId]['latest_date'])) {
            $groupedOrders[$userId]['latest_date'] = $order['created_at'];
        }
    }
}

        $groupedOrders = array_values($groupedOrders);
        $totalOrders = count($groupedOrders);
        $orders = array_slice($groupedOrders, $offset, $perPage);
        $totalPages = ceil($totalOrders / $perPage);
    }
}
?>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .invoice-modal, .invoice-modal * {
        visibility: visible;
    }
    .invoice-modal {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
    .modal-header, .modal-footer {
        display: none !important;
    }
    .modal-content {
        border: none;
        box-shadow: none;
        padding: 20px;
    }
}
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="bg-primary text-white py-4 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-1">
                        <i class="fas fa-shopping-cart mr-2"></i>Today's Orders
                    </h1>
                    <p class="mb-0 text-light">Manage and track your daily customer orders</p>
                </div>
                <div class="col-md-4 text-md-right">
                    <div class="bg-white text-primary d-inline-block px-4 py-2 rounded">
                        <h3 class="h4 mb-0"><?= count($orders) ?></h3>
                        <small class="text-muted">Orders Today</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Search Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form class="row" method="get">
                    <input type="hidden" name="page" value="orders">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label for="search" class="form-label font-weight-bold">Search Orders</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <input type="text" id="search" name="search" class="form-control" 
                                   placeholder="Search by product, customer, or payment method..."
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-2"></i>Search Orders
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($orders)): ?>
            <!-- Orders Table -->
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list mr-2"></i>Order List
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Products</th>
                                <th class="text-center">Quantity</th>
                                <th>Total Price</th>
                                <th>Payment</th>
                                <th>Customer Info</th>
                                <th>Order Date</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $group): ?>
                                <tr>
                                    <td>
                                        <?php foreach ($group['orders'] as $o): ?>
                                            <div class="d-flex align-items-center mb-2 p-2 bg-light rounded">
                                                <img src="/face_card/Uploads/products/<?= htmlspecialchars(basename($o['product_image'])) ?>"
                                                     alt="<?= htmlspecialchars($o['product_name']) ?>" 
                                                     class="rounded mr-2"
                                                     style="width: 40px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='https://via.placeholder.com/40x40/e9ecef/6c757d?text=No+Image';">
                                                <div class="flex-grow-1">
                                                    <div class="font-weight-bold small"><?= htmlspecialchars($o['product_name']) ?></div>
                                                    <small class="text-muted">Qty: <?= $o['quantity'] ?></small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary badge-pill px-3 py-2">
                                            <?= $group['total_quantity'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-success font-weight-bold h5">₱<?= number_format($group['total_price'], 2) ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $paymentClass = 'badge-success';
                                        if (strtolower($group['payment_method']) === 'card') $paymentClass = 'badge-primary';
                                        elseif (strpos(strtolower($group['payment_method']), 'online') !== false) $paymentClass = 'badge-warning';
                                        ?>
                                        <span class="badge <?= $paymentClass ?> px-3 py-2">
                                            <?= htmlspecialchars($group['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold"><?= htmlspecialchars($group['fullname']) ?></div>
                                        <small class="text-muted d-block"><?= htmlspecialchars($group['contact']) ?></small>
                                        <small class="text-muted"><?= htmlspecialchars($group['address']) ?></small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold"><?= date('M j, Y', strtotime($group['latest_date'])) ?></div>
                                        <small class="text-muted"><?= date('g:i A', strtotime($group['latest_date'])) ?></small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-dark btn-sm generate-invoice" title="Generate Invoice">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=orders&search=<?= urlencode($search) ?>&order_page=<?= max(1, $page - 1) ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=orders&search=<?= urlencode($search) ?>&order_page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=orders&search=<?= urlencode($search) ?>&order_page=<?= min($totalPages, $page + 1) ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <!-- Empty State -->
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-shopping-cart text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h4 class="mt-3 text-muted">No Orders Found</h4>
                    <p class="text-muted mb-3">
                        <?php if ($search): ?>
                            No orders found matching "<?= htmlspecialchars($search) ?>" for today.
                        <?php else: ?>
                            No orders have been placed today yet.
                        <?php endif; ?>
                    </p>
                    <?php if ($search): ?>
                        <a href="?page=orders" class="btn btn-primary">
                            <i class="fas fa-list mr-2"></i>View All Orders
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade invoice-modal" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-file-invoice mr-2"></i>Transaction Invoice
                </h5>
                <button type="button" class="close text-white" onclick="closeInvoiceModal()">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="invoiceContent"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeInvoiceModal()">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // For Print Button
document.querySelectorAll(".generate-invoice").forEach((btn) => {
    btn.addEventListener("click", function () {
        const row = this.closest("tr");
        const productItems = [...row.querySelectorAll(".d-flex.align-items-center")];
        const products = productItems.map(item => {
            const name = item.querySelector('.font-weight-bold').innerText.trim();
            const qty = item.querySelector('small').innerText.trim();
            return `${name} - ${qty}`;
        });
        
        const quantity = row.children[1].querySelector('.badge').innerText.trim();
        const total = row.children[2].querySelector('.text-success').innerText.trim();
        const payment = row.children[3].querySelector('.badge').innerText.trim();
        const customerInfo = row.children[4];
        const name = customerInfo.querySelector('.font-weight-bold').innerText.trim();
        const contact = customerInfo.querySelectorAll('small')[0].innerText.trim();
        const address = customerInfo.querySelectorAll('small')[1].innerText.trim();
        const dateInfo = row.children[5];
        const date = dateInfo.querySelector('.font-weight-bold').innerText.trim();
        const time = dateInfo.querySelector('small').innerText.trim();

        // Displaying Invoice to modal
        const invoiceHTML = `
            <div class="text-center mb-4 pb-3 border-bottom">
                <h2 class="text-primary mb-2">
                    <i class="fas fa-store mr-2"></i>Face Card
                </h2>
                <h4 class="text-muted">Transaction Receipt</h4>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user mr-2 text-primary"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Name:</strong> ${name}</p>
                            <p class="mb-2"><strong>Contact:</strong> ${contact}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Address:</strong> ${address}</p>
                            <p class="mb-2"><strong>Payment Method:</strong> ${payment}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag mr-2 text-primary"></i>Order Details</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${products.map(p => `<tr><td>${p.split(' - ')[0]}</td><td class="text-center">${p.split(' - ')[1].replace('Qty: ', '')}</td></tr>`).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-0">Total Quantity: ${quantity}</h5>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <h4 class="mb-0">Total Amount: ${total}</h4>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center pt-3 border-top">
                <p class="text-muted mb-1">Transaction Date: ${date} at ${time}</p>
                <p class="text-muted mb-0"><strong>Thank you for your business!</strong></p>
            </div>
        `;

        document.getElementById("invoiceContent").innerHTML = invoiceHTML;
        $('#invoiceModal').modal('show');
    });
});

function closeInvoiceModal() {
    $('#invoiceModal').modal('hide');
}

// Loading animation for search
document.querySelector('form').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 1000);
});
</script>