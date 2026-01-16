<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\CrimeCase;
use App\Models\CaseNote;
use App\Models\Evidence;
use App\Models\User;

class CaseController extends Controller
{

    public function index()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        $caseModel = new CrimeCase();

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? 'All'; // Should default to 'All' or user specific? Prompt said "All"

        $cases = $caseModel->getAll($search, $status);

        $this->view('cases/index', [
            'cases' => $cases,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create()
    {
        $this->requireRole(['admin', 'officer']);
        // Need officers list
        $userModel = new User();
        // User model currently only has findByUsername. I'll define a custom query here or update User model.
        // For speed, I'll direct SQL or update User model. 
        // Let's rely on DB instance directly for specific query to avoid bloat in User model for now
        $db = \App\Core\Database::getInstance()->getConnection();
        $officers = $db->query("SELECT id, full_name FROM users WHERE role IN ('officer', 'detective')")->fetchAll();

        $this->view('cases/create', ['officers' => $officers]);
    }

    public function store()
    {
        $this->requireRole(['admin', 'officer']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $data = [
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'type' => $_POST['type'],
            'priority' => $_POST['priority'],
            'incident_date' => $_POST['incident_date'],
            'location' => $_POST['location'],
            'assigned_officer_id' => $_POST['assigned_officer_id'],
        ];

        $caseModel = new CrimeCase();
        if ($caseModel->create($data)) {
            \App\Services\Logger::log("Opened New Case: " . $data['title']);
            $this->redirect(BASE_URL . '/cases');
        } else {
            die("Failed to create case");
        }
    }

    public function show()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect(BASE_URL . '/cases');

        $caseModel = new CrimeCase();
        $noteModel = new CaseNote();
        $evidenceModel = new Evidence();

        // FindById is basic, we need officer name too. 
        // Since view_case uses a specific query, let's execute that here or update FindById.
        // Actually getAll query has join. 
        // Let's do a specific query or use db.
        $db = \App\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT cases.*, users.username as officer_name 
                      FROM cases 
                      LEFT JOIN users ON cases.assigned_officer_id = users.id 
                      WHERE cases.id = ?");
        $stmt->execute([$id]);
        $case = $stmt->fetch();

        if (!$case)
            die("Case not found");

        $notes = $noteModel->getByCaseId($id);
        $evidence = $evidenceModel->getByCaseId($id);
        $suspects = $caseModel->getSuspects($id);

        $this->view('cases/show', [
            'case' => $case,
            'notes' => $notes,
            'evidence' => $evidence,
            'suspects' => $suspects
        ]);
    }

    public function updateStatus()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $id = $_POST['case_id'];
        $status = $_POST['status'];

        $caseModel = new CrimeCase();
        $caseModel->updateStatus($id, $status);

        // Log
        \App\Services\Logger::log("Updated Case #$id Status to: $status");

        $this->redirect(BASE_URL . '/cases/show?id=' . $id);
    }

    public function addNote()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $caseId = $_POST['case_id'];
        $note = $_POST['note'];
        $userId = $_SESSION['user_id'];

        $noteModel = new CaseNote();
        $noteModel->add($caseId, $userId, $note);

        $this->redirect(BASE_URL . '/cases/show?id=' . $caseId);
    }

    public function uploadEvidence()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $caseId = $_POST['case_id'];
        $title = $_POST['title'];

        if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new \App\Services\FileUploader('evidence');
            // Allow more types for evidence
            $uploader->setAllowedTypes(['image/jpeg', 'image/png', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

            $filename = $uploader->upload($_FILES['evidence_file']);

            if ($filename) {
                $evidenceModel = new Evidence();
                $evidenceModel->add([
                    'case_id' => $caseId,
                    'title' => $title,
                    'file_path' => $filename,
                    'file_type' => $_FILES['evidence_file']['type'],
                    'uploaded_by' => $_SESSION['user_id']
                ]);

                \App\Services\Logger::log("Uploaded Evidence for Case #$caseId: $title");
            }
        }

        $this->redirect(BASE_URL . '/cases/show?id=' . $caseId);
    }
}
