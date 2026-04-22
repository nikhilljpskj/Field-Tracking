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

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role_name'] ?? $user['role']; // Handle join if model updated
                $_SESSION['profile_pic'] = $user['profile_pic'] ?? 'assets/avatars/default.jpg';
                
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
