<?php
// Include database connection
include 'db.php'; // Ensure correct database connection
include("functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize variable
$requirement_name = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the value from the form
    $requirement_name = $_POST['requirement_name'];

    // Basic validation: Check if the input is not empty
    if (empty($requirement_name)) {
        $message = "Please fill in all the fields.";
    } else {
        // Prepare the SQL query to insert the data into the database
        $sql = "INSERT INTO requirements (requirement_name) VALUES (?)";

        // Prepare the statement
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameter to the statement
            $stmt->bind_param("s", $requirement_name);

            // Execute the statement
            if ($stmt->execute()) {
                $message = "Requirement added successfully.";
                log_action($conn, $_SESSION['user_id'], "Added a new rwquirement");
                $requirement_name = ""; // Clear the input field after successful submission
            } else {
                $message = "Error: Could not add requirement. " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            // If the statement failed to prepare, show an error message
            $message = "Error: Could not prepare the query. " . $conn->error;
        }
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
    <title>Add Requirement</title>
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
        <h4 class="fw-bold text-center">Add Requirement</h4>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="add_requirement.php" method="POST">
            <div class="mb-3">
                <label for="requirement_name" class="form-label">Requirement Name</label>
                <input type="text" class="form-control" id="requirement_name" name="requirement_name" required value="<?php echo htmlspecialchars($requirement_name); ?>">
            </div>
            <div class="d-flex justify-content-between">
                <a href="studentrequirements.php" class="btn btn-danger">Cancel</a>
                <button type="submit" class="btn btn-primary">Add Requirement</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
