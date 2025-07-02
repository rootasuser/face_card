<?php 
require_once './../controllers/UserProductsController.php'; 

?>


    <div class="container mt-5">

       <!-- Floating Cart Card -->
    <div class="floating-cart-card">
        <a href="?page=cart" class="btn btn-block" style="background-color: #FFEACA; color: #835151; border-radius: 10px; font-weight: bold; text-decoration: none;">
            <i class="fas fa-cart-plus mr-2"></i> View my shopping cart
        </a>
    </div>

        <!-- Toast Notification for adding add to cart success -->
<div aria-live="polite" aria-atomic="true" style="position: fixed; top: 1rem; right: 1rem; z-index: 2000;">
  <div id="cartToast" class="toast" data-delay="3000" style="min-width: 250px;">
    <div class="toast-header bg-success text-white">
      <strong class="mr-auto"><i class="fas fa-check-circle mr-1"></i>Success</strong>
      <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body">
      Product added to cart successfully!
    </div>
  </div>
</div>


        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="display-4 font-weight-bold text-dark mb-2">Explore Our Products</h1>
                <p class="lead text-muted">Discover a wide range of products from trusted stores.</p>
            </div>
        </div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-lg-6 col-md-8 mx-auto">
                <form method="GET" class="input-group input-group-lg">
                    <input type="hidden" name="page" value="products">
                    <input type="text" name="search" class="form-control border-0" 
                           placeholder="Search for products..." 
                           value="<?= htmlspecialchars($search) ?>" 
                           style="border-radius: 50px 0 0 50px;">
                    <div class="input-group-append">
                        <button class="btn" type="submit" style="border-radius: 0 50px 50px 0; background-color: #F08FC0; color: #fff; border: none;">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="row">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <?php
                        $imagePath = $product['image'];
                        if (!preg_match('/^https?:\/\//', $imagePath)) {
                            $imagePath = '/face_card/Uploads/products/' . basename($imagePath);
                        }
                        $isOutOfStock = $product['quantity'] == 0;
                    ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; background-color: #FFEACA;">
                            <div class="position-relative">
                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.src='';">
                                <span class="badge badge-<?= $isOutOfStock ? 'danger' : 'success' ?> position-absolute" 
                                      style="top: 10px; right: 10px;">
                                    <?= $isOutOfStock ? 'Out of Stock' : 'In Stock' ?>
                                </span>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title font-weight-bold text-dark mb-2">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h5>
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-store mr-1"></i>
                                    <?= htmlspecialchars($product['store_name']) ?>
                                </p>
                                <p class="card-text text-muted small flex-grow-1">
                                    <?= htmlspecialchars(substr($product['description'], 0, 100)) . 
                                        (strlen($product['description']) > 100 ? '...' : '') ?>
                                </p>
                                <h4 class="font-weight-bold mb-3" style="color: #835151;">
                                    ₱<?= number_format($product['price'], 2) ?>

                                    <p class="text-muted small mb-2 mt-2">
                                        <?= number_format($product['quantity']) ?> pieces available
                                    </p>

                               <form method="POST" onsubmit="event.preventDefault(); showConfirmModal(this);">
                                    <input type="hidden" name="user_id" value="<?= $userId ?>">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']) ?>">
                                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($product['image']) ?>">
                                    <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                                    <input type="hidden" name="product_quantity" value="<?= $product['quantity']; ?>">
                                   <button type="submit"
                                    class="btn btn-lg btn-block"
                                    style="
                                        background-color: <?= $isOutOfStock ? '#835151' : '#F08FC0' ?>;
                                        color: #fff;
                                        border: none;
                                    "
                                    <?= $isOutOfStock ? 'disabled title="This product is out of stock."' : '' ?>>
                                    <i class="fas fa-<?= $isOutOfStock ? 'ban' : 'cart-plus' ?> mr-2"></i>
                                    <?= $isOutOfStock ? 'Unavailable' : 'Add to Cart' ?>
                                </button>

                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 15px;">
                        <div class="card-body">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h4 class="card-title font-weight-bold text-dark">No Products Found</h4>
                            <p class="text-muted mb-4">
                                <?php if ($search): ?>
                                    No products match your search: "<strong><?= htmlspecialchars($search) ?></strong>".
                                <?php else: ?>
                                    No products are available at the moment.
                                <?php endif; ?>
                            </p>
                            <?php if ($search): ?>
                                <a href="?page=products" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-times mr-2"></i>Clear Search
                                </a>
                            <?php else: ?>
                                <a href="?page=products" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

                <!-- Pagination -->
            <?php if ($totalPages > 1): ?>

            <?php endif; ?>


        <!-- Pagination -->
        <div class="row mt-4">
            <div class="col-12">
                <nav aria-label="Product pagination">
                    <ul class="pagination pagination-sm justify-content-end">
                        <li class="page-item <?= $productPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link px-2 py-1" 
                            href="?page=products&search=<?= urlencode($search) ?>&product_page=<?= max(1, $productPage - 1) ?>" 
                            tabindex="-1" 
                            style="border-radius: 25px 0 0 25px; font-size: 0.875rem;">
                                <i class="fas fa-chevron-left mr-1"></i>Prev
                            </a>
                        </li>
                        <?php
                        $start = max(1, $productPage - 2);
                        $end = max(1, min($totalPages, $productPage + 2)); // Ensure at least one page is rendered
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <li class="page-item <?= $i == $productPage ? 'active' : '' ?>">
                                <a class="page-link px-3 py-1" 
                                href="?page=products&search=<?= urlencode($search) ?>&product_page=<?= $i ?>" 
                                style="font-size: 0.875rem;">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $productPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link px-2 py-1" 
                            href="?page=products&search=<?= urlencode($search) ?>&product_page=<?= min($totalPages, $productPage + 1) ?>" 
                            style="border-radius: 0 25px 25px 0; font-size: 0.875rem;">
                                Next<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

           <!-- Add to Cart Confirmation Modal -->
