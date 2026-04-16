<?php
require_once('../classes/database.php');
$con = new database();

$allusers = $con->viewBorrowerUser();

$borrowerCreateStatus = null;
$borrowerCreateMessage = '';

if(isset($_POST['add_borrower'])){

  // 1. Collect and validate input from user
  $firstname = $_POST['borrower_firstname'];
  $lastname = $_POST['borrower_lastname'];
  $email = $_POST['borrower_email'];
  $phone = $_POST['borrower_phone_number'];
  $member_since = $_POST['borrower_member_since'];
  $is_active = $_POST['is_active'];
  $temp_password = $_POST['temp_password'];

  // 2. Hashed the password
  $password_hash = password_hash($temp_password, PASSWORD_DEFAULT);
try {
  // 3. Insert into Users table and get a new user_id
  $user_id = $con->insertUser($email,$password_hash,$is_active);

  // 4. Insert into Borrowers table and get a new borrower_id
  $borrower_id = $con->insertBorrower($firstname, $lastname, $email, $phone, $member_since, $is_active);

  // 5. Insert into BorrowerUser mapping(lingking) table
  $con->insertBorrowerUser($user_id,  $borrower_id);

  $borrowerCreateStatus = 'success';
  $borrowerCreateMessage = 'Borrower created successfully.';

} catch (Exception $e) { 
  $borrowerCreateStatus = 'error';
  $borrowerCreateMessage = 'Error creating borrower.';
}
}

$borroweraddAddressStatus = null;
$borroweraddAddressMessage = '';

if(isset($_POST['add_borrower_address'])){

  $borrower_id = $_POST['borrower_id'];
  $house_number = $_POST['ba_house_number'];
  $street = $_POST['ba_street'];
  $barangay = $_POST['ba_barangay'];
  $city = $_POST['ba_city'];
  $province = $_POST['ba_province'];
  $postal_code = $_POST['ba_postal_code'];
  $is_primary = $_POST['is_primary'];

  try{
    $ba_id = $con->insertBorrowerAddress($borrower_id, $house_number, $street, $barangay, $city, $province, $postal_code, $is_primary);
    
    $borroweraddAddressStatus = 'success';
    $borroweraddAddressMessage = 'Address added successfuly';

  }catch (Exception $e) {
    $borroweraddAddressStatus = 'error';
    $borroweraddAddressMessage = 'Error adding address to borrower.';
  }
}
?>




<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Borrowers — Admin</title>
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
  <link rel="stylesheet" href="../assets/css/style.css"> 

  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../sweetalert/dist/sweetalert2.css">

