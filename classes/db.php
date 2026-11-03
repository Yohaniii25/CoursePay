<?php
class Database {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        // Automatically check if running locally (XAMPP) or on live server
        $isLocal = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1'])
                   || (php_sapi_name() === 'cli' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');

        if ($isLocal) {
            $host     = "localhost";
            $username = "root";
            $password = "";
            $dbname   = "coursepay";
        } else {
            // Live Server credentials (e.g. www.gjrti.gov.lk / cPanel)
            $host     = "localhost";
            $username = "user_01_gjrti_coursepay";
            $password = "G7~pk0g13";
            $dbname   = "user_01_gjrti_coursepay";
        }

        try {
            $this->conn = new mysqli($host, $username, $password, $dbname);
            if ($this->conn->connect_error) {
                throw new Exception("Database connection failed: " . $this->conn->connect_error);
            }
        } catch (Throwable $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>
