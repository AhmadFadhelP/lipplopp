<?php
/**
 * Database Connection Handler
 * Provides secure database connection with error handling
 */

require_once __DIR__ . '/config.php';

class Database {
    private $conn = null;
    private static $instance = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception("Database connection failed: " . $this->conn->connect_error);
            }

            // Set charset to UTF-8
            $this->conn->set_charset("utf8mb4");

        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database connection error'
            ]);
            exit();
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Prepare statement (wrapper for prepared statements)
     */
    public function prepare($query) {
        return $this->conn->prepare($query);
    }

    /**
     * Execute query safely with parameters
     */
    public function executeQuery($query, $params = [], $types = "") {
        try {
            $stmt = $this->conn->prepare($query);

            if ($stmt === false) {
                throw new Exception("Query preparation failed: " . $this->conn->error);
            }

            if (!empty($params) && !empty($types)) {
                $stmt->bind_param($types, ...$params);
            }

            $stmt->execute();
            return $stmt;

        } catch (Exception $e) {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all results
     */
    public function fetchAll($query, $params = [], $types = "") {
        $stmt = $this->executeQuery($query, $params, $types);

        if ($stmt === false) {
            return [];
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    /**
     * Fetch single result
     */
    public function fetchOne($query, $params = [], $types = "") {
        $stmt = $this->executeQuery($query, $params, $types);

        if ($stmt === false) {
            return null;
        }

        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        return $data;
    }

    /**
     * Insert data and return last insert ID
     */
    public function insert($query, $params = [], $types = "") {
        $stmt = $this->executeQuery($query, $params, $types);

        if ($stmt === false) {
            return false;
        }

        $insertId = $this->conn->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->begin_transaction();
    }

    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }

    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollback();
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Close connection on destruct
     */
    public function __destruct() {
        if ($this->conn !== null) {
            $this->conn->close();
        }
    }
}

// Export connection helper function
function getDB() {
    return Database::getInstance();
}
?>
