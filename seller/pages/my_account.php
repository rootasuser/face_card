<?php
require_once './../controllers/MyAccountController.php';
?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<!-- Toast container positioning -->
<style>
    #toast-container {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1055;
    }
</style>

<!-- Toast Notification for success or failed auto hide after 3sec -->
<?php if (isset($_SESSION['toast'])): ?>
    <div id="toast-container">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
            <div class="toast-header bg-<?= $_SESSION['toast']['type'] === 'success' ? 'success' : 'danger' ?> text-white">
                <button type="button" class="ml-auto mb-1 close text-white" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <?= htmlspecialchars($_SESSION['toast']['message']) ?>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>

<!-- Card and inside of this is the update form -->
<div class="card card-sm">
    <div class="card-body">
        <!-- Account Update Form -->
        <form method="POST" action="" class="mt-4">
            <input type="hidden" name="action" value="update_profile">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars($user['email']) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">New Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                >
            </div>
            <div class="d-flex align-items-end justify-content-end">
            <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>

    </div>
</div>

<!--- Show toast jquery function -->
<script>
    $(document).ready(function () {
        $('.toast').toast('show');
    });
</script>
