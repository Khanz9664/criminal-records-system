<?php

namespace App\Models;

use App\Core\Model;

class CrimeCase extends Model
{
    protected $table = 'cases';

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        return $stmt->fetchColumn();
    }

    public function countActive()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'Open' OR status = 'Under Investigation'");
        return $stmt->fetchColumn();
    }

    public function countClosed()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'Closed'");
        return $stmt->fetchColumn();
    }

    public function getAll($search = '', $status = 'All')
    {
        $sql = "SELECT cases.*, users.full_name as officer_name 
                FROM {$this->table} 
                LEFT JOIN users ON cases.assigned_officer_id = users.id 
                WHERE 1=1";
        $params = [];

        if ($status !== 'All') {
            $sql .= " AND cases.status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE ? OR description LIKE ?)";
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
        $sql = "INSERT INTO {$this->table} (title, description, type, status, priority, location, incident_date, assigned_officer_id, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'],
            $data['type'],
            'Open',
            $data['priority'],
            $data['location'],
            $data['incident_date'],
            !empty($data['assigned_officer_id']) ? $data['assigned_officer_id'] : null,
            $_SESSION['user_id']
        ]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getSuspects($caseId)
    {
        $stmt = $this->db->prepare("SELECT criminals.*, case_suspects.involvement 
                                   FROM criminals 
                                   JOIN case_suspects ON criminals.id = case_suspects.criminal_id 
                                   WHERE case_suspects.case_id = ?");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }
}
