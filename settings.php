<?php
$page_title  = "Settings";
$active_page = "settings";
include 'auth.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clinic_name = $conn->real_escape_string($_POST['clinic_name'] ?? '');
    $phone       = $conn->real_escape_string($_POST['phone'] ?? '');
    $address     = $conn->real_escape_string($_POST['address'] ?? '');
    $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
    $sms_reminders      = isset($_POST['sms_reminders']) ? 1 : 0;
    $daily_summary      = isset($_POST['daily_summary']) ? 1 : 0;
    $two_factor         = isset($_POST['two_factor']) ? 1 : 0;
    $session_timeout    = isset($_POST['session_timeout']) ? 1 : 0;

    $conn->query("
      UPDATE clinic_settings SET
        clinic_name='$clinic_name',
        phone='$phone',
        address='$address',
        email_notifications=$email_notifications,
        sms_reminders=$sms_reminders,
        daily_summary=$daily_summary,
        two_factor=$two_factor,
        session_timeout=$session_timeout
      WHERE id=1
    ");
}

$res = $conn->query("SELECT * FROM clinic_settings WHERE id=1");
$set = $res->fetch_assoc();

include 'header.php';
?>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card card-soft">
      <div class="card-body">
        <h5 class="card-title mb-3">Clinic Information</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Clinic Name</label>
            <input name="clinic_name" class="form-control"
                   value="<?php echo htmlspecialchars($set['clinic_name'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <input name="phone" class="form-control"
                   value="<?php echo htmlspecialchars($set['phone'] ?? ''); ?>">
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="2"><?php
              echo htmlspecialchars($set['address'] ?? '');
            ?></textarea>
          </div>

          <h6 class="mt-3 mb-2">Notifications</h6>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="email_notifications"
              <?php if(!empty($set['email_notifications'])) echo 'checked'; ?>>
            <label class="form-check-label">Email Notifications</label>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="sms_reminders"
              <?php if(!empty($set['sms_reminders'])) echo 'checked'; ?>>
            <label class="form-check-label">SMS Reminders</label>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="daily_summary"
              <?php if(!empty($set['daily_summary'])) echo 'checked'; ?>>
            <label class="form-check-label">Daily Summary</label>
          </div>

          <h6 class="mt-3 mb-2">Security</h6>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="two_factor"
              <?php if(!empty($set['two_factor'])) echo 'checked'; ?>>
            <label class="form-check-label">Two-Factor Authentication</label>
          </div>
          <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="session_timeout"
              <?php if(!empty($set['session_timeout'])) echo 'checked'; ?>>
            <label class="form-check-label">Session Timeout</label>
          </div>

          <button class="btn btn-success mt-3">Save Settings</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <!-- You can leave this blank or add extra info cards similar to UI -->
  </div>
</div>

<?php
include 'footer.php';
$conn->close();
?>
