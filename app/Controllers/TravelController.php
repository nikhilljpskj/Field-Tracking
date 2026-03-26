<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tracking;
use App\Models\User;

class TravelController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        $userModel = new User();
        
        $manager_id = ($_SESSION['role'] == 'Manager') ? $_SESSION['user_id'] : null;
        if ($_SESSION['role'] == 'Manager') {
            $employees = $userModel->getExecutivesByManagerId($manager_id);
        } else {
            $employees = $userModel->getAll();
        }

        $selected_user = $_GET['user_id'] ?? ($_SESSION['user_id'] ?? null);
        $selected_date = $_GET['date'] ?? date('Y-m-d');

        $trackingModel = new Tracking();
        $route = $trackingModel->getRoute($selected_user, $selected_date);
        $distance = $trackingModel->getDailyDistance($selected_user, $selected_date);

        $data = [
            'title' => 'Travel Intelligence Audit',
            'employees' => $employees,
            'route' => $route,
            'distance' => round($distance, 2),
            'selected_user' => $selected_user,
            'selected_date' => $selected_date
        ];

        $this->view('travel_history', $data);
    }
}
