<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once './../controllers/CartController.php';
require_once './../models/Order.php';
require_once './../controllers/OrderController.php';

$userId = $_SESSION['user_id'] ?? null;
$cartItems = [];

$cartController = new CartController();
$orderController = new OrderController();

if ($userId) {
    // Always load cart items first
    $cartItems = $cartController->getCartItemsByUser($userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_cart'])) {
        $cartItemId = $_POST['cart_id'];
        $cartController->removeCartItem($cartItemId, $userId);
        $cartItems = $cartController->getCartItemsByUser($userId); // Refresh cart after removal

    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
        $paymentMethod = $_POST['payment_method'];
        $address = $_POST['address'];
        $fullname = $_POST['fullname'];
        $contact = $_POST['contact'];

        foreach ($cartItems as $item) {
            $orderController->placeOrder(
                $userId,
                $item['product_id'],
                $item['product_image'],
                $item['product_name'],
                $item['quantity'],
                $item['product_price'],
                $paymentMethod,
                $address,
                $fullname,
                $contact
            );
        }

        $cartController->clearCart($userId);
    }
}
// Get my orders
$orders = $orderController->getOrdersByUser($userId);
?>


    <script src="https://www.paypal.com/sdk/js?client-id=AVk7_stWPA3hCZ0PUSIM0BemJPzmpxntsLBLUxe59tFBJQYphdifWniJz47aUQDouk3qMHwQKujCyjZp&currency=PHP"></script>
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --border-color: #dee2e6;
        }

     

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            margin: 30px auto;
            overflow: hidden;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #34495e 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .page-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .page-header .subtitle {
            opacity: 0.9;
            margin-top: 10px;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .cart-section {
            padding: 40px;
        }

        .cart-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }

        .cart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .cart-item {
            padding: 25px;
            border-bottom: 1px solid #f1f3f4;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f8f9fa;
        }

        .cart-item:last-child {
            border-bottom: none;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-right: 20px;
        }

        .product-details {
            flex: 1;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 1.1rem;
            color: var(--secondary-color);
            font-weight: 600;
        }

        .quantity-badge {
            background: linear-gradient(135deg, var(--warning-color), #e67e22);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin: 0 15px;
        }

        .subtotal {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--success-color);
            margin: 0 20px;
        }

        .remove-btn {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }

        .remove-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        .cart-total {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            text-align: center;
            border-top: 3px solid var(--secondary-color);
        }

        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .checkout-btn {
            background: linear-gradient(135deg, var(--success-color), #229954);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(39, 174, 96, 0.3);
        }

        .checkout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(39, 174, 96, 0.4);
            color: white;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 30px;
            color: #6c757d;
        }

        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .checkout-form {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid var(--border-color);
        }

        .form-group label {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 10px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .orders-section {
            background: white;
            border-radius: 15px;
            margin-top: 40px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .orders-header {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
            padding: 25px 30px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .orders-table {
            margin: 0;
        }

        .orders-table th {
            background-color: #f8f9fa;
            border: none;
            font-weight: 600;
            color: var(--dark-text);
            padding: 15px;
        }

        .orders-table td {
            padding: 15px;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        .order-id {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .payment-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .payment-cod {
            background-color: #fef9e7;
            color: #d68910;
        }

        .payment-paypal {
            background-color: #e8f4f8;
            color: #1abc9c;
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .success-modal-icon {
            font-size: 4rem;
            color: var(--success-color);
            margin-bottom: 20px;
        }

        #paypal-button-container {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                text-align: center;
            }
            
            .product-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .quantity-badge, .subtotal {
                margin: 10px 0;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="main-container">
            <!-- Page Header -->
            <div class="page-header">
                <h2><i class="fas fa-shopping-cart mr-3"></i>My Shopping Cart</h2>
                <div class="subtitle">Review your items and proceed to checkout</div>
            </div>

            <div class="cart-section">
                <?php if (count($cartItems) > 0): ?>
                    <div class="cart-card fade-in">
                        <?php 
                        $total = 0;
                        foreach ($cartItems as $item): 
                            $subtotal = $item['product_price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                            <div class="cart-item">
                                <img src="/face_card/Uploads/products/<?= htmlspecialchars(basename($item['product_image'])) ?>"
                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                     class="product-image"
                                     onerror="this.src='https://via.placeholder.com/80x80?text=No+Image';">
                                
                                <div class="product-details">
                                    <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="product-price">₱<?= number_format($item['product_price'], 2) ?></div>
                                </div>
                                
                                <div class="quantity-badge">
                                    <i class="fas fa-times mr-1"></i><?= $item['quantity'] ?>
                                </div>
                                
                                <div class="subtotal">₱<?= number_format($subtotal, 2) ?></div>
                                
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="remove_cart" class="remove-btn" title="Remove item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="cart-total">
                            <div class="total-amount">
                                <i class="fas fa-receipt mr-2"></i>Total: ₱<?= number_format($total, 2) ?>
                            </div>
                            <button id="showCheckoutBtn" class="btn checkout-btn">
                                <i class="fas fa-credit-card mr-2"></i>Proceed to Checkout
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <div class="checkout-form fade-in" style="display: none;">
                        <h4 class="mb-4" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-clipboard-list mr-2"></i>Checkout Information
                        </h4>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">
                                            <i class="fas fa-credit-card mr-2"></i>Payment Method
                                        </label>
                                        <select class="form-control" id="payment_method" name="payment_method" required>
                                            <option value="COD">Cash on Delivery (COD)</option>
                                            <option value="PayPal">PayPal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fullname">
                                            <i class="fas fa-user mr-2"></i>Full Name
                                        </label>
                                        <input type="text" class="form-control" id="fullname" name="fullname" 
                                               placeholder="Enter your full name" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">
                                    <i class="fas fa-map-marker-alt mr-2"></i>Delivery Address
                                </label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       placeholder="Enter your complete address" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact">
                                    <i class="fas fa-phone mr-2"></i>Contact Number
                                </label>
                                <input type="text" class="form-control" id="contact" name="contact" 
                                       placeholder="Enter your phone number" required>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="place_order" class="btn checkout-btn">
                                    <i class="fas fa-check-circle mr-2"></i>Place Order
                                </button>
                            </div>
                            
                            <div id="paypal-button-container"></div>
                        </form>
                    </div>

                <?php else: ?>
                    <div class="empty-cart fade-in">
                        <i class="fas fa-shopping-cart"></i>
                        <h4>Your cart is empty</h4>
                        <p>Add some products to your cart to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Orders Section -->
        <?php if (!empty($orders)): ?>
            <div class="container">
                <div class="orders-section fade-in">
                    <div class="orders-header">
                        <i class="fas fa-history mr-2"></i>Order History
                    </div>
                    <div class="table-responsive">
                        <table class="table orders-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><span class="order-id">#<?= $order['id'] ?></span></td>
                                        <td><?= htmlspecialchars($order['product_name']) ?></td>
                                        <td><strong><?= $order['quantity'] ?></strong></td>
                                        <td>₱<?= number_format($order['product_price'], 2) ?></td>
                                        <td><strong>₱<?= number_format($order['product_price'] * $order['quantity'], 2) ?></strong></td>
                                        <td>
                                            <span class="payment-badge <?= strtolower($order['payment_method']) === 'cod' ? 'payment-cod' : 'payment-paypal' ?>">
                                                <?= htmlspecialchars($order['payment_method']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center p-4">
                <div class="success-modal-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h5 class="modal-title mb-3" id="successModalLabel">Payment Successful!</h5>
                <p class="mb-4">Your order has been placed successfully via PayPal!</p>
                <button type="button" class="btn checkout-btn" onclick="window.location.reload()">
                    <i class="fas fa-thumbs-up mr-2"></i>Awesome!
                </button>
            </div>
        </div>
    </div>

    
    <script>
        // Show checkout form when button is clicked
        document.getElementById('showCheckoutBtn')?.addEventListener('click', function () {
            const form = document.querySelector('.checkout-form');
            form.style.display = 'block';
            form.scrollIntoView({ behavior: 'smooth' });
            this.style.display = 'none';
        });

        document.addEventListener('DOMContentLoaded', function () {
            const paymentMethod = document.getElementById('payment_method');
            const checkoutForm = document.querySelector('.checkout-form form');
            
            if (!paymentMethod || !checkoutForm) return;
            
            const paypalContainer = document.createElement('div');
            paypalContainer.id = 'paypal-button-container';
            paypalContainer.style.display = 'none'; 
            paypalContainer.style.marginTop = '15px'; 
            checkoutForm.appendChild(paypalContainer);

            const placeOrderBtn = document.querySelector('button[name="place_order"]');
            let paypalButtonsRendered = false;

            function togglePayPalUI(method) {
                const showPayPal = method === 'PayPal';
                
                // Hide/show PayPal container
                paypalContainer.style.display = showPayPal ? 'block' : 'none';
                
                // Hide/show place order button
                if (placeOrderBtn) {
                    placeOrderBtn.style.display = showPayPal ? 'none' : 'inline-block';
                }

                // Clear PayPal container when switching to COD to ensure clean state
                if (!showPayPal && paypalButtonsRendered) {
                    paypalContainer.innerHTML = '';
                    paypalButtonsRendered = false;
                }

                // Render PayPal buttons only when PayPal is selected and not already rendered
                if (showPayPal && !paypalButtonsRendered) {
                    renderPayPalButtons();
                    paypalButtonsRendered = true;
                }
            }

            function renderPayPalButtons() {
                <?php if (count($cartItems) > 0): ?>
                // Clear any existing content first
                paypalContainer.innerHTML = '';
                
                paypal.Buttons({
                    createOrder: function (data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?= number_format($total, 2, '.', '') ?>'
                                }
                            }]
                        });
                    },
                    onApprove: function (data, actions) {
                        return actions.order.capture().then(function (details) {
                            const fullname = document.getElementById('fullname').value.trim();
                            const address = document.getElementById('address').value.trim();
                            const contact = document.getElementById('contact').value.trim();

                            if (!fullname || !address || !contact) {
                                alert('Please complete all required fields before proceeding with PayPal payment.');
                                return;
                            }

                            const form = document.createElement('form');
                            form.method = 'POST';

                            const inputs = [
                                { name: 'payment_method', value: 'PayPal' },
                                { name: 'address', value: address },
                                { name: 'fullname', value: fullname },
                                { name: 'contact', value: contact },
                                { name: 'place_order', value: '1' }
                            ];

                            inputs.forEach(input => {
                                const hidden = document.createElement('input');
                                hidden.type = 'hidden';
                                hidden.name = input.name;
                                hidden.value = input.value;
                                form.appendChild(hidden);
                            });

                            document.body.appendChild(form);
                            $('#successModal').modal({ backdrop: 'static', keyboard: false });
                            setTimeout(() => form.submit(), 2000);
                        });
                    },
                    onError: function (err) {
                        console.error('PayPal Error:', err);
                        alert('An error occurred with PayPal. Please try again or select a different payment method.');
                    },
                    style: {
                        color: 'blue',
                        shape: 'pill',
                        label: 'pay',
                        height: 50
                    }
                }).render('#paypal-button-container');
                <?php endif; ?>
            }

            // Initialize with default value
            togglePayPalUI(paymentMethod.value);
            
            // Listen for payment method changes
            paymentMethod.addEventListener('change', function() {
                // console.log('Payment method changed to:', this.value); // Debug log
                togglePayPalUI(this.value);
            });
        });

        // smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in');
            elements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
</script>