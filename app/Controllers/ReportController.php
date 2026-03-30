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
        $users = [];

        if ($_SESSION['role'] == 'Manager') {
            $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            $users = $team;
            $teamIds = array_column($team, 'id');
            
            // Further filter if a specific user in the team is selected
            if ($userId !== 'all' && in_array($userId, $teamIds)) {
                $meetings = $meetingModel->getUserMeetings($userId);
                $allTravel = $travelModel->getTeamSummaries($teamIds);
                $travelSummaries = array_filter($allTravel, function($t) use ($userId) {
                    return $t['user_id'] == $userId;
                });
            } else {
                $meetings = $meetingModel->getTeamMeetings($teamIds);
                $travelSummaries = $travelModel->getTeamSummaries($teamIds);
            }
        } else {
            $users = $userModel->getAll();
            if ($userId !== 'all') {
                $meetings = $meetingModel->getUserMeetings($userId);
                // The Travel getTravelSummary is for a specific date, to keep format matched with manage page:
                $allTravel = $travelModel->getAllSummaries();
                $travelSummaries = array_filter($allTravel, function($t) use ($userId) {
                    return $t['user_id'] == $userId;
                });
            } else {
                $meetings = $meetingModel->getAllMeetings();
                $travelSummaries = $travelModel->getAllSummaries();
            }
        }
        
        $data = [
            'title' => 'Team Reports & Approvals',
            'meetings' => $meetings,
            'travelSummaries' => $travelSummaries,
            'users' => $users,
            'selectedUser' => $userId
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

    public function weekly() {
        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        
        // Last 7 days
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $endDate = date('Y-m-d');
        
        $data = [
            'title' => 'Weekly Performance Report',
            'meetings' => $meetingModel->getWeeklyUserStats($_SESSION['user_id'], $startDate, $endDate),
            'travel' => $travelModel->getWeeklyUserTravel($_SESSION['user_id'], $startDate, $endDate),
            'range' => ['start' => $startDate, 'end' => $endDate]
        ];
        $this->view('reports/weekly', $data);
    }

    public function export() {
        $type = $_GET['type'] ?? 'daily';
        $format = $_GET['format'] ?? 'csv';
        
        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        
        $data = [];
        $userId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $userModel = new User();
        $targetName = "My";

        if ($userId === 'all') {
            $this->checkRole(['Admin', 'Manager']);
            $targetName = "Team";
            if ($_SESSION['role'] == 'Manager') {
                $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                $teamIds = array_column($team, 'id');
                if ($type == 'daily') {
                    $data = $meetingModel->getTeamMeetings($teamIds);
                } else {
                    // Quick patch for monthly team data (using teamIds in SQL conceptually or fetching all and filtering)
                    // Currently we don't have getMonthlyTeamStats, so we use getTeamMeetings directly as a broad export
                    $data = $meetingModel->getTeamMeetings($teamIds);
                }
            } else {
                $data = $meetingModel->getAllMeetings();
            }
            $filename = "{$type}_team_report_" . date('Y_m_d');
        } else {
            // RBAC check if querying another user
            if ($userId != $_SESSION['user_id']) {
                $this->checkRole(['Admin', 'Manager', 'HR']);
                $targetUser = $userModel->findById($userId);
                $targetName = $targetUser['name'];
            }
            
            if ($type == 'daily') {
                $data = $meetingModel->getUserMeetings($userId);
                $filename = "daily_report_{$userId}_" . date('Y-m-d');
            } elseif ($type == 'weekly') {
                $startDate = date('Y-m-d', strtotime('-7 days'));
                $endDate = date('Y-m-d');
                $data = $meetingModel->getWeeklyUserStats($userId, $startDate, $endDate);
                $filename = "weekly_report_{$userId}_" . date('Y-m-d');
            } elseif ($type == 'monthly') {
                $data = $meetingModel->getMonthlyUserStats($userId, date('m'), date('Y'));
                $filename = "monthly_report_{$userId}_" . date('Y_m');
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
                fputcsv($output, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
            exit;
        } else {
            // PDF - Render print-friendly view
            $this->view('reports/print', ['data' => $data, 'type' => $type, 'targetName' => $targetName]);
        }
    }
}
