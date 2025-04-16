<?php
session_start();
include('db.php'); // Ensure this file contains your database connection

// Handle user deletion
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $success = "User deleted successfully!";
    } else {
        $error = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch users from the database
$result = $conn->query("SELECT user_id, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
    max-width: 119px;
}
        .btn-danger {
            background-color:rgb(252, 0, 0);
            color: white;
            gap: 4px;
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
            color:rgb(245, 13, 13);
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
        a {
            margin: 5px;
        }

        td{
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
    <a href="admin.php" class="btn btn-primary">Dashboard</a>
        <a href="add_user.php" class="btn logout-btn">Add New User</a>
        <h4 class="fw-bold text-center">User Management</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Username</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <!-- <td><?php //echo $row['user_id']; ?></td> -->
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                            <a href="edit_users.php?id=<?php echo $row['user_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="user_management.php?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- <a href="adduser.php" class="btn btn-primary">Add New User</a> -->
        <!-- <a href="admin.php" class="btn btn-primary">Cancel</a> -->
    </div>
</div>
</body>
</html>
