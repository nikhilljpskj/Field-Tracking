<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller {
    public function index() {
        $this->checkRole('Admin');
        $userModel = new User();
        $users = $userModel->getAll();
        $roles = $userModel->getRoles();
        $managers = $userModel->getManagers();
        
        $data = [
            'title' => 'User Management - Admin',
            'users' => $users,
            'roles' => $roles,
            'managers' => $managers
        ];
        $this->view('users', $data);
    }

    public function create() {
        $this->checkRole('Admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new User();
            $result = $userModel->create($_POST);
            if ($result) {
                $_SESSION['flash_success'] = "User created successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to create user.";
            }
        }
        $this->redirect('users');
    }

    public function edit() {
        $this->checkRole('Admin');
        if (isset($_GET['id'])) {
            $userModel = new User();
            $user = $userModel->findById($_GET['id']);
            $roles = $userModel->getRoles();
            $managers = $userModel->getManagers();
            
            $data = [
                'title' => 'Edit User - Admin',
                'user' => $user,
                'roles' => $roles,
                'managers' => $managers
            ];
            $this->view('users_edit', $data);
        } else {
            $this->redirect('users');
        }
    }

    public function update() {
        $this->checkRole('Admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $userModel = new User();
            $result = $userModel->update($_POST['id'], $_POST);
            if ($result) {
                $_SESSION['flash_success'] = "User updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update user.";
            }
        }
        $this->redirect('users');
    }

    public function delete() {
        $this->checkRole('Admin');
        if (isset($_GET['id'])) {
            $userModel = new User();
            $result = $userModel->delete($_GET['id']);
            if ($result) {
                $_SESSION['flash_success'] = "User deleted successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to delete user.";
            }
        }
        $this->redirect('users');
    }
}
