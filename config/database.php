<?php
use App\Core\Config;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = Config::get('DB_HOST', 'localhost');
        $user = Config::get('DB_USER', 'root');
        $pass = Config::get('DB_PASS', '');
        $name = Config::get('DB_NAME', 'sales_tracking');

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$name", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->exec("SET time_zone = '+05:30'");

        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
