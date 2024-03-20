<?php

require_once "Database.php";
require_once "authentication.php";
require_once "./roles/admin.php";
require_once "./roles/faculty.php";
require_once "./roles/student.php";

class View {
    private $auth;
    private $admin;
    private $faculty;
    private $student;

    public function __construct() {
        $db = new Database();
        $this->auth = new Authentication($db->conn);
        $this->admin = new Admin($db->conn);
        $this->faculty = new Faculty($db->conn);
        $this->student = new Student($db->conn);

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
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['faculty_action'])) {
            switch ($_GET['faculty_action']) {
                case 'createStudent':
                    $this->handleCreateStudent();
                    break;
                case 'disableStudent':
                    $this->handleDisableStudent();
                    break;
                case 'updateFacultyPassword':
                    $this->handleUpdateFacultyPassword();
                    break;
                default:
                    http_response_code(400);
                    echo "Bad Request";
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['student_action'])) {
            switch ($_GET['student_action']) {
                case 'updateStudentPassword':
                    $this->handleUpdateStudentPassword();
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

    // FACULTY PRIVILEGES
    private function handleCreateStudent() {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if ($this->faculty->createStudent($username, $password)) {
                echo "Student created successfully";
            } else {
                echo "Error creating student";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleDisableStudent() {
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            if ($this->faculty->disableStudent($username)) {
                echo "Student disabled successfully";
            } else {
                echo "Error disabling student";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

    private function handleUpdateFacultyPassword() {
        if (isset($_POST['old_password']) && isset($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            if ($this->faculty->updatePassword($old_password, $new_password)) {
                echo "Faculty password updated successfully";
            } else {
                echo "Error updating faculty password";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }
    
    // STUDENT PRIVILEGES
    private function handleUpdateStudentPassword() {
        if (isset($_POST['old_password']) && isset($_POST['new_password'])) {
            $old_password = $_POST['old_password'];
            $new_password = $_POST['new_password'];
            if ($this->student->updatePassword($old_password, $new_password)) {
                echo "Student password updated successfully";
            } else {
                echo "Error updating student password";
            }
        } else {
            http_response_code(400);
            echo "Bad Request";
        }
    }

}

?>
