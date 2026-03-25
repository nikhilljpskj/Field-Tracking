<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Travel;

class SettingsController extends Controller {
    public function index() {
        $this->checkRole('Admin');
        $travelModel = new Travel();
        $rate = $travelModel->getCurrentRate();
        $history = $travelModel->getAllRates();
        
        $data = [
            'title' => 'Global System Settings',
            'rate' => $rate,
            'history' => $history
        ];
        $this->view('settings', $data);
    }

    public function updateRate() {
        $this->checkRole('Admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate'])) {
            $travelModel = new Travel();
            $result = $travelModel->setRate($_POST['rate']);
            if ($result) {
                $_SESSION['flash_success'] = "Travel allowance rate updated to ₹" . $_POST['rate'] . " per KM!";
            } else {
                $_SESSION['flash_error'] = "Failed to update travel rate.";
            }
        }
        $this->redirect('settings');
    }
}
