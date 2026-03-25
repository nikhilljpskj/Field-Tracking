<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller {
    public function index() {
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        
        $data = [
            'title' => 'My Profile - Sales Tracking',
            'user' => $user
        ];
        $this->view('profile', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $userId = $_SESSION['user_id'];
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'bank_name' => $_POST['bank_name'] ?? '',
                'account_number' => $_POST['account_number'] ?? '',
                'ifsc_code' => $_POST['ifsc_code'] ?? ''
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            // Handle Profile Picture Upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                $uploadDir = 'assets/avatars/';
                $fullUploadDir = BASE_PATH . '/' . $uploadDir;
                
                if (!is_dir($fullUploadDir)) {
                    mkdir($fullUploadDir, 0777, true);
                }
                
                $extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($extension, $allowedExtensions)) {
                    $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
                    $targetFile = $uploadDir . $filename;
                    $fullTargetFile = BASE_PATH . '/' . $targetFile;
                    
                    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $fullTargetFile)) {
                        $data['profile_pic'] = $targetFile;
                        $_SESSION['profile_pic'] = $targetFile; // Update session
                    } else {
                        error_log("Failed to move uploaded file: " . $_FILES['profile_pic']['error']);
                    }
                }
            }

            $result = $userModel->updateProfile($userId, $data);
            
            if ($result) {
                if(isset($data['name'])) $_SESSION['user_name'] = $data['name']; 
                $_SESSION['flash_success'] = "Profile updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update profile data.";
            }
        }
        $this->redirect('profile');
    }
}
