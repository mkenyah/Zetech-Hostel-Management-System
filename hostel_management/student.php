<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #0f032b;
            color: white;
            position: fixed;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: white;
            color: #0f032b;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        h2{
            text-align: center;
            color: #0f032b;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    
    <div class="sidebar">
        <h4 class="text-center">Admin Panel</h4>
        <a href="#">Dashboard</a>
        <a href="#">Manage Users</a>
        <a href="#">Manage Products</a>
        <a href="#">Sales Reports</a>
        <a href="#">Logout</a>
    </div>
    
    <div class="content">
        <h2>Welcome, Admin</h2>
       
    </div>
    
</body>
</html>
