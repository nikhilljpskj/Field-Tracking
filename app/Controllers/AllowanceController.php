<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Travel;
use App\Models\Tracking;

class AllowanceController extends Controller {
    public function index() {
        $travelModel = new Travel();
        $trackingModel = new Tracking();
        
        $rate = $travelModel->getCurrentRate();
        $date = date('Y-m-d');
        $summary = $travelModel->getTravelSummary($_SESSION['user_id'], $date);
        
        // If no summary for today, let's calculate it based on tracking logs
        if (!$summary) {
            $mapService = new \App\Services\MapService();
            $route = $trackingModel->getRoute($_SESSION['user_id'], $date);
            $distance = 0;
            
            // Optimization: Sampling every 5th point or only if distance > 200m to reduce API hits
            // Also ensures road-matched routing via HERE
            if (count($route) > 1) {
                for ($i = 0; $i < count($route) - 1; $i += 2) {
                    $nextIdx = min($i + 2, count($route) - 1);
                    $distance += $mapService->getRouteDistance(
                        $route[$i]['latitude'], $route[$i]['longitude'],
                        $route[$nextIdx]['latitude'], $route[$nextIdx]['longitude']
                    );
                }
            }

            $distanceKm = $distance / 1000;
            $allowance = $distanceKm * $rate;
            
            $travelModel->updateTravelSummary($_SESSION['user_id'], $date, $distanceKm, $allowance);
            $summary = ['total_distance' => $distanceKm, 'allowance_earned' => $allowance, 'date' => $date];
        }

        $data = [
            'title' => 'Travel Allowance - Sales Tracking',
            'summary' => $summary,
            'rate' => $rate
        ];
        $this->view('allowance', $data);
    }

    private function vincentyGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
            pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);

        $angle = atan2(sqrt($a), $b);
        return $angle * $earthRadius;
    }
}
