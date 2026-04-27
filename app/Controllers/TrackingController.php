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
        
        // 1. Get only active (not disabled/deleted) users
        $allUsers = $userModel->getAll();
        $activeUsers = array_filter($allUsers, function($u) {
            return $u['is_active'] == 1 && in_array($u['role_name'], ['Executive', 'Manager', 'Field Executive']); 
        });
        
        if (empty($activeUsers)) {
            $this->view('tracking_team', ['activePersonnel' => [], 'inactivePersonnel' => [], 'locations' => []]);
            return;
        }

        $teamIds = array_column($activeUsers, 'id');
        $locations = $trackingModel->getTeamLastLocations($teamIds);
        
        // Map locations for quick lookup
        $locMap = [];
        foreach ($locations as $loc) {
            $locMap[$loc['user_id']] = $loc;
        }

        $activePersonnel = [];
        $inactivePersonnel = [];
        $threshold = time() - 3600; // 1 hour ago

        foreach ($activeUsers as $user) {
            $loc = $locMap[$user['id']] ?? null;
            if ($loc && strtotime($loc['logged_at']) >= $threshold) {
                $user['location'] = $loc;
                $activePersonnel[] = $user;
            } else {
                $user['location'] = $loc;
                $inactivePersonnel[] = $user;
            }
        }
        
        $data = [
            'title' => 'Team Live Monitoring',
            'activePersonnel' => $activePersonnel,
            'inactivePersonnel' => $inactivePersonnel,
            'locations' => $locations // For mapping all recent dots
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
