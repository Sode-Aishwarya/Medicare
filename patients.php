<?php
$page_title  = "Patients";
$active_page = "patients";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id      = intval($_POST['patient_id'] ?? 0);
    $name    = $conn->real_escape_string($_POST['name'] ?? '');
    $gender  = $conn->real_escape_string($_POST['gender'] ?? '');
    $age     = intval($_POST['age'] ?? 0);
    $phone   = $conn->real_escape_string($_POST['phone'] ?? '');
    $address = $conn->real_escape_string($_POST['address'] ?? '');
    $doc_id  = ($_POST['doctor_id'] === '' ? "NULL" : intval($_POST['doctor_id']));

    if ($id > 0) {
        $conn->query("UPDATE patients SET
            name='$name', gender='$gender', age=$age, phone='$phone', address='$address', doctor_id=$doc_id
            WHERE patient_id=$id");
    } else {
        $conn->query("INSERT INTO patients (name, gender, age, phone, address, doctor_id)
                      VALUES ('$name','$gender',$age,'$phone','$address',$doc_id)");
    }
    header("Location: patients.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM patients WHERE patient_id=$id");
    header("Location: patients.php");
    exit;
}

$edit = null;
if (isset($_GET['edit_id'])) {
    $id   = intval($_GET['edit_id']);
    $res  = $conn->query("SELECT * FROM patients WHERE patient_id=$id");
    $edit = $res->fetch_assoc();
}

$doctors = $conn->query("SELECT * FROM doctor ORDER BY name");
$rows = $conn->query("
  SELECT p.*, d.name AS doctor_name, d.specialty
  FROM patients p
  LEFT JOIN doctor d ON p.doctor_id = d.doctor_id
  ORDER BY p.patient_id DESC
");
?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Patient" : "Add Patient"; ?></h5>
        <form method="post">
          <input type="hidden" name="patient_id" value="<?php echo $edit['patient_id'] ?? 0; ?>">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input name="name" class="form-control" required
                   value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Gender</label>
            <select name="gender" class="form-select">
              <?php $g = $edit['gender'] ?? ''; ?>
              <option value="">Select</option>
              <option value="Male"   <?php if($g=='Male')   echo 'selected'; ?>>Male</option>
              <option value="Female" <?php if($g=='Female') echo 'selected'; ?>>Female</option>
              <option value="Other"  <?php if($g=='Other')  echo 'selected'; ?>>Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Age</label>
            <input type="number" name="age" class="form-control"
                   value="<?php echo htmlspecialchars($edit['age'] ?? ''); ?>">
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
          <div class="mb-3">
            <label class="form-label">Assigned Doctor</label>
            <select name="doctor_id" class="form-select">
              <option value="">-- None --</option>
              <?php
              $sel = $edit['doctor_id'] ?? '';
              mysqli_data_seek($doctors, 0);
              while ($d = $doctors->fetch_assoc()): ?>
                <option value="<?php echo $d['doctor_id']; ?>"
                  <?php if($sel == $d['doctor_id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($d['name']." (".$d['specialty'].")"); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Add"; ?> Patient</button>
          <?php if ($edit): ?>
            <a href="patients.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Patients</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>ID</th><th>Name</th><th>Gender / Age</th><th>Phone</th><th>Doctor</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($p = $rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo $p['patient_id']; ?></td>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['gender']." / ".$p['age']); ?></td>
                <td><?php echo htmlspecialchars($p['phone']); ?></td>
                <td>
                  <?php if ($p['doctor_name']): ?>
                    <?php echo htmlspecialchars($p['doctor_name']); ?><br>
                    <small class="text-muted"><?php echo htmlspecialchars($p['specialty']); ?></small>
                  <?php else: ?>
                    <span class="text-muted">Not assigned</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="patients.php?edit_id=<?php echo $p['patient_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="patients.php?delete_id=<?php echo $p['patient_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this patient?');">Delete</a>
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
