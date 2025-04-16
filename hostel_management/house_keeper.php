<?php
session_start();

include('db.php');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


    // Fetch total students
$studentsQuery = "SELECT COUNT(*) AS total_students FROM students";
$result = $conn->query($studentsQuery);
$totalStudents = $result->fetch_assoc()['total_students'];

// Fetch total allocations
$allocationsQuery = "SELECT COUNT(*) AS total_allocations FROM allocations";
$result = $conn->query($allocationsQuery);
$totalAllocations = $result->fetch_assoc()['total_allocations'];

// Fetch total available beds
$availableBedsQuery = "SELECT COUNT(*) AS total_beds_available FROM beds WHERE status = 'Available'";
$result = $conn->query($availableBedsQuery);
$totalAvailableBeds = $result->fetch_assoc()['total_beds_available'];

// Fetch total occupied beds
$occupiedBedsQuery = "SELECT COUNT(*) AS total_beds_occupied FROM beds WHERE status = 'Occupied'";
$result = $conn->query($occupiedBedsQuery);
$totalOccupiedBeds = $result->fetch_assoc()['total_beds_occupied'];

// Fetch total rooms
$roomsQuery = "SELECT COUNT(*) AS total_rooms FROM rooms";
$result = $conn->query($roomsQuery);
$totalRooms = $result->fetch_assoc()['total_rooms'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color:rgb(255, 255, 255);
      color: white;
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
      background-color:rgb(244, 244, 247);
      color: #0f032b;
      border-radius: 5px;
    }
    .content {
      margin-left: 220px;
      padding: 20px;
      text-align: center;
      margin-bottom: 170px; /* 👈 Moves the content higher */
      color: #0f032b;
    }
    .menu-toggle {
      background-color: white;
      color: #0f032b;
      border: none;
      padding: 10px 15px;
      font-weight: bold;
      border-radius: 5px;
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1001;
      display: none; /* hidden by default */
    }


    .logout {
      margin-left: 40px;
      margin-top: 199px;
    }
    .logout a {
        max-width: 100px;
        background-color: white;
        color: #0f032b;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      display: block;
      text-decoration: none;
      font-weight: bold;
    }
    .logout a:hover {
     transition: ease-in-out cubic-bezier(0.075, 0.82, 0.165, 1);
    }

    .flex-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      justify-content: center;
      margin-top: 50px;
    }
    .flex-box {
      background-color: white;
      color: #1c1142;
      padding: 30px;
      width: 320px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
      font-weight: bold;
      font-size: 18px;
      height: 20vh;
    }

    h5, p{
        font-size: 25px;
    }

    
    @media (max-width: 768px) {
      .menu-toggle {
        display: block;
      }
      .sidebar {
        transform: translateX(-100%);
        z-index: 1000;
      }
      .sidebar.active {
        transform: translateX(0);
      }
      .content {
      margin-left: 220px;
      padding: 20px;
      text-align: center;
      margin-bottom: 170px; /* 👈 Moves the content higher */
    }
    }
  </style>
</head>

<body>

<!-- Menu Button -->
<button class="menu-toggle" onclick="toggleSidebar()">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  
  <a href="./h_student.php">View Students</a>
  <a href="./h_beds.php">View Beds </a>
  <a href="./h_rooms.php">View Rooms</a>
  <a href="./h_hostel.php">View Hostels</a>
  <a href="./h_allocations.php">Allocate Student</a>


  <div class="logout">
      <a href="login.php">Logout</a>
    </div>

</div>





<!-- Main Content -->
<div class="content">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
  <!-- <p>Select an action from the sidebar to continue.</p> -->




  <div class="container">
    <div class="flex-container">
        <div class="flex-box">
            <h5>Total Students</h5>
            <p><?= $totalStudents ?></p>
        </div>
        <div class="flex-box">
            <h5>Total Allocations</h5>
            <p><?= $totalAllocations ?></p>
        </div>
        <div class="flex-box">
            <h5>Total Beds Available</h5>
            <p><?= $totalAvailableBeds ?></p>
        </div>
        <div class="flex-box">
            <h5>Total Beds Occupied</h5>
            <p><?= $totalOccupiedBeds ?></p>
        </div>
        <div class="flex-box">
            <h5>Total Rooms</h5>
            <p><?= $totalRooms ?></p>
        </div>
</div>




<script>
  function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
  }
</script>

</body>
</html>
