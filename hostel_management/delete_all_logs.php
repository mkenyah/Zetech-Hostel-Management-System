<?php
session_start();
include('db.php');

// Delete all logs from the database
$deleteAllQuery = "DELETE FROM logs";
if ($conn->query($deleteAllQuery) === TRUE) {
    echo "All logs deleted successfully.";
} else {
    echo "Error deleting logs: " . $conn->error;
}
?>
