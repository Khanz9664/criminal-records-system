<?php

namespace App\Models;

use App\Core\Model;

class Evidence extends Model
{
    protected $table = 'evidence';

    public function getByCaseId($caseId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE case_id = ? ORDER BY created_at DESC");
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }

    public function getAll($search = '')
    {
        $sql = "SELECT e.*, c.title as case_title, u.username as uploader_name 
                FROM {$this->table} e
                JOIN cases c ON e.case_id = c.id
                LEFT JOIN users u ON e.uploaded_by = u.id
                WHERE 1=1";

        $params = [];
        if (!empty($search)) {
            $sql .= " AND (e.title LIKE ? OR c.title LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY e.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function add($data)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (case_id, title, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['case_id'],
            $data['title'],
            $data['file_path'],
            $data['file_type'],
            $data['uploaded_by']
        ]);
    }
}
