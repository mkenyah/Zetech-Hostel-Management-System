<?php
session_start();
include('db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    $query = "SELECT password FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data && password_verify($current_password, $data['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE students SET password = '$hashed_new_password' WHERE student_id = '$student_id'";
            if (mysqli_query($conn, $update)) {
                $message = "<div class='alert alert-success text-center' id='successMessage'>Password changed successfully.</div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Error updating password. Please try again.</div>";
            }
        } else {
            $message = "<div class='alert alert-warning text-center'>New passwords do not match.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger text-center'>Current password is incorrect.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    body {
      background-color: #fff;
      color: #0f032b;
      margin: 0;
      padding: 0;
      height: 100vh;
      overflow-x: hidden;
    }

    .sidebar {
      height: 100vh;
      background-color: #1c1142;
      padding-top: 60px;
      position: fixed;
      width: 220px;
      top: 0;
      left: 0;
      transition: transform 0.3s ease;
    }

    .sidebar a {
      color: white;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
      font-weight: bold;
    }

    .sidebar a:hover {
      background-color: #f4f4f7;
      color: #0f032b;
      border-radius: 5px;
    }

    .logout {
      position: absolute;
      bottom: 30px;
      left: 40px;
    }

    .logout a {
      background-color: white;
      color: #0f032b;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }

    .content {
      margin-left: 220px;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .profile-card {
      max-width: 400px;
      width: 100%;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
      background-color: #0f032b !important;
      border: none !important;
      color: white !important;
    }

    .btn-primary:hover {
      background-color: white !important;
      color: #0f032b !important;
      border: 1px solid #0f032b !important;
    }

    .menu-toggle {
      display: none;
    }

    h1{
        text-align: center;
    }

    #key {
      font-size: 70px;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
        position: fixed;
        top: 15px;
        left: 15px;
        background-color: white;
        border: none;
        padding: 10px 15px;
        z-index: 1001;
        font-weight: bold;
        border-radius: 5px;
      }

      .sidebar {
        transform: translateX(-100%);
        z-index: 1000;
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .content {
        margin-left: 0;
        padding-top: 80px;
      }
    }
  </style>
</head>
<body>

<button class="menu-toggle" onclick="toggleSidebar()">☰ Menu</button>

<div class="sidebar" id="sidebar">
  <a href="./stude_d.php">Dashboard</a>
  <div class="logout">
    <a href="login.php">Logout</a>
  </div>
</div>

<div class="content">
  <div class="profile-card">
  <h1>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <i id="key" class="fa fa-key" aria-hidden="true"></i>
    <h4 class="text-center mb-4">Change Password</h4>

    <?php echo $message; ?>

    <form method="post" action="">
      <div class="mb-3 position-relative">
        <label class="form-label">Current Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="currentPassword" name="current_password" required>
          <span class="input-group-text" onclick="togglePassword('currentPassword', this)">
            <i class="fa fa-eye"></i>
          </span>
        </div>
      </div>
      
      <div class="mb-3 position-relative">
        <label class="form-label">New Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="newPassword" name="new_password" required>
          <span class="input-group-text" onclick="togglePassword('newPassword', this)">
            <i class="fa fa-eye"></i>
          </span>
        </div>
      </div>

      <div class="mb-3 position-relative">
        <label class="form-label">Confirm New Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
          <span class="input-group-text" onclick="togglePassword('confirmPassword', this)">
            <i class="fa fa-eye"></i>
          </span>
        </div>
      </div>

      <div class="d-grid mt-4">
        <button type="submit" class="btn btn-primary">Update Password</button>
      </div>
    </form>
  </div>
</div>

<script>
  function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
  }

  function togglePassword(fieldId, el) {
    const input = document.getElementById(fieldId);
    const icon = el.querySelector('i');

    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = "password";
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  // Auto-hide success message after 3 seconds
  const successMessage = document.getElementById('successMessage');
  if (successMessage) {
    setTimeout(() => {
      successMessage.style.display = 'none';
    }, 3000);
  }
</script>

</body>
</html>
