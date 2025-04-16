<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT  
    a.allocation_id, 
    s.full_name, 
    s.admission_number, 
    b.bed_number, 
    a.allocated_date, 
    a.allocations_from, 
    a.allocations_to, 
    u.username AS allocated_by,
    hs.hostel_name,
    c.campus_name,
    h.username AS house_keeper,
    r.room_number AS room_number,
    s.gender AS gender,
    a.status AS status
FROM allocations a
JOIN students s ON a.student_id = s.student_id
JOIN beds b ON a.bed_id = b.bed_id
JOIN users u ON a.user_id = u.user_id
JOIN rooms r ON b.room_id = r.room_id
JOIN hostels hs ON r.hostel_id = hs.hostel_id
JOIN campus c ON hs.campus_id = c.campus_id
LEFT JOIN users h ON hs.user_id = h.user_id";

if (!empty($search)) {
    $sql .= " WHERE s.full_name LIKE '%$search%'";
}

$sql .= " ORDER BY a.allocated_date DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Allocations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .container {
            margin-top: 50px;
            max-width: 100%;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .btn-primary {
            background-color: #0f032b !important;
            color: white !important;
            border: none !important;
        }
        .btn-danger {
            background-color: rgb(252, 0, 0);
            color: white;
            border: none;
        }
        .btn-primary:hover {
            background-color: white !important;
            border: 1px solid #0f032b !important;
            color: #0f032b !important;
        }
        .btn-danger:hover {
            background-color: white;
            border: 1px solid rgb(252, 6, 6);
            color: rgb(245, 13, 13);
        }
        .table thead {
            background-color: #0f032b;
            color: white;
        }
        .top-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 15px;
        }
        td, th {
            text-align: center;
        }

        #view_b{
            color:  #0f032b !important;
            background-color: white !important ;
            border: 1px solid #0f032b !important;
        }

        input[name="search"] {
    min-width: 350px;
}

    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="top-buttons text-end mb-3">
            <a href="house_keeper.php" class="btn btn-primary">Dashboard</a>
            <a href="h_addal.php" class="btn btn-primary">Add Allocation</a>
        </div>
        <h4 class="fw-bold text-center">Manage Allocations</h4>
        <form method="GET" class="d-flex justify-content-end mb-3" role="search">
    <input type="text" name="search" class="form-control w-25 me-2" placeholder="Search by student name..." value="<?php echo htmlspecialchars($search); ?>">
    <button class="btn btn-primary" type="submit">Search</button>
</form>

        <div class="table-responsive">
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <!-- <th>Allocation ID</th> -->
                        <th>Student Name</th>
                        <th>Admission Number</th>
                        <th>Bed Number</th>
                        <!-- <th>Allocated Date</th> -->
                        <th>From</th>
                        <th>To</th>
                        <!-- <th>Allocated By</th> -->
                        <!-- <th>Hostel</th> -->
                        <!-- <th>Campus</th>
                        <th>Housekeeper</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>


                        <!-- Modal -->
<div class="modal fade" id="viewModal<?php echo $row['allocation_id']; ?>" tabindex="-1" aria-labelledby="viewModalLabel<?php echo $row['allocation_id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel<?php echo $row['allocation_id']; ?>">Student Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Student Name:</strong> <?php echo $row['full_name']; ?></p>
        <p><strong>Admission Number:</strong> <?php echo $row['admission_number']; ?></p>
        <p><strong>Bed Number:</strong> <?php echo $row['bed_number']; ?></p>
        <p><strong>Allocated From:</strong> <?php echo $row['allocations_from']; ?></p>
        <p><strong>To:</strong> <?php echo $row['allocations_to']; ?></p>
        <p><strong>Hostel:</strong> <?php echo $row['hostel_name']. ' ( ' . $row['gender']. ' ) '; ?></p>
        <p><strong>Campus:</strong> <?php echo $row['campus_name']; ?></p>

                    <p><strong>Housekeeper:</strong> <?php echo $row['house_keeper'] ? $row['house_keeper'] : "No housekeeper assigned"; ?></p>
                    <p><strong>Room Number:</strong> <?php echo isset($row['room_number']) ? $row['room_number'] : "N/A"; ?></p>
                    <p>
  <strong>Status:</strong> 
  <span class="<?php echo strtolower($row['status']) === 'expired' ? 'text-danger' : 'text-success'; ?>">
    <?php echo $row['status']; ?>
  </span>
</p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

                            <!-- <td><?php // echo $row['allocation_id']; ?></td> -->
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['admission_number']; ?></td>
                            <td><?php echo $row['bed_number']; ?></td>
                            <!-- <td><?php // echo $row['allocated_date']; ?></td> -->
                            <td><?php echo $row['allocations_from']; ?></td>
                            <td><?php echo $row['allocations_to']; ?></td>
                            <!-- <td><?php echo $row['allocated_by'] ? $row['allocated_by'] : "N/A"; ?></td> -->
                             
                            
                            <td>


                            <!-- View Button -->
<button 
    class="btn btn-info btn-sm"  id="view_b"
    data-bs-toggle="modal" 
    data-bs-target="#viewModal<?php echo $row['allocation_id']; ?>">
    View
</button>



    
<a href="./h_edditall.php?id=<?php echo $row['allocation_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
<a href="delete_allocation.php?id=<?php echo $row['allocation_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this allocation?');">Delete</a>
</td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

