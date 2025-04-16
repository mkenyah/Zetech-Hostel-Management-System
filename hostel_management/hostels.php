<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch hostels from the database
$sql = "SELECT * FROM hostels";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hostels</title>
    <style>
        body {
            background-color: white;
            color: #0f032b;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #0f032b;
            text-align: left;
        }
        th {
            background-color: #0f032b;
            color: white;
        }
        .btn {
            background-color: #0f032b;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            border-radius: 3px;
        }
        .btn:hover {
            background-color: #140542;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Hostels</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td>
                        <a href="edit_hostel.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                        <a href="delete_hostel.php?id=<?php echo $row['id']; ?>" class="btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <br>
        <a href="add_hostel.php" class="btn">Add New Hostel</a>
    </div>
</body>
</html>
