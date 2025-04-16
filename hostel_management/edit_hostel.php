<?php
session_start();
include('db.php'); // Ensure this file contains your database connection
include("functions.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all housekeepers (users with role 'house_manager') with user names
$housekeeperList = [];
$housekeeperQuery = $conn->query("SELECT user_id, username FROM users WHERE role = 'house_manager'");
while ($row = $housekeeperQuery->fetch_assoc()) {
    $housekeeperList[] = $row;
}

// Fetch all campuses
$campusList = [];
$campusQuery = $conn->query("SELECT campus_name FROM campus");
while ($row = $campusQuery->fetch_assoc()) {
    $campusList[] = $row['campus_name'];
}

// Get campus_id from campus_name
$campus_name = ''; // Placeholder for campus name
$campus_id = null;
if (isset($_POST['campus_name'])) {
    $campus_name = $_POST['campus_name'];
    $stmt = $conn->prepare("SELECT campus_id FROM campus WHERE campus_name = ?");
    $stmt->bind_param("s", $campus_name);
    $stmt->execute();
    $campusResult = $stmt->get_result();
    $campusData = $campusResult->fetch_assoc();
    $campus_id = $campusData['campus_id'];
    $stmt->close();
}

// Fetch hostel details
if (isset($_GET['id'])) {
    $hostelId = $_GET['id'];

    // Fetch hostel details
    $stmt = $conn->prepare("SELECT h.hostel_name, c.campus_name, h.capacity, h.gender, h.user_id
                            FROM hostels h 
                            JOIN campus c ON h.campus_id = c.campus_id 
                            WHERE h.hostel_id = ?");
    $stmt->bind_param("i", $hostelId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hostel = $result->fetch_assoc();
    $stmt->close();

    // Fetch services assigned to this hostel
    $assignedServices = [];
    $assignedQuery = $conn->prepare("SELECT service_id FROM hostel_services WHERE hostel_id = ?");
    $assignedQuery->bind_param("i", $hostelId);
    $assignedQuery->execute();
    $assignedResult = $assignedQuery->get_result();
    while ($row = $assignedResult->fetch_assoc()) {
        $assignedServices[] = $row['service_id'];
    }
    $assignedQuery->close();

    // Fetch all available services
    $services = [];
    $serviceQuery = $conn->query("SELECT id, service_name FROM services");
    while ($row = $serviceQuery->fetch_assoc()) {
        $services[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['services'])) {
        $selectedServices = $_POST['services']; // array of selected service ids

        // Delete all current hostel services
        $conn->query("DELETE FROM hostel_services WHERE hostel_id = $hostelId");

        // Insert new selected services
        $insertStmt = $conn->prepare("INSERT INTO hostel_services (hostel_id, service_id) VALUES (?, ?)");
        foreach ($selectedServices as $serviceId) {
            $insertStmt->bind_param("ii", $hostelId, $serviceId);
            $insertStmt->execute();
        }
        $insertStmt->close();

        // Display success message on the same page
        $success = "Hostel services updated successfully!";
        
    }

    // Handle other hostel details update
    $hostel_name = trim($_POST['hostel_name']);
    $campus_name = trim($_POST['campus_name']);
    $capacity = trim($_POST['capacity']);
    $gender = trim($_POST['gender']);
    $housekeeper_id = trim($_POST['housekeeper_id']); // Get selected housekeeper (user_id)

    // Get campus_id from campus_name
    $stmt = $conn->prepare("SELECT campus_id FROM campus WHERE campus_name = ?");
    $stmt->bind_param("s", $campus_name);
    $stmt->execute();
    $campusResult = $stmt->get_result();
    $campusData = $campusResult->fetch_assoc();
    $campus_id = $campusData['campus_id'];
    $stmt->close();

    if (!empty($hostel_name) && !empty($campus_id) && !empty($capacity) && !empty($gender)) {
        // Update hostel details
        $stmt = $conn->prepare("UPDATE hostels SET hostel_name = ?, campus_id = ?, capacity = ?, gender = ?, user_id = ? WHERE hostel_id = ?");
        $stmt->bind_param("siisii", $hostel_name, $campus_id, $capacity, $gender, $housekeeper_id, $hostelId);

        if ($stmt->execute()) {
            // Display success message on the same page
            $success = "Hostel updated successfully!";
            log_action($conn, $_SESSION['user_id'], "Editted a hostel");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hostel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .card {
            width: 500px;
            background-color: rgb(255, 255, 255);
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
            background-color: rgb(239, 236, 245);
            border: 1px solid #0f032b;
            color: #0f032b;
        }
        a {
            text-align: center;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }
        label, h4 {
            color: #0f032b;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card">
        <h4 class="fw-bold text-center">Edit Hostel</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Hostel Name</label>
                <input type="text" name="hostel_name" class="form-control" value="<?php echo htmlspecialchars($hostel['hostel_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Campus Name</label>
                <select name="campus_name" class="form-control" required>
                    <?php foreach ($campusList as $campus) : ?>
                        <option value="<?php echo $campus; ?>" <?php echo ($campus == $hostel['campus_name']) ? 'selected' : ''; ?>>
                            <?php echo $campus; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Hostel Capacity</label>
                <input type="number" name="capacity" class="form-control" value="<?php echo htmlspecialchars($hostel['capacity']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male" <?php echo ($hostel['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($hostel['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Mixed" <?php echo ($hostel['gender'] == 'Mixed') ? 'selected' : ''; ?>>Mixed</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Housekeeper</label>
                <select name="housekeeper_id" class="form-control" required>
                    <?php foreach ($housekeeperList as $housekeeper) : ?>
                        <option value="<?php echo $housekeeper['user_id']; ?>" <?php echo ($housekeeper['user_id'] == $hostel['user_id']) ? 'selected' : ''; ?>>
                            <?php echo $housekeeper['username']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h3>Services</h3>
            <?php foreach ($services as $service): ?>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="services[]" 
                        value="<?= $service['id'] ?>" 
                        id="service<?= $service['id'] ?>"
                        <?= in_array($service['id'], $assignedServices) ? 'checked' : '' ?>
                    >
                    <label class="form-check-label" for="service<?= $service['id'] ?>">
                        <?= htmlspecialchars($service['service_name']) ?>
                    </label>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn_update">Update Hostel</button>
        </form>

        <a href="hostel_management.php" class="btn_cancel">Cancel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
