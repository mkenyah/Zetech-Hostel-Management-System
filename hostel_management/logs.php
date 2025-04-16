<?php 
session_start();
include 'db.php'; // Include database connection
include("functions.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch logs
$sql = "SELECT logs.log_id, users.username, users.role, logs.action, logs.log_date 
        FROM logs 
        INNER JOIN users ON logs.user_id = users.user_id 
        ORDER BY logs.log_date DESC";

$result = mysqli_query($conn, $sql);



if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_sql = "DELETE FROM logs WHERE log_id = $delete_id";
    mysqli_query($conn, $delete_sql);
}


// Function to delete all logs
if (isset($_POST['delete_all'])) {
    $delete_all_sql = "DELETE FROM logs";
    if (mysqli_query($conn, $delete_all_sql)) {
        echo "All logs have been deleted.";
        log_action($conn, $_SESSION['user_id'], "Deleted all logs");
    } else {
        echo "Error deleting logs: " . mysqli_error($conn);
    }
}

// Function to delete a single log
if (isset($_GET['delete_id'])) {
    // Sanitize delete_id to avoid SQL injection
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_sql = "DELETE FROM logs WHERE log_id = $delete_id";
    if (mysqli_query($conn, $delete_sql)) {
        // echo "Log has been deleted.";
    } else {
        echo "Error deleting log: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
    body {
        background-color: white;
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
        <form method="post" onsubmit="return confirm('Are you sure you want to delete all logs?');">
    <button type="submit" name="delete_all" class="btn btn-danger">Delete All Logs</button>
</form>


            <a href="admin.php" class="btn btn-primary">Dashboard</a>
        </div>
        <h4 class="fw-bold text-center">Manage Logs</h4>
        <form method="post" class="mb-3">
           
        </form>
        <table class="table table-bordered mt-5">
    <thead>
        <tr>
            <th>User</th>
            <th>Role</th> <!-- Add Role column header -->
            <th>Action</th>
            <th>Timestamp</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td> <!-- Display Username -->
                <td><?php echo htmlspecialchars($row['role']); ?></td> <!-- Display Role -->
                <td><?php echo htmlspecialchars($row['action']); ?></td> <!-- Display Action -->
                <td><?php echo htmlspecialchars($row['log_date']); ?></td> <!-- Display Timestamp -->
                <td>
                    <a href="logs.php?delete_id=<?php echo $row['log_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this log?');">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

    </div>
</div>
</body>
</html>


