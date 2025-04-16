<?php
session_start();
include('db.php'); 


$error = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {

        // First check in users table
        $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Found in users table
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // Log action and redirect
                // log_action($conn, $_SESSION['user_id'], "logged in");

                if ($row['role'] === 'Admin') {
                    header("Location: admin.php");
                } elseif ($row['role'] === 'house_manager') {
                    header("Location: house_keeper.php");
                } else {
                    // Just in case some other role is added
                    header("Location: dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            // Not found in users, now check in students
            $stmt = $conn->prepare("SELECT student_id, username, password FROM students WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $student_result = $stmt->get_result();

            if ($student_result->num_rows === 1) {
                $student_row = $student_result->fetch_assoc();
                if (password_verify($password, $student_row['password'])) {
                    $_SESSION['student_id'] = $student_row['student_id'];
                    $_SESSION['username'] = $student_row['username'];
                    $_SESSION['role'] = 'Student';

                    // Log action for student (with student_logs)
                    

                    header("Location:stude_d.php");
                    exit();
                } else {
                    $error = "Incorrect password!";
                }
            } else {
                $error = "Username not found!";
            }
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <style>
        /* Full-page background */
        .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-image: url('./images/school.png'); /* Ensure this path is correct */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .bg-image {
                height: 100vh;
                background-size: cover;
            }
        }

        .logo {
            width: 120px;
        }

        .card {
            width: 380px;
            backdrop-filter: blur(20px);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-size: 15px;
            margin-left: -220px;
        }

        .btn_login{
            background-color: #0f032b;
            color: white;
            width: 120px;
            height: 6vh;
            border-radius: 5px;
            border: none;
            font-weight: bold;
        }

        .btn_login:hover{
            background-color: white;
            border: 1px solid  #0f032b;
            color: #0f032b;
        }

    </style>
</head>
<body>

<!-- Background Image -->
<div class="bg-image"></div>

<!-- Login Form -->
<section id="login_container" class="d-flex flex-column justify-content-center align-items-center min-vh-100 mt-n5">
    <div class="card shadow-5-strong bg-body-tertiary text-center">
        <div class="card-body">
            <h4 class="fw-bold mb-3">Hostel Management system</h4>
            <img class="logo mb-3" src="./images/logo.png" alt="Logo">

            <!-- Display error messages -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <h3>User Name</h3>
                <div class="form-outline mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required />
                </div>

                <h3>Password</h3>
                <div class="form-outline mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required />
                </div>

                <button class="btn_login" type="submit">Login</button>
            </form>
        </div>
    </div>
</section>

</body>
</html>
