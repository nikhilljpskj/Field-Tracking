<?php
namespace App\Services;

use App\Core\Config;
use App\Core\Model;

class MapService extends Model {
    private $apiKey;
    private $baseUrl = 'https://geocode.search.hereapi.com/v1/';
    private $routingUrl = 'https://router.hereapi.com/v8/';

    public function __construct() {
        parent::__construct();
        $this->apiKey = Config::get('HERE_API_KEY');
    }

    /**
     * Convert address to coordinates with database caching
     */
    public function geocode($address) {
        $addressHash = hash('sha256', strtolower(trim($address)));

        // 1. Check Cache
        $stmt = $this->db->prepare("SELECT latitude, longitude FROM geocoding_cache WHERE address_hash = ?");
        $stmt->execute([$addressHash]);
        $cached = $stmt->fetch();

        if ($cached) {
            return ['lat' => $cached['latitude'], 'lng' => $cached['longitude'], 'source' => 'cache'];
        }

        // 2. Call HERE API
        $url = $this->baseUrl . "geocode?q=" . urlencode($address) . "&apiKey=" . $this->apiKey;
        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if (isset($data['items'][0]['position'])) {
            $lat = $data['items'][0]['position']['lat'];
            $lng = $data['items'][0]['position']['lng'];

            // 3. Save to Cache
            $stmt = $this->db->prepare("INSERT INTO geocoding_cache (address_hash, address, latitude, longitude) VALUES (?, ?, ?, ?)");
            $stmt->execute([$addressHash, $address, $lat, $lng]);

            return ['lat' => $lat, 'lng' => $lng, 'source' => 'api'];
        }

        return null;
    }

    /**
     * Calculate route and distance between two points
     */
    public function getRouteDistance($startLat, $startLng, $endLat, $endLng) {
        $url = $this->routingUrl . "routes?transportMode=car&origin={$startLat},{$startLng}&destination={$endLat},{$endLng}&return=summary&apiKey=" . $this->apiKey;
        $response = @file_get_contents($url);
        if (!$response) return 0;

        $data = json_decode($response, true);
        if (isset($data['routes'][0]['sections'][0]['summary']['length'])) {
            // Returns distance in meters
            return $data['routes'][0]['sections'][0]['summary']['length'];
        }

        return 0;
    }
}
