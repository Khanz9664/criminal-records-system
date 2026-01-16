<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Evidence;

class EvidenceController extends Controller
{
    public function index()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);

        $evidenceModel = new Evidence();
        $search = $_GET['search'] ?? '';

        $evidenceList = $evidenceModel->getAll($search);

        $this->view('evidence/index', [
            'evidenceList' => $evidenceList,
            'search' => $search
        ]);
    }

    public function create()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);

        // Need list of open cases to attach evidence to
        $db = \App\Core\Database::getInstance()->getConnection();
        $cases = $db->query("SELECT id, title FROM cases WHERE status != 'Closed' ORDER BY created_at DESC")->fetchAll();

        $this->view('evidence/create', ['cases' => $cases]);
    }

    public function store()
    {
        $this->requireRole(['admin', 'officer', 'detective', 'forensics']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $caseId = $_POST['case_id'];
        $title = $_POST['title'];

        if (isset($_FILES['evidence_file']) && $_FILES['evidence_file']['error'] === UPLOAD_ERR_OK) {
            $uploader = new \App\Services\FileUploader('evidence');
            $uploader->setAllowedTypes(['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain']);

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

                \App\Services\Logger::log("Uploaded Evidence from Locker for Case #$caseId");
                $this->redirect(BASE_URL . '/evidence');
            } else {
                die("File upload failed. Invalid type or server error.");
            }
        }
        $this->redirect(BASE_URL . '/evidence');
    }
}
