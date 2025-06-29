<!-- Request Authentication -->
<?php require_once 'controllers/AuthController.php'; ?>

<!--- Toast Notification Upper Rightside Corner-->
<?php if (!empty($toast['message'])): ?>
<div class="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 9999;">
    <div id="liveToast" class="toast bg-<?= $toast['type'] ?> text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
        <div class="toast-header bg-<?= $toast['type'] ?> text-white">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?= htmlspecialchars($toast['message']) ?>
        </div>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('liveToast');
        if (toastEl) {
            var toast = new bootstrap.Toast(toastEl); 
            toast.show();
        }
    });
</script>
<?php endif; ?>



<div class="card card-signin">
    <div class="card-body text-center">
        <img src="assets/images/face_card_logo.png" alt="Logo" class="mb-3 rounded-circle" width="100" height="100">
        <h5 class="card-title">Sign In</h5>
                <form method="POST" action="" id="signin-form">
                <div class="form-group">
                    <input type="email" name="email" id="email" class="form-signin-input" placeholder="Email Address" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" class="form-signin-input" placeholder="Password" required>
                </div>
                <button type="submit" class="form-signin-button" name="login">Sign In</button>
            </form>

         <form method="POST" action="" id="signup-form">
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-signin-input" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-signin-input" placeholder="Password" required>
            </div>
             <div class="form-group">
                <select name="role" id="role" class="form-signin-input">
                    <option value="user">User</option>
                    <option value="seller">Seller</option>
                </select>
            </div>
            <button type="submit" class="form-signin-button" name="register">Sign up</button>
        </form>
        <div class="card-text mt-3 mb-3">
            <a href="#" class="create-text" id="create-account-button" onclick="loadSignUpForm()">Create Account</a>
             <a href="#" class="create-text" id="login-account-button" onclick="loadSignInForm()">Login Account</a>
        </div>
    </div>
</div>

<script>
//==> Show first 
 document.getElementById('signin-form').style.display = 'block';
 document.getElementById('create-account-button').style.display = 'block';

 document.getElementById('signup-form').style.display = 'none';
 document.getElementById('login-account-button').style.display = 'none';

function loadSignUpForm() {
    document.getElementById('signin-form').style.display = 'none';
    document.getElementById('create-account-button').style.display = 'none';

    document.getElementById('signup-form').style.display = 'block';
    document.getElementById('login-account-button').style.display = 'block';
}

function loadSignInForm() {
    document.getElementById('signin-form').style.display = 'block';
    document.getElementById('create-account-button').style.display = 'block';

    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('login-account-button').style.display = 'none';
}
</script>