<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller {
    public function login() {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('dashboard');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = new User();
            $user = $userModel->findByEmail($email);

            if ($user && (int)($user['is_active'] ?? 1) !== 1) {
                $data['error'] = 'Your account has been disabled. Please contact admin.';
                $this->view('auth/login', $data);
                return;
            }

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role_name'] ?? $user['role']; // Handle join if model updated
                $profilePic = $user['profile_pic'] ?? '';
                if ($profilePic === '' || $profilePic === 'assets/avatars/default.jpg') {
                    $profilePic = 'assets/avatars/default.svg';
                }
                $_SESSION['profile_pic'] = $profilePic;
                
                $this->redirect('dashboard');
            } else {
                $data['error'] = 'Invalid email or password';
                $this->view('auth/login', $data);
            }
        } else {
            $this->view('auth/login');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('login');
    }
}
