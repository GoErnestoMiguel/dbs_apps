<?php
  require_once('../classes/database.php');
  session_start();

  $con = new database();

  $allbooks = $con->viewBooks();
  $allauthors = $con->viewAuthors();
  $allgenres = $con->viewGenres();

  $addBookStatus = null;
  $addBookMessage = '';

  if(isset($_POST['delete_books'])){
    $book_id = $_POST['book_id'];
    $book_title = $_POST['book_title'];

    try{
      $con->deletebooks($book_id);
      $_SESSION['success_message'] = $book_title . ' has been deleted in the database.';
      header('Location: books.php');
      exit();

    }catch(Exception $e){
      $error_message = "Cannot delete this book. It may have active loans or copies in use";
    }
  }

  if(isset($_POST['add_book'])) {
    $title = $_POST['book_title'];
    $isbn = $_POST['book_isbn'] ?: null;
    $year = $_POST['book_publication_year'] ?: null;
    $edition = $_POST['book_edition'] ?: null;
    $publisher = $_POST['book_publisher'] ?: null;

    try {
      $book_id = $con->addBooks($title, $isbn, $year, $edition, $publisher);

      $addBookStatus = 'success';
      $addBookMessage = 'Book added successfully.';

    }catch (Exception $e) {
      $addBookStatus = 'error';
      $addBookMessage = 'Error adding book.';
    }
  }

  $bookcopyStatus = null;
  $bookcopyMessage = '';

  if(isset($_POST['add_copy'])) {
    $book_id = $_POST['book_id'];
    $status = $_POST['c_status'];

    try {
      $copy_id = $con->addCopy($book_id, $status);

      $bookcopyStatus = 'success';
      $bookcopyMessage = 'Book copy added successfully.';
    }catch (Exception $e) {
      $bookcopyStatus = 'error';
      $bookcopyMessage = 'Error adding book copy.';
    }
  }

  $bookauthorStatus = null;
  $bookauthorMessage = '';

  if(isset($_POST['addAuthor'])) {
    $book_id = $_POST['book_id'];
    $author_id = $_POST['author_id'];

    try {
      $con->addBookAuthor($book_id, $author_id);

      $bookauthorStatus = 'success';
      $bookauthorMessage = 'Author assigned to book successfully.';
    }catch (Exception $e) {
      $bookauthorStatus = 'error';
      $bookauthorMessage = 'Error assigning author to book.';
    }
  }

  $bookgenreStatus = null;
  $bookgenreMessage = '';

  if(isset($_POST['addGenre'])) {
    $book_id = $_POST['book_id'];
    $genre_id = $_POST['genre_id'];

    try {
      $con->addGenre($genre_id, $book_id);

      $bookgenreStatus = 'success';
      $bookgenreMessage = 'Genre assigned to book successfully.';
    }catch (Exception $e) {
      $bookgenreStatus = 'error';
      $bookgenreMessage = 'Error assigning genre to book.';
    }
  }

  $editBookStatus = null;
  $editBookMessage = '';

  if(isset($_POST['edit_book'])) {
    $book_id = $_POST['edit_book_id'];
    $title = $_POST['edit_book_title'];
    $isbn = $_POST['edit_book_isbn'] ?: null;
    $year = $_POST['edit_book_publication_year'] ?: null;
    $publisher = $_POST['edit_book_publisher'] ?: null;

    try {
      $con->updateBook($book_id, $title, $isbn, $year, $publisher);

      $editBookStatus = 'success';
      $editBookMessage = 'Book updated successfully.';
    }catch (Exception $e) {
      $editBookStatus = 'error';
      $editBookMessage = 'Error updating book.';
    }
  }

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Books — Admin</title>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="../assets/css/style.css">

  <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="../sweetalert/dist/sweetalert2.css">

</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.php">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBooks">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navBooks" class="collapse navbar-collapse">
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
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

  </button>
</div>
<?php } ?>