</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="admin-dashboard.html">Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navBorrowersAdmin">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navBorrowersAdmin" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="admin-dashboard.html">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="books.php">Books</a></li>
        <li class="nav-item"><a class="nav-link active" href="borrowers.php">Borrowers</a></li>
        <li class="nav-item"><a class="nav-link" href="checkout.html">Checkout</a></li>
        <li class="nav-item"><a class="nav-link" href="return.html">Return</a></li>
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
    <div class="col-12 col-lg-12">
      <div class="card p-4">
        <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end mb-3">
          <div>
            <h5 class="mb-1">Borrowers List</h5>
            <div class="small-muted">Includes account status and mapping.</div>
          </div>
          <div class="d-flex gap-2">
            <input class="form-control" style="max-width: 260px;" placeholder="Search name / email...">
            <button class="btn btn-outline-secondary">Search</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Borrower ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Borrower Active</th>
                <th>User Account</th>
                <th>User Active</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Juan Dela Cruz</td>
                <td>juan.delacruz@samplemail.com</td>
                <td><span class="badge text-bg-success">Yes</span></td>
                <td><span class="badge text-bg-primary">Linked</span></td>
                <td><span class="badge text-bg-success">Yes</span></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resetPassModal">Reset Password</button>
                  <button class="btn btn-sm btn-outline-secondary">Toggle Active</button>
                </td>
              </tr>
              <tr>
                <td>5</td>
                <td>Paolo Garcia</td>
                <td>paolo.garcia@samplemail.com</td>
                <td><span class="badge text-bg-secondary">No</span></td>
                <td><span class="badge text-bg-primary">Linked</span></td>
                <td><span class="badge text-bg-secondary">No</span></td>
                <td class="text-end">
                  <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resetPassModal">Reset Password</button>
                  <button class="btn btn-sm btn-outline-secondary">Toggle Active</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="small-muted mt-2">
          Rule reminder: each borrower must have exactly one account (BorrowerUser 1-to-1 mapping).
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <div class="card p-4 h-100">
            <h5 class="mb-1">Register Borrower (with Account)</h5>
            <p class="small-muted mb-3">Creates <b>Borrowers</b> + <b>Users</b> + <b>BorrowerUser</b>.</p>

            <!-- Later in PHP: action="../php/borrowers/register.php" method="POST" -->
            <form action="#" method="POST">
              <div class="row g-2">
                <div class="col-12 col-md-6">
                  <label class="form-label">First Name</label>
                  <input class="form-control" name="borrower_firstname" required>
                </div>
                
                <div class="col-12 col-md-6">
                  <label class="form-label">Last Name</label>
                  <input class="form-control" name="borrower_lastname" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Email (also username)</label>
                  <input class="form-control" name="borrower_email" type="email" required>
                </div>

                <div class="col-12">
                  <label class="form-label">Mobile Number</label>
                  <input class="form-control" name="borrower_phone_number" placeholder="09xxxxxxxxx" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Member Since</label>
                  <input class="form-control" name="borrower_member_since" type="date" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Active</label>
                  <select class="form-select" name="is_active">
                    <option value="1" selected>Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>

                <hr class="my-2">

                <div class="col-12">
                  <label class="form-label">Temporary Password</label>
                  <input class="form-control" name="temp_password" type="password" required>
                  <div class="small-muted mt-1">In PHP: hash this and store in Users.password_hash</div>
                </div>
              </div>

              <button name="add_borrower" class="btn btn-primary w-100 mt-3" type="submit">Create Borrower Account</button>
            </form>
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <div class="card p-4 h-100">
            <h6 class="mb-2">Add Borrower Address</h6>
            <p class="small-muted mb-3">Creates <b>BorrowerAddress</b> row.</p>
            <!-- Later in PHP: action="../php/addresses/create.php" method="POST" -->
            <form action="#" method="POST" class="row g-2">
              <div class="col-12">
                <label class="form-label">Borrower</label>

                <select class="form-select" name="borrower_id" required>
                  <option value="">Select borrower</option>
                  <?php
                  foreach($allusers as $borrower){
                  echo '<option value="'.$borrower['borrower_id'] .'">' .$borrower['borrower_firstname'].' '.$borrower['borrower_lastname']. '</option>';
                  }
                  ?>
                  </select>
              </div>
              <div class="col-6">
                <label class="form-label">House #</label>
                <input class="form-control" name="ba_house_number">
              </div>
              <div class="col-6">
                <label class="form-label">Street</label>
                <input class="form-control" name="ba_street">
              </div>
              <div class="col-12">
                <label class="form-label">Barangay</label>
                <input class="form-control" name="ba_barangay">
              </div>
              <div class="col-6">
                <label class="form-label">City</label>
                <input class="form-control" name="ba_city">
              </div>
              <div class="col-6">
                <label class="form-label">Province</label>
                <input class="form-control" name="ba_province">
              </div>
              <div class="col-6">
                <label class="form-label">Postal Code</label>
                <input class="form-control" name="ba_postal_code">
              </div>
              <div class="col-6">
                <label class="form-label">Primary?</label>
                <select class="form-select" name="is_primary">
                  <option value="1">Yes</option>
                  <option value="0" selected>No</option>
                </select>
              </div>
              <div class="col-12">
                <button name="add_borrower_address" class="btn btn-outline-primary w-100" type="submit">Add Address</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    
  </div>
</main>

<!-- Reset Password Modal (UI only) -->
<div class="modal fade" id="resetPassModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reset Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Later in PHP: action="../php/users/reset_password.php" method="POST" -->
        <form action="#" method="POST">
          <div class="mb-3">
            <label class="form-label">User ID</label>
            <input class="form-control" type="number" value="2">
          </div>
          <div class="mb-3">
            <label class="form-label">New Temporary Password</label>
            <input class="form-control" type="password" placeholder="temporary password">
          </div>
          <button class="btn btn-primary w-100" type="button">Reset</button>
        </form>
        <div class="small-muted mt-2">In PHP: hash and update Users.password_hash.</div>
      </div>
    </div>
  </div>
</div>

<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

<script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../sweetalert/dist/sweetalert2.js"></script>

<script>

  const createStatus = <?php echo json_encode($borrowerCreateStatus)?>;
  const createMessage = <?php echo json_encode($borrowerCreateMessage)?>;

  if(createStatus == 'success'){
    Swal.fire({
      icon: 'success',
      title: 'Success',
        text: createMessage,
        confirmButtonText: 'OK'
    });
  }else if(createStatus == 'error'){
    Swal.fire({
      icon: 'error',
      title: 'Error',
        text: createMessage,
        confirmButtonText: 'OK'
    });
  }

</script>

<script>
  const addressStatus = <?php echo json_encode($borroweraddAddressStatus)?>;
  const addressMessage = <?php echo json_encode($borroweraddAddressMessage)?>;

  if(addressStatus == 'success'){
    Swal.fire({
      icon: 'success',
      title: 'Success',
        text: addressMessage,
        confirmButtonText: 'OK'
    });
  }else if(addressStatus == 'error'){
    Swal.fire({
      icon: 'error',
      title: 'Error',
        text: createMessage,
        confirmButtonText: 'OK'
    });
  }
</script>

</body>
</html>