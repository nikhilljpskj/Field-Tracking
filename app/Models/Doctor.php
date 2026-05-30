<?php
namespace App\Models;

use App\Core\Model;

class Doctor extends Model {
    private $supportedColumns = null;

    private function getSupportedColumns() {
        if ($this->supportedColumns !== null) {
            return $this->supportedColumns;
        }
        $cols = [];
        $stmt = $this->db->query("SHOW COLUMNS FROM doctors");
        foreach ($stmt->fetchAll() as $row) {
            $cols[] = $row['Field'];
        }
        $this->supportedColumns = $cols;
        return $this->supportedColumns;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM doctors ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function add($data) {
        $cols = $this->getSupportedColumns();
        $insertCols = ['name'];
        $params = [$data['name']];

        if (in_array('phone', $cols, true)) {
            $insertCols[] = 'phone';
            $params[] = $data['phone'] ?? null;
        }
        if (in_array('allotted_day', $cols, true)) {
            $insertCols[] = 'allotted_day';
            $params[] = $data['allotted_day'] ?? null;
        }
        if (in_array('allotted_time', $cols, true)) {
            $insertCols[] = 'allotted_time';
            $params[] = $data['allotted_time'] ?? null;
        }

        $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
        $sql = "INSERT INTO doctors (" . implode(',', $insertCols) . ") VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function update($id, $data) {
        $cols = $this->getSupportedColumns();
        $set = ["name = ?"];
        $params = [$data['name']];

        if (in_array('phone', $cols, true)) {
            $set[] = "phone = ?";
            $params[] = $data['phone'] ?? null;
        }
        if (in_array('allotted_day', $cols, true)) {
            $set[] = "allotted_day = ?";
            $params[] = $data['allotted_day'] ?? null;
        }
        if (in_array('allotted_time', $cols, true)) {
            $set[] = "allotted_time = ?";
            $params[] = $data['allotted_time'] ?? null;
        }

        $params[] = $id;
        $sql = "UPDATE doctors SET " . implode(', ', $set) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM doctors WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
