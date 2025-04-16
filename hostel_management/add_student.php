<?php
session_start();
include('db.php');
include('functions.php');

// Ensure this file contains your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $admission_number = trim($_POST['admission_number']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $gender = trim($_POST['gender']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password before saving

    if (!empty($full_name) && !empty($admission_number) && !empty($email) && !empty($contact) && !empty($gender) && !empty($username) && !empty($password)) {
        // Check if the admission number, email, or username already exists
        $checkStmt = $conn->prepare("SELECT * FROM students WHERE admission_number = ? OR email = ? OR username = ?");
        $checkStmt->bind_param("sss", $admission_number, $email, $username);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Admission Number, Email, or Username already exists!";
        } else {
            // Insert student into the database with gender, username, and password
            $stmt = $conn->prepare("INSERT INTO students (full_name, admission_number, email, contact, gender, username, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $full_name, $admission_number, $email, $contact, $gender, $username, $hashed_password);

            if ($stmt->execute()) {
                $success = "Student added successfully!";
                log_action($conn, $_SESSION['user_id'], "Added a new student: $full_name");

            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    } else {
        $error = "All fields are required!";
    }
}

// Fetch requirements from the database
$requirements = [];
$result = $conn->query("SELECT requirement_id, requirement_name FROM requirements");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requirements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .card {
            width: 400px;
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn_add, .btn_Dashboard {
            background-color: #0f032b;
            color: white;
            width: 100%;
            height: 6vh;
            border-radius: 5px;
            border: none;
            font-weight: bold;
            margin-top: 10px;
        }
        .btn_add:hover, .btn_Dashboard:hover {
            background-color: white;
            border: 1px solid #0f032b;
            color: #0f032b;
        }
        a {
            text-align: center;
            margin: 5px;
            text-decoration: none;
        }

        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #0f032b;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout-btn:hover {
            background-color: white;
            border: 1px solid #0f032b;
            color: #0f032b;
        }

        .requirements-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .requirements-flex div {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card">
        <h4 class="fw-bold text-center">Add Student</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Admission Number</label>
                <input type="text" name="admission_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact</label>
                <input type="text" name="contact" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <!-- Username and Password Fields -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Requirements Checkboxes -->
            <div class="mb-3">
                <label class="form-label">Requirements:</label>
                <div class="requirements-flex">
                    <?php foreach ($requirements as $requirement): ?>
                        <div>
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="requirements[]" 
                                value="<?= htmlspecialchars($requirement['requirement_id']) ?>" 
                                id="req<?= $requirement['requirement_id'] ?>"
                            >
                            <label class="form-check-label ms-1" for="req<?= $requirement['requirement_id'] ?>">
                                <?= htmlspecialchars($requirement['requirement_name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn_add">Add Student</button>
            <button type="button" class="btn_add" onclick="window.location.href='student_managent.php'">Cancel</button>
        </form>
    </div>
</div>

</body>
</html>
