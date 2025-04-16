<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in as a student
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
$sql = "SELECT  
    a.allocation_id, 
    s.full_name, 
    s.admission_number, 
    b.bed_number, 
    a.allocated_date, 
    a.allocations_from, 
    a.allocations_to, 
    u.username AS allocated_by,
    hs.hostel_name,
    c.campus_name,
    h.username AS house_keeper,
    r.room_number  -- Added room_number here
FROM allocations a
JOIN students s ON a.student_id = s.student_id
JOIN beds b ON a.bed_id = b.bed_id
JOIN users u ON a.user_id = u.user_id
JOIN rooms r ON b.room_id = r.room_id
JOIN hostels hs ON r.hostel_id = hs.hostel_id
JOIN campus c ON hs.campus_id = c.campus_id
LEFT JOIN users h ON hs.user_id = h.user_id";
  // Changed alias 'u' to 'h' for housekeeper

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $student = mysqli_fetch_assoc($result);
} else {
    echo "No allocation found for this student.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: white;
            color: #0f032b;
        }
        .container {
            margin-top: 50px;
            max-width: 90%; /* Adjusted width to make it responsive */
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
        }
        .btn-primary:hover {
            background-color: white !important;
            border: 1px solid #0f032b !important;
            color: #0f032b !important;
        }
        td, th {
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
    <div class="text-end mt-4">
            <a href="./stude_d.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        <h4 class="fw-bold text-center">Student Allocation Details</h4>
        <table class="table table-bordered mt-4">
            <tr>
                <th>Student Name</th>
                <td><?php echo $student['full_name']; ?></td>
            </tr>
            <tr>
                <th>Admission number</th>
                <td><?php echo $student['admission_number']; ?></td>
            </tr>
            <tr>
                <th>Hostel Name</th>
                <td><?php echo $student['hostel_name']; ?></td>
            </tr>
            <tr>
                <th>Room Number</th>
                <td><?php echo $student['room_number']; ?></td>
            </tr>
            <tr>
                <th>Bed Number</th>
                <td><?php echo $student['bed_number']; ?></td>
            </tr>
            <tr>
                <th>Allocation date</th>
                <td><?php echo ucfirst($student['allocated_date']); ?></td>
            </tr>
                <th>End date</th>
                <td><?php echo ucfirst($student['allocations_to']); ?></td>
            </tr>
            <tr>
                <th>House Manager </th>
                <td><?php echo $student['house_keeper'] ? $student['house_keeper'] : "No house manager assigned"; ?></td>
            </tr>
            <!-- <tr>
                <th>House Manager Phone Number</th>
                <td><?php // echo $student['house_keeper_phone'] ? $student['house_keeper_phone'] : "No phone number available"; ?></td>
            </tr> -->
        </table>
       
        </div>
    </div>
</div>
</body>
</html>
