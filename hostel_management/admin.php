<?php 
session_start();

include("db.php"); 

include("functions.php");
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();

    
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #0f032b;
            color: white;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .logout-btn {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: absolute;
            font-weight: bold;
            top: 10px;
            right: 20px;
            background-color: white;
            color: #0f032b ;
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            color:  #0f032b;
            transition: transform 0.3s, box-shadow 0.3s;
           
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background-color: white;
            color: #0f032b;
            width: 250px;
            height: 70px;
            padding: 30px;
            margin: 20px;
            border-radius: 10px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px white;
        }
    </style>
</head>
<body>
    <button class="logout-btn" onclick="location.href='login.php'">Log out</button>
    <?php  log_action($conn, $_SESSION['user_id'], "logged out");?>
    <h1>Admin Dashboard</h1>
    <div class="container">
        <div class="card" onclick="location.href='student_managent.php'">Manage Students</div>
        <div class="card" onclick="location.href='campus.php'">Campus</div>
        <div class="card" onclick="location.href='hostel_management.php'">Manage Hostels</div>
        <div class="card" onclick="location.href='hostel_services.php'">Hostels Services</div>
        <div class="card" onclick="location.href='manage_rooms.php'">Manage Rooms</div>
        <div class="card" onclick="location.href='beds_management.php'">Manage Beds</div>
        <div class="card" onclick="location.href='allocation.php'">Allocations</div>
        <div class="card" onclick="location.href='logs.php'">View Logs</div>
        <div class="card" onclick="location.href='usermanagement.php'">User Management</div>
        <div class="card" onclick="location.href='studentrequirements.php'">Student Requirements</div>
    </div>
</body>
</html>


