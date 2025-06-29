<?php
require_once './../controllers/StoreController.php';
?>

<!--- Toast Alert Notification if success or failed and auto hide after 3sec -->
<?php if (isset($_SESSION['toast'])): ?>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1055;">
        <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
            <div class="toast-header bg-<?= $_SESSION['toast']['type'] === 'success' ? 'success' : 'danger' ?> text-white">
                <strong class="mr-auto"><?= ucfirst($_SESSION['toast']['type']) ?></strong>
                <button type="button" class="ml-auto mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <?= htmlspecialchars($_SESSION['toast']['message']) ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('.toast').toast('show');
        });
    </script>
    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>


<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: 100vh;">
    <div class="container" style="max-height: 800px; overflow-x: scroll; overflow-y: scroll;">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-4 font-weight-bold text-dark mb-1">My Store</h1>
                        <p class="text-muted lead">Manage your products and store settings</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-store-alt fa-3x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!$store): ?>
            <!-- No Store State -->
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-lg border-0" style="border-radius: 15px;">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-store fa-4x text-primary mb-3"></i>
                                <h3 class="font-weight-bold text-dark">Welcome to Your Store</h3>
                                <p class="text-muted">Create your store to start selling amazing products to customers worldwide.</p>
                            </div>
                            <button class="btn btn-primary btn-lg px-5 py-3" data-toggle="modal" data-target="#createStoreModal" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(0,123,255,0.3);">
                                <i class="fas fa-plus-circle mr-2"></i>Create Your Store
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Store Information Card -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                        <div class="row no-gutters">
                            <?php if (!empty($store['store_profile'])): ?>
                                <div class="col-md-4">
                                    <div class="position-relative h-100">
                                        <img src="../<?= htmlspecialchars($store['store_profile']) ?>" 
                                             class="img-fluid h-100 w-100" 
                                             style="object-fit: cover; min-height: 200px;" 
                                             alt="Store Profile">
                                        <div class="position-absolute" style="top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(45deg, rgba(0,123,255,0.1), rgba(0,123,255,0.0));"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="<?= !empty($store['store_profile']) ? 'col-md-8' : 'col-12' ?>">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h2 class="card-title font-weight-bold text-dark mb-2">
                                                <i class="fas fa-store primary mr-2"></i>
                                                <?= htmlspecialchars($store['store_name']) ?>
                                            </h2>
                                            <div class="d-flex align-items-center mb-3">
                                                <span class="badge badge-success mr-2">
                                                    <i class="fas fa-check-circle mr-1"></i>Active
                                                </span>
                                                <span class="text-muted">
                                                    <i class="fas fa-box mr-1"></i><?= count($products) ?> Products
                                                </span>
                                            </div>
                                        </div>
                                        <button class="btn btn-success btn-lg" data-toggle="modal" data-target="#addProductModal" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(40,167,69,0.3);">
                                            <i class="fas fa-plus mr-2"></i>Add Product
                                        </button>
                                    </div>
                                    <p class="card-text text-muted lead"><?= nl2br(htmlspecialchars($store['store_description'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius: 15px;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <form method="GET" class="d-flex">
                                        <input type="hidden" name="page" value="store">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-primary text-white border-primary">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                            <input type="text" name="search" class="form-control form-control-lg" 
                                                   placeholder="Search your products..." 
                                                   value="<?= htmlspecialchars($search) ?>"
                                                   style="border-left: none;">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-primary btn-lg">Search</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                    <div class="d-flex justify-content-md-end justify-content-start align-items-center">
                                        <span class="text-muted mr-3">
                                            <i class="fas fa-list mr-1"></i>
                                            Showing <?= count($displayedProducts) ?> of <?= $total ?> products
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="font-weight-bold text-dark mb-0">
                            <i class="fas fa-boxes text-primary mr-2"></i>Product Catalog
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <?php if (count($displayedProducts) > 0): ?>
                <div class="row mb-4">
                    <?php foreach ($displayedProducts as $p): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm border-0 product-card" style="border-radius: 15px; transition: all 0.3s; margin-bottom: 20px;">
                                <div class="position-relative">
                                    <?php if (!empty($p['image'])): ?>
                                        <!---- Product Image --->
                                        <img src="../<?= htmlspecialchars($p['image']) ?>" 
                                             class="card-img-top" 
                                             style="height: 200px; object-fit: cover; border-radius: 15px 15px 0 0;"
                                             alt="<?= htmlspecialchars($p['name']) ?>">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px; border-radius: 15px 15px 0 0;">
                                            <i class="fas fa-image fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="position-absolute" style="top: 10px; right: 10px;">
                                        <!--- If quantity == 0 then display Out of Stock and if quantity greater than 0 then 
                                        display In Stock -->
                                        <span class="badge badge-<?= $p['quantity'] > 0 ? 'success' : 'danger' ?> badge-pill">
                                            <?= $p['quantity'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title font-weight-bold text-dark mb-2"><?= htmlspecialchars($p['name']) ?></h5>
                                    <p class="card-text text-muted small flex-grow-1"><?= nl2br(htmlspecialchars(substr($p['description'], 0, 100) . (strlen($p['description']) > 100 ? '...' : ''))) ?></p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h4 class="text-primary font-weight-bold mb-0">₱<?= number_format($p['price'], 2) ?></h4>
                                                <small class="text-muted">
                                                    <i class="fas fa-cubes mr-1"></i>Qty: <?= (int)$p['quantity'] ?>
                                                </small>
                                           </div>
                                        </div>
                                    </div>
                                     <div class="btn-group mb-5" role="group" style="gap: 4px;">
                                                <!-- Edit Button Product -->
                                                <button class="btn btn-warning px-2 py-1" 
                                                        style="font-size: 12px;"
                                                        data-toggle="modal"
                                                        data-target="#editProductModal"
                                                        data-product-id="<?= $p['id'] ?>">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>

                                                <!-- Delete Button Product -->
                                                <button class="btn btn-outline-danger px-2 py-1" 
                                                        style="font-size: 12px;"
                                                        data-toggle="modal" 
                                                        data-target="#deleteProductModal" 
                                                        data-product-id="<?= $p['id'] ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Products State -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0" style="border-radius: 15px;">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                <h4 class="font-weight-bold text-dark">No Products Found</h4>
                                <?php if ($search): ?>
                                    <p class="text-muted">No products match your search criteria: "<strong><?= htmlspecialchars($search) ?></strong>"</p>
                                    <a href="?page=store" class="btn btn-outline-primary">
                                        <i class="fas fa-times mr-2"></i>Clear Search
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">Start adding products to your store to see them here.</p>
                                    <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#addProductModal">
                                        <i class="fas fa-plus mr-2"></i>Add Your First Product
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($pages > 1): ?>
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Product pagination">
                            <ul class="pagination pagination-lg justify-content-center">
                                <!-- Previous Button -->
                                <li class="page-item <?= $productPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=store&search=<?= urlencode($search) ?>&product_page=<?= max(1, $productPage - 1) ?>" style="border-radius: 50px 0 0 50px;">
                                        <i class="fas fa-chevron-left mr-1"></i>Previous
                                    </a>
                                </li>
                                <!-- Page Number Links -->
                                <?php 
                                $start = max(1, $productPage - 2);
                                $end = min($pages, $productPage + 2);
                                for ($i = $start; $i <= $end; $i++): 
                                ?>
                                    <li class="page-item <?= $i == $productPage ? 'active' : '' ?>">
                                        <a class="page-link" href="?page=store&search=<?= urlencode($search) ?>&product_page=<?= $i ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                <!-- Next Button -->
                                <li class="page-item <?= $productPage >= $pages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?page=store&search=<?= urlencode($search) ?>&product_page=<?= min($pages, $productPage + 1) ?>" style="border-radius: 0 50px 50px 0;">
                                        Next<i class="fas fa-chevron-right ml-1"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Create Store Modal -->
<div class="modal fade" id="createStoreModal" tabindex="-1" role="dialog" aria-labelledby="createStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 15px; border: none;">
            <input type="hidden" name="action" value="create_store">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="createStoreModalLabel">
                    <i class="fas fa-store mr-2"></i>Create Your Store
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-tag mr-2 text-primary"></i>Store Name
                    </label>
                    <input type="text" name="store_name" class="form-control form-control-lg" 
                           placeholder="Enter your store name" required style="border-radius: 10px;">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-image mr-2 text-primary"></i>Store Profile Image
                    </label>
                    <div class="custom-file">
                        <input type="file" name="store_profile" class="custom-file-input" id="storeProfile" accept="image/*">
                        <label class="custom-file-label" for="storeProfile">Choose store image</label>
                    </div>
                    <small class="form-text text-muted">Upload a high-quality image to represent your store</small>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-align-left mr-2 text-primary"></i>Store Description
                    </label>
                    <textarea name="store_description" class="form-control" rows="4" 
                              placeholder="Tell customers about your store..." style="border-radius: 10px;"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" style="border-radius: 50px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-lg px-4" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(0,123,255,0.3);">
                    <i class="fas fa-plus-circle mr-2"></i>Create Store
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 15px; border: none;">
            <input type="hidden" name="action" value="add_product">
            <div class="modal-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="addProductModalLabel">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Product
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-box mr-2 text-success"></i>Product Name
                            </label>
                            <input type="text" name="name" required placeholder="Enter product name" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-dollar-sign mr-2 text-success"></i>Price (₱)
                            </label>
                            <input type="number" name="price" step="0.01" required placeholder="0.00" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-align-left mr-2 text-success"></i>Description
                    </label>
                    <textarea name="description" placeholder="Describe your product..." 
                              class="form-control" rows="3" style="border-radius: 10px;"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-cubes mr-2 text-success"></i>Quantity
                            </label>
                            <input type="number" name="quantity" required placeholder="0" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-camera mr-2 text-success"></i>Product Image
                            </label>
                            <div class="custom-file">
                                <input type="file" name="image" accept="image/*" class="custom-file-input" id="productImage">
                                <label class="custom-file-label" for="productImage">Choose image</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" style="border-radius: 50px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-success btn-lg px-4" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(40,167,69,0.3);">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 15px; border: none;">
            <input type="hidden" name="action" value="edit_product">
            <input type="hidden" name="product_id" id="editProductId">
            <div class="modal-header bg-warning text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="editProductModalLabel">
                    <i class="fas fa-edit mr-2"></i>Edit Product
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-box mr-2 text-warning"></i>Product Name
                            </label>
                            <input type="text" name="name" id="editProductName" required placeholder="Enter product name" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-dollar-sign mr-2 text-warning"></i>Price (₱)
                            </label>
                            <input type="number" name="price" id="editProductPrice" step="0.01" required placeholder="0.00" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-align-left mr-2 text-warning"></i>Description
                    </label>
                    <textarea name="description" id="editProductDescription" placeholder="Describe your product..." 
                              class="form-control" rows="3" style="border-radius: 10px;"></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-cubes mr-2 text-warning"></i>Quantity
                            </label>
                            <input type="number" name="quantity" id="editProductQuantity" required placeholder="0" 
                                   class="form-control form-control-lg" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">
                                <i class="fas fa-camera mr-2 text-warning"></i>Product Image
                            </label>
                            <div class="custom-file">
                                <input type="file" name="image" accept="image/*" class="custom-file-input" id="editProductImage">
                                <label class="custom-file-label" for="editProductImage">Choose image</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" style="border-radius: 50px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-warning btn-lg px-4" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(255,193,7,0.3);">
                    <i class="fas fa-edit mr-2"></i>Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content" style="border-radius: 15px; border: none;">
            <input type="hidden" name="action" value="delete_product">
            <input type="hidden" name="product_id" id="deleteProductId">
            <div class="modal-header bg-danger text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="deleteProductModalLabel">
                    <i class="fas fa-trash mr-2"></i>Delete Product
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <p class="text-dark">Are you sure you want to delete this product? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" style="border-radius: 50px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger btn-lg px-4" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(220,53,69,0.3);">
                    <i class="fas fa-trash mr-2"></i>Delete Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Store Modal -->
<div class="modal fade" id="renameStoreModal" tabindex="-1" role="dialog" aria-labelledby="renameStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" class="modal-content" style="border-radius: 15px; border: none;">
            <input type="hidden" name="action" value="rename_store">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold" id="renameStoreModalLabel">
                    <i class="fas fa-edit mr-2"></i>Rename Store
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold text-dark">
                        <i class="fas fa-tag mr-2 text-primary"></i>New Store Name
                    </label>
                    <input type="text" name="store_name" required placeholder="Enter new store name" 
                           class="form-control form-control-lg" style="border-radius: 10px;">
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal" style="border-radius: 50px;">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary btn-lg px-4" style="border-radius: 50px; box-shadow: 0 4px 15px rgba(0,123,255,0.3);">
                    <i class="fas fa-edit mr-2"></i>Rename Store
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}
.pagination .page-link {
    border: none;
    margin: 0 2px;
    border-radius: 10px !important;
    padding: 10px 15px;
    color: #007bff;
    font-weight: 500;
}
.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
}
.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}
.custom-file-label::after {
    content: "Browse";
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
.badge {
    font-size: 0.75em;
    padding: 0.375rem 0.75rem;
}
.input-group-text {
    border-right: none;
}
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    .product-card {
        margin-bottom: 1rem;
    }
    .pagination {
        flex-wrap: wrap;
    }
    .pagination .page-item {
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Custom file input labels
    document.querySelector('#storeProfile')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose store image';
        document.querySelector('label[for="storeProfile"]').textContent = fileName;
    });
    document.querySelector('#productImage')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose image';
        document.querySelector('label[for="productImage"]').textContent = fileName;
    });
    document.querySelector('#editProductImage')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Choose image';
        document.querySelector('label[for="editProductImage"]').textContent = fileName;
    });

    // Populate Edit and Delete Modals
    document.querySelectorAll('[data-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const modalTarget = this.getAttribute('data-target');

            if (modalTarget === '#editProductModal') {
                fetch(`./../controllers/StoreController.php?action=get_product&product_id=${encodeURIComponent(productId)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Network response not ok: ${response.status} ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            alert('Error: ' + data.error);
                            return;
                        }
                        document.getElementById('editProductId').value = data.id;
                        document.getElementById('editProductName').value = data.name;
                        document.getElementById('editProductPrice').value = data.price;
                        document.getElementById('editProductDescription').value = data.description;
                        document.getElementById('editProductQuantity').value = data.quantity;
                        document.querySelector('label[for="editProductImage"]').textContent =
                            data.image ? data.image.split('/').pop() : 'Choose image';
                    })
                    .catch(error => {
                        console.error('Error getting product data:', error);
                    });
            } else if (modalTarget === '#deleteProductModal') {
                document.getElementById('deleteProductId').value = productId;
            }
        });
    });
});
</script>