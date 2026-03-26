<?php
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/app/Core/Config.php';
\App\Core\Config::load(BASE_PATH . '/.env');
date_default_timezone_set('Asia/Kolkata');


require_once BASE_PATH . '/config/database.php';
session_start();

// Autoloading classes (Simple PSR-4-like)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Simple Router
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'login';

// Define controllers/routes
$routes = [
    'dashboard' => 'DashboardController',
    'login' => 'AuthController',
    'logout' => 'AuthController',
    'attendance' => 'AttendanceController',
    'tasks' => 'TaskController',
    'meetings' => 'MeetingController',
    'tracking' => 'TrackingController',
    'targets' => 'TargetController',
    'allowance' => 'AllowanceController',
    'reports' => 'ReportController',
    'reports-daily' => 'ReportController',
    'reports-weekly' => 'ReportController',
    'reports-monthly' => 'ReportController',
    'users' => 'UserController',
    'settings' => 'SettingsController',
    'map' => 'MapController',
    'profile' => 'ProfileController',
    'leaves' => 'LeaveController',
    'leave-manage' => 'LeaveController',
    'leave-allocate' => 'LeaveController',
    'payroll' => 'PayrollController',
    'payroll-manage' => 'PayrollController',
    'travel-history' => 'TravelController',
    'attendance-history' => 'AttendanceController'
];

if (array_key_exists($url, $routes)) {
    $controllerName = "App\\Controllers\\" . $routes[$url];
    
    if (!class_exists($controllerName)) {
        // Fallback for case sensitivity or path issues
        $file = __DIR__ . "/app/Controllers/" . $routes[$url] . ".php";
        if (file_exists($file)) {
            require_once $file;
        } else {
            http_response_code(500);
            echo "500 - Controller $controllerName not found";
            exit;
        }
    }
    
    $controller = new $controllerName();
    
    // Auth bypass for login/logout
    if ($url == 'login') {
        $controller->login();
        exit;
    }
    if ($url == 'logout') {
        $controller->logout();
        exit;
    }

    // Protected routes check
    if (!isset($_SESSION['user_id'])) {
        header("Location: login");
        exit;
    }

    // Handle actions
    $action = $_GET['action'] ?? 'index';
    
    // Manual overrides for specific URL-to-method mappings
    if ($url == 'reports-monthly') {
        $action = 'monthly';
    } elseif ($url == 'reports-daily') {
        $action = 'index';
    } elseif ($url == 'leave-manage') {
        $action = $action === 'index' ? 'manage' : $action;
    } elseif ($url == 'leave-allocate') {
        $action = $action === 'index' ? 'allocate' : $action;
    } elseif ($url == 'attendance-history') {
        $action = 'history';
    } elseif ($url == 'payroll-manage') {
        $action = $action === 'index' ? 'manage' : $action;
    }

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        $controller->index();
    }

} else {
    // 404
    http_response_code(404);
    echo "404 - Page Not Found";
}
