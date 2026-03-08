<?php
$page_title  = "Staff";
$active_page = "staff";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id = intval($_POST['staff_id'] ?? 0);
  $name = $conn->real_escape_string($_POST['name'] ?? '');
  $role = $conn->real_escape_string($_POST['role'] ?? '');
  $phone= $conn->real_escape_string($_POST['phone'] ?? '');
  $addr = $conn->real_escape_string($_POST['address'] ?? '');

  if ($id>0) {
    $conn->query("UPDATE staff SET name='$name', role='$role', phone='$phone', address='$addr'
                  WHERE staff_id=$id");
  } else {
    $conn->query("INSERT INTO staff (name, role, phone, address)
                  VALUES ('$name','$role','$phone','$addr')");
  }
  header("Location: staff.php"); exit;
}

if (isset($_GET['delete_id'])) {
  $id=intval($_GET['delete_id']); $conn->query("DELETE FROM staff WHERE staff_id=$id");
  header("Location: staff.php"); exit;
}

$edit=null;
if (isset($_GET['edit_id'])) {
  $id=intval($_GET['edit_id']); $res=$conn->query("SELECT * FROM staff WHERE staff_id=$id"); $edit=$res->fetch_assoc();
}

$rows=$conn->query("SELECT * FROM staff ORDER BY staff_id DESC");
?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Staff" : "Add Staff"; ?></h5>
        <form method="post">
          <input type="hidden" name="staff_id" value="<?php echo $edit['staff_id'] ?? 0; ?>">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required
                   value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <input name="role" class="form-control"
                   value="<?php echo htmlspecialchars($edit['role'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone</label>
            <input name="phone" class="form-control"
                   value="<?php echo htmlspecialchars($edit['phone'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" rows="2" class="form-control"><?php
              echo htmlspecialchars($edit['address'] ?? '');
            ?></textarea>
          </div>
          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Add"; ?> Staff</button>
          <?php if($edit): ?><a href="staff.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a><?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Staff</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr><th>ID</th><th>Name</th><th>Role</th><th>Phone</th><th>Address</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php while($s=$rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo $s['staff_id']; ?></td>
                <td><?php echo htmlspecialchars($s['name']); ?></td>
                <td><?php echo htmlspecialchars($s['role']); ?></td>
                <td><?php echo htmlspecialchars($s['phone']); ?></td>
                <td><?php echo htmlspecialchars($s['address']); ?></td>
                <td>
                  <a href="staff.php?edit_id=<?php echo $s['staff_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="staff.php?delete_id=<?php echo $s['staff_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this staff member?');">Delete</a>
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
