<?php 
session_start();
include('db.php'); // Ensure this file contains your database connection

// Fetch hostels and their assigned services from the database
$query = "SELECT h.hostel_name, GROUP_CONCAT(hs.service_name ORDER BY hs.service_name ASC) AS services 
          FROM hostels h
          LEFT JOIN hostel_services hs ON h.hostel_id = hs.hostel_id
          GROUP BY h.hostel_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Service Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
            max-width: 95%;
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
        .btn-primary:hover {
            background-color: white !important;
            border: 1px solid #0f032b !important;
            color: #0f032b !important;
        }
        .table thead {
            background-color: #0f032b;
            color: white;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #0f032b;
            color: white;
            border: none;
        }
        .logout-btn:hover {
            background-color: white;
            border: 1px solid #0f032b;
            color: #0f032b;
        }
        #dashboard{
max-width: 120px;
/* margin-left: 270px; */
/* margin-bottom: 30px; */


        }



        a {
            margin: 5px;
        }
        td {
            text-align: center;
        }
        th{
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
    <a id="dashboard" href="admin.php" class="btn btn-primary">Dashboard</a>
        <a href="hostel_services.php" class="btn logout-btn">New Service</a>
        <h4 class="fw-bold text-center">Service Management</h4>
        
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Hostel Name</th>
                    <th>Assigned Services</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['hostel_name']; ?></td>
                        <td><?php echo $row['services'] ?: 'No services assigned'; ?></td>
                        <td>
                            <a href="edit_hostelservice.php?hostel_name=<?php echo urlencode($row['hostel_name']); ?>" class="btn btn-primary btn-sm">Edit</a>
                           <a href="remove_service.php?hostel_name=<?php echo urlencode($row['hostel_name']); ?>" class="btn btn-danger btn-sm">Remove Services</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- <a href="admin.php" class="btn btn-primary">Cancel</a> -->
    </div>
</div>
</body>
</html>
