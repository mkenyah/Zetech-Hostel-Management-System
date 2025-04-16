<?php
session_start();
include('db.php'); // Ensure this file contains your database connection
include("functions.php");

if (isset($_GET['id'])) {
    $campusId = $_GET['id'];
    $stmt = $conn->prepare("SELECT campus_name, location FROM campus WHERE campus_id = ?");
    $stmt->bind_param("i", $campusId);
    $stmt->execute();
    $result = $stmt->get_result();
    $campus = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $campus_name = trim($_POST['campus_name']);
    $location = trim($_POST['location']);

    if (!empty($campus_name) && !empty($location)) {
        $stmt = $conn->prepare("UPDATE campus SET campus_name = ?, location = ? WHERE campus_id = ?");
        $stmt->bind_param("ssi", $campus_name, $location, $campusId);

        if ($stmt->execute()) {
            $success = "Campus updated successfully!";
            log_action($conn, $_SESSION['user_id'], "Editted Campus");
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
    <title>Edit Campus</title>
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
        <h4 class="fw-bold text-center">Edit Campus</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Campus Name</label>
                <input type="text" name="campus_name" class="form-control" value="<?php echo htmlspecialchars($campus['campus_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($campus['location']); ?>" required>
            </div>
            <button type="submit" class="btn_update">Update Campus</button>
        </form>
        <a href="campus.php" class="btn_cancel text-center">Cancel</a>
    </div>
</div>

</body>
</html>
