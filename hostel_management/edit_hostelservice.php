<?php
session_start();
include('db.php'); // Ensure this file contains your database connection

// Fetch the service details if the ID is set in the URL
if (isset($_GET['id'])) {
    $serviceId = $_GET['id'];
    $stmt = $conn->prepare("SELECT service_name, price FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $result = $stmt->get_result();
    $service = $result->fetch_assoc();
    $stmt->close();
}

// Handle form submission for updating the service
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST['service_name']);
    $price = trim($_POST['price']);

    if (!empty($service_name) && !empty($price)) {
        // Update the service in the database
        $stmt = $conn->prepare("UPDATE services SET service_name = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssi", $service_name, $price, $serviceId);

        if ($stmt->execute()) {
            $success = "Service updated successfully!";
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
    <title>Edit Service</title>
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

        th{
            text-align: center;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card">
        <h4 class="fw-bold text-center">Edit Service</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Service Name</label>
                <input type="text" name="service_name" class="form-control" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="text" name="price" class="form-control" value="<?php echo htmlspecialchars($service['price']); ?>" required>
            </div>
            <button type="submit" class="btn_update">Update Service</button>
        </form>
        <a href="manage_services.php" class="btn_cancel text-center">Cancel</a>
    </div>
</div>

</body>
</html>
