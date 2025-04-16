<?php
// Include database connection
include 'db.php'; // Ensure correct database connection
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$campus_name = $location = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form
    $campus_name = $_POST['campus_name'];
    $location = $_POST['location'];

    // Basic validation: Check if the inputs are not empty
    if (empty($campus_name) || empty($location)) {
        echo "<script>alert('Please fill in all the fields'); window.location.href = 'add_campus.php';</script>";
        exit();
    }

    // Prepare the SQL query to insert the data into the database
    $sql = "INSERT INTO campus (campus_name, location) VALUES (?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        $stmt->bind_param("ss", $campus_name, $location);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Campus added successfully'); window.location.href = 'campus.php';</script>";
            log_action($conn, $_SESSION['user_id'], "Added a new Campus");
        } else {
            echo "<script>alert('Error: Could not add campus. " . $stmt->error . "'); window.location.href = 'add_campus.php';</script>";
        }

        // Close the statement
        $stmt->close();
    } else {
        // If the statement failed to prepare, show an error message
        echo "<script>alert('Error: Could not prepare the query. " . $conn->error . "'); window.location.href = 'add_campus.php';</script>";
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Campus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .container {
            margin-top: 50px;
            max-width: 500px;
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
        .btn-danger {
            background-color: rgb(252, 0, 0);
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: white;
            border: 1px solid rgb(252, 6, 6);
            color: rgb(245, 13, 13);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="fw-bold text-center">Add Campus</h4>
        <form action="add_campus.php" method="POST">
            <div class="mb-3">
                <label for="campus_name" class="form-label">Campus Name</label>
                <input type="text" class="form-control" id="campus_name" name="campus_name" required value="<?php echo htmlspecialchars($campus_name); ?>">
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" required value="<?php echo htmlspecialchars($location); ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="campus.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Campus</button>
                <!-- <button type="submit" class="btn btn-primary">Add Campus</button> -->
            </div>
        </form>
    </div>
</div>
</body>
</html>
