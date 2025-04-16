<?php  

// functions.php

// Function to log actions into the 'logs' table
function log_action($conn, $user_id, $action) {
    // Prepare the SQL query to insert the log
    $sql = "INSERT INTO logs (user_id, action) VALUES (?, ?)";
    
    // Prepare the statement
    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "is", $user_id, $action);
        
        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            return true; // Log successfully inserted
        } else {
            return false; // Error in inserting log
        }
        
        // Close the statement
        mysqli_stmt_close($stmt);
    }
    return false; // Error in preparing the query
}

?>