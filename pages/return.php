<?php
require_once('../classes/database.php');
$con = new database();

$loanitems = $con->getOnLoanItem();

$flashMessage = '';
$flashStatus = null;

if (isset($_POST['process_return'])){
  $loanitemID = $_POST['loan_item_id'];
  $returnedAt = $_POST['li_returned_at'];
  $conditionIn = $_POST['condition_in'];

  try{
    $con->processLoanReturns($loanitemID, $returnedAt, $conditionIn);
    $flashStatus = 'success';
    $flashMessage = 'Copy returned to library';
  }catch(Exception $e){
    $flashStatus = 'error';
    $flashMessage = 'Failed to return copy to library '. $e;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Return — Admin</title>
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

<?php if (isset($flashStatus) && $flashStatus): ?>
<div class="container py-3">

  <div class="alert alert-<?php echo $flashStatus === 'success' ? 'success' : 'danger'; ?>">

    <strong>
      <?php echo $flashStatus === 'success' ? 'Success!' : 'Error!'; ?>
    </strong>

    <?php echo $flashMessage; ?>

  </div>

</div>
<?php endif; ?>

<main class="container py-4">
  <div class="row g-3">
    <div class="col-12 col-lg-6">
    <div class="card p-4 h-100">
    <h5 class="mb-1">Process Return</h5>
    <p class="small-muted mb-4">Update LoanItem.li_returned_at and condition_in; then update BookCopy.status.</p>

    <!-- Later in PHP: action="../php/loans/return.php" method="POST" -->
    <form action="#" method="POST" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Loan Item ID</label>
        <input class="form-control" name="loan_item_id" type="number" placeholder="e.g., 5006" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Returned At</label>
        <input class="form-control" name="li_returned_at" type="datetime-local" value="<?php echo date('Y-m-d\TH:i'); ?>" readonly required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Condition In</label>
        <select class="form-select" name="condition_in" required>
          <option value="GOOD">GOOD</option>
          <option value="DAMAGED">DAMAGED</option>
        </select>
      </div>

      <div class="col-12">
        <button name="process_return" class="btn btn-primary" type="submit">Confirm Return</button>
      </div>
      </div>
    </form>
    </div> 

    <div class="col-12 col-lg-6">
      <div class="card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-2">Loaned Books List</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
            <thead class="table-light">
            <tr>
                <th>Loan Item ID</th>
                <th>Book Title</th>
                <th>Due Date</th>
            </tr>
            </thead>
            <tbody>
                <?php 
                  foreach ($loanitems as $loanitem): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($loanitem['loan_item_id'] ?? '')); ?></td>
                    <td class="small"><?php echo htmlspecialchars((string)($loanitem['book_title'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($loanitem['li_due date'] ?? '')); ?></td>
                    
                </tr>
                <?php endforeach; ?>
              <?php if (empty($loanitems)): ?>
                <tr>
                <td colspan="6" class="text-center small-muted py-4">No active loans found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
  </div>

  
</main>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>