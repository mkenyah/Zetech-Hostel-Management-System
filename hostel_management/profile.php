<?php
session_start();
include('db.php');

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$message = "";

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);

    $update_sql = "UPDATE students 
                   SET full_name='$full_name', email='$email', contact='$contact' 
                   WHERE student_id='$student_id'";

if (mysqli_query($conn, $update_sql)) {
    $message = "<div class='alert alert-success text-center' id='successMessage'>Profile updated successfully.</div>";
} else {
    $message = "<div class='alert alert-danger text-center'>Error updating profile: " . mysqli_error($conn) . "</div>";
}

}

// Get student details
$sql = "SELECT * FROM students WHERE student_id = '$student_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);




?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    body {
      background-color: #f5f6fa;
      padding-top: 50px;
    }

    .profile-card {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .btn-primary {
      background-color: #0f032b !important;
      border: none;
    }

    .btn-primary:hover {
      background-color: white !important;
      color: #0f032b !important;
      border: 1px solid #0f032b !important;
    }

    #userprofile {
      font-size: 90px;
      text-align: center;
      color: #0f032b;
      display: block;
      margin: 0 auto 20px;
    }

    #editToggle {
      font-size: 30px;
      position: absolute;
      top: 20px;
      right: 20px;
      cursor: pointer;
      color: #0f032b;
    }



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
      text-align: center;
    }
    img {
      margin-top: -50px;
      max-width: 100%;
      height: auto;
    }
    .card {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
      overflow-x: auto;
    }
    .btn-primary {
      background-color: #0f032b !important;
      color: white !important;
      border: none !important;
    }
    .btn-primary:hover {
      background-color: white !important;
      border: 1px solid #0f032b !important;
      color: #0f032b !important;
    }
    td, th {
      text-align: center;
    }
    .menu-toggle {
      display: none;
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

    .table-responsive {
      overflow-x: auto;
      width: 100%;
    }

    .container {
      max-width: 100%;
    }

    h1{
        text-align: center;
    }
  </style>
  </style>
</head>
<body>

<button class="menu-toggle" onclick="toggleSidebar()">☰ Menu</button>
<div class="sidebar" id="sidebar">
  <a href="./stude_d.php">Dashboard</a>
  <a href="./change_password.php">change password</a>
  <!-- <a href="#">My Allocation</a> -->
  <div class="logout">
    <a href="login.php">Logout</a>
  </div>
</div>

<div class="profile-card">
  <i id="userprofile" class="fa fa-user-circle" aria-hidden="true"></i>
  <i id="editToggle" class="fa fa-pencil-square-o" aria-hidden="true" title="Edit Profile"></i>

 
  <h1><?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

  <?php echo $message; ?>

  <form method="post" action="" id="profileForm">
    <div class="mb-3">
      <label for="full_name" class="form-label">Full Name</label>
      <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" >
    </div>

    <!-- <div class="mb-3">
      <label class="form-label">Admission Number</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['admission_number']); ?>" readonly>
    </div> -->

    <div class="mb-3">
      <label class="form-label">Username</label>
      <input type="text" class="form-control" value="<?php echo htmlspecialchars($student['username']); ?>" >
    </div>

    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($student['email']); ?>" >
    </div>

    <div class="mb-3">
      <label class="form-label">Contact</label>
      <input type="text" class="form-control" name="contact" id="contact" value="<?php echo htmlspecialchars($student['contact']); ?>" >
    </div>

    <div class="d-grid mt-4">
      <button type="submit" class="btn btn-primary" id="updateBtn" >Update Profile</button>
    </div>
  </form>
</div>

<!-- <script>
  const editIcon = document.getElementById('editToggle');
  const inputs = document.querySelectorAll('#profileForm input');
  const updateBtn = document.getElementById('updateBtn');

  editIcon.addEventListener('click', () => {
    inputs.forEach(input => {
      if (input.name) {
        input.removeAttribute('readonly');
      }
    });
    updateBtn.removeAttribute('disabled');
  });
</script> -->




<script>
  // Auto-hide success message after 3 seconds
  const successMessage = document.getElementById('successMessage');
  if (successMessage) {
    setTimeout(() => {
      successMessage.style.display = 'none';
    }, 3000); // 3000ms = 3 seconds
  }
</script>



<script>
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}
</script>


</body>
</html>
