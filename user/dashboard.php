<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: ../pages/login.php');
    exit;
}
$email = $_SESSION['email']; 

// Get added to cart counts
require_once './../controllers/CartController.php';

$userId = $_SESSION['user_id'] ?? null;
$cartCount = 0;

if ($userId) {
    $cartController = new CartController();
    $cartCount = $cartController->getCartCountByUser($userId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Face Card</title>
    <?php include('../assets/dependencies/bootstrap.php'); echo $bootstrap4; ?>
    <?php include('../assets/dependencies/fontawesome.php'); echo $fontawesome; ?>
    <link rel="stylesheet" href="../assets/css/index.css" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <a class="navbar-brand" href="?page=home">
        <img src="../assets/images/face_card_logo.png" alt="logo" width="30" height="30" class="d-inline-block align-top rounded-circle">
    </a>
    <a href="?page=login" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
    </a>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="?page=color_analysis">Color Analysis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=products">Products</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=about">About</a>
            </li>
            <li class="nav-item">
                <form class="search-form mx-2">
                    <input type="text" class="form-control" placeholder="Search..." aria-label="Search">
                    <i class="fas fa-search search-icon"></i>
                </form>
            </li>


          <!-- Show number of added to cart at the top of the basket icon -->
                <li class="nav-item">
                <a class="nav-link position-relative" href="?page=cart">
                    <i class="fas fa-shopping-basket"></i>

                    <?php if ($cartCount > 0): ?>
                    <span class="badge badge-danger position-absolute"
                            style="top: 0; right: 0; font-size: 0.6rem; border-radius: 50%; padding: 4px 6px; pointer-events: none;">
                        <?= $cartCount ?>
                    </span>
                    <?php endif; ?>
                </a>
                </li>

                



           <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle no-caret" href="#" id="userDropdown" role="button" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                    </a>

                    <style>
                        /**
                            To remove caret down default icon for collapsed dropdown
                        */
                        .no-caret::after {
                            display: none !important;
                        }
                    </style>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="?page=my_account">My Account</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
                </div>
            </li>

        </ul>
    </div>
</nav>

<!-- Content:: Loaded dynamically based on the page parameter -->
<div class="content-container">
    <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';

        $allowed_pages = ['home', 'cart', 'color_analysis', 'products', 'about', 'my_account'];

        if (in_array($page, $allowed_pages)) {
            include("pages/{$page}.php");
        } else {
            include("error/404.php");
        }
    ?>

</div>


 <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../controllers/LogoutController.php">Logout</a>
                </div>
            </div>
        </div>
    </div>




<?php include('../assets/dependencies/jquery.php'); echo $jquery; ?>
<?php include('../assets/dependencies/bootstrap.php'); echo $bootstrap4js; ?>


</body>
</html>
