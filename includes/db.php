<?php
require_once 'config.php';

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Set connection options to prevent timeouts
            $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 30);
            $this->conn->options(MYSQLI_OPT_READ_TIMEOUT, 30);

            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            // Check if connection is lost and try to reconnect once
            if ($this->conn->errno == 2006 || strpos($this->conn->error, 'MySQL server has gone away') !== false) {
                try {
                    $this->reconnect();
                    $result = $this->conn->query($sql);
                    if (!$result) {
                        throw new Exception("Query Error after reconnect: " . $this->conn->error);
                    }
                } catch (Exception $e) {
                    throw new Exception("Query Error: " . $this->conn->error . " (Reconnection failed: " . $e->getMessage() . ")");
                }
            } else {
                throw new Exception("Query Error: " . $this->conn->error);
            }
        }
        return $result;
    }
    
    public function prepare($sql) {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare Error: " . $this->conn->error);
        }
        return $stmt;
    }
    
    public function escape($value) {
        return $this->conn->real_escape_string($value);
    }

    private function reconnect() {
        $this->conn->close();
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            throw new Exception("Reconnection failed: " . $this->conn->connect_error);
        }

        // Set connection options again
        $this->conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 30);
        $this->conn->options(MYSQLI_OPT_READ_TIMEOUT, 30);
        $this->conn->set_charset("utf8mb4");
    }
    
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Global database instance
$db = new Database();
?>
