<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Secure the id

    // Check if the service exists before deleting
    $check_sql = "SELECT * FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "i", $id); // "i" for integer
    mysqli_stmt_execute($stmt);
    $check_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($check_result) > 0) {
        // Proceed with deletion if service exists
        $delete_sql = "DELETE FROM services WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $_SESSION['success'] = "Service deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete service.";
        }
    } else {
        $_SESSION['error'] = "Service not found.";
    }

    header("Location: manage_services.php");
    exit();
}

// Fetch existing services
$sql = "SELECT * FROM services";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Services</title>
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
    td, th {
      text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="card">
    <div class="top-buttons">
      <a href="admin.php" class="btn btn-primary">Dashboard</a>
      <a href="add_service.php" class="btn btn-primary">Add Service</a>
    </div>

    <h3 class="text-center mb-4">Manage Services</h3>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
        <?php echo $_SESSION['success']; ?>
        <?php unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?php echo $_SESSION['error']; ?>
        <?php unset($_SESSION['error']); ?>
      </div>
    <?php endif; ?>

    <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
      
          <th>Service Name</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <tr>
            <!-- <td><?php //echo htmlspecialchars($row['id']); ?></td> -->
            <td><?php echo htmlspecialchars($row['service_name']); ?></td>
            <td>
              <a href="edit_service.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                Edit
              </a>
              <a href="manage_services.php?delete=<?php echo $row['id']; ?>" 
                 class="btn btn-danger btn-sm"
                 onclick="return confirm('Are you sure you want to delete this service?');">
                 Delete
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>
</body>
</html>
