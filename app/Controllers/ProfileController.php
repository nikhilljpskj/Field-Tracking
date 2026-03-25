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
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone']
            ];

            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            // Handle Profile Picture Upload
            if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
                $uploadDir = 'assets/avatars/';
                if (!is_dir(BASE_PATH . '/' . $uploadDir)) {
                    mkdir(BASE_PATH . '/' . $uploadDir, 0777, true);
                }
                
                $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
                $filename = 'user_' . $userId . '_' . time() . '.' . $extension;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], BASE_PATH . '/' . $targetFile)) {
                    $data['profile_pic'] = $targetFile;
                    $_SESSION['profile_pic'] = $targetFile; // Update session
                }
            }

            $result = $userModel->updateProfile($userId, $data);
            
            if ($result) {
                $_SESSION['user_name'] = $data['name']; // Update session name
                $_SESSION['flash_success'] = "Profile updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update profile.";
            }
        }
        $this->redirect('profile');
    }
}
