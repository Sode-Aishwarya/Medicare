<?php
$page_title  = "Doctors";
$active_page = "doctors";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = intval($_POST['doctor_id'] ?? 0);
    $name     = $conn->real_escape_string($_POST['name'] ?? '');
    $spec     = $conn->real_escape_string($_POST['specialty'] ?? '');
    $phone    = $conn->real_escape_string($_POST['phone'] ?? '');
    $address  = $conn->real_escape_string($_POST['address'] ?? '');

    if ($id > 0) {
        $conn->query("UPDATE doctor SET name='$name', specialty='$spec', phone='$phone', address='$address' WHERE doctor_id=$id");
    } else {
        $conn->query("INSERT INTO doctor (name, specialty, phone, address)
                      VALUES ('$name','$spec','$phone','$address')");
    }
    header("Location: doctors.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM doctor WHERE doctor_id=$id");
    header("Location: doctors.php");
    exit;
}

$edit = null;
if (isset($_GET['edit_id'])) {
    $id   = intval($_GET['edit_id']);
    $res  = $conn->query("SELECT * FROM doctor WHERE doctor_id=$id");
    $edit = $res->fetch_assoc();
}

$rows = $conn->query("SELECT * FROM doctor ORDER BY doctor_id DESC");
?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Doctor" : "Add Doctor"; ?></h5>
        <form method="post">
          <input type="hidden" name="doctor_id" value="<?php echo $edit['doctor_id'] ?? 0; ?>">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required
                   value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Specialty</label>
            <input name="specialty" class="form-control"
                   value="<?php echo htmlspecialchars($edit['specialty'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control"
                   value="<?php echo htmlspecialchars($edit['phone'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?php
              echo htmlspecialchars($edit['address'] ?? '');
            ?></textarea>
          </div>
          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Add"; ?> Doctor</button>
          <?php if ($edit): ?>
            <a href="doctors.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Doctors</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th><th>Name</th><th>Specialty</th><th>Phone</th><th>Address</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($d = $rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo $d['doctor_id']; ?></td>
                <td><?php echo htmlspecialchars($d['name']); ?></td>
                <td><?php echo htmlspecialchars($d['specialty']); ?></td>
                <td><?php echo htmlspecialchars($d['phone']); ?></td>
                <td><?php echo htmlspecialchars($d['address']); ?></td>
                <td>
                  <a href="doctors.php?edit_id=<?php echo $d['doctor_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="doctors.php?delete_id=<?php echo $d['doctor_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this doctor?');">Delete</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include 'footer.php';
$conn->close();
?>
