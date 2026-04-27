<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Attendance;

class AttendanceController extends Controller {
    public function index() {
        // If Admin or Manager is visiting, show the management list, else show personal attendance
        // Check for ?mode=self to allow marking own attendance via /attendance?mode=self
        if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager']) && !isset($_GET['mode'])) {
            return $this->manage();
        }

        $attendanceModel = new Attendance();
        $todayAttendance = $attendanceModel->getTodayAttendance($_SESSION['user_id']);
        $todaySessions = $attendanceModel->getSessionsForToday($_SESSION['user_id']);
        
        $data = [
            'title' => 'Attendance - Sales Tracking',
            'attendance' => $todayAttendance,
            'sessions' => $todaySessions
        ];
        $this->view('attendance', $data);
    }

    public function manage() {
        $this->checkRole(['Admin', 'Manager']);
        $attendanceModel = new Attendance();
        $userModel = new \App\Models\User();
        
        $filters = [
            'user_id' => $_GET['user_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? date('Y-m-01'), // Default to current month start
            'date_to' => $_GET['date_to'] ?? date('Y-m-d'),
            'search' => $_GET['search'] ?? null
        ];

        $records = $attendanceModel->getFilteredRecords($filters);
        $users = $userModel->getAll();
        
        $data = [
            'title' => 'Attendance Logistics - Command Center',
            'records' => $records,
            'users' => $users,
            'filters' => $filters
        ];
        $this->view('attendance_manage', $data);
    }

    public function edit() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $attendanceModel = new Attendance();
            $record = $attendanceModel->getById($_GET['id']);
            
            $data = [
                'title' => 'Edit Attendance Record - Admin',
                'record' => $record
            ];
            $this->view('attendance_edit', $data);
        } else {
            $this->redirect('attendance');
        }
    }

    public function update() {
        $this->checkRole(['Admin', 'Manager']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $attendanceModel = new Attendance();
            $result = $attendanceModel->update($_POST['id'], $_POST);
            if ($result) {
                $_SESSION['flash_success'] = "Attendance record updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update record.";
            }
        }
        $this->redirect('attendance');
    }

    public function delete() {
        $this->checkRole(['Admin']);
        if (isset($_GET['id'])) {
            $attendanceModel = new Attendance();
            $record = $attendanceModel->getById($_GET['id']);
            if ($record) {
                $photos = ['check_in_photo', 'check_out_photo', 'odometer_photo', 'check_out_odometer_photo'];
                foreach ($photos as $photo) {
                    if (!empty($record[$photo]) && file_exists(BASE_PATH . '/' . $record[$photo])) {
                        @unlink(BASE_PATH . '/' . $record[$photo]);
                    }
                }
                $result = $attendanceModel->delete($_GET['id']);
                if ($result) {
                    $_SESSION['flash_success'] = "Attendance record and associated media purged successfully.";
                } else {
                    $_SESSION['flash_error'] = "Failed to purge attendance record.";
                }
            }
        }
        // Safely redirect back to referer or history
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'attendance-history';
        header("Location: $redirect");
        exit;
    }

    public function checkIn() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['latitude']) && isset($_POST['longitude'])) {
            $attendanceModel = new Attendance();
            $lat = $_POST['latitude'];
            $lng = $_POST['longitude'];
            $address = $_POST['address'] ?? 'Unknown location';
            
            // Radius Validation removed as requested - can be marked from anywhere

            // 2. Handle Primary Photo (Selfie)
            $photoPath = null;
            if (isset($_POST['photo_data']) && !empty($_POST['photo_data'])) {
                $photoPath = $this->saveBase64Image($_POST['photo_data'], 'checkin');
            }
            
            // 3. Handle Odometer Photo (Executive only)
            $odometerPath = null;
            if (isset($_POST['odometer_data']) && !empty($_POST['odometer_data'])) {
                $odometerPath = $this->saveBase64Image($_POST['odometer_data'], 'odometer');
            }
            
            // 4. Handle Manual Inputs (Executive only)
            $odometerReading = $_POST['odometer_reading'] ?? null;
            $ticketDetails = $_POST['ticket_details'] ?? null;
            
            $result = $attendanceModel->checkIn($_SESSION['user_id'], $lat, $lng, $address, $photoPath, $odometerPath, $odometerReading, $ticketDetails);
            
            if ($result) {
                $_SESSION['is_checked_in'] = true;
                $_SESSION['flash_success'] = "Checked in successfully with photo and location verification!";
            } else {
                $_SESSION['flash_error'] = "Check-in failed.";
            }
        }
        $this->redirect('attendance');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $R = 6371000; // Radius of earth in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $R * $c;
    }

    public function checkOut() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance_id'])) {
            $attendanceModel = new Attendance();
            $id = $_POST['attendance_id'];
            $lat = $_POST['latitude'];
            $lng = $_POST['longitude'];
            $address = $_POST['address'] ?? 'Unknown location';
            
            // Handle Photo
            $photoPath = null;
            if (isset($_POST['photo_data']) && !empty($_POST['photo_data'])) {
                $photoPath = $this->saveBase64Image($_POST['photo_data'], 'checkout');
            }

            // Handle Odometer (Closing)
            $odometerPath = null;
            if (isset($_POST['odometer_data']) && !empty($_POST['odometer_data'])) {
                $odometerPath = $this->saveBase64Image($_POST['odometer_data'], 'odometer_out');
            }
            $odometerReading = $_POST['odometer_reading'] ?? null;
            
            $result = $attendanceModel->checkOut($id, $lat, $lng, $address, $photoPath, $odometerPath, $odometerReading);
            if ($result) {
                // TA Calculation Integration (Milestone-based)
                $travelModel = new \App\Models\Travel();
                $distKm = $travelModel->calculateMilestoneDistance($_SESSION['user_id'], date('Y-m-d'));
                $rate = $travelModel->getCurrentRate();
                $allowance = $distKm * $rate;
                
                $travelModel->updateTravelSummary($_SESSION['user_id'], date('Y-m-d'), $distKm, $allowance);

                unset($_SESSION['is_checked_in']);
                $_SESSION['flash_success'] = "Checked out successfully! Travel allowance updated.";
            } else {
                $_SESSION['flash_error'] = "Check-out failed.";
            }
        }
        $this->redirect('attendance');
    }

    public function history() {
        $this->checkRole(['Admin', 'Manager', 'HR', 'Executive']);
        $attendanceModel = new Attendance();
        $leaveModel = new \App\Models\Leave();
        $userModel = new \App\Models\User();

        $userId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $month = $_GET['month'] ?? date('n');
        $year = $_GET['year'] ?? date('Y');

        // RBAC: Non-admin/manager can only view their own history
        if (!in_array($_SESSION['role'], ['Admin', 'Manager', 'HR']) && $userId != $_SESSION['user_id']) {
            $userId = $_SESSION['user_id'];
        }

        $records = $attendanceModel->getMonthlyHistory($userId, $month, $year);
        $leaves = $leaveModel->getApprovedLeavesForMonth($userId, $month, $year);
        
        $users = [];
        if (in_array($_SESSION['role'], ['Admin', 'Manager', 'HR'])) {
            $users = $userModel->getAll();
        }

        $data = [
            'title' => 'Attendance History - Sales Tracking',
            'records' => $records,
            'leaves' => $leaves,
            'users' => $users,
            'selectedUser' => $userId,
            'month' => $month,
            'year' => $year
        ];
        $this->view('attendance_history', $data);
    }

    public function exportHistory() {
        $this->checkRole(['Admin', 'Manager', 'HR', 'Executive']);
        $attendanceModel = new Attendance();
        $userModel = new \App\Models\User();

        $userId = $_GET['user_id'] ?? $_SESSION['user_id'];
        $month = $_GET['month'] ?? date('n');
        $year = $_GET['year'] ?? date('Y');
        $format = $_GET['format'] ?? 'csv';

        // RBAC validation
        if (!in_array($_SESSION['role'], ['Admin', 'Manager', 'HR']) && $userId != $_SESSION['user_id']) {
            $userId = $_SESSION['user_id'];
        }

        $records = $attendanceModel->getMonthlyHistory($userId, $month, $year);
        $user = $userModel->findById($userId);
        
        $filename = "attendance_report_" . str_replace(' ', '_', $user['name']) . "_{$year}_{$month}";

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Employee Name: ' . $user['name'], 'Month: ' . date('F Y', mktime(0, 0, 0, $month, 10, $year))]);
            fputcsv($output, []); // blank row
            fputcsv($output, ['Date', 'Day', 'Location (Check-In)', 'Check-In Time', 'Location (Check-Out)', 'Check-Out Time']);
            
            foreach ($records as $r) {
                fputcsv($output, [
                    date('d M Y', strtotime($r['check_in_time'])),
                    date('l', strtotime($r['check_in_time'])),
                    $r['check_in_address'],
                    date('h:i A', strtotime($r['check_in_time'])),
                    $r['check_out_address'] ?: '-',
                    $r['check_out_time'] ? date('h:i A', strtotime($r['check_out_time'])) : 'Active'
                ]);
            }
            fclose($output);
            exit;
        } else {
            // PDF/Print view
            $data = [
                'user' => $user,
                'records' => $records,
                'month' => $month,
                'year' => $year
            ];
            $this->view('reports/print_history', $data);
        }
    }

    private function saveBase64Image($base64String, $prefix) {
        $dir = \App\Core\Config::get('UPLOAD_DIR', 'uploads/attendance/');
        $fullPath = BASE_PATH . '/' . $dir;
        
        if (!is_dir($fullPath)) {
            if (!@mkdir($fullPath, 0777, true)) {
                $_SESSION['flash_error'] = "Upload directory permission denied: $dir";
                return null;
            }
        }

        if (!is_writable($fullPath)) {
            $_SESSION['flash_error'] = "Upload directory is not writable. Please check permissions.";
            return null;
        }

        $data = explode(',', $base64String);
        if (count($data) < 2) {
            $_SESSION['flash_error'] = "Invalid image data format.";
            return null;
        }
        
        $imageContent = base64_decode($data[1]);
        if ($imageContent === false) {
            $_SESSION['flash_error'] = "Failed to decode image data.";
            return null;
        }

        $filename = $prefix . '_' . $_SESSION['user_id'] . '_' . time() . '.jpg';
        $filePath = $dir . $filename;
        $fileSavePath = BASE_PATH . '/' . $filePath;
        
        if (file_put_contents($fileSavePath, $imageContent) === false) {
            $_SESSION['flash_error'] = "Failed to write image to disk. Disk full or permission error?";
            return null;
        }

        return $filePath;
    }
}
