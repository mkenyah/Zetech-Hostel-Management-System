<?php
// Include database connection
include 'db.php'; // Ensure correct database connection
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$room_id = $bed_number = $status = "";

// Fetch rooms from the database (assuming `rooms` table exists with a `room_id` and `room_number`)
$room_query = "SELECT room_id, room_number FROM rooms"; 
$room_result = $conn->query($room_query);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form and sanitize them
    $room_id = $conn->real_escape_string($_POST['room_id']);
    $bed_number = $conn->real_escape_string($_POST['bed_number']);
    $status = $conn->real_escape_string($_POST['status']);

    // Basic validation: Check if the inputs are not empty
    if (empty($room_id) || empty($bed_number) || empty($status)) {
        echo "<script>alert('Please fill in all the fields'); window.location.href = 'add_beds.php';</script>";
        exit();
    }

    try {
        // Prepare the SQL query to insert the data into the database
        $sql = "INSERT INTO beds (room_id, bed_number, status) VALUES (?, ?, ?)";
        
        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the statement
            $stmt->bind_param("iss", $room_id, $bed_number, $status);

            // Execute the statement
            if ($stmt->execute()) {
                echo "<script>alert('Bed added successfully'); window.location.href = 'beds_management.php';</script>";
                log_action($conn, $_SESSION['user_id'], "Added a new bed: $bed_number");
            } else {
                throw new Exception("Error: Could not add bed. " . $stmt->error);
            }

            // Close the statement
            $stmt->close();
        } else {
            throw new Exception("Error: Could not prepare the query. " . $conn->error);
        }
    } catch (Exception $e) {
        // Catch errors and display them
        echo "<script>alert('" . $e->getMessage() . "'); window.location.href = 'add_beds.php';</script>";
    }
}

// Close the database connection
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Bed</title>
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
        <h4 class="fw-bold text-center">Add Bed</h4>
        <form action="add_beds.php" method="POST">
            <div class="mb-3">
                <label for="room_id" class="form-label">Room</label>
                <select class="form-control" id="room_id" name="room_id" required>
                    <option value="">Select a Room</option>
                    <?php while ($row = $room_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['room_id']; ?>"><?php echo htmlspecialchars($row['room_number']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="bed_number" class="form-label">Bed Number</label>
                <input type="text" class="form-control" id="bed_number" name="bed_number" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="Available">Available</option>
                    <option value="Occupied">Occupied</option>
                </select>
            </div>
            <div class="d-flex justify-content-between">
                <a href="beds_management.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Bed</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
