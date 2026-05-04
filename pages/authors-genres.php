<?php
require_once('../classes/database.php');
$con = new database();

$flashStatus = null;
$flashMessage = '';

if (isset($_POST['add_author'])) {
    $firstname = trim($_POST['author_firstname'] ?? '');
    $lastname = trim($_POST['author_lastname'] ?? '');
    $birth = $_POST['author_birthyear'] ?? null;
    $nationality = trim($_POST['author_nationality'] ?? '');

    try {
        $con->addAuthor($firstname, $lastname, $birth ?: null, $nationality ?: null);
        $flashStatus = 'success';
        $flashMessage = 'Author added successfully.';
    } catch (Exception $e) {
        $flashStatus = 'error';
        $flashMessage = 'Error adding author.';
    }
}

if (isset($_POST['add_genre'])) {
    $genreName = trim($_POST['genre_name'] ?? '');

    try {
        $con->addGenres($genreName);
        $flashStatus = 'success';
        $flashMessage = 'Genre added successfully.';
    } catch (Exception $e) {
        $flashStatus = 'error';
        $flashMessage = 'Error adding genre.';
    }   
}

$authors = [];
$genres = [];

try {
    $authors = $con->getAuthors();
    $genres = $con->getGenres();
} catch (Exception $e) {
    $authors = [];
    $genres = [];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Authors and Genres - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../sweetalert/dist/sweetalert2.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navAdminAuthorsGenres">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navAdminAuthorsGenres" class="collapse navbar-collapse">
        <ul class="navbar-nav me-auto gap-lg-1">
            <li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>
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
        <div class="col-12 col-lg-6">
        <div class="card p-4 h-100">
            <h5 class="mb-1">Add Author</h5>
            <p class="small-muted mb-3">Creates a row in the Authors table.</p>

        <form action="#" method="POST" class="row g-2">
            <div class="col-12 col-md-6">
                <label class="form-label">First Name</label>
                <input class="form-control" name="author_firstname" placeholder="e.g., Jose" required />
            </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Last Name</label>
            <input class="form-control" name="author_lastname" placeholder="e.g., Rizal" required />
        </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Birth Year</label>
                <input class="form-control" name="author_birthyear" type="number" min="1" max="2100" placeholder="optional" />
            </div>
        <div class="col-12 col-md-6">
            <label class="form-label">Nationality</label>
            <input class="form-control" name="author_nationality" placeholder="optional" />
        </div>
        <div class="col-12">
            <button name="add_author" class="btn btn-primary w-100" type="submit">Save Author</button>
        </div>
        </form>
    </div>
</div>

    <div class="col-12 col-lg-6">
        <div class="card p-4 h-100">
            <h5 class="mb-1">Add Genre</h5>
            <p class="small-muted mb-3">Creates a row in the Genres table.</p>

        <form action="#" method="POST" class="row g-2">
            <div class="col-12">
                <label class="form-label">Genre Name</label>
                <input class="form-control" name="genre_name" placeholder="e.g., Classic" required />
            </div>
            <div class="col-12">
                <button name="add_genre" class="btn btn-outline-primary w-100" type="submit">Save Genre</button>
            </div>
        </form>
    </div>
</div>

<div class="col-12 col-lg-8">
    <div class="card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Authors List</h5>
            <span class="small-muted">Live data from MySQL</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
            <thead class="table-light">
            <tr>
                <th>Author ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Birth Year</th>
                <th>Nationality</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($authors)): ?>
                <?php foreach ($authors as $author): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($author['author_id'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($author['author_firstname'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($author['author_lastname'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($author['author_birthyear'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($author['author_nationality'] ?? '')); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                <td colspan="5" class="text-center small-muted py-4">No authors found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-12 col-lg-4">
    <div class="card p-4 h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Genres List</h5>
        <span class="small-muted">Live data from MySQL</span>
    </div>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead class="table-light">
            <tr>
                <th>Genre ID</th>
                <th>Genre Name</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($genres)): ?>
                <?php foreach ($genres as $genre): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($genre['genre_id'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($genre['genre_name'] ?? '')); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                <td colspan="2" class="text-center small-muted py-4">No genres found.</td>
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

<script>
    const flashStatus = <?php echo json_encode($flashStatus); ?>;
    const flashMessage = <?php echo json_encode($flashMessage); ?>;

    if(flashStatus == 'success') {
        Swal.fire({
        icon: 'success',
        title: 'Success',
        text: flashMessage,
        confirmButtonText: 'OK'
    });
    } else if(flashStatus == 'error') {
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: flashMessage,
        confirmButtonText: 'OK'
    });
}
</script>
</body>
</html>