<?php if(isset($_SESSION['success_message'])){ ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success! </strong> <?php echo $_SESSION['success_message']; ?>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">

  </button>
</div>
<?php 
  unset($_SESSION['success_message']);
} ?>

  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card p-4">
        <h5 class="mb-1">Add Book</h5>
        <p class="small-muted mb-3">Creates a row in <b>Books</b>.</p>

        <!-- Later in PHP: action="../php/books/create.php" method="POST" -->
        <form action="#" method="POST">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" name="book_isbn" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" name="book_publication_year" type="number" min="1500" max="2100" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Edition</label>
            <input class="form-control" name="book_edition" placeholder="optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" name="book_publisher" placeholder="optional">
          </div>
          <button name="add_book" class="btn btn-primary w-100" type="submit">Save Book</button>
        </form>
      </div>

      <div class="card p-4 mt-3">
        <h6 class="mb-2">Add Copy</h6>
        <p class="small-muted mb-3">Creates a row in <b>BookCopy</b>.</p>
        <!-- Later in PHP: action="../php/copies/create.php" method="POST" -->
        <form action="#" method="POST">
          <div class="mb-3">
            <label class="form-label">Book</label>
            <select class="form-select" name="book_id" required>
              <option value="">Select book</option>
              <?php
              foreach($allbooks as $book) {
                echo '<option value="' . $book['book_id'] . '">' . $book['book_title'] . '</option>';
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="c_status" required>
              <option value="">Set Status</option>
              <option value="AVAILABLE">AVAILABLE</option>
              <option value="ON_LOAN">ON_LOAN</option>
              <option value="LOST">LOST</option>
              <option value="DAMAGED">DAMAGED</option>
              <option value="REPAIR">REPAIR</option>
            </select>
          </div>
          <button name="add_copy" class="btn btn-outline-primary w-100" type="submit">Add Copy</button>
        </form>
      </div>
    </div>

    <div class="col-12 col-lg-8">
      <div class="card p-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
          <div>
            <h5 class="mb-1">Books List</h5>
            <div class="small-muted">NOT Placeholder rows. DO NOT Replace with PHP + MySQL output.</div>
          </div>
          <div class="d-flex gap-2">
            <input class="form-control" style="max-width: 260px;" placeholder="Search title / ISBN...">
            <button class="btn btn-outline-secondary">Search</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Book ID</th>
                <th>Title</th>
                <th>ISBN</th>
                <th>Year</th>
                <th>Publisher</th>
                <th>Copies</th>
                <th>Available</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $viewcopies = $con->viewCopies();
              foreach($viewcopies as $vc){
              echo'<tr>';
              echo'<td>'.$vc['book_id'].'</td>';
              echo'<td>'.$vc['book_title'].'</td>';
              echo'<td>'.$vc['book_isbn'].'</td>';
              echo'<td>'.$vc['book_publication_year'].'</td>';
              echo'<td>'.$vc['book_publisher'].'</td>';
              echo'<td class="text-center">'.$vc['Copies'].'</td>';
              echo'<td class="text-center"><span class="badge text-bg-success">'.$vc['Available_Copies'].'</span></td>';
              echo'<td class="text-end">';
              echo'<div class="btn-group" role="group">';

              echo'<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editBookModal"

                data-book-id="'.$vc['book_id'] . '" 
                data-book-title="'. $vc['book_title'] . '" 
                data-book-isbn="'. $vc['book_isbn'] . '" 
                data-book-publication-year="'. $vc['book_publication_year'] . '" 
                data-book-publisher="' . $vc['book_publisher'] . '" >Edit</button>';
                
              echo'<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBookModal"
              
              data-book-id="' . $vc['book_id'] . '"
              data-book-title="' . $vc['book_title'] . '"

              >Delete</button>';
              echo'</td>';
              echo'</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>

        <hr class="my-4">

        <div class="row g-3">
          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Author to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookAuthors</b>.</p>
              <!-- Later in PHP: action="../php/bookauthors/create.php" method="POST" -->
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php
                    foreach($allbooks as $book) {
                      echo '<option value="' . $book['book_id'] . '">' . $book['book_title'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="author_id" required>
                    <option value="">Select Author</option>
                    <?php
                    foreach($allauthors as $author) {
                      echo '<option value="' . $author['author_id'] . '">' . $author['author_firstname'] . ' ' . $author['author_lastname'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="col-12">
                  <button name="addAuthor" class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (book_id, author_id).</div>
            </div>
          </div>

          <div class="col-12 col-lg-6">
            <div class="border rounded p-3">
              <h6 class="mb-2">Assign Genre to Book</h6>
              <p class="small-muted mb-3">Creates a row in <b>BookGenre</b>.</p>
              <!-- Later in PHP: action="../php/bookgenre/create.php" method="POST" -->
              <form action="#" method="POST" class="row g-2">
                <div class="col-12 col-md-6">
                  <select class="form-select" name="book_id" required>
                    <option value="">Select book</option>
                    <?php
                    foreach($allbooks as $book) {
                      echo '<option value="' . $book['book_id'] . '">' . $book['book_title'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="col-12 col-md-6">
                  <select class="form-select" name="genre_id" required>
                    <option value="">Select genre</option>
                    <?php
                    foreach($allgenres as $genre) {
                      echo '<option value="' . $genre['genre_id'] . '">' . $genre['genre_name'] . '</option>';
                    }
                    ?>
                  </select>
                </div>
                <div class="col-12">
                  <button name="addGenre" class="btn btn-outline-primary w-100" type="submit">Assign</button>
                </div>
              </form>
              <div class="small-muted mt-2">Unique constraint prevents duplicate (genre_id, book_id).</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>

<!-- Edit Book Modal (NOT UI only) -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Later in PHP: load existing values -->
        <form action="#" method="POST">
          <input type="hidden" id="edit_book_id" name="edit_book_id">
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" id="edit_book_title" name="edit_book_title" required>
          </div>
          <div class="mb-3">
            <label class="form-label">ISBN</label>
            <input class="form-control" id="edit_book_isbn" name="edit_book_isbn">
          </div>
          <div class="mb-3">
            <label class="form-label">Publication Year</label>
            <input class="form-control" id="edit_book_publication_year" name="edit_book_publication_year" type="number" min="1500" max="2100">
          </div>
          <div class="mb-3">
            <label class="form-label">Publisher</label>
            <input class="form-control" id="edit_book_publisher" name="edit_book_publisher">
          </div>
          <button class="btn btn-primary w-100" name="edit_book" type="submit">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Book Modal (NOT UI only) -->
<div class="modal fade" id="deleteBookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Book</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete <strong id="delete_book_title"></strong>?</p>
        <p class="text-danger small">This action cannot be undone.</p>
        
        <form action="#" method="POST">
          <input type="hidden" name="book_id" id="delete_book_id">
          <input type="hidden" name="book_title" id="delete_book_titles">
          <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger" name="delete_books">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../sweetalert/dist/sweetalert2.js"></script>



<script>
  const addBookStatus = <?php echo json_encode($addBookStatus); ?>;
  const addBookMessage = <?php echo json_encode($addBookMessage); ?>;

  if(addBookStatus == 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: addBookMessage,
    });
  } else if(addBookStatus == 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: addBookMessage,
    });
  }

  const bookcopyStatus = <?php echo json_encode($bookcopyStatus); ?>;
  const bookcopyMessage = <?php echo json_encode($bookcopyMessage); ?>;

  if(bookcopyStatus == 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: bookcopyMessage,
    });
  } else if(bookcopyStatus == 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: bookcopyMessage,
    });
  }

  const bookauthorStatus = <?php echo json_encode($bookauthorStatus); ?>;
  const bookauthorMessage = <?php echo json_encode($bookauthorMessage); ?>;
    
  if(bookauthorStatus == 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: bookauthorMessage,
    });
  } else if(bookauthorStatus == 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: bookauthorMessage,
    });
  }

  const bookgenreStatus = <?php echo json_encode($bookgenreStatus); ?>;
  const bookgenreMessage = <?php echo json_encode($bookgenreMessage); ?>;

  if(bookgenreStatus == 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: bookgenreMessage,
    });
  } else if(bookgenreStatus == 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: bookgenreMessage,
    });
  } 

  const editBookStatus = <?php echo json_encode($editBookStatus); ?>;
  const editBookMessage = <?php echo json_encode($editBookMessage); ?>;

  if(editBookStatus == 'success') {
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: editBookMessage,
    });
  } else if(editBookStatus == 'error') {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: editBookMessage,
    });
  }

  const editBookModal = document.getElementById('editBookModal');

  if(editBookModal) {
    editBookModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;

      if(!button) {
        return;
      }

      document.getElementById('edit_book_id').value = button.getAttribute('data-book-id') || '';
      document.getElementById('edit_book_title').value = button.getAttribute('data-book-title') || '';
      document.getElementById('edit_book_isbn').value = button.getAttribute('data-book-isbn') || '';
      document.getElementById('edit_book_publication_year').value = button.getAttribute('data-book-publication-year') || '';
      document.getElementById('edit_book_publisher').value = button.getAttribute('data-book-publisher') || '';
    });
  }

  const deleteBookModal = document.getElementById('deleteBookModal');
  deleteBookModal.addEventListener('show.bs.modal', function(event){

    const btn = event.relatedTarget;
    if(!btn){ 
      return;
    }

    document.getElementById('delete_book_id').value = btn.getAttribute('data-book-id') || '';
    document.getElementById('delete_book_titles').value = btn.getAttribute('data-book-title') || '';
    
    document.getElementById('delete_book_title').textContent = btn.getAttribute('data-book-title') || '';

  });

</script>

</body>
</html>