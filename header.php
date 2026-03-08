<?php
// usage: set $page_title and $active_page before including this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?php echo $page_title ?? "MediCare"; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
      background-color: #f5f7fb;
    }
    .sidebar {
      min-height: 100vh;
      background: #020617;
      color: #fff;
    }
    .sidebar .brand {
      font-weight: 700;
      font-size: 1.3rem;
      padding: 1.2rem 1.5rem;
      border-bottom: 1px solid rgba(148,163,184,0.25);
    }
    .sidebar a {
      color: #e5e7eb;
      text-decoration: none;
      display: block;
      padding: 0.7rem 1.5rem;
      border-radius: 999px;
      margin: 0.2rem 0.7rem;
      font-size: 0.95rem;
    }
    .sidebar a.active,
    .sidebar a:hover {
      background: #22c55e;
      color: #022c22;
    }
    .topbar {
      height: 64px;
      background: #ffffff;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
    }
    .page-title {
      font-size: 1.5rem;
      font-weight: 600;
    }
    .card-soft {
      border-radius: 20px;
      border: none;
      box-shadow: 0 10px 25px rgba(15,23,42,0.07);
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- SIDEBAR -->
    <nav class="col-md-2 sidebar d-none d-md-block">
      <div class="brand d-flex align-items-center">
        <div class="rounded-circle bg-success d-flex justify-content-center align-items-center me-2" style="width:36px;height:36px;">
          <span class="fw-bold">M</span>
        </div>
        <div>
          MediCare<br>
          <small class="text-muted" style="color:#9ca3af!important;">Clinic Management</small>
        </div>
      </div>
      <div class="mt-3">
        <a href="dashboard.php"       class="<?php echo ($active_page=='dashboard') ? 'active' : ''; ?>">Dashboard</a>
        <a href="doctors.php"         class="<?php echo ($active_page=='doctors') ? 'active' : ''; ?>">Doctors</a>
        <a href="patients.php"        class="<?php echo ($active_page=='patients') ? 'active' : ''; ?>">Patients</a>
        <a href="appointments.php"    class="<?php echo ($active_page=='appointments') ? 'active' : ''; ?>">Appointments</a>
        <a href="medical_records.php" class="<?php echo ($active_page=='records') ? 'active' : ''; ?>">Medical Records</a>
        <a href="tests.php"           class="<?php echo ($active_page=='tests') ? 'active' : ''; ?>">Tests</a>
        <a href="staff.php"           class="<?php echo ($active_page=='staff') ? 'active' : ''; ?>">Staff</a>
        <hr style="border-color:rgba(148,163,184,0.2);">
        <a href="settings.php"        class="<?php echo ($active_page=='settings') ? 'active' : ''; ?>">Settings</a>
        <a href="logout.php">Logout</a>
      </div>
    </nav>

    <!-- MAIN -->
    <main class="col-md-10 ms-sm-auto px-0">
      <div class="topbar">
        <div class="page-title">
          <?php echo $page_title ?? "Dashboard"; ?>
        </div>
        <div class="d-flex align-items-center gap-3">
          <input type="search" class="form-control form-control-sm" placeholder="Search..." style="width:260px;">
          <div class="text-end me-2 small">
            <div><?php echo $_SESSION['username'] ?? 'Admin User'; ?></div>
            <div class="text-muted">Administrator</div>
          </div>
          <div class="rounded-circle d-flex justify-content-center align-items-center" style="width:40px;height:40px;background:#e0fbea;">
            <span class="fw-bold" style="color:#059669;">
              <?php
              $u = $_SESSION['username'] ?? 'AD';
              echo strtoupper(substr($u,0,2));
              ?>
            </span>
          </div>
        </div>
      </div>
      <div class="p-4">
