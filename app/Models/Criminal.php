<?php

namespace App\Models;

use App\Core\Model;

class Criminal extends Model
{
    protected $table = 'criminals';

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function getAll($search = '', $status = 'All')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($status !== 'All') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (first_name LIKE ? OR last_name LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (first_name, last_name, date_of_birth, gender, blood_type, address, photo_path, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'] ?? null,
            $data['gender'] ?? null,
            $data['blood_type'] ?? null,
            $data['address'] ?? null,
            $data['photo_path'] ?? null,
            $data['status']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET first_name = ?, last_name = ?, date_of_birth = ?, gender = ?, blood_type = ?, 
                address = ?, status = ? WHERE id = ?";
        $params = [
            $data['first_name'],
            $data['last_name'],
            $data['date_of_birth'],
            $data['gender'],
            $data['blood_type'],
            $data['address'],
            $data['status'],
            $id
        ];

        // Only update photo if provided
        if (isset($data['photo_path']) && !empty($data['photo_path'])) {
            $sql = str_replace("status = ?", "status = ?, photo_path = ?", $sql);
            // Rebuild params
            $params = [
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['gender'],
                $data['blood_type'],
                $data['address'],
                $data['status'],
                $data['photo_path'],
                $id
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getLinkedCases($criminalId)
    {
        $sql = "SELECT cases.id, cases.title, cases.status, case_suspects.involvement 
                FROM case_suspects 
                JOIN cases ON case_suspects.case_id = cases.id 
                WHERE case_suspects.criminal_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$criminalId]);
        return $stmt->fetchAll();
    }

    public function linkCase($criminalId, $caseId, $involvement)
    {
        $stmt = $this->db->prepare("INSERT INTO case_suspects (criminal_id, case_id, involvement) VALUES (?, ?, ?)");
        return $stmt->execute([$criminalId, $caseId, $involvement]);
    }
}
