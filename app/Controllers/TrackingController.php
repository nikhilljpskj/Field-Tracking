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
        $attendanceModel = new \App\Models\Attendance();
        $leaveModel = new \App\Models\Leave();
        
        // 1. Get only active (not disabled/deleted) users
        $allUsers = $userModel->getAll();
        $activeUsers = array_filter($allUsers, function($u) {
            return $u['is_active'] == 1 && in_array($u['role_name'], ['Executive', 'Manager', 'Field Executive']); 
        });
        
        if (empty($activeUsers)) {
            $this->view('tracking_team', ['activePersonnel' => [], 'inactivePersonnel' => [], 'onLeave' => [], 'absent' => [], 'locations' => []]);
            return;
        }

        $teamIds = array_column($activeUsers, 'id');
        $locations = $trackingModel->getTeamLastLocations($teamIds);
        $attendance = $attendanceModel->getTodayAttendanceBatch($teamIds);
        $onLeaveTodayIds = $leaveModel->getUsersOnLeaveToday();
        
        // Map data for quick lookup
        $locMap = []; foreach ($locations as $loc) { $locMap[$loc['user_id']] = $loc; }
        $attMap = []; foreach ($attendance as $att) { $attMap[$att['user_id']] = $att; }

        $activePersonnel = [];
        $inactivePersonnel = [];
        $loggedInNotCheckedIn = [];
        $checkedOut = [];
        $onLeave = [];
        $absent = [];
        $threshold = time() - 3600; // 1 hour ago

        foreach ($activeUsers as $user) {
            $uid = $user['id'];
            $att = $attMap[$uid] ?? null;
            $loc = $locMap[$uid] ?? null;
            $is_on_leave = in_array($uid, $onLeaveTodayIds);
            $last_active = $user['last_activity_at'] ? strtotime($user['last_activity_at']) : 0;

            if ($att) {
                // Logged In & Checked In
                $user['attendance'] = $att;
                $user['location'] = $loc;
                
                if ($att['check_out_time']) {
                    $checkedOut[] = $user;
                } elseif ($loc && strtotime($loc['logged_at']) >= $threshold) {
                    $activePersonnel[] = $user;
                } else {
                    $inactivePersonnel[] = $user;
                }
            } else {
                // Not Checked In
                if ($is_on_leave) {
                    $onLeave[] = $user;
                } elseif ($last_active >= $threshold) {
                    // Logged in to app but no attendance check-in
                    $loggedInNotCheckedIn[] = $user;
                } else {
                    $absent[] = $user;
                }
            }
        }
        
        $data = [
            'title' => 'Team Live Monitoring',
            'activePersonnel' => $activePersonnel,
            'inactivePersonnel' => $inactivePersonnel,
            'loggedInNotCheckedIn' => $loggedInNotCheckedIn,
            'checkedOut' => $checkedOut,
            'onLeave' => $onLeave,
            'absent' => $absent,
            'locations' => $locations
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
