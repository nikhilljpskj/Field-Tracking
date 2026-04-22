<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Travel;
use App\Models\User;

class ReportController extends Controller {
    public function index() {
        // If Manager or Admin, show team reports, else show daily personal report
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            return $this->manage();
        }

        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $travelModel = new Travel();

        $data = [
            'title' => 'Daily Report - Sales Tracking',
            'attendance' => $attendanceModel->getTodayAttendance($_SESSION['user_id']),
            'meetings' => $meetingModel->getUserMeetings($_SESSION['user_id']),
            'travel' => $travelModel->getTravelSummary($_SESSION['user_id'], date('Y-m-d'))
        ];
        $this->view('reports/daily', $data);
    }

    public function manage() {
        $this->checkRole(['Admin', 'Manager']);
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        $userModel = new User();
        
        $userId = $_GET['user_id'] ?? 'all';
        $selectedDate = $_GET['date'] ?? null;
        $users = [];

        if ($_SESSION['role'] == 'Manager') {
            $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            $users = $team;
            $teamIds = array_column($team, 'id');
            
            // Further filter if a specific user in the team is selected
            if ($userId !== 'all' && in_array($userId, $teamIds)) {
                if ($selectedDate) {
                    $meetings = $meetingModel->getMeetingsByDate($selectedDate, $userId);
                } else {
                    $meetings = $meetingModel->getUserMeetings($userId);
                }
                $allTravel = $travelModel->getTeamSummaries($teamIds);
                $travelSummaries = array_filter($allTravel, function($t) use ($userId, $selectedDate) {
                    $userMatch = $t['user_id'] == $userId;
                    $dateMatch = $selectedDate ? ($t['date'] == $selectedDate) : true;
                    return $userMatch && $dateMatch;
                });
            } else {
                if ($selectedDate) {
                    $meetings = array_filter($meetingModel->getMeetingsByDate($selectedDate, 'all'), function($m) use ($teamIds) {
                        return in_array($m['user_id'], $teamIds);
                    });
                } else {
                    $meetings = $meetingModel->getTeamMeetings($teamIds);
                }
                $allTravel = $travelModel->getTeamSummaries($teamIds);
                $travelSummaries = array_filter($allTravel, function($t) use ($selectedDate) {
                    return $selectedDate ? ($t['date'] == $selectedDate) : true;
                });
            }
        } else {
            $users = $userModel->getAll();
            if ($userId !== 'all') {
                if ($selectedDate) {
                    $meetings = $meetingModel->getMeetingsByDate($selectedDate, $userId);
                } else {
                    $meetings = $meetingModel->getUserMeetings($userId);
                }
                $allTravel = $travelModel->getAllSummaries();
                $travelSummaries = array_filter($allTravel, function($t) use ($userId, $selectedDate) {
                    $userMatch = $t['user_id'] == $userId;
                    $dateMatch = $selectedDate ? ($t['date'] == $selectedDate) : true;
                    return $userMatch && $dateMatch;
                });
            } else {
                if ($selectedDate) {
                    $meetings = $meetingModel->getMeetingsByDate($selectedDate, 'all');
                } else {
                    $meetings = $meetingModel->getAllMeetings();
                }
                $allTravel = $travelModel->getAllSummaries();
                $travelSummaries = array_filter($allTravel, function($t) use ($selectedDate) {
                    return $selectedDate ? ($t['date'] == $selectedDate) : true;
                });
            }
        }
        
        $data = [
            'title' => 'Team Reports & Approvals',
            'meetings' => $meetings,
            'travelSummaries' => $travelSummaries,
            'users' => $users,
            'selectedUser' => $userId,
            'selectedDate' => $selectedDate
        ];
        $this->view('reports/manage', $data);
    }

    public function approveMeeting() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id']) || isset($_POST['id'])) {
            $id = $_POST['id'] ?? $_GET['id'];
            $reason = $_POST['reason'] ?? $_GET['reason'] ?? null;
            $meetingModel = new Meeting();
            $meetingModel->updateStatus($id, 'Approved', $_SESSION['user_id'], $reason);
            $_SESSION['flash_success'] = "Meeting approved!";
        }
        $this->redirect('reports');
    }

    public function rejectMeeting() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id']) || isset($_POST['id'])) {
            $id = $_POST['id'] ?? $_GET['id'];
            $reason = $_POST['reason'] ?? $_GET['reason'] ?? null;
            $meetingModel = new Meeting();
            $meetingModel->updateStatus($id, 'Rejected', $_SESSION['user_id'], $reason);
            $_SESSION['flash_error'] = "Meeting rejected.";
        }
        $this->redirect('reports');
    }

    public function approveTravel() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id']) || isset($_POST['id'])) {
            $id = $_POST['id'] ?? $_GET['id'];
            $reason = $_POST['reason'] ?? $_GET['reason'] ?? null;
            $travelModel = new Travel();
            $travelModel->updateStatus($id, 'Approved', $_SESSION['user_id'], $reason);
            $_SESSION['flash_success'] = "Travel allowance approved!";
        }
        $this->redirect('reports');
    }

    public function rejectTravel() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id']) || isset($_POST['id'])) {
            $id = $_POST['id'] ?? $_GET['id'];
            $reason = $_POST['reason'] ?? $_GET['reason'] ?? null;
            $travelModel = new Travel();
            $travelModel->updateStatus($id, 'Rejected', $_SESSION['user_id'], $reason);
            $_SESSION['flash_error'] = "Travel allowance rejected.";
        }
        $this->redirect('reports');
    }

    public function editMeeting() {
        $this->checkRole('Admin');
        if (isset($_GET['id'])) {
            $meetingModel = new Meeting();
            $meeting = $meetingModel->getById($_GET['id']);
            $data = [
                'title' => 'Edit Meeting Record - Admin',
                'meeting' => $meeting
            ];
            $this->view('reports/edit_meeting', $data);
        } else {
            $this->redirect('reports');
        }
    }

    public function updateMeeting() {
        $this->checkRole('Admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $meetingModel = new Meeting();
            $result = $meetingModel->update($_POST['id'], $_POST);
            if ($result) {
                $_SESSION['flash_success'] = "Meeting updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update meeting.";
            }
        }
        $this->redirect('reports');
    }

    public function getMeetingDetails() {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'ID missing']);
            return;
        }
        $meetingModel = new Meeting();
        $meeting = $meetingModel->getById($_GET['id']);
        if ($meeting) {
            echo json_encode(['success' => true, 'data' => $meeting]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Meeting not found']);
        }
    }

    public function monthly() {
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        $userModel = new User();
        
        $selectedUserId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        
        // RBAC Check
        if ($selectedUserId !== $_SESSION['user_id']) {
            $this->checkRole(['Admin', 'Manager']);
        }

        $users = [];
        if (in_array($_SESSION['role'], ['Admin', 'Manager'])) {
            if ($_SESSION['role'] == 'Manager') {
                $users = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            } else {
                $users = $userModel->getAll();
            }
        }

        if ($selectedUserId === 'all') {
            $teamIds = array_column($users, 'id');
            $data = [
                'title' => 'Team Performance Leadership - ' . date('F Y', mktime(0,0,0,$month,1,$year)),
                'is_aggregate' => true,
                'user_aggregates' => $meetingModel->getTeamMonthlyAggregates($month, $year, $teamIds),
                'travel_aggregates' => $travelModel->getMonthlyTeamAggregates($month, $year, $teamIds),
                'meetings_list' => $meetingModel->getMonthlyUserStats('all', $month, $year) // Global logs for the month
            ];
            // Filter global logs if Manager
            if ($_SESSION['role'] == 'Manager') {
                $data['meetings_list'] = array_filter($data['meetings_list'], function($m) use ($teamIds) {
                    return in_array($m['user_id'], $teamIds);
                });
            }
        } else {
            $data = [
                'title' => 'Monthly Performance - ' . date('F Y', mktime(0,0,0,$month,1,$year)),
                'is_aggregate' => false,
                'target_user' => $userModel->findById($selectedUserId),
                'meetings' => $meetingModel->getMonthlySummary($selectedUserId, $month, $year),
                'meetings_list' => $meetingModel->getMonthlyUserStats($selectedUserId, $month, $year),
                'travel' => $travelModel->getMonthlyUserTravel($selectedUserId, $month, $year),
                'breakdown' => $travelModel->getMonthlyBreakdown($selectedUserId, $month, $year)
            ];
        }

        $data['users'] = $users;
        $data['selectedUser'] = $selectedUserId;
        $data['selectedMonth'] = $month;
        $data['selectedYear'] = $year;
        
        $this->view('reports/monthly', $data);
    }

    public function export() {
        $type = $_GET['type'] ?? 'daily';
        $format = $_GET['format'] ?? 'csv';
        
        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        
        $data = [];
        $userId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $date = $_GET['date'] ?? null;
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $userModel = new User();
        $targetName = "My";

        if ($userId === 'all') {
            $this->checkRole(['Admin', 'Manager']);
            $targetName = "Team";
            if ($type == 'monthly') {
                $users_list = [];
                if ($_SESSION['role'] == 'Manager') {
                    $users_list = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                } else {
                    $users_list = $userModel->getAll();
                }
                $teamIds = array_column($users_list, 'id');
                $data = $meetingModel->getMonthlyUserStats('all', $month, $year);
                if ($_SESSION['role'] == 'Manager') {
                    $data = array_filter($data, function($m) use ($teamIds) { return in_array($m['user_id'], $teamIds); });
                }
                $filename = "monthly_team_report_{$year}_{$month}";
            } else {
                if ($_SESSION['role'] == 'Manager') {
                    $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                    $teamIds = array_column($team, 'id');
                    if ($date) {
                        $data = $meetingModel->getMeetingsByDate($date, 'all'); 
                        $data = array_filter($data, function($m) use ($teamIds) { return in_array($m['user_id'], $teamIds); });
                    } else {
                        $data = $meetingModel->getTeamMeetings($teamIds);
                    }
                } else {
                    if ($date) {
                        $data = $meetingModel->getMeetingsByDate($date, 'all');
                    } else {
                        $data = $meetingModel->getAllMeetings();
                    }
                }
                $filename = "{$type}_team_report_" . ($date ?: date('Y_m_d'));
            }
        } else {
            // RBAC check if querying another user
            if ($userId != $_SESSION['user_id']) {
                $this->checkRole(['Admin', 'Manager', 'HR']);
                $targetUser = $userModel->findById($userId);
                $targetName = $targetUser['name'];
            }
            
            if ($date) {
                $data = $meetingModel->getMeetingsByDate($date, $userId);
                $filename = "daily_report_{$userId}_{$date}";
            } elseif ($type == 'daily') {
                $data = $meetingModel->getUserMeetings($userId);
                $filename = "daily_report_{$userId}_" . date('Y-m-d');
            } elseif ($type == 'weekly') {
                $startDate = date('Y-m-d', strtotime('-7 days'));
                $endDate = date('Y-m-d');
                $data = $meetingModel->getWeeklyUserStats($userId, $startDate, $endDate);
                $filename = "weekly_report_{$userId}_" . date('Y-m-d');
            } elseif ($type == 'monthly') {
                $data = $meetingModel->getMonthlyUserStats($userId, $month, $year);
                $filename = "monthly_report_{$userId}_{$year}_{$month}";
            }
        }

        // Apply specific category filtering
        $category = $_GET['category'] ?? null;
        if ($category) {
            $data = array_filter($data, function($item) use ($category) {
                return ($item['visit_category'] ?? 'Meeting') === $category;
            });
            // Re-index array after filtering for PDF logic
            $data = array_values($data);
            $filename .= "_" . strtolower(str_replace(' ', '_', $category));
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            if (!empty($data)) {
                // Customized Header for Premium Export
                fputcsv($output, [
                    'S.No', 'Employee Name', 'Client/Hospital', 'Category', 
                    'Visit Date/Time (IST)', 'Outcome', 'Location Address', 
                    'Status', 'Approved By', 'Comments'
                ]);
                
                $i = 1;
                foreach ($data as $row) {
                    fputcsv($output, [
                        $i++,
                        $row['user_name'] ?? 'N/A',
                        $row['client_name'] . ' (' . $row['hospital_office_name'] . ')',
                        $row['visit_category'],
                        date('d M Y - h:i A', strtotime($row['meeting_time'])),
                        $row['outcome'],
                        $row['address'],
                        $row['status'],
                        $row['approver_name'] ?? 'N/A',
                        $row['admin_comments'] ?? ''
                    ]);
                }
            }
            fclose($output);
            exit;
            // PDF - Render print-friendly view
            $period = date('F Y', mktime(0, 0, 0, $month, 1, $year));
            $this->view('reports/print', ['data' => $data, 'type' => $type, 'targetName' => $targetName, 'period' => $period]);
    }
}
