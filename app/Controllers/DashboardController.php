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
        $user_id = $_SESSION['user_id'];
        $db = \Database::getInstance()->getConnection();
        
        $data = [
            'title' => 'Dashboard - Performance Intelligence',
            'role' => $role
        ];

        // Fetch Performance Thresholds
        $stmt = $db->query("SELECT * FROM performance_configs WHERE key_name = 'daily_visits'");
        $thresholds = $stmt->fetch() ?: ['value_low' => 4, 'value_avg' => 7, 'value_exc' => 10];
        $data['thresholds'] = $thresholds;

        // Check if role is Admin, Manager, or HR to show administrative stats
        $isAdminOrMgr = in_array($role, ['Admin', 'Manager', 'HR']);

        if ($isAdminOrMgr) {
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
            
            // 4. Total meetings today (Approved only)
            $stmt = $db->query("SELECT COUNT(*) as count FROM client_meetings " . ($coverage ? $coverage . " AND" : "WHERE") . " DATE(meeting_time) = CURDATE() AND status = 'Approved'");
            $count = $stmt->fetch()['count'];
            $data['today_meetings'] = $count;
            $data['today_meetings_count'] = $count;

            // Performance Trends (last 7 days - Approved only)
            $trends = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM client_meetings " . ($coverage ? $coverage . " AND" : "WHERE") . " DATE(meeting_time) = ? AND status = 'Approved'");
                $stmt->execute([$date]);
                $trends[] = [
                    'date' => date('D', strtotime($date)),
                    'meetings' => $stmt->fetch()['count']
                ];
            }
            $data['trends'] = $trends;

        } else {
            // Executive Dashboard - Provide default values for variables used in common dashboard sections
            $attendanceModel = new \App\Models\Attendance();
            $notifModel = new \App\Models\Notification();
            
            $data['attendance'] = $attendanceModel->getTodayAttendance($user_id);
            
            // Daily Performance Alert
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM client_meetings WHERE user_id = ? AND DATE(meeting_time) = CURDATE() AND status = 'Approved'");
            $stmt->execute([$user_id]);
            $dailyCount = $stmt->fetch()['count'];
            $data['today_meetings'] = $dailyCount;
            $data['today_meetings_count'] = $dailyCount;
            
            if ($dailyCount < $thresholds['value_low']) {
                $data['perf_level'] = 'Critical';
                // Trigger notification if it's afternoon and still low
                if (date('H') >= 14) {
                    $notifModel->create($user_id, 'Performance', "Daily visit audit is currently CRITICAL ($dailyCount visits). Target is " . $thresholds['value_low'] . "+.");
                }
            }
            elseif ($dailyCount < $thresholds['value_avg']) $data['perf_level'] = 'Average';
            elseif ($dailyCount < $thresholds['value_exc']) $data['perf_level'] = 'Good';
            else $data['perf_level'] = 'Excellent';

            // Monthly Progress
            $month = date('n'); $year = date('Y');
            $stmt = $db->prepare("SELECT target_count FROM monthly_targets WHERE user_id = ? AND month = ? AND year = ?");
            $stmt->execute([$user_id, $month, $year]);
            $target = $stmt->fetch()['target_count'] ?? 0;
            
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM client_meetings WHERE user_id = ? AND MONTH(meeting_time) = ? AND YEAR(meeting_time) = ? AND status = 'Approved'");
            $stmt->execute([$user_id, $month, $year]);
            $monthlyCount = $stmt->fetch()['count'];
            
            $data['monthly_target'] = $target;
            $data['monthly_count'] = $monthlyCount;
            $data['monthly_percent'] = ($target > 0) ? round(($monthlyCount / $target) * 100) : 0;

            if ($data['monthly_percent'] > 0 && $data['monthly_percent'] < 40 && date('j') > 15) {
                 $notifModel->create($user_id, 'Performance', "Monthly target realization is low (" . $data['monthly_percent'] . "%). Please expedite field visits.");
            }

            $data['trends'] = [];
        }

        $this->view('dashboard', $data);
    }
}
