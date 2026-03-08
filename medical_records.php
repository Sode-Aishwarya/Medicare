<?php
$page_title  = "Medical Records";
$active_page = "records";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id     = intval($_POST['record_id'] ?? 0);
  $pid    = intval($_POST['patient_id']);
  $did    = intval($_POST['doctor_id']);
  $date   = $conn->real_escape_string($_POST['record_date']);
  $diag   = $conn->real_escape_string($_POST['diagnosis'] ?? '');
  $treat  = $conn->real_escape_string($_POST['treatment'] ?? '');
  $fee    = floatval($_POST['fee'] ?? 0);

  if ($id>0) {
    $conn->query("UPDATE medical_record SET patient_id=$pid, doctor_id=$did,
      record_date='$date', diagnosis='$diag', treatment='$treat', fee=$fee
      WHERE record_id=$id");
  } else {
    $conn->query("INSERT INTO medical_record (patient_id, doctor_id, record_date, diagnosis, treatment, fee)
      VALUES ($pid,$did,'$date','$diag','$treat',$fee)");
  }
  header("Location: medical_records.php"); exit;
}

if (isset($_GET['delete_id'])) {
  $id=intval($_GET['delete_id']);
  $conn->query("DELETE FROM medical_record WHERE record_id=$id");
  header("Location: medical_records.php"); exit;
}

$edit=null;
if (isset($_GET['edit_id'])) {
  $id=intval($_GET['edit_id']);
  $res=$conn->query("SELECT * FROM medical_record WHERE record_id=$id");
  $edit=$res->fetch_assoc();
}

$patients=$conn->query("SELECT patient_id,name FROM patients ORDER BY name");
$doctors =$conn->query("SELECT doctor_id,name FROM doctor ORDER BY name");

$rows=$conn->query("
  SELECT r.*, p.name AS patient_name, d.name AS doctor_name
  FROM medical_record r
  JOIN patients p ON r.patient_id=p.patient_id
  JOIN doctor   d ON r.doctor_id=d.doctor_id
  ORDER BY r.record_date DESC, r.record_id DESC
");
?>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Record" : "Add Record"; ?></h5>
        <form method="post">
          <input type="hidden" name="record_id" value="<?php echo $edit['record_id'] ?? 0; ?>">

          <div class="mb-3">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-select" required>
              <option value="">Select</option>
              <?php $selp=$edit['patient_id'] ?? ''; mysqli_data_seek($patients,0);
              while($p=$patients->fetch_assoc()): ?>
                <option value="<?php echo $p['patient_id']; ?>" <?php if($selp==$p['patient_id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($p['name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Doctor</label>
            <select name="doctor_id" class="form-select" required>
              <option value="">Select</option>
              <?php $seld=$edit['doctor_id'] ?? ''; mysqli_data_seek($doctors,0);
              while($d=$doctors->fetch_assoc()): ?>
                <option value="<?php echo $d['doctor_id']; ?>" <?php if($seld==$d['doctor_id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($d['name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="record_date" class="form-control" required
                   value="<?php echo $edit['record_date'] ?? ''; ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Diagnosis</label>
            <input name="diagnosis" class="form-control"
                   value="<?php echo htmlspecialchars($edit['diagnosis'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Treatment</label>
            <textarea name="treatment" rows="3" class="form-control"><?php
              echo htmlspecialchars($edit['treatment'] ?? '');
            ?></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Fee</label>
            <input type="number" step="0.01" name="fee" class="form-control"
                   value="<?php echo htmlspecialchars($edit['fee'] ?? ''); ?>">
          </div>

          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Add"; ?> Record</button>
          <?php if($edit): ?><a href="medical_records.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a><?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Records</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Record</th><th>Patient</th><th>Doctor</th><th>Diagnosis</th><th>Fee</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php while($r=$rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo $r['record_id']."<br><small>".$r['record_date']."</small>"; ?></td>
                <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($r['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($r['diagnosis']); ?></td>
                <td>$<?php echo $r['fee']; ?></td>
                <td>
                  <a href="medical_records.php?edit_id=<?php echo $r['record_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="medical_records.php?delete_id=<?php echo $r['record_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this record?');">Delete</a>
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
