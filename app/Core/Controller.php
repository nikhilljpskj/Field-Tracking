<?php
namespace App\Core;

class Controller {
    public function __construct() {
        if (isset($_SESSION['user_id'])) {
            $this->updateActivity();
        }
    }

    private function updateActivity() {
        // Update activity every 5 minutes to avoid DB spam
        $now = time();
        if (!isset($_SESSION['last_activity_update']) || ($now - $_SESSION['last_activity_update']) > 300) {
            try {
                $db = \Database::getInstance()->getConnection();
                $stmt = $db->prepare("UPDATE users SET last_activity_at = NOW() WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $_SESSION['last_activity_update'] = $now;
            } catch (\Exception $e) {
                // Fail silently
            }
        }
    }

    public function view($view, $data = []) {
        extract($data);
        $viewFile = BASE_PATH . "/app/Views/" . $view . ".php";
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View $view does not exist.");
        }
    }

    public function model($model) {
        $modelClass = "App\\Models\\" . $model;
        if (!class_exists($modelClass)) {
            require_once BASE_PATH . "/app/Models/" . $model . ".php";
        }
        return new $modelClass();
    }

    public function checkRole($roles) {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], (array)$roles)) {
            header("Location: dashboard");
            exit;
        }
    }
    
    public function redirect($url) {
        header("Location: " . $url);
        exit;
    }
}
