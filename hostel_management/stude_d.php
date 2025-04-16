<?php
session_start();
include('db.php');
include("functions.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$sql = "SELECT  
    a.allocation_id, 
    s.full_name, 
    s.admission_number, 
    b.bed_number, 
    a.allocated_date, 
    a.allocations_from, 
    a.allocations_to, 
    a.status AS status,
    u.username AS allocated_by,
    hs.hostel_name,
    c.campus_name,
    h.username AS house_keeper,
    r.room_number,
    s.gender
FROM allocations a
JOIN students s ON a.student_id = s.student_id
JOIN beds b ON a.bed_id = b.bed_id
JOIN users u ON a.user_id = u.user_id
JOIN rooms r ON b.room_id = r.room_id
JOIN hostels hs ON r.hostel_id = hs.hostel_id
JOIN campus c ON hs.campus_id = c.campus_id
LEFT JOIN users h ON hs.user_id = h.user_id
WHERE s.student_id = '$student_id'
ORDER BY a.allocations_to DESC";


$result = mysqli_query($conn, $sql);
$allocations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allocations[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
      text-align: center;
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
  </style>
</head>
<body>

<!-- Menu Toggle -->
<button class="menu-toggle" onclick="toggleSidebar()">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <a href="./profile.php">Profile</a>
  <div class="logout">
    <a href="login.php">Logout</a>
  </div>
</div>

<!-- Main Content -->
<div class="content">
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

  <div class="container mt-4">
    <div class="card">
      <h4 class="fw-bold text-center">Student Allocation Summary</h4>
      <?php if (!empty($allocations)): ?>
<div class="table-responsive">
  <table class="table table-bordered mt-4">
    <thead>
      <tr>
        <th>Student Name</th>
        <th>Admission Number</th>
        <th>Hostel</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($allocations as $allocation): ?>
      <tr>
        <td><?php echo $allocation['full_name']; ?></td>
        <td><?php echo $allocation['admission_number']; ?></td>
        <td><?php echo $allocation['hostel_name']; ?></td>
        <td style="color: <?php echo ($allocation['status'] === 'Expired') ? 'red' : 'green'; ?>;">
          <?php echo $allocation['status']; ?>
        </td>
        <td>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#detailsModal<?php echo $allocation['allocation_id']; ?>">View Details</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
  <div class="alert alert-warning text-center fw-bold">No allocation record found.</div>
<?php endif; ?>

         
    </div>
  </div>
</div>

<!-- Modal -->
<?php foreach ($allocations as $allocation): ?>
<div class="modal fade" id="detailsModal<?php echo $allocation['allocation_id']; ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?php echo $allocation['allocation_id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Full Allocation Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($allocation['status'] === 'Expired'): ?>
          <div class="alert alert-danger text-center fw-bold">
            Your period has expired. Kindly visit the allocation office.
          </div>
        <?php endif; ?>
        <table class="table table-bordered">
          <tr><th>Student Name</th><td><?php echo $allocation['full_name']; ?></td></tr>
          <tr><th>Admission Number</th><td><?php echo $allocation['admission_number']; ?></td></tr>
          <tr><th>Gender</th><td><?php echo $allocation['gender']; ?></td></tr>
          <tr><th>Hostel</th><td><?php echo $allocation['hostel_name']; ?></td></tr>
          <tr><th>Room Number</th><td><?php echo $allocation['room_number']; ?></td></tr>
          <tr><th>Bed Number</th><td><?php echo $allocation['bed_number']; ?></td></tr>
          <tr><th>Campus</th><td><?php echo $allocation['campus_name']; ?></td></tr>
          <tr><th>House Keeper</th><td><?php echo $allocation['house_keeper']; ?></td></tr>
          <tr><th>Allocation From</th><td><?php echo $allocation['allocations_from']; ?></td></tr>
          <tr><th>Allocation To</th><td><?php echo $allocation['allocations_to']; ?></td></tr>
          <tr><th>Allocated By</th><td><?php echo $allocation['allocated_by']; ?></td></tr>
          <tr><th>Status</th>
              <td style="color: <?php echo ($allocation['status'] === 'Expired') ? 'red' : 'green'; ?>;">
                <?php echo $allocation['status']; ?>
              </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>


<script>
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}
</script>

</body>
</html>
