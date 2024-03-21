<?php

class Cashier extends Database {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Check if the user is an cashier
    public function isCashier() {
        return isset($_SESSION['roleid']) && $_SESSION['roleid'] === 2;
    }

    // Create an order (only for cashier)
    public function createOrder($name, $price, $customer) {
        if ($this->isCashier()) {
            $sql = "INSERT INTO orders_table (name, price, customer) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $name, $price, $customer);
            if ($stmt->execute()) {
                return true; // Order created successfully
            } else {
                return false; // Error creating order
            }
        } else {
            return false; // Only cashiers can create orders
        }
    }

    // Update an order (only for cashier)
    public function updateOrder($orderid, $name, $price, $customer) {
        if ($this->isCashier()) {
            $sql = "UPDATE orders_table SET name = ?, price = ?, customer = ? WHERE orderid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $name, $price, $customer, $orderid);
            if ($stmt->execute()) {
                return true; // Order updated successfully
            } else {
                return false; // Error updating order
            }
        } else {
            return false; // Only cashiers can update orders
        }
    }

    // Cancel an order (only for cashier)
    public function cancelOrder($orderid) {
        if ($this->isCashier()) {
            $sql = "DELETE FROM orders_table WHERE orderid = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $orderid);
            if ($stmt->execute()) {
                return true; // Order canceled successfully
            } else {
                return false; // Error canceling order
            }
        } else {
            return false; // Only cashiers can cancel orders
        }
    }

    // View all orders of a specific customer (only for cashier)
    public function viewCustomerOrders($customer) {
        if ($this->isCashier()) {
            $sql = "SELECT * FROM orders_table WHERE customer = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $customer);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                return json_encode(["orders" => $data]);
            } else {
                return json_encode(["error" => "No orders found for this customer"]);
            }
        } else {
            return json_encode(["warning" => "You do not have permission to view orders for this customer"]);
        }
    }

    // Generate total amount of orders per customer (only for cashier)
    public function generateTotalAmountPerCustomer($customer) {
        if ($this->isCashier()) {
            $sql = "SELECT SUM(price) AS total_amount FROM orders_table WHERE customer = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $customer);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row['total_amount'];
        } else {
            return false; // Only cashiers can generate total amount per customer
        }
    }

    
    // Update cashier password with old password required
    public function updatePassword($oldPassword, $newPassword) {
        if ($this->isCashier()) {
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
