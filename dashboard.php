<?php
$page_title  = "Dashboard";
$active_page = "dashboard";
include 'db.php';
include 'header.php';

$patients_count   = $conn->query("SELECT COUNT(*) c FROM patients")->fetch_assoc()['c'];
$doctors_count    = $conn->query("SELECT COUNT(*) c FROM doctor")->fetch_assoc()['c'];
$appointments_today = $conn->query("SELECT COUNT(*) c FROM appointment WHERE appointment_date = CURDATE()")->fetch_assoc()['c'];
$staff_count      = $conn->query("SELECT COUNT(*) c FROM staff")->fetch_assoc()['c'];

$today_appts = $conn->query("
  SELECT a.*, p.name AS patient_name, d.name AS doctor_name
  FROM appointment a
  JOIN patients p ON a.patient_id = p.patient_id
  JOIN doctor   d ON a.doctor_id  = d.doctor_id
  WHERE a.appointment_date = CURDATE()
  ORDER BY appointment_time
");

$recent_patients = $conn->query("SELECT * FROM patients ORDER BY patient_id DESC LIMIT 5");
?>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card card-soft">
      <div class="card-body">
        <div class="text-muted small">Total Patients</div>
        <div class="display-6 fw-bold"><?php echo $patients_count; ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-soft">
      <div class="card-body">
        <div class="text-muted small">Active Doctors</div>
        <div class="display-6 fw-bold"><?php echo $doctors_count; ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-soft">
      <div class="card-body">
        <div class="text-muted small">Today’s Appointments</div>
        <div class="display-6 fw-bold"><?php echo $appointments_today; ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-soft">
      <div class="card-body">
        <div class="text-muted small">Staff Members</div>
        <div class="display-6 fw-bold"><?php echo $staff_count; ?></div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3">Today’s Appointments</h5>
        <?php if ($today_appts->num_rows == 0): ?>
          <p class="text-muted">No appointments today.</p>
        <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php while ($a = $today_appts->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <strong><?php echo htmlspecialchars($a['patient_name']); ?></strong><br>
                <small>with <?php echo htmlspecialchars($a['doctor_name']); ?></small>
              </div>
              <div class="text-end">
                <div><?php echo substr($a['appointment_time'],0,5); ?></div>
                <small class="text-muted"><?php echo htmlspecialchars($a['status']); ?></small>
              </div>
            </li>
          <?php endwhile; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3">Recent Patients</h5>
        <ul class="list-group list-group-flush">
          <?php while ($p = $recent_patients->fetch_assoc()): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <strong><?php echo htmlspecialchars($p['name']); ?></strong><br>
                <small><?php echo htmlspecialchars($p['gender'] . " • " . $p['age'] . " yrs"); ?></small>
              </div>
              <div class="text-end">
                <small class="text-muted"><?php echo htmlspecialchars($p['phone']); ?></small>
              </div>
            </li>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<?php
include 'footer.php';
$conn->close();
?>
