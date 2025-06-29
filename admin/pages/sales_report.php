<?php
require_once './../database/connection.php';

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../pages/login.php");
    exit;
}

function getIncome($pdo, $interval) {
    switch ($interval) {
        case 'daily':
            $sql = "SELECT SUM(product_price * quantity) AS income FROM orders_tbl WHERE DATE(created_at) = CURDATE()";
            break;
        case 'weekly':
            $sql = "SELECT SUM(product_price * quantity) AS income FROM orders_tbl WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'monthly':
            $sql = "SELECT SUM(product_price * quantity) AS income FROM orders_tbl WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
            break;
        case 'yearly':
            $sql = "SELECT SUM(product_price * quantity) AS income FROM orders_tbl WHERE YEAR(created_at) = YEAR(CURDATE())";
            break;
        default:
            return 0;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchColumn() ?? 0;
}


?>

<!-- HTML DISPLAY -->
<div class="row text-white mb-4">
    <div class="col-md-3">
        <div class="card bg-primary p-3">
            <h5>Today</h5>
            <p>₱<?= number_format(getIncome($pdo, 'daily'), 2) ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success p-3">
            <h5>This Week</h5>
            <p>₱<?= number_format(getIncome($pdo, 'weekly'), 2) ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info p-3">
            <h5>This Month</h5>
            <p>₱<?= number_format(getIncome($pdo, 'monthly'), 2) ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning p-3">
            <h5>This Year</h5>
            <p>₱<?= number_format(getIncome($pdo, 'yearly'), 2) ?></p>
        </div>
    </div>
</div>

<div class="d-flex justify-content-end">
    <button onclick="exportTableToCSV()" class="btn btn-outline-primary mb-3">Export to CSV</button>
</div>




<div class="table-responsive">
    <table class="table table-bordered table-striped" style="color: #000;">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM orders_tbl ORDER BY created_at DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                $total = $row['quantity'] * $row['product_price'];
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>₱<?= number_format($row['product_price'], 2) ?></td>
                <td>₱<?= number_format($total, 2) ?></td>
                <td><?= $row['payment_method'] ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>


<script>
function exportTableToCSV() {
    const rows = document.querySelectorAll("table tr");
    let csv = [];

    rows.forEach(row => {
        const cols = row.querySelectorAll("td, th");
        let rowData = [];

        cols.forEach(col => {
            // Escape double quotes and commas
            let data = col.innerText.replace(/"/g, '""');
            rowData.push(`"${data}"`);
        });

        csv.push(rowData.join(","));
    });

    // Download the CSV file
    const csvBlob = new Blob([csv.join("\n")], { type: "text/csv" });
    const url = URL.createObjectURL(csvBlob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "sales_report.csv";
    a.click();
    URL.revokeObjectURL(url);
}
</script>
