<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Doctor;

class DoctorController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        $doctorModel = new Doctor();
        $doctors = $doctorModel->getAll();
        
        $data = [
            'title' => 'Master Data: Doctors & Points of Contact',
            'doctors' => $doctors
        ];
        $this->view('masters/doctors', $data);
    }

    public function add() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name'])) {
            $doctorModel = new Doctor();
            $result = $doctorModel->add([
                'name' => $_POST['name'],
                'phone' => $_POST['phone'] ?? null,
                'allotted_day' => $_POST['allotted_day'] ?? null,
                'allotted_time' => $_POST['allotted_time'] ?? null
            ]);
            
            if ($result) {
                $_SESSION['flash_success'] = "Doctor / Point of Contact added successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to add doctor.";
            }
        }
        $this->redirect('doctors');
    }

    public function update() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id']) && !empty($_POST['name'])) {
            $doctorModel = new Doctor();
            $result = $doctorModel->update($_POST['id'], [
                'name' => $_POST['name'],
                'phone' => $_POST['phone'] ?? null,
                'allotted_day' => $_POST['allotted_day'] ?? null,
                'allotted_time' => $_POST['allotted_time'] ?? null
            ]);
            if ($result) {
                $_SESSION['flash_success'] = "Doctor / POC updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update doctor / POC.";
            }
        }
        $this->redirect('doctors');
    }

    public function delete() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if (isset($_GET['id'])) {
            $doctorModel = new Doctor();
            $doctorModel->delete($_GET['id']);
            $_SESSION['flash_success'] = "Record deleted successfully!";
        }
        $this->redirect('doctors');
    }
}
