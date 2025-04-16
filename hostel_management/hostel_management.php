<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT 
    h.hostel_id, 
    h.hostel_name, 
    c.campus_name, 
    h.capacity, 
    h.gender, 
    u.username AS house_keeper,
    GROUP_CONCAT(sr.service_name ORDER BY sr.service_name SEPARATOR ', ') AS services
FROM 
    hostels h
LEFT JOIN campus c ON h.campus_id = c.campus_id
LEFT JOIN hostel_services hs ON h.hostel_id = hs.hostel_id
LEFT JOIN services sr ON hs.service_id = sr.id
LEFT JOIN users u ON h.user_id = u.user_id
GROUP BY 
    h.hostel_id, h.hostel_name, c.campus_name, h.capacity, h.gender, u.username";

        
        // Group by hostel ID
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hostels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .container {
            margin-top: 50px;
            max-width: 90%; /* Adjusted width to make it responsive */
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
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <div class="top-buttons text-end mb-3">
            <a href="admin.php" class="btn btn-primary">Dashboard</a>
            <a href="add_hostel.php" class="btn btn-primary">Add Hostel</a>
        </div>
        <h4 class="fw-bold text-center">Manage Hostels</h4>
        <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <!-- <th>ID</th> -->
                <th>Hostel Name</th>
                 <th>House Keeper</th>
                        <th>Campus</th>
                        <th>Capacity</th>
                        <th>Gender</th>
                       
                        <th>Services</th>
                        <th>Actions</th>

            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <!-- <td><?php //echo $row['hostel_id']; ?></td> -->
                    <td><?php echo $row['hostel_name']; ?></td>
                    <td><?php echo $row['house_keeper'] ? $row['house_keeper'] : "No housekeeper assigned"; ?></td>

                    <td><?php echo $row['campus_name'] ? $row['campus_name'] : "N/A"; ?></td>
                    <td><?php echo $row['capacity']; ?></td>
                    <td><?php echo ucfirst($row['gender']); ?></td>
                    <td><?php echo $row['services'] ? $row['services'] : "No services available"; ?></td> <!-- Display services -->
                    <td>
                        <a href="edit_hostel.php?id=<?php echo $row['hostel_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_hostel.php?id=<?php echo $row['hostel_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this hostel?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        </table>
        <!-- <a href="admin.php" class="btn btn-primary">Cancel</a> -->
    </div>
</div>
</body>
</html>
