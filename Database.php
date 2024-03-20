<?php

class Database {
    private $_server = "localhost";
    private $_username = "root";
    private $_password = "";
    private $_database = "problem_1";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->_server, $this->_username, $this->_password, $this->_database);

        if ($this->conn->connect_error) {
            die("Cannot connect to database: " . $this->conn->connect_error);
        } 
    }
}

?>
