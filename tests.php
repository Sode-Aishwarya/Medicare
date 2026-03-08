<?php
$page_title  = "Tests";
$active_page = "tests";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $id   = intval($_POST['test_id'] ?? 0);
  $pid  = intval($_POST['patient_id']);
  $did  = intval($_POST['doctor_id']);
  $date = $conn->real_escape_string($_POST['test_date']);
  $name = $conn->real_escape_string($_POST['test_name'] ?? '');
  $report = $conn->real_escape_string($_POST['report'] ?? '');
  $bill = floatval($_POST['bill'] ?? 0);
  $status = $conn->real_escape_string($_POST['status'] ?? '');

  if ($id>0) {
    $conn->query("UPDATE tests SET patient_id=$pid, doctor_id=$did, test_date='$date',
      test_name='$name', report='$report', bill=$bill, status='$status'
      WHERE test_id=$id");
  } else {
    $conn->query("INSERT INTO tests (patient_id, doctor_id, test_date, test_name, report, bill, status)
      VALUES ($pid,$did,'$date','$name','$report',$bill,'$status')");
  }
  header("Location: tests.php"); exit;
}

if (isset($_GET['delete_id'])) {
  $id=intval($_GET['delete_id']); $conn->query("DELETE FROM tests WHERE test_id=$id");
  header("Location: tests.php"); exit;
}

$edit=null;
if (isset($_GET['edit_id'])) {
  $id=intval($_GET['edit_id']);
  $res=$conn->query("SELECT * FROM tests WHERE test_id=$id");
  $edit=$res->fetch_assoc();
}

$patients=$conn->query("SELECT patient_id,name FROM patients ORDER BY name");
$doctors =$conn->query("SELECT doctor_id,name FROM doctor ORDER BY name");

$rows=$conn->query("
  SELECT t.*, p.name AS patient_name, d.name AS doctor_name
  FROM tests t
  JOIN patients p ON t.patient_id=p.patient_id
  JOIN doctor   d ON t.doctor_id=d.doctor_id
  ORDER BY t.test_date DESC, t.test_id DESC
");
?>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Test" : "Order Test"; ?></h5>
        <form method="post">
          <input type="hidden" name="test_id" value="<?php echo $edit['test_id'] ?? 0; ?>">

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
            <label class="form-label">Ordered By (Doctor)</label>
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
            <input type="date" name="test_date" class="form-control" required
                   value="<?php echo $edit['test_date'] ?? ''; ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Test Name</label>
            <input name="test_name" class="form-control"
                   value="<?php echo htmlspecialchars($edit['test_name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Report</label>
            <input name="report" class="form-control"
                   value="<?php echo htmlspecialchars($edit['report'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Bill</label>
            <input type="number" step="0.01" name="bill" class="form-control"
                   value="<?php echo htmlspecialchars($edit['bill'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <?php $s=$edit['status'] ?? ''; ?>
              <option value="">Select</option>
              <option value="Normal"      <?php if($s=='Normal')      echo 'selected'; ?>>Normal</option>
              <option value="Pending"     <?php if($s=='Pending')     echo 'selected'; ?>>Pending</option>
              <option value="Abnormal"    <?php if($s=='Abnormal')    echo 'selected'; ?>>Abnormal</option>
              <option value="Completed"   <?php if($s=='Completed')   echo 'selected'; ?>>Completed</option>
            </select>
          </div>

          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Save"; ?> Test</button>
          <?php if($edit): ?><a href="tests.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a><?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Tests</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Test</th><th>Patient</th><th>Doctor</th><th>Report</th><th>Bill</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php while($t=$rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($t['test_name'])."<br><small>".$t['test_date']."</small>"; ?></td>
                <td><?php echo htmlspecialchars($t['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($t['doctor_name']); ?></td>
                <td><?php echo htmlspecialchars($t['report']); ?></td>
                <td>$<?php echo $t['bill']; ?></td>
                <td><?php echo htmlspecialchars($t['status']); ?></td>
                <td>
                  <a href="tests.php?edit_id=<?php echo $t['test_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="tests.php?delete_id=<?php echo $t['test_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this test?');">Delete</a>
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
