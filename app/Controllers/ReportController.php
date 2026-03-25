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
        
        if ($_SESSION['role'] == 'Manager') {
            $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
            $teamIds = array_column($team, 'id');
            $meetings = $meetingModel->getTeamMeetings($teamIds);
            $travelSummaries = $travelModel->getTeamSummaries($teamIds);
        } else {
            $meetings = $meetingModel->getAllMeetings();
            $travelSummaries = $travelModel->getAllSummaries();
        }
        
        $data = [
            'title' => 'Team Reports & Approvals',
            'meetings' => $meetings,
            'travelSummaries' => $travelSummaries
        ];
        $this->view('reports/manage', $data);
    }

    public function approveMeeting() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $meetingModel = new Meeting();
            $meetingModel->updateStatus($_GET['id'], 'Approved');
            $_SESSION['flash_success'] = "Meeting approved!";
        }
        $this->redirect('reports');
    }

    public function rejectMeeting() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $meetingModel = new Meeting();
            $meetingModel->updateStatus($_GET['id'], 'Rejected');
            $_SESSION['flash_error'] = "Meeting rejected.";
        }
        $this->redirect('reports');
    }

    public function approveTravel() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $travelModel = new Travel();
            $travelModel->updateStatus($_GET['id'], 'Approved');
            $_SESSION['flash_success'] = "Travel allowance approved!";
        }
        $this->redirect('reports');
    }

    public function rejectTravel() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $travelModel = new Travel();
            $travelModel->updateStatus($_GET['id'], 'Rejected');
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
        if ($type == 'daily') {
            $data = $meetingModel->getUserMeetings($_SESSION['user_id']);
            $filename = "daily_report_" . date('Y-m-d') . ".csv";
        } elseif ($type == 'weekly') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
            $data = $meetingModel->getWeeklyUserStats($_SESSION['user_id'], $startDate, $endDate);
            $filename = "weekly_report_" . $startDate . "_to_" . $endDate . ".csv";
        } elseif ($type == 'monthly') {
            $data = $meetingModel->getMonthlyUserStats($_SESSION['user_id'], date('m'), date('Y'));
            $filename = "monthly_report_" . date('Y_m') . ".csv";
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
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
            $this->view('reports/print', ['data' => $data, 'type' => $type]);
        }
    }
}
