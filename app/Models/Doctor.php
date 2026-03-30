<?php
namespace App\Models;

use App\Core\Model;

class Doctor extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM doctors ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function add($data) {
        $stmt = $this->db->prepare("INSERT INTO doctors (name, phone) VALUES (?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['phone'] ?? null
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM doctors WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
