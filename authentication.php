<?php

class Authentication extends Database {
    public $conn;

    public function __construct($conn) {
        // Start session
        session_start();
        $this->conn = $conn;
    }

    // Login method
    public function login($username, $password) {

        // Check if a session is already active
        if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
            return "An account is already logged in. Please logout first.";
        }

        $sql = "SELECT * FROM users_table WHERE username = ? AND password = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($user['status'] == 0) {
                return "Your account is disabled. Please contact the administrator.";
            }
            $_SESSION['userid'] = $user['userid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['roleid'] = $user['roleid'];
            $_SESSION['is_logged_in'] = true;
            // Update is_logged_in status in database
            $sql = "UPDATE users_table SET is_logged_in = 1 WHERE userid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user['userid']);
            $stmt->execute();
            return true; // Login successful
        } else {
            return false; // Login failed
        }
    }

   // Logout method
    public function logout($username) {
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            return "No account currently logged in"; // Return a message indicating no account logged in
        }
        // End session
        session_unset();
        session_destroy();
        // Update is_logged_in status in database
        $sql = "UPDATE users_table SET is_logged_in = 0 WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return true; // Logout successful
    }

}

?>
