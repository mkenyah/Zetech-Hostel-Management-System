<?php
// Include database connection
include 'db.php'; // Ensure correct database connection
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variables
$service_name = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the values from the form
    $service_name = $_POST['service_name'];

    // Basic validation: Check if the input is not empty
    if (empty($service_name)) {
        $message = "Please fill in all the fields.";
    } else {
        // Prepare the SQL query to insert the data into the database
        $sql = "INSERT INTO services (service_name) VALUES (?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters to the statement
            $stmt->bind_param("s", $service_name);

            // Execute the statement
            if ($stmt->execute()) {
                $message = "Service added successfully!";
                log_action($conn, $_SESSION['user_id'], "Added a new service");
            } else {
                $message = "Error: Could not add service. " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // If the statement failed to prepare, show an error message
            $message = "Error: Could not prepare the query. " . $conn->error;
        }

        // Close the database connection
        $conn->close();
    }
    
    // Output the message for AJAX
    echo $message;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script>
        function showMessage(message) {
            document.getElementById('message').innerHTML = message;
            document.getElementById('message').style.display = 'block';
        }

        function submitForm(event) {
            event.preventDefault(); // Prevent form from redirecting
            const formData = new FormData(document.getElementById('addServiceForm'));
            
            fetch('add_service.php', {
                method: 'POST',
                body: formData
            }).then(response => response.text())
              .then(data => {
                  showMessage(data);  // Show the success or error message
              }).catch(error => {
                  showMessage("An error occurred. Please try again.");
              });
        }
    </script>
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
        #message {
            display: none;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">

        <h4 class="fw-bold text-center">Add Service</h4>

        <div id="message"><?php echo $message; ?></div>
        <form id="addServiceForm" method="POST" onsubmit="submitForm(event)">
            <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <input type="text" class="form-control" id="service_name" name="service_name" required value="<?php echo htmlspecialchars($service_name); ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="hostel_services.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Service</button>
            </div>
        </form>
        
        <!-- Displaying the message -->
        
    </div>
</div>
</body>
</html>
