<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Travel;

class SettingsController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager']);
        $travelModel = new \App\Models\Travel();
        $rate = $travelModel->getCurrentRate();
        $history = $travelModel->getAllRates();
        
        $db = \Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM performance_configs WHERE key_name = 'daily_visits'");
        $thresholds = $stmt->fetch() ?: ['value_low' => 4, 'value_avg' => 7, 'value_exc' => 10];

        $data = [
            'title' => 'Global Performance & Financial Settings',
            'rate' => $rate,
            'history' => $history,
            'thresholds' => $thresholds
        ];
        $this->view('settings', $data);
    }

    public function update_thresholds() {
        $this->checkRole(['Admin', 'Manager']);
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = \Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE performance_configs SET value_low = ?, value_avg = ?, value_exc = ? WHERE key_name = 'daily_visits'");
            $result = $stmt->execute([$_POST['low'], $_POST['avg'], $_POST['exc']]);
            
            if ($result) {
                $_SESSION['flash_success'] = "Performance audit thresholds updated!";
            } else {
                $_SESSION['flash_error'] = "Failed to update thresholds.";
            }
        }
        $this->redirect('settings');
    }

    public function updateRate() {
        $this->checkRole('Admin');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate'])) {
            $travelModel = new \App\Models\Travel();
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
