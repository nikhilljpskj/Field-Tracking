<?php
namespace App\Core;

class Controller {
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
