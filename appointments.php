<?php
$page_title  = "Appointments";
$active_page = "appointments";
include 'db.php';
include 'auth.php';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = intval($_POST['appointment_id'] ?? 0);
    $pid   = intval($_POST['patient_id']);
    $did   = intval($_POST['doctor_id']);
    $date  = $conn->real_escape_string($_POST['appointment_date']);
    $time  = $conn->real_escape_string($_POST['appointment_time']);
    $reason= $conn->real_escape_string($_POST['reason'] ?? '');
    $status= $conn->real_escape_string($_POST['status'] ?? '');

    if ($id > 0) {
       $conn->query("UPDATE appointment SET patient_id=$pid, doctor_id=$did,
           appointment_date='$date', appointment_time='$time',
           reason='$reason', status='$status'
           WHERE appointment_id=$id");
    } else {
       $conn->query("INSERT INTO appointment (patient_id, doctor_id, appointment_date, appointment_time, reason, status)
                     VALUES ($pid,$did,'$date','$time','$reason','$status')");
    }
    header("Location: appointments.php");
    exit;
}

if (isset($_GET['delete_id'])) {
   $id = intval($_GET['delete_id']);
   $conn->query("DELETE FROM appointment WHERE appointment_id=$id");
   header("Location: appointments.php");
   exit;
}

$edit = null;
if (isset($_GET['edit_id'])) {
   $id = intval($_GET['edit_id']);
   $res = $conn->query("SELECT * FROM appointment WHERE appointment_id=$id");
   $edit = $res->fetch_assoc();
}

$patients = $conn->query("SELECT patient_id, name FROM patients ORDER BY name");
$doctors  = $conn->query("SELECT doctor_id, name FROM doctor ORDER BY name");

$rows = $conn->query("
  SELECT a.*, p.name AS patient_name, d.name AS doctor_name
  FROM appointment a
  JOIN patients p ON a.patient_id = p.patient_id
  JOIN doctor   d ON a.doctor_id  = d.doctor_id
  ORDER BY a.appointment_date, a.appointment_time
");
?>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3"><?php echo $edit ? "Edit Appointment" : "New Appointment"; ?></h5>
        <form method="post">
          <input type="hidden" name="appointment_id" value="<?php echo $edit['appointment_id'] ?? 0; ?>">

          <div class="mb-3">
            <label class="form-label">Patient</label>
            <select name="patient_id" class="form-select" required>
              <option value="">Select patient</option>
              <?php
              $selp = $edit['patient_id'] ?? '';
              mysqli_data_seek($patients, 0);
              while ($p = $patients->fetch_assoc()): ?>
                <option value="<?php echo $p['patient_id']; ?>"
                  <?php if($selp==$p['patient_id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($p['name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Doctor</label>
            <select name="doctor_id" class="form-select" required>
              <option value="">Select doctor</option>
              <?php
              $seld = $edit['doctor_id'] ?? '';
              mysqli_data_seek($doctors, 0);
              while ($d = $doctors->fetch_assoc()): ?>
                <option value="<?php echo $d['doctor_id']; ?>"
                  <?php if($seld==$d['doctor_id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($d['name']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="appointment_date" class="form-control" required
                   value="<?php echo $edit['appointment_date'] ?? ''; ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Time</label>
            <input type="time" name="appointment_time" class="form-control" required
                   value="<?php echo substr($edit['appointment_time'] ?? '',0,5); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Reason</label>
            <input name="reason" class="form-control"
                   value="<?php echo htmlspecialchars($edit['reason'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <?php $s = $edit['status'] ?? ''; ?>
              <option value="">Select</option>
              <option value="Scheduled"   <?php if($s=='Scheduled')   echo 'selected'; ?>>Scheduled</option>
              <option value="In Progress" <?php if($s=='In Progress') echo 'selected'; ?>>In Progress</option>
              <option value="Completed"   <?php if($s=='Completed')   echo 'selected'; ?>>Completed</option>
              <option value="Cancelled"   <?php if($s=='Cancelled')   echo 'selected'; ?>>Cancelled</option>
            </select>
          </div>

          <button class="btn btn-success w-100"><?php echo $edit ? "Update" : "Create"; ?> Appointment</button>
          <?php if ($edit): ?>
            <a href="appointments.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
          <?php endif; ?>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card card-soft">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-3">
          <h5 class="card-title mb-0">All Appointments</h5>
          <span class="text-muted small">Total: <?php echo $rows->num_rows; ?></span>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($a = $rows->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                <td><?php echo htmlspecialchars($a['doctor_name']); ?></td>
                <td><?php echo $a['appointment_date']; ?></td>
                <td><?php echo substr($a['appointment_time'],0,5); ?></td>
                <td><?php echo htmlspecialchars($a['reason']); ?></td>
                <td><?php echo htmlspecialchars($a['status']); ?></td>
                <td>
                  <a href="appointments.php?edit_id=<?php echo $a['appointment_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="appointments.php?delete_id=<?php echo $a['appointment_id']; ?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this appointment?');">Delete</a>
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
