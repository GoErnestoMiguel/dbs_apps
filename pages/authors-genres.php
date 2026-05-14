<?php
require_once('../classes/database.php');
session_start();

$con = new database();

$flashStatus = null;
$flashMessage = '';
$error_message = null;
$editAuthorStatus = null;
$editAuthorMessage = '';

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

if (isset($_POST['edit_author'])) {
    $author_id = $_POST['edit_author_id'] ?? '';
    $firstname = trim($_POST['edit_author_firstname'] ?? '');
    $lastname = trim($_POST['edit_author_lastname'] ?? '');
    $birth = $_POST['edit_author_birthyear'] ?? null;
    $nationality = trim($_POST['edit_author_nationality'] ?? '');

    try {
        $con->updateAuthor($author_id, $firstname, $lastname, $birth ?: null, $nationality ?: null);
        $editAuthorStatus = 'success';
        $editAuthorMessage = 'Author updated successfully.';
    } catch (Exception $e) {
        $editAuthorStatus = 'error';
        $editAuthorMessage = 'Error updating author.';
    }
}

if (isset($_POST['delete_author'])) {
    $author_id = $_POST['author_id'];
    $author_name = $_POST['author_name'];

    try {
        $con->deleteAuthor($author_id);
        $_SESSION['success_message'] = $author_name . ' has been deleted from the database.';
        header('Location: authors-genres.php');
        exit();
    } catch (Exception $e) {
        $error_message = "Cannot delete this author. It may be linked to books.";
    }
}

if (isset($_POST['delete_genre'])) {
    $genre_id = $_POST['genre_id'];
    $genre_name = $_POST['genre_name'];

    try {
        $con->deleteGenre($genre_id);
        $_SESSION['success_message'] = $genre_name . ' has been deleted from the database.';
        header('Location: authors-genres.php');
        exit();
    } catch (Exception $e) {
        $error_message = "Cannot delete this genre. It may be linked to books.";
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
            <li class="nav-item"><a class="nav-link" href="checkout.php">Checkout</a></li>
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
    <?php if(isset($error_message)){ ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error! </strong> <?php echo $error_message; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php } ?>

    <?php if(isset($_SESSION['success_message'])){ ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success! </strong> <?php echo $_SESSION['success_message']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php 
      unset($_SESSION['success_message']);
    } ?>

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
                <th class="text-end">Actions</th>
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
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editAuthorModal"
                            data-author-id="<?php echo htmlspecialchars((string)($author['author_id'] ?? '')); ?>"
                            data-author-firstname="<?php echo htmlspecialchars((string)($author['author_firstname'] ?? '')); ?>"
                            data-author-lastname="<?php echo htmlspecialchars((string)($author['author_lastname'] ?? '')); ?>"
                            data-author-birthyear="<?php echo htmlspecialchars((string)($author['author_birthyear'] ?? '')); ?>"
                            data-author-nationality="<?php echo htmlspecialchars((string)($author['author_nationality'] ?? '')); ?>">
                            Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAuthorModal"
                            data-author-id="<?php echo htmlspecialchars((string)($author['author_id'] ?? '')); ?>"
                            data-author-name="<?php echo htmlspecialchars((string)($author['author_firstname'] . ' ' . $author['author_lastname'])); ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                <td colspan="6" class="text-center small-muted py-4">No authors found.</td>
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
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($genres)): ?>
                <?php foreach ($genres as $genre): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)($genre['genre_id'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars((string)($genre['genre_name'] ?? '')); ?></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteGenreModal"
                            data-genre-id="<?php echo htmlspecialchars((string)($genre['genre_id'] ?? '')); ?>"
                            data-genre-name="<?php echo htmlspecialchars((string)($genre['genre_name'] ?? '')); ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                <td colspan="3" class="text-center small-muted py-4">No genres found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
    </div>
</div>
</main>

<div class="modal fade" id="editAuthorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST">
                    <input type="hidden" id="edit_author_id" name="edit_author_id">
                    <div class="row g-2">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input class="form-control" id="edit_author_firstname" name="edit_author_firstname" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input class="form-control" id="edit_author_lastname" name="edit_author_lastname" required>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Birth Year</label>
                            <input class="form-control" id="edit_author_birthyear" name="edit_author_birthyear" type="number" min="1" max="2100">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label">Nationality</label>
                            <input class="form-control" id="edit_author_nationality" name="edit_author_nationality">
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary w-100" name="edit_author" type="submit">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAuthorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Author</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="delete_author_name"></strong>?</p>
        <p class="text-danger small">This action cannot be undone.</p>
        
        <form action="#" method="POST">
          <input type="hidden" name="author_id" id="delete_author_id">
          <input type="hidden" name="author_name" id="delete_author_name_input">
          <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger" name="delete_author">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteGenreModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Delete Genre</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="delete_genre_name"></strong>?</p>
        <p class="text-danger small">This action cannot be undone.</p>
        
        <form action="#" method="POST">
          <input type="hidden" name="genre_id" id="delete_genre_id">
          <input type="hidden" name="genre_name" id="delete_genre_name_input">
          <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger" name="delete_genre">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

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

        const editAuthorStatus = <?php echo json_encode($editAuthorStatus); ?>;
        const editAuthorMessage = <?php echo json_encode($editAuthorMessage); ?>;

        if(editAuthorStatus == 'success') {
            Swal.fire({
            icon: 'success',
            title: 'Success',
            text: editAuthorMessage,
            confirmButtonText: 'OK'
        });
        } else if(editAuthorStatus == 'error') {
            Swal.fire({
            icon: 'error',
            title: 'Error',
            text: editAuthorMessage,
            confirmButtonText: 'OK'
        });
    }

        const editAuthorModal = document.getElementById('editAuthorModal');
        if(editAuthorModal) {
            editAuthorModal.addEventListener('show.bs.modal', function(event){
                const btn = event.relatedTarget;
                if(!btn){ 
                    return;
                }

                document.getElementById('edit_author_id').value = btn.getAttribute('data-author-id') || '';
                document.getElementById('edit_author_firstname').value = btn.getAttribute('data-author-firstname') || '';
                document.getElementById('edit_author_lastname').value = btn.getAttribute('data-author-lastname') || '';
                document.getElementById('edit_author_birthyear').value = btn.getAttribute('data-author-birthyear') || '';
                document.getElementById('edit_author_nationality').value = btn.getAttribute('data-author-nationality') || '';
            });
        }

    const deleteAuthorModal = document.getElementById('deleteAuthorModal');
    if(deleteAuthorModal) {
        deleteAuthorModal.addEventListener('show.bs.modal', function(event){
            const btn = event.relatedTarget;
            if(!btn){ 
                return;
            }

            document.getElementById('delete_author_id').value = btn.getAttribute('data-author-id') || '';
            document.getElementById('delete_author_name_input').value = btn.getAttribute('data-author-name') || '';
            document.getElementById('delete_author_name').textContent = btn.getAttribute('data-author-name') || '';
        });
    }

    const deleteGenreModal = document.getElementById('deleteGenreModal');
    if(deleteGenreModal) {
        deleteGenreModal.addEventListener('show.bs.modal', function(event){
            const btn = event.relatedTarget;
            if(!btn){ 
                return;
            }

            document.getElementById('delete_genre_id').value = btn.getAttribute('data-genre-id') || '';
            document.getElementById('delete_genre_name_input').value = btn.getAttribute('data-genre-name') || '';
            document.getElementById('delete_genre_name').textContent = btn.getAttribute('data-genre-name') || '';
        });
    }
</script>
</body>
</html>