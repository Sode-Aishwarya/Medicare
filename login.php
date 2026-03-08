<?php
include 'db.php';

session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $password = $conn->real_escape_string($_POST['password'] ?? '');

    $res = $conn->query("SELECT * FROM users WHERE username = '$username' LIMIT 1");
    if ($res && $res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // simple plain-text check for project demo
        if ($password === $user['password']) {
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            header("Location: dashboard.php");
            exit;
        }
    }
    $error = "Invalid username or password";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - MediCare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: radial-gradient(circle at top, #22c55e33, #0f172a);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .login-card {
      max-width: 420px;
      width: 100%;
      border-radius: 24px;
      border: none;
      box-shadow: 0 20px 40px rgba(15,23,42,0.35);
    }
  </style>
</head>
<body>
<div class="card login-card">
  <div class="card-body p-4">
    <div class="d-flex align-items-center mb-3">
      <div class="rounded-circle bg-success d-flex justify-content-center align-items-center me-2" style="width:40px;height:40px;">
        <span class="fw-bold text-white">M</span>
      </div>
      <div>
        <div class="fw-bold fs-5">MediCare</div>
        <div class="text-muted small">Clinic Management Login</div>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input name="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? 'admin'); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required value="admin123">
      </div>
      <button class="btn btn-success w-100">Login</button>
    </form>
  </div>
</div>
</body>
</html>
