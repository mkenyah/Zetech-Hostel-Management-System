<?php
// Include database connection
include 'db.php'; // Ensure correct database connection
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$hostel_name = $location = $capacity = $gender = $housekeeper_id = "";
$selected_services = [];

// Fetch campuses from the database
$campus_query = "SELECT campus_name, campus_id FROM campus";
$campus_result = $conn->query($campus_query);

// Fetch available services from the database
$service_query = "SELECT id, service_name FROM services";
$service_result = $conn->query($service_query);

// Fetch housekeepers (users with role 'house_manager') from the database
$housekeeper_query = "SELECT user_id, username FROM users WHERE ROLE = 'house_manager'";
$housekeeper_result = $conn->query($housekeeper_query);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form
    $hostel_name = isset($_POST['hostel_name']) ? trim($_POST['hostel_name']) : '';
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $capacity = isset($_POST['capacity']) ? $_POST['capacity'] : '';
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $housekeeper_id = isset($_POST['housekeeper']) ? $_POST['housekeeper'] : null;
    $selected_services = isset($_POST['services']) ? $_POST['services'] : [];

    // Basic validation: Check if the inputs are not empty
    if (empty($hostel_name) || empty($location) || empty($capacity) || empty($gender)) {
        echo "<script>alert('Please fill in all the fields'); window.location.href = 'add_hostel.php';</script>";
        exit();
    }
    
    // Ensure at least one service is selected
    if (empty($selected_services)) {
        echo "<script>alert('Please select at least one service'); window.location.href = 'add_hostel.php';</script>";
        exit();
    }

    // Prepare the SQL query to insert the data into the database
    $sql = "INSERT INTO hostels (hostel_name, campus_id, capacity, gender, user_id) VALUES (?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the statement
        $stmt->bind_param("ssiis", $hostel_name, $location, $capacity, $gender, $housekeeper_id);

        // Execute the statement
        if ($stmt->execute()) {
            $hostel_id = $stmt->insert_id; // Get the inserted hostel ID

            // Now insert the selected services into the hostel_services table
            foreach ($selected_services as $service_id) {
                $service_sql = "INSERT INTO hostel_services (hostel_id, service_id) VALUES (?, ?)";
                if ($service_stmt = $conn->prepare($service_sql)) {
                    $service_stmt->bind_param("ii", $hostel_id, $service_id);
                    if (!$service_stmt->execute()) {
                        echo "<script>alert('Error: Could not add services. " . $service_stmt->error . "'); window.location.href = 'add_hostel.php';</script>";
                        exit();
                    }
                    $service_stmt->close();
                } else {
                    echo "<script>alert('Error: Could not prepare service insertion query. " . $conn->error . "'); window.location.href = 'add_hostel.php';</script>";
                    exit();
                }
            }

            echo "<script>alert('Hostel added successfully'); window.location.href = 'hostel_management.php';</script>";
            log_action($conn, $_SESSION['user_id'], "Added a  hostel");
        } else {
            echo "<script>alert('Error: Could not add hostel. " . $stmt->error . "'); window.location.href = 'add_hostel.php';</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error: Could not prepare hostel insertion query. " . $conn->error . "'); window.location.href = 'add_hostel.php';</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Hostel</title>
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
        .form-check {
            display: flex;
            flex-direction: column; /* Flexbox for vertical stacking */
            gap: 10px; /* Add space between the checkboxes */
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="fw-bold text-center">Add Hostel</h4>
        <form action="add_hostel.php" method="POST">
            <div class="mb-3">
                <label for="hostel_name" class="form-label">Hostel Name</label>
                <input type="text" class="form-control" id="hostel_name" name="hostel_name" required value="<?php echo htmlspecialchars($hostel_name); ?>">
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Campus</label>
                <select class="form-control" id="location" name="location" required>
                    <option value="">Select a Campus</option>
                    <?php while ($row = $campus_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['campus_id']; ?>"><?php echo htmlspecialchars($row['campus_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capacity" name="capacity" required value="<?php echo htmlspecialchars($capacity); ?>" min="1">
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Mixed" <?php echo ($gender == 'Mixed') ? 'selected' : ''; ?>>Mixed</option>
                </select>
            </div>

            <!-- Housekeeper Dropdown -->
            <div class="mb-3">
                <label for="housekeeper" class="form-label">Select Housekeeper</label>
                <select class="form-control" id="housekeeper" name="housekeeper" required>
                    <option value="">Select a Housekeeper</option>
                    <?php while ($row = $housekeeper_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['user_id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Services Checkboxes -->
            <div class="mb-3">
                <label for="services" class="form-label">Select Services</label>
                <div class="form-check" style="display: flex; flex-direction: row; gap: 30px; flex-wrap: wrap;">
                    <?php while ($service = $service_result->fetch_assoc()) { ?>
                        <div>
                            <input type="checkbox" class="form-check-input" name="services[]" value="<?php echo $service['id']; ?>" id="service_<?php echo $service['id']; ?>">
                            <label class="form-check-label" for="service_<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['service_name']); ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="hostel_management.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Hostel</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
