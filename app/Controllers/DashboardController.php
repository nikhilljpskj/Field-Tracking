<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Travel;
use App\Models\User;

class DashboardController extends Controller {
    public function index() {
        $role = $_SESSION['role'] ?? 'Executive';
        $data = [
            'title' => 'Dashboard - Sales Tracking',
            'role' => $role
        ];

        $db = \Database::getInstance()->getConnection();

        if ($role == 'Admin' || $role == 'Manager') {
            $userModel = new User();
            if ($role == 'Manager') {
                $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                $teamIds = array_column($team, 'id');
                $teamIdList = !empty($teamIds) ? implode(',', $teamIds) : '0';
                $coverage = "WHERE user_id IN ($teamIdList)";
                $userCoverage = "WHERE id IN ($teamIdList)";
                $assignedCoverage = "WHERE assigned_to IN ($teamIdList)";
            } else {
                $coverage = "";
                $userCoverage = "";
                $assignedCoverage = "";
            }

            // 1. Total Employees
            $stmt = $db->query("SELECT COUNT(*) as count FROM users $userCoverage");
            $data['total_employees'] = $stmt->fetch()['count'];
            
            // 2. Today's Attendance count
            $stmt = $db->query("SELECT COUNT(*) as count FROM attendance " . ($coverage ? $coverage . " AND" : "WHERE") . " DATE(check_in_time) = CURDATE()");
            $data['today_attendance'] = $stmt->fetch()['count'];
            
            // 3. Total distance today
            $stmt = $db->query("SELECT SUM(total_distance) as total FROM travel_summary " . ($coverage ? $coverage . " AND" : "WHERE") . " date = CURDATE()");
            $data['total_distance'] = $stmt->fetch()['total'] ?? 0;
            
            // 4. Total meetings today
            $stmt = $db->query("SELECT COUNT(*) as count FROM client_meetings " . ($coverage ? $coverage . " AND" : "WHERE") . " DATE(meeting_time) = CURDATE()");
            $data['today_meetings'] = $stmt->fetch()['count'];

            // Performance Trends (last 7 days)
            $trends = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM client_meetings " . ($coverage ? $coverage . " AND" : "WHERE") . " DATE(meeting_time) = ?");
                $stmt->execute([$date]);
                $trends[] = [
                    'date' => date('D', strtotime($date)),
                    'meetings' => $stmt->fetch()['count']
                ];
            }
            $data['trends'] = $trends;

        } else {
            // Executive Dashboard
            $attendanceModel = new Attendance();
            $travelModel = new Travel();
            $meetingModel = new Meeting();
            
            $data['attendance'] = $attendanceModel->getTodayAttendance($_SESSION['user_id']);
            $summary = $travelModel->getTravelSummary($_SESSION['user_id'], date('Y-m-d'));
            $data['total_distance'] = $summary['total_distance'] ?? 0;
            
            $meetings = $meetingModel->getUserMeetings($_SESSION['user_id']);
            $todayMeetings = array_filter($meetings, function($m) {
                return date('Y-m-d', strtotime($m['meeting_time'])) == date('Y-m-d');
            });
            $data['today_meetings_count'] = count($todayMeetings);
        }

        $this->view('dashboard', $data);
    }
}
