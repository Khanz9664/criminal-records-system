<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $this->requireRole('admin');

        $userModel = new User();
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? 'All';

        $users = $userModel->getAll($search, $role);

        $this->view('users/index', [
            'users' => $users,
            'search' => $search,
            'role' => $role
        ]);
    }

    public function create()
    {
        $this->requireRole('admin');
        $this->view('users/create');
    }

    public function store()
    {
        $this->requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $userModel = new User();
        // Basic validation could be added here

        if ($userModel->create($_POST)) {
            $this->redirect(BASE_URL . '/users');
        } else {
            die("Failed to create user");
        }
    }

    public function edit()
    {
        $this->requireRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect(BASE_URL . '/users');

        $userModel = new User();
        $user = $userModel->findById($id);

        if (!$user)
            die("User not found");

        $this->view('users/edit', ['user' => $user]);
    }

    public function update()
    {
        $this->requireRole('admin');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $id = $_POST['id'];
        $userModel = new User();

        if ($userModel->update($id, $_POST)) {
            $this->redirect(BASE_URL . '/users');
        } else {
            die("Failed to update user");
        }
    }

    public function delete()
    {
        $this->requireRole('admin');
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect(BASE_URL . '/users');

        $userModel = new User();
        $userModel->delete($id);
        $this->redirect(BASE_URL . '/users');
    }

    public function profile()
    {
        // Any logged in user
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);

        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);

        $this->view('users/profile', ['user' => $user]);
    }

    public function updateProfile()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $userModel = new User();
        $userModel->updateProfile($_SESSION['user_id'], $_POST);

        // Update session name if changed
        $_SESSION['user_full_name'] = $_POST['full_name'];

        $this->redirect(BASE_URL . '/profile?success=1');
    }
}