<div id="addToCartModal" style="display:none; position: fixed; top: 0; left: 0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 1050;">
    <div style="background: white; width: 90%; max-width: 400px; margin: 10% auto; border-radius: 10px; padding: 20px; position: relative;">
        <h4 class="mb-3">Add to Cart</h4>
        <p>Are you sure you want to add <strong id="confirmProductName"></strong> to your cart?</p>
        <form id="confirmAddToCartForm" method="POST" action="">
            <input type="hidden" name="confirm_add_to_cart" value="1">
            <input type="hidden" name="user_id">
            <input type="hidden" name="product_id">
            <input type="hidden" name="product_name">
            <input type="hidden" name="product_image">
            <input type="hidden" name="product_price">
            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-secondary mr-2" onclick="hideConfirmModal()">Cancel</button>
                <button type="submit" class="btn btn-success">Confirm</button>
            </div>
        </form>
    </div>
</div>
 </div>

                    

 <style>
    .floating-cart-card {
        position: fixed;
        top: 10%; 
        right: 20px;
        width: 300px;
        z-index: 1000;
        border-radius: 10px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
        padding: 8px;
    }

    .floating-cart-card a {
        display: block;
        width: 100%;
        text-align: center;
        padding: 8px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        color: #fff;
        background-color: rgb(255,116,0);
        transition: background-color 0.3s ease;
    }

    .floating-cart-card a:hover {
        background-color: rgb(255,77,0);
    }
</style>


<script>
   
    // Confirm Modal for add to cart 
function showConfirmModal(formElement) {
    const modal = document.getElementById('addToCartModal');
    const confirmForm = document.getElementById('confirmAddToCartForm');


    // get the value of elements in the modal
    confirmForm.user_id.value       = formElement.querySelector('input[name="user_id"]').value;
    confirmForm.product_id.value    = formElement.querySelector('input[name="product_id"]').value;
    confirmForm.product_name.value  = formElement.querySelector('input[name="product_name"]').value;
    confirmForm.product_image.value = formElement.querySelector('input[name="product_image"]').value;
    confirmForm.product_price.value = formElement.querySelector('input[name="product_price"]').value;

    document.getElementById('confirmProductName').innerText = confirmForm.product_name.value;

    modal.style.display = 'block';
}

// Hide add to cart after success
function hideConfirmModal() {
    document.getElementById('addToCartModal').style.display = 'none';
}

// Toast Handler
document.getElementById('confirmAddToCartForm').addEventListener('submit', function (e) {
    e.preventDefault(); // prevent actual form submission if doing Ajax, otherwise remove this line

    this.submit(); // fallback to real form submission if needed

    hideConfirmModal();
});


</script>




