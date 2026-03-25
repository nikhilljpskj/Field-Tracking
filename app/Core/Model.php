<?php
namespace App\Core;

require_once BASE_PATH . '/config/database.php';

class Model {
    protected $db;

    public function __construct() {
        $this->db = \Database::getInstance()->getConnection();
    }
}
