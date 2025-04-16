<?php
session_start();
include('db.php'); // Ensure this file contains your database connection

// Check if hostel name is provided
if (!isset($_GET['hostel_name'])) {
    die("Hostel not specified.");
}

$hostel_name = urldecode($_GET['hostel_name']);

// Fetch hostel ID
$query = "SELECT hostel_id FROM hostels WHERE hostel_name = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $hostel_name);
$stmt->execute();
$result = $stmt->get_result();
$hostel = $result->fetch_assoc();

if (!$hostel) {
    die("Hostel not found.");
}

$hostel_id = $hostel['hostel_id'];

// Fetch assigned services
$query = "SELECT service_id, service_name FROM hostel_services WHERE hostel_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $hostel_id);
$stmt->execute();
$services = $stmt->get_result();

// Handle service removal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['services_to_remove'])) {
    $services_to_remove = $_POST['services_to_remove'];
    
    foreach ($services_to_remove as $service_id) {
        $delete_query = "DELETE FROM hostel_services WHERE hostel_id = ? AND service_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $hostel_id, $service_id);
        $stmt->execute();
    }

    // Redirect back with success message
    header("Location: manage_hostels.php?message=Services removed successfully");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Services - <?php echo htmlspecialchars($hostel_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
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
            background-color: red;
            color: white;
            border: none;
        }
        .btn-danger:hover {
            background-color: darkred;
        }
        .table thead {
            background-color: #0f032b;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="fw-bold text-center">Remove Services from <?php echo htmlspecialchars($hostel_name); ?></h4>

        <?php if ($services->num_rows > 0): ?>
            <form method="post">
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Service Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($service = $services->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" name="services_to_remove[]" value="<?php echo $service['service_id']; ?>"></td>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-danger" onclick="window.location.href='hostel_servicesmanagement.php'">
    Remove Selected Services
</button>

                <a href="hostel_servicesmanagement.php" class="btn btn-primary">Cancel</a>
            </form>
        <?php else: ?>
            <p class="text-center">No services assigned to this hostel.</p>
            <a href="manage_hostels.php" class="btn btn-primary">Back</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
