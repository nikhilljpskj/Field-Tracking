<?php
namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Models\User;

class EmployeeCodeController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'HR']);

        $userModel = new User();
        $filters = [
            'q' => trim($_GET['q'] ?? '')
        ];
        $users = $userModel->listForQrModule($filters);

        $data = [
            'title' => 'Employee QR / Barcode - User Management',
            'users' => $users,
            'q' => $filters['q']
        ];
        $this->view('users_qr_codes', $data);
    }

    public function bulkGenerate() {
        $this->checkRole(['Admin', 'HR']);
        $userModel = new User();
        $count = $userModel->bulkGenerateMissingEmployeeCodes();
        $_SESSION['flash_success'] = $count . ' employee code(s) generated successfully.';
        $this->redirect('employee-codes');
    }

    public function regenerate() {
        $this->checkRole(['Admin', 'HR']);
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'Invalid employee selection.';
            $this->redirect('employee-codes');
        }

        $userModel = new User();
        $user = $userModel->findByIdWithRole($id);
        if (!$user) {
            $_SESSION['flash_error'] = 'Employee not found.';
            $this->redirect('employee-codes');
        }

        $userModel->ensureEmployeeCode($id);
        $userModel->regenerateQrToken($id);
        $userModel->touchBarcodeGeneratedAt($id);
        $_SESSION['flash_success'] = 'QR/Barcode data regenerated for ' . $user['name'] . '.';
        $this->redirect('employee-codes');
    }

    public function download() {
        $this->checkRole(['Admin', 'HR']);
        $id = (int)($_GET['id'] ?? 0);
        $type = strtolower(trim($_GET['type'] ?? 'qr'));
        $format = strtolower(trim($_GET['format'] ?? 'png'));
        $preview = (int)($_GET['preview'] ?? 0) === 1;

        if ($id <= 0 || !in_array($type, ['qr', 'barcode'], true) || !in_array($format, ['png', 'svg'], true)) {
            http_response_code(400);
            echo 'Invalid request.';
            return;
        }

        $userModel = new User();
        $user = $userModel->findByIdWithRole($id);
        if (!$user) {
            http_response_code(404);
            echo 'Employee not found.';
            return;
        }

        $code = $userModel->ensureEmployeeCode($id);
        $token = $userModel->ensureQrToken($id);
        if (!$code || !$token) {
            http_response_code(500);
            echo 'Could not prepare employee code.';
            return;
        }

        $payload = $this->buildQrPayload($code, $token);
        $safeCode = preg_replace('/[^A-Za-z0-9_-]/', '', $code);
        $filename = $safeCode . '-' . $type . '.' . $format;
        $userModel->touchBarcodeGeneratedAt($id);

        if ($type === 'qr' && $format === 'png') {
            $this->downloadQrPng($payload, $filename, $preview);
            return;
        }
        if ($type === 'qr' && $format === 'svg') {
            $this->downloadQrSvg($payload, $filename, $preview);
            return;
        }
        if ($type === 'barcode' && $format === 'svg') {
            $this->downloadBarcodeSvg($code, $filename, $preview);
            return;
        }
        $this->downloadBarcodePng($code, $filename, $preview);
    }

    public function verify() {
        $token = trim($_GET['token'] ?? '');
        if ($token === '') {
            http_response_code(404);
            echo 'Invalid verification link.';
            return;
        }

        $userModel = new User();
        $user = $userModel->findByQrToken($token);
        if (!$user) {
            http_response_code(404);
            echo 'Employee not found.';
            return;
        }

        $data = [
            'title' => 'Employee Verification',
            'employee' => $user
        ];
        $this->view('employee_verify', $data);
    }

    private function buildQrPayload($employeeCode, $token) {
        $appUrl = rtrim(Config::get('APP_URL', ''), '/');
        if ($appUrl !== '') {
            return $appUrl . '/verify-employee?token=' . rawurlencode($token);
        }
        return $employeeCode;
    }

    private function downloadQrPng($payload, $filename, $preview = false) {
        require_once BASE_PATH . '/app/Libraries/phpqrcode.php';
        ob_start();
        \QRcode::png($payload, false, QR_ECLEVEL_M, 8, 2);
        $png = ob_get_clean();

        $image = @imagecreatefromstring($png);
        if ($image !== false) {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagecolortransparent($image, $white);
            ob_start();
            imagepng($image);
            $png = ob_get_clean();
            imagedestroy($image);
        }

        header('Content-Type: image/png');
        header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
        echo $png;
    }

    private function downloadQrSvg($payload, $filename, $preview = false) {
        require_once BASE_PATH . '/app/Libraries/phpqrcode.php';
        ob_start();
        \QRcode::svg($payload, false, QR_ECLEVEL_M, 8, 2);
        $svg = ob_get_clean();
        $svg = preg_replace('/<rect[^>]*fill="white"[^>]*\/>/i', '', $svg);
        $svg = str_replace('<svg ', '<svg style="background:transparent" ', $svg);

        header('Content-Type: image/svg+xml');
        header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
        echo $svg;
    }

    private function code39Patterns() {
        return [
            '0' => 'nnnwwnwnn', '1' => 'wnnwnnnnw', '2' => 'nnwwnnnnw', '3' => 'wnwwnnnnn',
            '4' => 'nnnwwnnnw', '5' => 'wnnwwnnnn', '6' => 'nnwwwnnnn', '7' => 'nnnwnnwnw',
            '8' => 'wnnwnnwnn', '9' => 'nnwwnnwnn', 'A' => 'wnnnnwnnw', 'B' => 'nnwnnwnnw',
            'C' => 'wnwnnwnnn', 'D' => 'nnnnwwnnw', 'E' => 'wnnnwwnnn', 'F' => 'nnwnwwnnn',
            'G' => 'nnnnnwwnw', 'H' => 'wnnnnwwnn', 'I' => 'nnwnnwwnn', 'J' => 'nnnnwwwnn',
            'K' => 'wnnnnnnww', 'L' => 'nnwnnnnww', 'M' => 'wnwnnnnwn', 'N' => 'nnnnwnnww',
            'O' => 'wnnnwnnwn', 'P' => 'nnwnwnnwn', 'Q' => 'nnnnnnwww', 'R' => 'wnnnnnwwn',
            'S' => 'nnwnnnwwn', 'T' => 'nnnnwnwwn', 'U' => 'wwnnnnnnw', 'V' => 'nwwnnnnnw',
            'W' => 'wwwnnnnnn', 'X' => 'nwnnwnnnw', 'Y' => 'wwnnwnnnn', 'Z' => 'nwwnwnnnn',
            '-' => 'nwnnnnwnw', '.' => 'wwnnnnwnn', ' ' => 'nwwnnnwnn', '$' => 'nwnwnwnnn',
            '/' => 'nwnwnnnwn', '+' => 'nwnnnwnwn', '%' => 'nnnwnwnwn', '*' => 'nwnnwnwnn'
        ];
    }

    private function barcodeSvgMarkup($value) {
        $value = strtoupper($value);
        $encoded = '*' . $value . '*';
        $patterns = $this->code39Patterns();

        $x = 10;
        $h = 120;
        $unitNarrow = 2;
        $unitWide = 5;
        $gap = 2;
        $bars = [];

        foreach (str_split($encoded) as $char) {
            if (!isset($patterns[$char])) {
                continue;
            }
            $pattern = str_split($patterns[$char]);
            foreach ($pattern as $i => $w) {
                $width = $w === 'w' ? $unitWide : $unitNarrow;
                if ($i % 2 === 0) {
                    $bars[] = '<rect x="' . $x . '" y="10" width="' . $width . '" height="' . $h . '" fill="#000" />';
                }
                $x += $width;
            }
            $x += $gap;
        }

        $totalWidth = $x + 10;
        $labelY = $h + 35;
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" width="' . $totalWidth . '" height="180" viewBox="0 0 ' . $totalWidth . ' 180">'
            . implode('', $bars)
            . '<text x="' . ($totalWidth / 2) . '" y="' . $labelY . '" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" fill="#000">' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '</text>'
            . '</svg>';
    }

    private function downloadBarcodeSvg($employeeCode, $filename, $preview = false) {
        $svg = $this->barcodeSvgMarkup($employeeCode);
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
        echo $svg;
    }

    private function downloadBarcodePng($employeeCode, $filename, $preview = false) {
        $svg = $this->barcodeSvgMarkup($employeeCode);
        $width = 700;
        $height = 220;
        $img = imagecreatetruecolor($width, $height);
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefill($img, 0, 0, $transparent);
        $black = imagecolorallocate($img, 0, 0, 0);

        preg_match_all('/<rect x="(\d+)" y="(\d+)" width="(\d+)" height="(\d+)"/', $svg, $matches, PREG_SET_ORDER);
        foreach ($matches as $m) {
            imagefilledrectangle($img, (int)$m[1], (int)$m[2], (int)$m[1] + (int)$m[3], (int)$m[2] + (int)$m[4], $black);
        }
        imagestring($img, 5, 260, 170, $employeeCode, $black);

        header('Content-Type: image/png');
        header('Content-Disposition: ' . ($preview ? 'inline' : 'attachment') . '; filename="' . $filename . '"');
        imagepng($img);
        imagedestroy($img);
    }
}
