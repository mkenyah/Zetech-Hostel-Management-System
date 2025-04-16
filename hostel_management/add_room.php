<?php
// Include database connection
include 'db.php';
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$room_number = $bed_capacity = $hostel_id = "";
$error_message = "";

// Fetch hostels from the database
$hostel_query = "SELECT hostel_name, hostel_id FROM hostels";
$hostel_result = $conn->query($hostel_query);

if (!$hostel_result) {
    die("Database error: " . $conn->error);
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = trim($_POST['room_number']);
    $hostel_id = trim($_POST['hostel_id']);
    $bed_capacity = trim($_POST['bed_capacity']);

    // Validate inputs
    if (empty($room_number) || empty($hostel_id) || empty($bed_capacity)) {
        $error_message = "Please fill in all the fields.";
    } elseif (!is_numeric($bed_capacity) || $bed_capacity <= 0) {
        $error_message = "Bed capacity must be a positive number.";
    } else {
        // Check for duplicate room number
        $check_sql = "SELECT * FROM rooms WHERE room_number = ? AND hostel_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $room_number, $hostel_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Room number already exists in this hostel!";
        } else {
            // Insert into database
            $sql = "INSERT INTO rooms (hostel_id, room_number, bed_capacity) VALUES (?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $hostel_id, $room_number, $bed_capacity);
                if ($stmt->execute()) {
                    echo "<script>alert('Room added successfully'); window.location.href = 'manage_rooms.php';</script>";
                    log_action($conn, $_SESSION['user_id'], "Added a new room");
                    exit();
                } else {
                    $error_message = "Error: Could not add room. " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Error: Could not prepare the query. " . $conn->error;
            }
        }
        $check_stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: white; color: #0f032b; }
        .container { margin-top: 50px; max-width: 500px; }
        .card { background-color: white; border-radius: 10px; padding: 20px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .btn-primary { background-color: #0f032b !important; color: white !important; border: none !important; }
        .btn-primary:hover { background-color: white !important; border: 1px solid #0f032b !important; color: #0f032b !important; }
        .btn-danger { background-color: rgb(252, 0, 0); color: white; border: none; }
        .btn-danger:hover { background-color: white; border: 1px solid rgb(252, 6, 6); color: rgb(245, 13, 13); }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="fw-bold text-center">Add Room</h4>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="add_room.php" method="POST">
            <div class="mb-3">
                <label for="room_number" class="form-label">Room Number</label>
                <input type="text" class="form-control" id="room_number" name="room_number" required value="<?php echo htmlspecialchars($room_number); ?>">
            </div>
            <div class="mb-3">
                <label for="hostel_id" class="form-label">Hostel</label>
                <select class="form-control" id="hostel_id" name="hostel_id" required>
                    <option value="">Select a Hostel</option>
                    <?php if ($hostel_result->num_rows > 0) {
                        while ($row = $hostel_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['hostel_id']; ?>" <?php if ($hostel_id == $row['hostel_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($row['hostel_name']); ?>
                            </option>
                        <?php }
                    } else { ?>
                        <option value="">No hostels available</option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="bed_capacity" class="form-label">Bed Capacity</label>
                <input type="number" class="form-control" id="bed_capacity" name="bed_capacity" required value="<?php echo htmlspecialchars($bed_capacity); ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="manage_rooms.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Room</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>