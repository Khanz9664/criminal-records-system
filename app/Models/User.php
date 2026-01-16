<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected $table = 'users';

    public function getAll($search = '', $role = 'All')
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($role !== 'All') {
            $sql .= " AND role = ?";
            $params[] = $role;
        }

        if (!empty($search)) {
            $sql .= " AND (full_name LIKE ? OR username LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
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
        $sql = "INSERT INTO {$this->table} (full_name, username, email, password_hash, role, status, avatar) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['full_name'],
            $data['username'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['role'],
            $data['status'] ?? 'Active',
            $data['avatar'] ?? 'default_avatar.png'
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET full_name = ?, username = ?, email = ?, role = ?, status = ? WHERE id = ?";
        $params = [
            $data['full_name'],
            $data['username'],
            $data['email'],
            $data['role'],
            $data['status'],
            $id
        ];

        if (!empty($data['password'])) {
            $sql = str_replace("WHERE id", ", password_hash = ? WHERE id", $sql);
            array_splice($params, 5, 0, password_hash($data['password'], PASSWORD_DEFAULT));
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateProfile($id, $data)
    {
        $sql = "UPDATE {$this->table} SET full_name = ?, email = ? WHERE id = ?";
        $params = [$data['full_name'], $data['email'], $id];

        if (!empty($data['password'])) {
            $sql = str_replace("WHERE id", ", password_hash = ? WHERE id", $sql);
            array_splice($params, 2, 0, password_hash($data['password'], PASSWORD_DEFAULT));
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$userId]);
    }
}
