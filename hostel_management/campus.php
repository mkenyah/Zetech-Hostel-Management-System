<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch existing campuses
$sql = "SELECT * FROM campus";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Campuses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
    body {
        background-color: white;
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
        <div class="top-buttons">
            <a href="admin.php" class="btn btn-primary">Dashboard</a>
            <a href="add_campus.php" class="btn btn-primary">Add Campus</a>
        </div>
        <h4 class="fw-bold text-center">Manage Campuses</h4>
        <table class="table table-bordered mt-5">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Campus Name</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <!-- <td><?php //echo $row['campus_id']; ?></td> -->
                        <td><?php echo $row['campus_name']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td>
                            <a href="edit_campus.php?id=<?php echo $row['campus_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_campus.php?id=<?php echo $row['campus_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this campus?');">Delete</a>
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
