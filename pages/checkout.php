<?php

require_once('../classes/database.php');
$con = new database();

$activeBorrowers = $con->getActiveBorrowers();
$availableCopies = $con->getAvailableCopies();

$checkoutStatus = null;
$checkoutMessage = '';

if (isset($_POST['create_loan'])) {

  $borrower_id = $_POST['borrower_id'];
  $processed_by_user_id = $_POST['processed_by_user_id'];
  $copy_ids_input = $_POST['copy_ids'];
  $li_duedate = $_POST['li_duedate'];
  $condition_out = $_POST['condition_out'];

  $copy_ids = array_map('trim', explode(',', $copy_ids_input));

  $copy_ids = array_filter($copy_ids, function($id) {
    return is_numeric($id) && $id > 0;
  });

  if (empty($copy_ids)) {
    $checkoutStatus = 'error';
    $checkoutMessage = 'Please provide at least one valid copy ID.';
  } else {
    try {
      $loan_id = $con->createLoanWithItems(
        $borrower_id,
        $processed_by_user_id,
        $copy_ids,
        $li_duedate,
        $condition_out
      );

      $checkoutStatus = 'success';
      $checkoutMessage = 'Loan created successfully! (Loan ID: ' . $loan_id . ')';

    } catch (Exception $e) {
      $checkoutStatus = 'error';
      $checkoutMessage = 'Error creating loan: ' . $e->getMessage();
    }
  }
}

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout — Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary" href="admin-dashboard.php">Back</a>
      <a class="btn btn-sm btn-outline-secondary" href="login.html">Logout</a>
    </div>
  </div>
</nav>

<?php if (isset($checkoutStatus) && $checkoutStatus): ?>
<div class="container py-3">

  <div class="alert alert-<?php echo $checkoutStatus === 'success' ? 'success' : 'danger'; ?>">

    <strong>
      <?php echo $checkoutStatus === 'success' ? 'Success!' : 'Error!'; ?>
    </strong>

    <?php echo $checkoutMessage; ?>

  </div>

</div>
<?php endif; ?>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-12 col-lg-7">
      <div class="card p-4">
        <h5 class="mb-1">Process Checkout</h5>
        <p class="small-muted mb-4">Create a Loan + LoanItems. Processor is required.</p>

        <form action="" method="POST">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Borrower</label>
              <select class="form-select" name="borrower_id" required>
                <option value="">Select borrower</option>

                <?php foreach ($activeBorrowers as $borrower): ?>
                  <option value="<?php echo $borrower['borrower_id']?>"><?php echo $borrower['borrower_name']?></option>
                <?php endforeach; ?>

              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Processed By (User ID)</label>
              <input class="form-control" name="processed_by_user_id" type="number" value="1" required readonly>
              <div class="small-muted mt-1">Should be the logged-in ADMIN user_id.</div>
            </div>

            <div class="col-12">
              <label class="form-label">Copy IDs to Borrow (comma-separated)</label>
              <input class="form-control" name="copy_ids" placeholder="e.g., 102, 401" required>
              <div class="small-muted mt-1">In PHP: validate copy status is AVAILABLE and not currently on loan.</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Due Date</label>
              <input class="form-control" name="li_duedate" type="date" required>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Condition Out</label>
              <select class="form-select" name="condition_out" required>
                <option value="GOOD">GOOD</option>
                <option value="DAMAGED">DAMAGED</option>
              </select>
            </div>
          </div>

          <hr class="my-4">
          <button class="btn btn-primary" type="submit" name="create_loan">Create Loan</button>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-5">
<div class="card p-4 mb-3">
<h6 class="mb-2">Checkout Rules Reminder</h6>
<ul class="small-muted mb-0">
<li>Loan must have a borrower_id.</li>
<li>Loan must have processed_by_user_id (ADMIN).</li>
<li><strong>Each copy can only be actively on loan once.</strong></li>
<li>Loan requires at least one LoanItem.</li>
<li>Copy status automatically changes to ON_LOAN.</li>
<li>Loan status starts as OPEN and can be CLOSED or CANCELLED.</li>
</ul>
</div>
‌
<div class="card p-4">
<h6 class="mb-3">Available Copies</h6>
<div class="table-responsive">
<table class="table table-sm mb-0">
<thead class="table-light">
<tr>
<th>Copy ID</th>
<th>Book Title</th>
</tr>
</thead>
<tbody>
<?php foreach ($availableCopies as $copy): ?>
<tr>
<td><?php echo htmlspecialchars($copy['copy_id']); ?></td>
<td class="small"><?php echo htmlspecialchars($copy['book_title']); ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($availableCopies)): ?>
<tr>
<td colspan="2" class="text-center small-muted">No copies available</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>


  </div>
</main>

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../sweetalert/dist/sweetalert2.js"></script>
</body>
</html>