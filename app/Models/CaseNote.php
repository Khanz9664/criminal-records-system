<?php

namespace App\Models;

use App\Core\Model;

class CaseNote extends Model
{
    protected $table = 'case_notes';

    public function getByCaseId($caseId)
    {
        $sql = "SELECT case_notes.*, users.username, users.role 
                FROM {$this->table} 
                JOIN users ON case_notes.user_id = users.id 
                WHERE case_id = ? 
                ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$caseId]);
        return $stmt->fetchAll();
    }

    public function add($caseId, $userId, $note)
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (case_id, user_id, note) VALUES (?, ?, ?)");
        return $stmt->execute([$caseId, $userId, $note]);
    }
}
