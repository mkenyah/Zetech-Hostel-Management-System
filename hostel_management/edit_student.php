<?php
session_start();
include('db.php'); // Ensure this file contains your database connection
include("functions.php");

if (isset($_GET['id'])) {
    $studentId = $_GET['id'];
    $stmt = $conn->prepare("SELECT full_name, email, contact, username FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);  // Password field

    if (!empty($fullName) && !empty($email) && !empty($contact) && !empty($username) && !empty($password)) {
        // Hash the password before updating it in the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE students SET full_name = ?, email = ?, contact = ?, username = ?, password = ? WHERE student_id = ?");
        $stmt->bind_param("sssssi", $fullName, $email, $contact, $username, $hashedPassword, $studentId);

        if ($stmt->execute()) {
            $success = "Student details updated successfully!";
            log_action($conn, $_SESSION['user_id'], "Edited student: $fullName");


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
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .card {
            width: 400px;
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

        label, h4{
            color:  #0f032b;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card">
        <h4 class="fw-bold text-center">Edit Student</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($student['contact']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($student['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn_update">Update Student</button>
        </form>
        <a href="student_managent.php" class="btn_cancel text-center">Cancel</a>
    </div>
</div>

</body>
</html>
