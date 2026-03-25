<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Meeting;

class MeetingController extends Controller {
    public function index() {
        $meetingModel = new Meeting();
        $meetings = $meetingModel->getUserMeetings($_SESSION['user_id']);
        
        $data = [
            'title' => 'Client Meetings - Sales Tracking',
            'meetings' => $meetings
        ];
        $this->view('meetings', $data);
    }

    public function log() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['latitude'])) {
            $meetingModel = new Meeting();
            $selfiePath = null;
            if (isset($_POST['selfie_data']) && !empty($_POST['selfie_data'])) {
                $selfiePath = $this->saveBase64Image($_POST['selfie_data'], 'meetings');
            }
            
            $data = [
                'user_id' => $_SESSION['user_id'],
                'client_name' => $_POST['client_name'],
                'hospital_name' => $_POST['hospital_name'],
                'meeting_type' => $_POST['meeting_type'],
                'notes' => $_POST['notes'],
                'outcome' => $_POST['outcome'],
                'meeting_time' => date('Y-m-d H:i:s'),
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude'],
                'address' => $_POST['address'],
                'selfie_path' => $selfiePath
            ];
            
            $result = $meetingModel->logMeeting($data);
            if ($result) {
                $_SESSION['flash_success'] = "Meeting & Selfie logged successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to log meeting.";
            }
        }
        $this->redirect('meetings');
    }

    private function saveBase64Image($base64Data, $subfolder) {
        $uploadDir = 'uploads/' . $subfolder . '/';
        if (!is_dir(BASE_PATH . '/' . $uploadDir)) {
            mkdir(BASE_PATH . '/' . $uploadDir, 0777, true);
        }
        
        $imageParts = explode(";base64,", $base64Data);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $imageType = $imageTypeAux[1];
        $imageBase64 = base64_decode($imageParts[1]);
        
        $filename = $_SESSION['user_id'] . '_' . time() . '.' . $imageType;
        $filePath = $uploadDir . $filename;
        
        if (file_put_contents(BASE_PATH . '/' . $filePath, $imageBase64)) {
            return $filePath;
        }
        return null;
    }
}
