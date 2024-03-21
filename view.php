<?php

require_once "database.php";
require_once "authentication.php";
require_once "./roles/admin.php";
require_once "./roles/cashier.php";

class View {
    private $auth;
    private $admin;
    private $cashier;

    public function __construct() {
        $db = new Database();
        $this->auth = new Authentication($db->conn);
        $this->admin = new Admin($db->conn);
        $this->cashier = new Cashier($db->conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['authentication'])) {
            switch ($_GET['authentication']) {
                case 'login':
                    $this->handleLogin();
                    break;
                case 'logout':
                    $this->handleLogout();
                    break;
                default:
                    http_response_code(400);
                    echo "Bad Request";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['admin_action'])) {
            switch ($_GET['admin_action']) {
                case 'createRole':
                    $this->handleCreateRole();
                    break;
                case 'createUser':
                    $this->handleCreateUser();
                    break;
                case 'disableUser':
                    $this->handleDisableUser();
                    break;
                case 'changeUserRole':
                    $this->handleChangeUserRole();
                    break;
                case 'changeAdminPassword':
                    $this->handleUpdateAdminPassword();
                    break;    
                default:
                    http_response_code(400);
                    echo "Bad Request";
            }
        }  else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['admin_view_all'])) {
            switch ($_GET['admin_view_all']) {
                case 'viewAllUsers':
                    $this->handleViewAllUsers();
                    break;
                default:
                    http_response_code(400);
                    echo "Bad Request";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['cashier_action'])) {
            switch ($_GET['cashier_action']) {
                case 'createOrder':
                    $this->handleCreateOrder();
                    break;
                case 'updateOrder':
                    $this->handleUpdateOrder();
                    break;
                case 'cancelOrder':
                    $this->handleCancelOrder();
                    break;
                case 'viewCustomerOrders':
                    $this->handleViewCustomerOrders();
                    break;
                case 'generateTotalAmountPerCustomer':
                    $this->handleGenerateTotalAmountPerCustomer();
                    break;
                case 'changeCashierPassword':
                    $this->handleUpdateCashierPassword();
                    break;
                default:
                    http_response_code(400);
                    echo "Bad Request";
            }
        }
    }
    
    // AUTHENTICATION
    private function handleLogin() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $loginResult = $this->auth->login($username, $password);
            if ($loginResult === true) {
                echo json_encode(["message" => "Logged in successfully"]);
            } elseif (is_string($loginResult)) {
                echo json_encode(["error" => $loginResult]);
            } else {
                echo json_encode(["error" => "Invalid username or password"]);
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }
    
    private function handleLogout() {
        if (isset($_POST['username'])) {
            $userid = $_POST['username'];
            $logoutResult = $this->auth->logout($userid);
            if ($logoutResult === true) {
                echo json_encode(["message" => "Logged out successfully"]);
            } elseif (is_string($logoutResult)) {
                echo json_encode(["error" => $logoutResult]); // No account logged in message
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }
    


    //ADMIN PRIVILEDGES
    private function handleCreateRole() {
        if (isset($_POST['role']) && isset($_POST['description'])) {
            $role = $_POST['role'];
            $description = $_POST['description'];
            if ($this->admin->createRole($role, $description)) {
                echo "Role created successfully";
            } else {
                echo "Error creating role";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleCreateUser() {
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['roleid'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $roleid = $_POST['roleid'];
            if ($this->admin->createUser($username, $password, $roleid)) {
                echo "User created successfully";
            } else {
                echo "Error creating user";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleDisableUser() {
        if (isset($_POST['userid'])) {
            $userid = $_POST['userid'];
            if ($this->admin->disableUser($userid)) {
                echo "User disabled successfully";
            } else {
                echo "Error disabling user";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleViewAllUsers() {
        $response = $this->admin->viewAllUsers();
        echo $response;
    }
    
    private function handleChangeUserRole() {
        if (isset($_POST['userid']) && isset($_POST['roleid'])) {
            $userid = $_POST['userid'];
            $roleid = $_POST['roleid'];
            if ($this->admin->changeUserRole($userid, $roleid)) {
                echo "User role changed successfully";
            } else {
                echo "Error changing user role";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleUpdateAdminPassword() {
        if (isset($_POST['old_password']) && isset($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            if ($this->admin->updatePassword($old_password, $new_password)) {
                echo "Admin password updated successfully";
            } else {
                echo "Error updating admin password";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }


    // CASHIER ACTIONS
    private function handleCreateOrder() {
        if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['customer'])) {
            $name = $_POST['name'];
            $price = $_POST['price'];
            $customer = $_POST['customer'];
            if ($this->cashier->createOrder($name, $price, $customer)) {
                echo "Order created successfully";
            } else {
                echo "Error creating order";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleUpdateOrder() {
        if (isset($_POST['orderid']) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['customer'])) {
            $orderid = $_POST['orderid'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $customer = $_POST['customer'];
            if ($this->cashier->updateOrder($orderid, $name, $price, $customer)) {
                echo "Order updated successfully";
            } else {
                echo "Error updating order";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleCancelOrder() {
        if (isset($_POST['orderid'])) {
            $orderid = $_POST['orderid'];
            if ($this->cashier->cancelOrder($orderid)) {
                echo "Order canceled successfully";
            } else {
                echo "Error canceling order";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleViewCustomerOrders() {
        if (isset($_POST['customer'])) {
            $customer = $_POST['customer'];
            $response = $this->cashier->viewCustomerOrders($customer);
            echo $response;
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleGenerateTotalAmountPerCustomer() {
        if (isset($_POST['customer'])) {
            $customer = $_POST['customer'];
            $totalAmount = $this->cashier->generateTotalAmountPerCustomer($customer);
            if ($totalAmount !== false) {
                echo json_encode(["message" => "Total amount for customer $customer: P$totalAmount"]);
            } else {
                echo "Error generating total amount";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleUpdateCashierPassword() {
        if (isset($_POST['old_password']) && isset($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            if ($this->cashier->updatePassword($old_password, $new_password)) {
                echo "Cashier password updated successfully";
            } else {
                echo "Error updating cashier password";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    

}

?>
