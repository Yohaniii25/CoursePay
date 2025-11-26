<?php
class Database {
    private $host = "localhost";
    private $username = "sltdigi2_coursepay";
    private $password = "pGz[t%1LZWZZ";
    private $dbname = "sltdigi2_coursepay";
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);
        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>