<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Travel;
use App\Models\User;

class ReportController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager', 'HR']);

        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $travelModel = new Travel();
        $userModel = new User();

        $selectedUserId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $viewerRole = $_SESSION['role'] ?? '';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
        
        if ($selectedUserId !== $_SESSION['user_id']) {
            $this->checkRole(['Admin', 'Manager', 'HR']);
        }

        $users = [];
        if (in_array($viewerRole, ['Admin', 'Manager', 'HR'], true)) {
            if ($viewerRole == 'Manager') {
                $users = $userModel->getExecutivesByManagerId($viewerId);
                $self = $userModel->findById($viewerId);
                if ($self) {
                    $users[] = $self;
                }
                $allowedIds = array_map('intval', array_column($users, 'id'));
                if ($selectedUserId !== 'all' && !in_array((int)$selectedUserId, $allowedIds, true)) {
                    $selectedUserId = (string)$viewerId;
                }
            } else {
                $users = $userModel->getAll();
            }
        }

        $attendance = null;
        $meetings = [];
        $travel = [];
        if ($selectedUserId === 'all') {
            $this->checkRole(['Admin', 'Manager', 'HR']);
            $teamIds = array_map('intval', array_column($users, 'id'));
            if ($viewerRole === 'Manager') {
                $teamIds[] = $viewerId;
            }
            $meetings = $meetingModel->getMeetingsByDate($selectedDate, 'all');
            if ($viewerRole === 'Manager') {
                $meetings = array_values(array_filter($meetings, function($meeting) use ($teamIds) {
                    return in_array((int)$meeting['user_id'], $teamIds, true);
                }));
            }
        } else {
            $attendance = $attendanceModel->getTodayAttendance($selectedUserId, $selectedDate);
            $meetings = $meetingModel->getMeetingsByDate($selectedDate, $selectedUserId);
            $travel = $travelModel->getTravelSummary($selectedUserId, $selectedDate);
        }

        $data = [
            'title' => 'Daily Performance Radar',
            'attendance' => $attendance,
            'meetings' => $meetings,
            'travel' => $travel,
            'users' => $users,
            'selectedUserId' => $selectedUserId,
            'selectedDate' => $selectedDate,
            'here_api_key' => \App\Core\Config::get('HERE_API_KEY')
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
        $this->checkRole(['Admin', 'Manager', 'HR']);

        $meetingModel = new Meeting();
        $travelModel = new Travel();
        $userModel = new User();
        
        $selectedUserId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $viewerRole = $_SESSION['role'] ?? '';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
        
        // RBAC Check
        if ($selectedUserId !== $_SESSION['user_id']) {
            $this->checkRole(['Admin', 'Manager', 'HR']);
        }

        $users = [];
        if (in_array($viewerRole, ['Admin', 'Manager', 'HR'])) {
            if ($viewerRole == 'Manager') {
                $users = $userModel->getExecutivesByManagerId($viewerId);
                $self = $userModel->findById($viewerId);
                if ($self) {
                    $users[] = $self;
                }
                $allowedIds = array_map('intval', array_column($users, 'id'));
                if ($selectedUserId !== 'all' && !in_array((int)$selectedUserId, $allowedIds, true)) {
                    $selectedUserId = (string)$viewerId;
                }
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
            if ($viewerRole == 'Manager') {
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

        if ($type === 'daily' && in_array($format, ['xls', 'excel'], true)) {
            return $this->exportDailyExcel();
        }
        
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

        if ($userId !== 'all' && ($_SESSION['role'] ?? '') === 'Manager' && (int)$userId !== (int)$_SESSION['user_id']) {
            $stmt = \Database::getInstance()->getConnection()->prepare("SELECT COUNT(*) FROM users WHERE id = ? AND manager_id = ?");
            $stmt->execute([(int)$userId, (int)$_SESSION['user_id']]);
            if ((int)$stmt->fetchColumn() === 0) {
                header("Location: dashboard");
                exit;
            }
        }

        if ($userId === 'all') {
            $this->checkRole(['Admin', 'Manager', 'HR']);
            $targetName = "Team";
            if ($type == 'monthly') {
                $users_list = [];
                if ($_SESSION['role'] == 'Manager') {
                    $users_list = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                    $self = $userModel->findById($_SESSION['user_id']);
                    if ($self) {
                        $users_list[] = $self;
                    }
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
                    $teamIds[] = (int)$_SESSION['user_id'];
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

        if ($format == 'pdf') {
            $period = $type === 'monthly'
                ? date('F Y', mktime(0, 0, 0, (int)$month, 1, (int)$year))
                : (!empty($date) ? date('d M Y', strtotime($date)) : date('d M Y'));

            $attendanceRows = [];
            if ($type === 'daily') {
                $reportDate = $date ?: date('Y-m-d');
                if ($userId === 'all') {
                    if (($_SESSION['role'] ?? '') === 'Manager') {
                        $team = $userModel->getExecutivesByManagerId($_SESSION['user_id']);
                        $reportUserIds = array_map('intval', array_column($team, 'id'));
                        $reportUserIds[] = (int)$_SESSION['user_id'];
                    } else {
                        $reportUserIds = array_map('intval', array_column($userModel->getAll(), 'id'));
                    }
                } else {
                    $reportUserIds = [(int)$userId];
                }
                $attendanceRows = $attendanceModel->getDailyRecordsByUserIds($reportDate, $reportUserIds);
            }

            $this->view('reports/print', [
                'data' => $data,
                'attendanceRows' => $attendanceRows,
                'type' => $type,
                'targetName' => $targetName,
                'period' => $period
            ]);
            return;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            if (!empty($data)) {
                // Customized Header for Premium Export
                fputcsv($output, [
                    'S.No', 'Employee Name', 'Client/Hospital', 'Department', 'Category', 
                    'Visit Date/Time (IST)', 'Outcome', 'Location Address', 
                    'Status', 'Approved By', 'Comments'
                ]);
                
                $i = 1;
                foreach ($data as $row) {
                    fputcsv($output, [
                        $i++,
                        $row['user_name'] ?? 'N/A',
                        $row['client_name'] . ' (' . $row['hospital_office_name'] . ')',
                        $row['department'] ?? '',
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
        }
    }

    private function exportDailyExcel() {
        $this->checkRole(['Admin', 'Manager', 'HR']);

        $attendanceModel = new Attendance();
        $meetingModel = new Meeting();
        $userModel = new User();

        $viewerRole = $_SESSION['role'] ?? '';
        $viewerId = (int)($_SESSION['user_id'] ?? 0);
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        $selectedUserId = $_GET['user_id'] ?? 'all';

        if (!$this->isValidDate($selectedDate)) {
            $selectedDate = date('Y-m-d');
        }

        if ($viewerRole === 'Manager') {
            $users = $userModel->getExecutivesByManagerId($viewerId);
            $manager = $userModel->findById($viewerId);
            if ($manager) {
                $users[] = $manager;
            }
        } else {
            $users = $userModel->getAll();
        }

        $allowedUsers = [];
        foreach ($users as $user) {
            $allowedUsers[(int)$user['id']] = $user;
        }

        if ($selectedUserId !== 'all') {
            $selectedId = (int)$selectedUserId;
            if (!isset($allowedUsers[$selectedId])) {
                header("Location: dashboard");
                exit;
            }
            $allowedUsers = [$selectedId => $allowedUsers[$selectedId]];
        }

        $userIds = array_keys($allowedUsers);
        $attendanceRows = $attendanceModel->getDailyRecordsByUserIds($selectedDate, $userIds);
        $meetingRows = $meetingModel->getMeetingsByDate($selectedDate, 'all');
        $meetingRows = array_values(array_filter($meetingRows, function($meeting) use ($userIds) {
            return in_array((int)$meeting['user_id'], $userIds, true);
        }));

        $attendanceByUser = [];
        foreach ($attendanceRows as $row) {
            $attendanceByUser[(int)$row['user_id']][] = $row;
        }

        $meetingsByUser = [];
        foreach ($meetingRows as $row) {
            $meetingsByUser[(int)$row['user_id']][] = $row;
        }

        $scope = $selectedUserId === 'all' ? 'all_employees' : $this->filenamePart($allowedUsers[(int)$selectedUserId]['name'] ?? 'employee');
        $filename = "daily_employee_report_{$scope}_{$selectedDate}.xls";

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo "\xEF\xBB\xBF";
        echo '<html><head><meta charset="UTF-8">';
        echo '<style>table{border-collapse:collapse;}th,td{border:1px solid #999;padding:5px;vertical-align:top;}th{background:#e9eef7;font-weight:bold;}.section{background:#d9ead3;font-weight:bold;}.muted{color:#666;}</style>';
        echo '</head><body>';
        echo '<h2>Daily Employee Report</h2>';
        echo '<p><strong>Date:</strong> ' . $this->xls($selectedDate) . ' <strong>Scope:</strong> ' . $this->xls($selectedUserId === 'all' ? 'All Employees' : reset($allowedUsers)['name']) . '</p>';

        echo '<table><tr><td class="section" colspan="9">Employee Daily Summary</td></tr>';
        echo '<tr><th>S.No</th><th>Employee</th><th>Role</th><th>Phone</th><th>Login Time</th><th>Logout Time</th><th>Total Visits</th><th>Approved Visits</th><th>Status</th></tr>';
        $i = 1;
        foreach ($allowedUsers as $userId => $user) {
            $sessions = $attendanceByUser[$userId] ?? [];
            $visits = $meetingsByUser[$userId] ?? [];
            $firstIn = $this->firstTime($sessions, 'check_in_time');
            $lastOut = $this->lastTime($sessions, 'check_out_time');
            $approved = count(array_filter($visits, function($visit) {
                return ($visit['status'] ?? '') === 'Approved';
            }));
            echo '<tr>';
            echo '<td>' . $i++ . '</td>';
            echo '<td>' . $this->xls($user['name'] ?? '') . '</td>';
            echo '<td>' . $this->xls($user['role_name'] ?? '') . '</td>';
            echo '<td>' . $this->xls($user['phone'] ?? '') . '</td>';
            echo '<td>' . $this->xls($firstIn ? date('h:i A', strtotime($firstIn)) : '-') . '</td>';
            echo '<td>' . $this->xls($lastOut ? date('h:i A', strtotime($lastOut)) : '-') . '</td>';
            echo '<td>' . count($visits) . '</td>';
            echo '<td>' . $approved . '</td>';
            echo '<td>' . $this->xls($firstIn ? ($lastOut ? 'Completed' : 'Checked In') : 'No Attendance') . '</td>';
            echo '</tr>';
        }
        echo '</table><br>';

        echo '<table><tr><td class="section" colspan="12">Attendance Login / Logout Details</td></tr>';
        echo '<tr><th>S.No</th><th>Employee</th><th>Check-In Time</th><th>Check-In Address</th><th>Check-In Lat</th><th>Check-In Lng</th><th>Check-Out Time</th><th>Check-Out Address</th><th>Check-Out Lat</th><th>Check-Out Lng</th><th>Start Odometer</th><th>End Odometer</th></tr>';
        if (empty($attendanceRows)) {
            echo '<tr><td colspan="12" class="muted">No attendance records found for this date.</td></tr>';
        } else {
            $i = 1;
            foreach ($attendanceRows as $row) {
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . $this->xls($row['user_name'] ?? '') . '</td>';
                echo '<td>' . $this->xls(!empty($row['check_in_time']) ? date('d M Y h:i A', strtotime($row['check_in_time'])) : '-') . '</td>';
                echo '<td>' . $this->xls($row['check_in_address'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['check_in_lat'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['check_in_lng'] ?? '') . '</td>';
                echo '<td>' . $this->xls(!empty($row['check_out_time']) ? date('d M Y h:i A', strtotime($row['check_out_time'])) : '-') . '</td>';
                echo '<td>' . $this->xls($row['check_out_address'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['check_out_lat'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['check_out_lng'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['odometer_reading'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['check_out_odometer_reading'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</table><br>';

        echo '<table><tr><td class="section" colspan="14">Client Visit Details</td></tr>';
        echo '<tr><th>S.No</th><th>Employee</th><th>Visit Time</th><th>Client</th><th>Hospital / Office</th><th>Department</th><th>Category</th><th>Type</th><th>Notes</th><th>Outcome</th><th>Address</th><th>Status</th><th>Approved By</th><th>Admin Comments</th></tr>';
        if (empty($meetingRows)) {
            echo '<tr><td colspan="14" class="muted">No client visits found for this date.</td></tr>';
        } else {
            $i = 1;
            foreach ($meetingRows as $row) {
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . $this->xls($row['user_name'] ?? '') . '</td>';
                echo '<td>' . $this->xls(!empty($row['meeting_time']) ? date('d M Y h:i A', strtotime($row['meeting_time'])) : '-') . '</td>';
                echo '<td>' . $this->xls($row['client_name'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['hospital_office_name'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['department'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['visit_category'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['meeting_type'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['notes'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['outcome'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['address'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['status'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['approver_name'] ?? '') . '</td>';
                echo '<td>' . $this->xls($row['admin_comments'] ?? '') . '</td>';
                echo '</tr>';
            }
        }
        echo '</table>';
        echo '</body></html>';
        exit;
    }

    private function isValidDate($date) {
        $parsed = \DateTime::createFromFormat('Y-m-d', (string)$date);
        return $parsed && $parsed->format('Y-m-d') === $date;
    }

    private function xls($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function filenamePart($value) {
        $value = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string)$value);
        return trim($value, '_') ?: 'employee';
    }

    private function firstTime($rows, $field) {
        $times = array_filter(array_column($rows, $field));
        sort($times);
        return $times[0] ?? null;
    }

    private function lastTime($rows, $field) {
        $times = array_filter(array_column($rows, $field));
        sort($times);
        return !empty($times) ? $times[count($times) - 1] : null;
    }
}
