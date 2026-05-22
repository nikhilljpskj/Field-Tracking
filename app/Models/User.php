<?php
namespace App\Models;

use App\Core\Model;


class User extends Model {
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name 
                                    FROM users u 
                                    JOIN roles r ON u.role_id = r.id 
                                    WHERE u.email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT u.*, r.name as role_name, m.name as manager_name 
                                  FROM users u 
                                  LEFT JOIN roles r ON u.role_id = r.id 
                                  LEFT JOIN users m ON u.manager_id = m.id
                                  ORDER BY u.created_at DESC");
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO users (role_id, name, email, password, phone, manager_id) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['role_id'],
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['phone'],
            !empty($data['manager_id']) ? $data['manager_id'] : null
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE users SET role_id = ?, name = ?, email = ?, phone = ?, manager_id = ?, 
                        bank_name = ?, account_number = ?, ifsc_code = ?";
        $params = [
            $data['role_id'],
            $data['name'],
            $data['email'],
            $data['phone'],
            !empty($data['manager_id']) ? $data['manager_id'] : null,
            $data['bank_name'] ?? null,
            $data['account_number'] ?? null,
            $data['ifsc_code'] ?? null
        ];

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateProfile($id, $data) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, bank_name = ?, account_number = ?, ifsc_code = ?";
        $params = [
            $data['name'], $data['email'], $data['phone'],
            $data['bank_name'] ?? null,
            $data['account_number'] ?? null,
            $data['ifsc_code'] ?? null
        ];

        if (!empty($data['profile_pic'])) {
            $sql .= ", profile_pic = ?";
            $params[] = $data['profile_pic'];
        }

        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleActive($id) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function setActive($id, $status) {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        return $stmt->execute([(int)$status, $id]);
    }

    public function getRoles() {
        return $this->db->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();
    }

    public function getManagers() {
        // Find users with Manager or Admin role
        $stmt = $this->db->query("SELECT u.id, u.name FROM users u 
                                  JOIN roles r ON u.role_id = r.id 
                                  WHERE r.name IN ('Admin', 'Manager') 
                                  ORDER BY u.name ASC");
        return $stmt->fetchAll();
    }

    public function getExecutivesByManagerId($manager_id) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name 
                                    FROM users u 
                                    JOIN roles r ON u.role_id = r.id 
                                    WHERE u.manager_id = ? 
                                    ORDER BY u.name ASC");
        $stmt->execute([$manager_id]);
        return $stmt->fetchAll();
    }

    public function findByIdWithRole($id) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name
                                    FROM users u
                                    LEFT JOIN roles r ON u.role_id = r.id
                                    WHERE u.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function listForQrModule($filters = []) {
        $sql = "SELECT u.id, u.name, u.employee_code, u.qr_token, u.barcode_generated_at, u.is_active,
                       u.department, u.designation, r.name as role_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['q'])) {
            $sql .= " AND (u.name LIKE ? OR u.employee_code LIKE ? OR u.department LIKE ? OR u.designation LIKE ?)";
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }

        $sql .= " ORDER BY u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function formatEmployeeCodeFromUser(array $user) {
        $createdAt = $user['created_at'] ?? date('Y-m-d H:i:s');
        $year2 = date('y', strtotime($createdAt));
        $baseSeq = ((int)$user['id']) % 1000;

        // Force exactly 5 digits after RED: YY + XXX
        $candidate = 'RED' . $year2 . str_pad((string)$baseSeq, 3, '0', STR_PAD_LEFT);
        if (!$this->employeeCodeExists($candidate, (int)$user['id'])) {
            return $candidate;
        }

        // Collision fallback within the same 3-digit space.
        for ($i = 0; $i < 1000; $i++) {
            $seq = ($baseSeq + $i + 1) % 1000;
            $try = 'RED' . $year2 . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
            if (!$this->employeeCodeExists($try, (int)$user['id'])) {
                return $try;
            }
        }

        return null;
    }

    private function employeeCodeExists($employeeCode, $excludeUserId = 0) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE employee_code = ? AND id <> ? LIMIT 1");
        $stmt->execute([$employeeCode, (int)$excludeUserId]);
        return (bool)$stmt->fetch();
    }

    public function ensureEmployeeCode($userId) {
        $user = $this->findByIdWithRole($userId);
        if (!$user) {
            return null;
        }
        if (!empty($user['employee_code'])) {
            return $user['employee_code'];
        }

        for ($i = 0; $i < 5; $i++) {
            $code = $this->formatEmployeeCodeFromUser($user);
            if (!$code) {
                return null;
            }
            $stmt = $this->db->prepare("UPDATE users SET employee_code = ? WHERE id = ? AND (employee_code IS NULL OR employee_code = '')");
            try {
                $stmt->execute([$code, $userId]);
                if ($stmt->rowCount() > 0) {
                    return $code;
                }
                $fresh = $this->findByIdWithRole($userId);
                return $fresh['employee_code'] ?? null;
            } catch (\PDOException $e) {
                if ((int)$e->getCode() !== 23000) {
                    throw $e;
                }
            }
        }

        return null;
    }

    public function ensureQrToken($userId) {
        $user = $this->findByIdWithRole($userId);
        if (!$user) {
            return null;
        }
        if (!empty($user['qr_token'])) {
            return $user['qr_token'];
        }

        for ($i = 0; $i < 5; $i++) {
            $token = bin2hex(random_bytes(16));
            $stmt = $this->db->prepare("UPDATE users SET qr_token = ? WHERE id = ? AND (qr_token IS NULL OR qr_token = '')");
            try {
                $stmt->execute([$token, $userId]);
                if ($stmt->rowCount() > 0) {
                    return $token;
                }
                $fresh = $this->findByIdWithRole($userId);
                return $fresh['qr_token'] ?? null;
            } catch (\PDOException $e) {
                if ((int)$e->getCode() !== 23000) {
                    throw $e;
                }
            }
        }

        return null;
    }

    public function regenerateQrToken($userId) {
        for ($i = 0; $i < 5; $i++) {
            $token = bin2hex(random_bytes(16));
            $stmt = $this->db->prepare("UPDATE users SET qr_token = ? WHERE id = ?");
            try {
                $stmt->execute([$token, $userId]);
                return $token;
            } catch (\PDOException $e) {
                if ((int)$e->getCode() !== 23000) {
                    throw $e;
                }
            }
        }
        return null;
    }

    public function touchBarcodeGeneratedAt($userId) {
        $stmt = $this->db->prepare("UPDATE users SET barcode_generated_at = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function bulkGenerateMissingEmployeeCodes() {
        $stmt = $this->db->query("SELECT id FROM users WHERE employee_code IS NULL OR employee_code = '' ORDER BY id ASC");
        $users = $stmt->fetchAll();
        $count = 0;
        foreach ($users as $u) {
            if ($this->ensureEmployeeCode((int)$u['id'])) {
                $count++;
            }
        }
        return $count;
    }

    public function findByQrToken($token) {
        $stmt = $this->db->prepare("SELECT u.*, r.name as role_name
                                    FROM users u
                                    LEFT JOIN roles r ON u.role_id = r.id
                                    WHERE LOWER(u.qr_token) = LOWER(?)
                                    LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }
}
