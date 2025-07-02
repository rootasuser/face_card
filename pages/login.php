<!-- Request Authentication -->
<?php require_once 'controllers/AuthController.php'; ?>

<!-- Toast Notification -->
<?php if (!empty($toast['message'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="liveToast" class="toast bg-<?= $toast['type'] ?> text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
        <div class="toast-header bg-<?= $toast['type'] ?> text-white">
            <strong class="mr-auto"><?= ucfirst($toast['type']) ?></strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?= htmlspecialchars($toast['message']) ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#liveToast').toast('show');
    });
</script>
<?php endif; ?>

<!-- Sign In / Sign Up Card -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow mt-5" style="background-color: #FFE8CA; border-radius: 10px;">
        <div class="card-body text-center">
          <img src="assets/images/face_card_logo.png" alt="Logo" class="mb-3 rounded-circle" width="100" height="100">
          <h5 class="card-title">Sign In</h5>

          <!-- Sign In Form -->
          <form method="POST" action="" id="signin-form">
            <div class="form-group" style="max-width: 300px; margin: auto;">
              <input type="email" name="email" class="form-control" placeholder="Email Address" style="border-radius: 10px;" required>
            </div>
            <div class="form-group" style="max-width: 300px; margin: auto; margin-top: 10px;">
              <input type="password" name="password" class="form-control" placeholder="Password" style="border-radius: 10px;" required>
            </div>
            <button type="submit" class="btn btn-block" name="login" style="background-color: #FFBCBC; color: #835151; font-weight: bolder; max-width: 300px; margin: auto; margin-top: 10px; border-radius: 10px;">Sign In</button>
          </form>

          <!-- Sign Up Form -->
          <form method="POST" action="" id="signup-form" style="display: none;">
            <div class="form-group" style="max-width: 300px; margin: auto;">
              <input type="email" name="email" class="form-control" placeholder="Email Address" style="border-radius: 10px;" required>
            </div>
            <div class="form-group" style="max-width: 300px; margin: auto; margin-top: 10px;">
              <input type="password" name="password" class="form-control" placeholder="Password" style="border-radius: 10px;" required>
            </div>
            <div class="form-group" style="max-width: 300px; margin: auto; margin-top: 10px;">
              <select name="role" class="form-control" required>
                <option value="user">User</option>
                <option value="seller">Seller</option>
              </select>
            </div>
            <button type="submit" class="btn btn-block" name="register" style="background-color: #FFBCBC; color: #835151; font-weight: bolder; max-width: 300px; margin: auto; margin-top: 10px; border-radius: 10px;">Sign Up</button>
          </form>

          <!-- Toggle Links -->
          <div class="mt-3">
            <a href="#" id="create-account-button" style="color: #835151; font-weight: bolder;" onclick="loadSignUpForm()">Create Account</a>
            <a href="#" id="login-account-button" style="color: #835151; font-weight: bolder;" onclick="loadSignInForm()" style="display: none;">Login</a>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Toggle Logic -->
<script>
  // Default visibility
  document.getElementById('signin-form').style.display = 'block';
  document.getElementById('create-account-button').style.display = 'block';
  document.getElementById('signup-form').style.display = 'none';
  document.getElementById('login-account-button').style.display = 'none';

  function loadSignUpForm() {
    document.getElementById('signin-form').style.display = 'none';
    document.getElementById('create-account-button').style.display = 'none';
    document.getElementById('signup-form').style.display = 'block';
    document.getElementById('login-account-button').style.display = 'inline-block';
    document.querySelector('.card-title').textContent = "Create Account";
  }

  function loadSignInForm() {
    document.getElementById('signin-form').style.display = 'block';
    document.getElementById('create-account-button').style.display = 'inline-block';
    document.getElementById('signup-form').style.display = 'none';
    document.getElementById('login-account-button').style.display = 'none';
    document.querySelector('.card-title').textContent = "Sign In";
  }
</script>
