<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hospital;

class HospitalController extends Controller {
    public function index() {
        $hospitalModel = new Hospital();
        $hospitals = $hospitalModel->getAll();
        
        $data = [
            'title' => 'Master Data: Hospitals & Offices',
            'hospitals' => $hospitals
        ];
        $this->view('masters/hospitals', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name'])) {
            $hospitalModel = new Hospital();
            $result = $hospitalModel->add([
                'name' => $_POST['name'],
                'address' => $_POST['address'] ?? null
            ]);
            
            if ($result) {
                $_SESSION['flash_success'] = "Hospital / Office added successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to add facility.";
            }
        }
        $this->redirect('hospitals');
    }

    public function delete() {
        $this->checkRole(['Admin', 'Manager']);
        if (isset($_GET['id'])) {
            $hospitalModel = new Hospital();
            $hospitalModel->delete($_GET['id']);
            $_SESSION['flash_success'] = "Record deleted successfully!";
        }
        $this->redirect('hospitals');
    }
}
