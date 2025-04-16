<?php
session_start();
include('db.php'); // Ensure this file contains your database connection
include("functions.php");

if (isset($_GET['id'])) {
    $bedId = $_GET['id'];
    $stmt = $conn->prepare("SELECT bed_number, room_id, status FROM beds WHERE bed_id = ?");
    $stmt->bind_param("i", $bedId);
    $stmt->execute();
    $result = $stmt->get_result();
    $bed = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bed_number = trim($_POST['bed_number']);
    $room_id = trim($_POST['room_id']);
    $status = trim($_POST['status']);

    if (!empty($bed_number) && !empty($room_id) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE beds SET bed_number = ?, room_id = ?, status = ? WHERE bed_id = ?");
        $stmt->bind_param("sisi", $bed_number, $room_id, $status, $bedId);

        if ($stmt->execute()) {
            $success = "Bed updated successfully!";
            log_action($conn, $_SESSION['user_id'], "Editted a bed: $bed_number");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}

// Fetch rooms from the database to populate the dropdown
$room_query = "SELECT room_id, room_number FROM rooms";
$room_result = $conn->query($room_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .card {
            width: 400px;
            background-color:rgb(255, 255, 255);
            color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            background-color: white;
            color: black;
        }
        .btn_update, .btn_cancel {
            background-color: #0f032b;
            color: white;
            width: 100%;
            height: 6vh;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            margin-top: 10px;
        }
        .btn_update:hover, .btn_cancel:hover {
            background-color:rgb(239, 236, 245);
            border: 1px solid #0f032b ;
            color: #0f032b ;
        }
        a {
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        label, h4{
            color:  #0f032b;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card">
        <h4 class="fw-bold text-center">Edit Bed</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Bed Number</label>
                <input type="text" name="bed_number" class="form-control" value="<?php echo htmlspecialchars($bed['bed_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Room</label>
                <select class="form-control" name="room_id" required>
                    <option value="">Select a Room</option>
                    <?php while ($row = $room_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['room_id']; ?>" <?php if ($row['room_id'] == $bed['room_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($row['room_number']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select class="form-control" name="status" required>
                    <option value="Available" <?php if ($bed['status'] == 'Available') echo 'selected'; ?>>Available</option>
                    <option value="Occupied" <?php if ($bed['status'] == 'Occupied') echo 'selected'; ?>>Occupied</option>
                </select>
            </div>
            <button type="submit" class="btn_update">Update Bed</button>
        </form>
        <a href="beds_management.php" class="btn_cancel text-center">Cancel</a>
    </div>
</div>

</body>
</html>
