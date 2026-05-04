<?php
require_once('../classes/database.php');
$con = new database();

$overview= [
	'total_books' => 0,
	'total_copies' => 0,
	'open_loans' => 0,
	'overdue_items' => 0,
];
$recentLoans = [];

try {
	$overview = $con->viewDashboardOverview() ?: $overview;
	$recentLoans = $con->viewRecentLoans(5);
} catch (Exception $e) {
	$recentLoans = [];
}

$totalBooks = (int)($overview['total_books'] ?? 0);
$openLoans = (int)($overview['open_loans'] ?? 0); 
$overdueItems = (int)($overview['overdue_items'] ?? 0);
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Admin Dashboard — Library</title>
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
	<link rel="stylesheet" href="../assets/css/style.css">
	<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
	<div class="container">
		<a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdmin">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div id="navAdmin" class="collapse navbar-collapse">
			<ul class="navbar-nav me-auto gap-lg-1">
				<li class="nav-item"><a class="nav-link active" href="admin-dashboard.php">Dashboard</a></li>
				<li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
				<li class="nav-item"><a class="nav-link" href="authors-genres.php">Authors &amp; Genres</a></li>
				<li class="nav-item"><a class="nav-link" href="borrowers.php">Borrowers</a></li>
				<li class="nav-item"><a class="nav-link" href="checkout.html">Checkout</a></li>
				<li class="nav-item"><a class="nav-link" href="return.html">Return</a></li>
				<li class="nav-item"><a class="nav-link" href="catalog.html">Catalog</a></li>
			</ul>
			<div class="d-flex align-items-center gap-2">
				<span class="badge badge-soft">Role: ADMIN</span>
				<a class="btn btn-sm btn-outline-secondary" href="login.html">Logout</a>
			</div>
		</div>
	</div>
</nav>

<main class="container py-4">
	<div class="row g-3">
		<div class="col-12 col-lg-8">
			<div class="card p-4">
				<h5 class="mb-1">Quick Overview</h5>
				<p class="small-muted mb-4">These are NOT placeholder values—no need to connect to PHP later.</p>

				<div class="row g-3 mb-4">
					<div class="col-6 col-md-3">
						<div class="border rounded p-3 bg-white h-100">
							<div class="small-muted">Total Books</div>
							<div class="fs-4 fw-semibold"><?php echo $totalBooks; ?></div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="border rounded p-3 bg-white h-100">
							<div class="small-muted">Total Copies</div>
							<div class="fs-4 fw-semibold"><?php echo $totalCopies; ?></div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="border rounded p-3 bg-white h-100">
							<div class="small-muted">Open Loans</div>
							<div class="fs-4 fw-semibold"><?php echo $openLoans; ?></div>
						</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="border rounded p-3 bg-white h-100">
							<div class="small-muted">Overdue Items</div>
							<div class="fs-4 fw-semibold"><?php echo $overdueItems; ?></div>
						</div>
					</div>
				</div>

				<hr class="my-4">

				<h6 class="mb-2">Recent Loans (with Processor)</h6>
				<div class="table-responsive">
					<table class="table table-sm align-middle">
						<thead class="table-light">
							<tr>
								<th>Loan ID</th>
								<th>Borrower</th>
								<th>Status</th>
								<th>Loan Date</th>
								<th>Processed By</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($recentLoans)): ?>
								<?php foreach ($recentLoans as $loan): ?>
									<?php
										$status = strtoupper((string)($loan['loan_status'] ?? ''));
										$statusClass = 'text-bg-warning';
										if ($status === 'OPEN') {
											$statusClass = 'text-bg-success';
										} elseif ($status === 'CLOSED') {
											$statusClass = 'text-bg-danger';
										} elseif ($status === 'CANCELLED') {
											$statusClass = 'text-bg-danger';
										}
									?>
									<tr>
										<td><?php echo (int)($loan['loan_id'] ?? 0); ?></td>
										<td><?php echo htmlspecialchars($loan['fullname'] ?? ''); ?></td>
										<td><span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
										<td><?php echo htmlspecialchars((string)($loan['loan_date'] ?? '')); ?></td>
										<td><?php echo htmlspecialchars($loan['processed_by'] ?? ''); ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" class="text-center small-muted py-4">No loans found.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-12 col-lg-4">
			<div class="card p-4 h-100">
				<h6 class="mb-3">Admin Shortcuts</h6>
				<div class="d-grid gap-2">
					<a class="btn btn-primary" href="books.php">Manage Books</a>
					<a class="btn btn-outline-primary" href="borrowers.php">Manage Borrowers</a>
					<a class="btn btn-outline-secondary" href="checkout.html">Process Checkout</a>
					<a class="btn btn-outline-secondary" href="return.html">Process Return</a>
				</div>
				<hr class="my-4">
				<div class="small-muted">
					This dashboard uses the same PHP + Bootstrap setup as the other admin pages and now reads live counts from MySQL.
				</div>
			</div>
		</div>
	</div>
</main>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->
<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
