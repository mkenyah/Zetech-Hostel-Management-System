<?php
session_start();
include('db.php');
include("functions.php");

$success = $error = "";
$allocation_id = $_GET['id'] ?? null;

if (!$allocation_id) {
    die("Invalid Allocation ID.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bed_id = $_POST['bed_id'];
    $alloc_from = $_POST['allocations_from'];
    $alloc_to = $_POST['allocations_to'];

    $stmt = $conn->prepare("UPDATE allocations SET bed_id=?, allocations_from=?, allocations_to=? WHERE allocation_id=?");
    $stmt->bind_param("issi", $bed_id, $alloc_from, $alloc_to, $allocation_id);

    if ($stmt->execute()) {
        $success = "Allocation updated successfully!";
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            log_action($conn, $user_id, "Edited allocation ID $allocation_id");
        }
        
    } else {
        $error = "Error updating allocation: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch current allocation details
$stmt = $conn->prepare("
    SELECT a.*, s.full_name, s.gender 
    FROM allocations a
    JOIN students s ON a.student_id = s.student_id
    WHERE a.allocation_id = ?
");
$stmt->bind_param("i", $allocation_id);
$stmt->execute();
$result = $stmt->get_result();
$allocation = $result->fetch_assoc();
$stmt->close();

if (!$allocation) {
    die("Allocation not found.");
}

// Fetch all available beds (including current one)
$beds = $conn->query("
    SELECT beds.bed_id, beds.bed_number, hostels.hostel_name, hostels.gender AS hostel_gender
    FROM beds
    JOIN rooms ON beds.room_id = rooms.room_id
    JOIN hostels ON rooms.hostel_id = hostels.hostel_id
    WHERE beds.bed_id NOT IN (
        SELECT bed_id FROM allocations WHERE allocation_id != $allocation_id
    ) OR beds.bed_id = {$allocation['bed_id']}
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Allocation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: white;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0f032b !important;
            color: white !important;
            border: none !important;
        }
        .btn-primary:hover {
            background-color: white !important;
            color: #0f032b !important;
            border: 1px solid #0f032b !important;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0f032b;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h4 class="text-center fw-bold">Edit Allocation</h4>
        <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="post">
            <div class="mb-3">
                <label>Student Name:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($allocation['full_name']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="bed_id">Select Bed:</label>
                <select name="bed_id" id="bed_id" class="form-control" required>
                    <option value="">-- Select Bed --</option>
                    <?php while ($b = $beds->fetch_assoc()): ?>
                        <option value="<?= $b['bed_id']; ?>" data-gender="<?= $b['hostel_gender']; ?>"
                            <?= ($b['bed_id'] == $allocation['bed_id']) ? 'selected' : '' ?>>
                            <?= "Bed " . htmlspecialchars($b['bed_number']) . " - " . htmlspecialchars($b['hostel_name']) . " (" . $b['hostel_gender'] . ")" ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="allocations_from">From Date:</label>
                <input type="datetime-local" name="allocations_from" class="form-control"
                       value="<?= date('Y-m-d\TH:i', strtotime($allocation['allocations_from'])) ?>" required>
            </div>

            <div class="mb-3">
                <label for="allocations_to">To Date:</label>
                <input type="datetime-local" name="allocations_to" class="form-control"
                       value="<?= date('Y-m-d\TH:i', strtotime($allocation['allocations_to'])) ?>" required>
            </div>

            <div class="d-flex justify-content-between">
                <a href="./h_allocations.php" class="btn btn-primary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Allocation</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
