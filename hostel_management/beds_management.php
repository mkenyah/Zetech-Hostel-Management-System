<?php
session_start();
include('db.php'); // Ensure this file contains your database connection

// Find all expired allocations
$sql = "
    SELECT a.bed_id 
    FROM allocations a 
    JOIN beds b ON a.bed_id = b.bed_id 
    WHERE a.allocations_to < ? AND b.status = 'Occupied'
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $now);
$stmt->execute();
$result = $stmt->get_result();

$updatedBeds = 0;

while ($row = $result->fetch_assoc()) {
    $updateStmt = $conn->prepare("UPDATE beds SET status = 'Available' WHERE bed_id = ?");
    $updateStmt->bind_param("i", $row['bed_id']);
    $updateStmt->execute();
    $updatedBeds++;
    $updateStmt->close();
}

$stmt->close();
// echo "$updatedBeds bed(s) status updated.";



$result = $conn->query("
    SELECT 
        b.bed_id, 
        b.bed_number, 
        b.status, 
        r.room_id,
        r.room_number,
        h.hostel_name,
        h.gender,
        c.campus_name
    FROM beds b
    LEFT JOIN rooms r ON b.room_id = r.room_id
    LEFT JOIN hostels h ON r.hostel_id = h.hostel_id
    LEFT JOIN campus c ON h.campus_id = c.campus_id
");


// Handle bed deletion
if (isset($_GET['delete'])) {
    $bedId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM beds WHERE bed_id = ?");
    $stmt->bind_param("i", $bedId);
    if ($stmt->execute()) {
        $success = "Bed deleted successfully!";
    } else {
        $error = "Error deleting bed: " . $stmt->error;
    }
    $stmt->close();
}

// Handle bed allocation
if (isset($_POST['allocate'])) {
    $bedId = $_POST['bed_id'];
    $studentId = $_POST['student_id'];
    $allocatedFrom = $_POST['allocations_from'];
    $allocatedTo = $_POST['allocations_to'];
    $userId = $_SESSION['user_id']; // Assuming user is logged in

    // Insert the allocation record into the database
    $stmt = $conn->prepare("INSERT INTO allocations (student_id, bed_id, allocations_from, allocations_to, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $studentId, $bedId, $allocatedFrom, $allocatedTo, $userId);
    if ($stmt->execute()) {
        // After successful allocation, update the bed status to "Occupied"
        $updateStmt = $conn->prepare("UPDATE beds SET status = 'Occupied' WHERE bed_id = ?");
        $updateStmt->bind_param("i", $bedId);
        $updateStmt->execute();
        $updateStmt->close();

        $success = "Bed allocated successfully and status updated to 'Occupied'!";
    } else {
        $error = "Error allocating bed: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch beds from the database
// $result = $conn->query("SELECT b.bed_id, b.bed_number, b.status, r.room_id FROM beds b LEFT JOIN rooms r ON b.room_id = r.room_id");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bed Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
            max-width: 95%;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .btn-primary {
            background-color: #0f032b !important;
            color: white !important;
            border: none !important;
            width: 150px;
        }
        .btn-danger {
            background-color: rgb(252, 0, 0);
            color: white;
            gap: 4px;
            border: none;
        }
        .btn-primary:hover {
            background-color: white !important;
            border: 1px solid #0f032b !important;
            color: #0f032b !important;
        }
        .btn-danger:hover {
            background-color: white;
            border: 1px solid rgb(252, 6, 6);
            color: rgb(245, 13, 13);
        }


        /* #dashboard{
            background-color: aqua;
        } */
        .table thead {
            background-color: #0f032b;
            color: white;
            text-align: center;
        }
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            background-color: #0f032b;
            color: white;
            border: none;
        }
        .logout-btn:hover {
            background-color: white;
            border: 1px solid #0f032b;
            color: #0f032b;
        }
        a {
            margin: 5px;
        }
        td {
            text-align: center;
        }
        td.red {
            color: red;
        }
        td.green {
            color: green;
        }
        td.occupied {
            color: orange;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
    <a href="admin.php" class="btn btn-primary">Dashboard</a>
        <a href="add_beds.php" class="btn logout-btn">Add New Bed</a>
        <a id="dashbtn" href="add_beds.php" class="btn logout-btn">Add New Bed</a>
        <h4 class="fw-bold text-center">Bed Management</h4>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <!-- <th>ID</th> -->
                    <th>Bed Number</th>
                    <th>Room ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <!-- <td><?php //echo $row['bed_id']; ?></td> -->
                        <td><?php echo $row['bed_number']; ?></td>
                        <td>
    <?php 
        if ($row['room_id']) {
            echo "Room " . htmlspecialchars($row['room_number']) . 
                 " (" . htmlspecialchars($row['hostel_name']) . 
                 ", " . htmlspecialchars($row['gender']) . 
                 ", " . htmlspecialchars($row['campus_name']) . ")";
        } else {
            echo "Unassigned";
        }
    ?>
</td>

                        <td class="<?php 
                            if ($row['status'] == 'Unavailable') {
                                echo 'red';
                            } elseif ($row['status'] == 'Occupied') {
                                echo 'occupied';
                            } else {
                                echo 'green';
                            }
                        ?>"><?php echo $row['status']; ?></td>
                        <td>
                            <a href="edit_bed.php?id=<?php echo $row['bed_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="beds_management.php?delete=<?php echo $row['bed_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this bed?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- <a href="admin.php" class="btn btn-primary">Cancel</a> -->
    </div>
</div>
</body>
</html>
