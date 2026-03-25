<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\MapService;

class MapController extends Controller {
    public function geocode() {
        if (!isset($_GET['q'])) {
            echo json_encode(['error' => 'Address required']);
            return;
        }

        $mapService = new MapService();
        $result = $mapService->geocode($_GET['q']);
        
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function distance() {
        if (!isset($_GET['origin']) || !isset($_GET['destination'])) {
            echo json_encode(['error' => 'Origin and destination required']);
            return;
        }

        list($oLat, $oLng) = explode(',', $_GET['origin']);
        list($dLat, $dLng) = explode(',', $_GET['destination']);

        $mapService = new MapService();
        $distance = $mapService->getRouteDistance($oLat, $oLng, $dLat, $dLng);
        
        header('Content-Type: application/json');
        echo json_encode(['distance' => $distance]);
        exit;
    }
}
