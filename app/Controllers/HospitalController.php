<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Hospital;

class HospitalController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        $hospitalModel = new Hospital();
        $hospitals = $hospitalModel->getAll();
        
        $data = [
            'title' => 'Master Data: Hospitals & Offices',
            'hospitals' => $hospitals
        ];
        $this->view('masters/hospitals', $data);
    }

    public function add() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['name'])) {
            $hospitalModel = new Hospital();
            $result = $hospitalModel->add([
                'name' => $_POST['name'],
                'address' => $_POST['address'] ?? null,
                'location_url' => $_POST['location_url'] ?? null,
                'allotted_day' => $_POST['allotted_day'] ?? null,
                'allotted_time' => $_POST['allotted_time'] ?? null
            ]);
            
            if ($result) {
                $_SESSION['flash_success'] = "Hospital / Office added successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to add facility.";
            }
        }
        $this->redirect('hospitals');
    }

    public function update() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['id']) && !empty($_POST['name'])) {
            $hospitalModel = new Hospital();
            $result = $hospitalModel->update($_POST['id'], [
                'name' => $_POST['name'],
                'address' => $_POST['address'] ?? null,
                'location_url' => $_POST['location_url'] ?? null,
                'allotted_day' => $_POST['allotted_day'] ?? null,
                'allotted_time' => $_POST['allotted_time'] ?? null
            ]);
            if ($result) {
                $_SESSION['flash_success'] = "Hospital / Office updated successfully!";
            } else {
                $_SESSION['flash_error'] = "Failed to update facility.";
            }
        }
        $this->redirect('hospitals');
    }

    public function delete() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        if (isset($_GET['id'])) {
            $hospitalModel = new Hospital();
            $hospitalModel->delete($_GET['id']);
            $_SESSION['flash_success'] = "Record deleted successfully!";
        }
        $this->redirect('hospitals');
    }

    public function export() {
        $this->checkRole(['Admin', 'Manager', 'HR']);
        $format = $_GET['format'] ?? 'csv';
        $hospitalModel = new Hospital();
        $rows = $hospitalModel->getAll();

        if ($format === 'pdf') {
            $this->view('reports/print_hospitals', ['rows' => $rows, 'title' => 'Hospitals & Offices']);
            return;
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="hospitals_offices.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Facility Name', 'Allotted Day', 'Allotted Time', 'Location URL', 'Address', 'Created At']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['name'] ?? '',
                $r['allotted_day'] ?? '',
                $r['allotted_time'] ?? '',
                $r['location_url'] ?? '',
                $r['address'] ?? '',
                $r['created_at'] ?? ''
            ]);
        }
        fclose($out);
        exit;
    }
}
