<?php

class Student extends Database {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Check if the user is a student
    public function isStudent() {
        return isset($_SESSION['roleid']) && $_SESSION['roleid'] === 3;
    }

    // Update student password
    public function updatePassword($oldPassword, $newPassword) {
        if ($this->isStudent()) {
            // Verify old password
            $studentId = $_SESSION['userid'];
            $sql = "SELECT * FROM users_table WHERE userid = ? AND password = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $studentId, $oldPassword);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                // Update password
                $sql = "UPDATE users_table SET password = ? WHERE userid = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("si", $newPassword, $studentId);
                if ($stmt->execute()) {
                    return true; // Password updated successfully
                } else {
                    return false; // Error updating password
                }
            } else {
                return false; // Old password is incorrect
            }
        } else {
            return false; // Only students can update their password
        }
    }
}


?>
