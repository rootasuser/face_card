<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Face Card</title>
    <?php include('assets/dependencies/bootstrap.php'); echo $bootstrap4; ?>
    <?php include('assets/dependencies/fontawesome.php'); echo $fontawesome; ?>
    <link rel="stylesheet" href="assets/css/index.css" />
    <link rel="stylesheet" href="assets/css/login_card.css" />
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <a class="navbar-brand" href="?page=home">
        <img src="assets/images/face_card_logo.png" alt="logo" width="30" height="30" class="d-inline-block align-top rounded-circle">
    </a>
    <a href="?page=login" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
    </a>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto align-items-center">
            <li class="nav-item">
                <a class="nav-link" href="?page=login">Color Analysis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=login">Products</a>
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
            <li class="nav-item">
                <a class="nav-link" href="?page=login"><i class="fas fa-shopping-basket"></i></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?page=login"><i class="fas fa-user"></i></a>
            </li>
        </ul>
    </div>
</nav>

<!-- Content:: Loaded dynamically based on the page parameter -->
<div class="content-container">
    <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';

        $allowed_pages = ['home', 'login', 'color_analysis', 'products', 'about'];

        if (in_array($page, $allowed_pages)) {
            include("pages/{$page}.php");
        } else {
            include("error/404.php");
        }
    ?>

</div>



<?php include('assets/dependencies/jquery.php'); echo $jquery; ?>
<?php include('assets/dependencies/bootstrap.php'); echo $bootstrap4js; ?>

</body>
</html>
