<?php
namespace App\Controllers;

use App\Core\Controller;

class ContactController extends Controller {
    public function index() {
        $this->checkRole(['Admin', 'Manager', 'HR', 'Executive', 'Field Executive', 'Staff']);

        $contacts = $this->getContactsByRole();
        $data = [
            'title' => 'Contact Directory - Sales Tracking',
            'contacts' => $contacts
        ];
        $this->view('contacts', $data);
    }

    public function export() {
        $this->checkRole(['Admin', 'Manager', 'HR', 'Executive', 'Field Executive', 'Staff']);

        $format = $_GET['format'] ?? 'csv';
        $contacts = $this->getContactsByRole();

        if ($format === 'pdf') {
            $this->view('reports/print_contacts', [
                'title' => 'Contact Directory',
                'contacts' => $contacts
            ]);
            return;
        }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contact_directory.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Name', 'Role', 'Email', 'Mobile Number']);
        foreach ($contacts as $c) {
            fputcsv($out, [
                $c['name'] ?? '',
                $c['role_name'] ?? '',
                $c['email'] ?? '',
                $c['phone'] ?? ''
            ]);
        }
        fclose($out);
        exit;
    }

    private function getContactsByRole() {
        $db = \Database::getInstance()->getConnection();
        $role = $_SESSION['role'] ?? '';
        $userId = (int)($_SESSION['user_id'] ?? 0);

        if (in_array($role, ['Admin', 'HR'], true)) {
            $stmt = $db->query("SELECT u.name, u.email, u.phone, r.name AS role_name
                                FROM users u
                                LEFT JOIN roles r ON r.id = u.role_id
                                WHERE COALESCE(u.is_active, 1) = 1
                                ORDER BY u.name ASC");
            return $stmt->fetchAll();
        }

        if ($role === 'Manager') {
            $stmt = $db->prepare("SELECT u.name, u.email, u.phone, r.name AS role_name
                                  FROM users u
                                  LEFT JOIN roles r ON r.id = u.role_id
                                  WHERE COALESCE(u.is_active, 1) = 1
                                    AND u.manager_id = ?
                                  ORDER BY u.name ASC");
            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        }

        // Staff / Executive view: only their reporting manager and HR users.
        $stmt = $db->prepare("SELECT manager_id FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $managerId = (int)($stmt->fetchColumn() ?: 0);

        $params = [];
        $scope = ["r.name = 'HR'"];
        if ($managerId > 0) {
            $scope[] = "u.id = ?";
            $params[] = $managerId;
        }

        $sql = "SELECT DISTINCT u.name, u.email, u.phone, r.name AS role_name
                FROM users u
                LEFT JOIN roles r ON r.id = u.role_id
                WHERE COALESCE(u.is_active, 1) = 1
                  AND (" . implode(' OR ', $scope) . ")
                ORDER BY u.name ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
