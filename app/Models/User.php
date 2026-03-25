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
}
