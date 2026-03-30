<?php
namespace App\Models;

use App\Core\Model;

class Hospital extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM hospitals ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function add($data) {
        $stmt = $this->db->prepare("INSERT INTO hospitals (name, address) VALUES (?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['address'] ?? null
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM hospitals WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
