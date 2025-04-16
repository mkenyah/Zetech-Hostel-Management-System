<?php
class Logger
{
    private $db;
    private $user_id;

    public function __construct($db, $user_id)
    {
        $this->db = $db;
        $this->user_id = $user_id;
    }

    // Method to log an action
    public function logAction($action)
    {
        $log_date = date('Y-m-d H:i:s');
        
        // Insert action into logs table
        $query = "INSERT INTO logs (user_id, action, log_date) VALUES (:user_id, :action, :log_date)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':log_date', $log_date);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
