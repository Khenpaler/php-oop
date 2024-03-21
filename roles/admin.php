<?php

class Admin extends Database {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Check if the user is an admin
    public function isAdmin() {
        return isset($_SESSION['roleid']) && $_SESSION['roleid'] === 1;
    }

    // Create a role (only for admin)
    public function createRole($role, $description) {

        if ($this->isAdmin()) {
            $sql = "INSERT INTO roles_table (role, description) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $role, $description);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; // Only admins can create roles
        }
    }


    // Create a user (only for admin)
    public function createUser($username, $password, $roleid) {
        if ($this->isAdmin()) {
            // Check if username already exists
            $sql = "SELECT * FROM users_table WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return false; // Username already exists
            } else {
                // Insert the user
                $sql = "INSERT INTO users_table (username, password, roleid) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("sss", $username, $password, $roleid);
                if ($stmt->execute()) {
                    return true; // User created successfully
                } else {
                    return false; // Error creating user
                }
            }
        } else {
            return false; // Only admins can create users
        }
    }

    // Disable a user (only for admin)
    public function disableUser($userid) {

        if ($this->isAdmin()) {
            $sql = "UPDATE users_table SET status = 0 WHERE userid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $userid);
            if ($stmt->execute()) {
                return true; // User disabled successfully
            } else {
                return false; // Error disabling user
            }
        } else {
            return false; // Only admins can disable users
        }
    }

    
    // View all users (only for admin)
    public function viewAllUsers() {
        if ($this->isAdmin()) {
            $sql = "SELECT * FROM users_table";
            $result = $this->conn->query($sql);
            if ($result->num_rows > 0) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return json_encode(["users" => $data]);
            } else {
                return json_encode(["error" => "No records found"]);
            }
        } else {
            return json_encode(["warning" => "You do not have permission to view all users"]);
        }
    }


    // Change user role (only for admin)
    public function changeUserRole($userid, $roleid) {
        if ($this->isAdmin()) {
            $sql = "UPDATE users_table SET roleid = ? WHERE userid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $roleid, $userid); // Corrected parameter order
            if ($stmt->execute()) {
                return true; // User role changed successfully
            } else {
                return false; // Error changing user role
            }
        } else {
            return false; // Only admins can change user roles
        }
    }

    // Update admin password with old password required
    public function updatePassword($oldPassword, $newPassword) {
        if ($this->isAdmin()) {
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
