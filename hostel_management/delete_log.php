<?php
session_start();
include 'db.php'; // include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Perform delete all logs
$delete_all_sql = "DELETE FROM logs";

if (mysqli_query($conn, $delete_all_sql)) {
    echo json_encode(['status' => 'success', 'message' => 'All logs have been deleted.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error deleting logs: ' . mysqli_error($conn)]);
}
?>
