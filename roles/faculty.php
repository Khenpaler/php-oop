<?php

class Faculty extends Database {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Check if the user is a faculty member
    public function isFaculty() {
        return isset($_SESSION['roleid']) && $_SESSION['roleid'] === 2;
    }

    // Create a student user (faculty privilege)
    public function createStudent($username, $password, $roleid = 3) {
        if ($this->isFaculty()) {
            // Check if username already exists
            $sql = "SELECT * FROM users_table WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false; // Username already exists
            } else {
                // Insert the student user
                $sql = "INSERT INTO users_table (username, password, roleid) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sss", $username, $password, $roleid);
                if ($stmt->execute()) {
                    return true; // Student user created successfully
                } else {
                    return false; // Error creating student user
                }
            }
        } else {
            return false; // Only faculty can create student users
        }
    }

    // Disable a student user (faculty privilege)
    public function disableStudent($username) {
        if ($this->isFaculty()) {
            // Disable the student user
            $sql = "UPDATE users_table SET status = 0 WHERE username = ? AND roleid = 3";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                return true; // Student user disabled successfully
            } else {
                return false; // Error disabling student user
            }
        } else {
            return false; // Only faculty can disable student users
        }
    }

    // Update faculty password with old password required
    public function updatePassword($oldPassword, $newPassword) {
        if ($this->isFaculty()) {
            $loggedInUserId = $_SESSION['userid'];
            // Check if the old password matches the current password
            $sql = "SELECT * FROM users_table WHERE userid = ? AND password = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $loggedInUserId, $oldPassword);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                // Update the password
                $sqlUpdate = "UPDATE users_table SET password = ? WHERE userid = ?";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $newPassword, $loggedInUserId);
                if ($stmtUpdate->execute()) {
                    return true; // Password updated successfully
                } else {
                    return false; // Error updating password
                }
            } else {
                return false; // Old password does not match
            }
        } else {
            return false; // Only faculty can update their password
        }
    }
}

?>
