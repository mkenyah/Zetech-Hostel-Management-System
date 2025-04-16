<?php
session_start();
include 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch student requirements with only requirement_id and requirement_name fields
$sql = "SELECT requirement_id, requirement_name FROM requirements";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Requirements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .container {
            margin-top: 50px;
            max-width: 90%;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
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
            <a href="add_requirement.php" class="btn btn-primary">Add Requirement</a>
        </div>
        <h4 class="fw-bold text-center">Manage Student Requirements</h4>
        <table class="table table-bordered mt-5">
            <thead>
                <tr>
                    <th>Requirement</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['requirement_name']; ?></td>
                        <td>
                            <a href="edit_requirement.php?id=<?php echo $row['requirement_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_requirement.php?id=<?php echo $row['requirement_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this requirement?');">Delete</a>
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
