<?php
// I used this because I already have session_start() at the admin/dashboard.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validation of account who logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Handle search and pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; // get search inputted
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? (int)$_GET['p'] : 1; // get parameter
$limit = 2; // limit into 2 data's in the table row
$offset = ($page - 1) * $limit;

require_once './../models/User.php';
require_once './../controllers/AccountApprovalController.php';

$accounts = User::searchAccounts($search, $limit, $offset); // Search Account
$totalAccounts = User::countAccounts($search); // Count accounts 
$totalPages = ceil($totalAccounts / $limit); // total pages in pagination
?>

<!--- Toast notification only appear if there is approved,
 rejected, edit, delete at the upper right side corner of the table -->
<?php if (isset($_SESSION['toast'])): ?>
<div aria-live="polite" aria-atomic="true" style="position: relative;">
    <div class="toast bg-<?= $_SESSION['toast']['type'] ?> text-white" style="position: absolute; top: 1rem; right: 1rem;" data-delay="3000">
        <div class="toast-body">
            <?= htmlspecialchars($_SESSION['toast']['message']) ?>
        </div>
    </div>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<div class="container mt-4">

  <!--- Search Input & Search Button -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search email or role" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
    </form>

    <!-- Table -->
     <div class="table-responsive">
    <table class="table table-bordered table-hover" style="color: #000;">
        <thead>
            <tr>
                <th class="text-nowrap">#</th>
                <th class="text-nowrap">Email</th>
                <th class="text-nowrap">Role</th>
                <th class="text-nowrap">Status</th>
                <th class="text-nowrap">Created</th>
                <th class="text-nowrap">Actions</th>
            </tr>
        </thead>
        <tbody>
            <!--- Display Accounts info -->
        <?php if ($accounts): ?>
            <?php foreach ($accounts as $account): ?>
                <tr>
                    <td class="text-nowrap"><?= htmlspecialchars($account['id']) ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($account['email']) ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($account['role']) ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($account['status']) ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($account['created_at']) ?></td>
                    <!-- Actions Buttons -->
                    <td class="text-nowrap">
                        <div class="d-flex align-items-center gap-2 flex-wrap">

                          <!--- Approved, Reject Buttons inside the table -->
                        <?php if ($account['status'] === 'pending'): ?>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $account['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm mr-1"><i class="fas fa-check"></i> Approve</button>
                            </form>
                            <form method="POST" action="" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $account['id'] ?>">
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm"><i class="fas fa-times"></i> Reject</button>
                            </form>
                        <?php else: ?>
                              <!--- Edit Button inside the table -->
                            <button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#editModal"
                                    data-id="<?= $account['id'] ?>"
                                    data-email="<?= htmlspecialchars($account['email']) ?>"
                                    data-role="<?= htmlspecialchars($account['role']) ?>">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                            <!--- Delete Button inside the table -->
                            <?php if ($_SESSION['user_id'] != $account['id']): ?>
                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal"
                                        data-id="<?= $account['id'] ?>"
                                        data-email="<?= htmlspecialchars($account['email']) ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No accounts found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>

    <!-- Pagination for table pages  -->
   <nav>
    <ul class="pagination justify-content-end">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=accounts&p=<?= max(1, $page - 1) ?>&search=<?= urlencode($search) ?>">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=accounts&p=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?page=accounts&p=<?= min($totalPages, $page + 1) ?>&search=<?= urlencode($search) ?>">Next</a>
        </li>
    </ul>
</nav>

</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Account</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="editUserId">
          <div class="form-group">
            <label for="editEmail">Email</label>
            <input type="text" class="form-control" id="editEmail" name="email" readonly>
          </div>
          <div class="form-group">
            <label for="editRole">Role</label>
            <select class="form-control" id="editRole" name="role">
              <option value="admin">Admin</option>
              <option value="seller">Seller</option>
              <option value="user">User</option>
            </select>
          </div>
          <input type="hidden" name="action" value="edit">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteModalLabel">Delete Account</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="user_id" id="deleteUserId">
          <input type="hidden" name="action" value="delete">
          <p>Are you sure you want to delete this account: <strong id="deleteUserEmail"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- This Content Delivery Network will support the jquery codes below -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>

    // Show toast notification code
$(document).ready(function () {
    $('.toast').toast('show');

    // Edit Modal Code to display data based on element id
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#editUserId').val(button.data('id'));
        $('#editEmail').val(button.data('email'));
        $('#editRole').val(button.data('role'));
    });

    // Delete Modal Code to delete data based on the value of element id
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        $('#deleteUserId').val(button.data('id'));
        $('#deleteUserEmail').text(button.data('email'));
    });

    // Optional
    $('#editModal, #deleteModal').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $(this).find('input[type="hidden"]').val('');
        $('#deleteUserEmail').text('');
    });
});
</script>


