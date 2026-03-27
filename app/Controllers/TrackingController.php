<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tracking;
use App\Models\User;

class TrackingController extends Controller {
    public function index() {
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            return $this->team();
        }

        $trackingModel = new Tracking();
        $route = $trackingModel->getRoute($_SESSION['user_id'], date('Y-m-d'));
        
        $data = [
            'title' => 'Live Tracking - My Route',
            'route' => $route
        ];
        $this->view('tracking', $data);
    }

    public function team() {
        $this->checkRole(['Admin', 'Manager']);
        $trackingModel = new Tracking();
        $userModel = new User();
        
        $team = $userModel->getAll(); // Allow both Admin and Manager to monitor all users
        
        $teamIds = array_column($team, 'id');
        $locations = $trackingModel->getTeamLastLocations($teamIds);
        
        $data = [
            'title' => 'Team Live Monitoring',
            'locations' => $locations,
            'team' => $team
        ];
        $this->view('tracking_team', $data);
    }

    public function log() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['latitude']) && isset($_SESSION['is_checked_in'])) {
            $trackingModel = new Tracking();
            $lat = $_POST['latitude'];
            $lng = $_POST['longitude'];
            $accuracy = $_POST['accuracy'] ?? 0;
            
            $result = $trackingModel->logLocation($_SESSION['user_id'], $lat, $lng, $accuracy);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }
    }
}
