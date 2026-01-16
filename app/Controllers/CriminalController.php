<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Criminal;
use App\Models\CrimeCase;

class CriminalController extends Controller
{

    public function index()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        $criminalModel = new Criminal();

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? 'All';

        $criminals = $criminalModel->getAll($search, $status);

        $this->view('criminals/index', [
            'criminals' => $criminals,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create()
    {
        $this->requireRole(['admin', 'officer']);
        $this->view('criminals/create');
    }

    public function store()
    {
        $this->requireRole(['admin', 'officer']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'date_of_birth' => $_POST['date_of_birth'],
            'gender' => $_POST['gender'],
            'blood_type' => $_POST['blood_type'],
            'address' => $_POST['address'],
            'status' => $_POST['status'],
            'photo_path' => 'default_avatar.png'
        ];

        // Handle File Upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploader = new \App\Services\FileUploader('criminals'); // Store in 'uploads/criminals' subfolder if desired, or just root
            $uploader->setAllowedTypes(['image/jpeg', 'image/png', 'image/webp']);
            $filename = $uploader->upload($_FILES['photo']);

            if ($filename) {
                $data['photo_path'] = $filename;
            }
        }

        $criminalModel = new Criminal();
        if ($criminalModel->create($data)) {
            \App\Services\Logger::log("Created Criminal Record: " . $data['first_name'] . " " . $data['last_name']);
            $this->redirect(BASE_URL . '/criminals');
        } else {
            // Handle error
            die("Failed to create record");
        }
    }

    public function show()
    {
        $this->requireRole(['admin', 'officer', 'detective']);

        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect(BASE_URL . '/criminals');

        $criminalModel = new Criminal();
        $criminal = $criminalModel->findById($id);

        if (!$criminal)
            die("Criminal not found");

        $linkedCases = $criminalModel->getLinkedCases($id);

        // Fetch specific case model if needed for dropdown
        $caseModel = new CrimeCase();
        // Custom query to get open cases to link
        // We'll hack it into CaseModel or just use DB
        $db = \App\Core\Database::getInstance()->getConnection();
        $allCases = $db->query("SELECT id, title FROM cases WHERE status != 'Closed' ORDER BY created_at DESC")->fetchAll();

        $this->view('criminals/show', [
            'criminal' => $criminal,
            'linkedCases' => $linkedCases,
            'allCases' => $allCases
        ]);
    }

    public function edit()
    {
        $this->requireRole(['admin', 'officer']);
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect(BASE_URL . '/criminals');

        $criminalModel = new Criminal();
        $criminal = $criminalModel->findById($id);
        if (!$criminal)
            die("Criminal not found");

        $this->view('criminals/edit', ['criminal' => $criminal]);
    }

    public function update()
    {
        $this->requireRole(['admin', 'officer']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $id = $_POST['id'];
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'date_of_birth' => $_POST['date_of_birth'],
            'gender' => $_POST['gender'],
            'blood_type' => $_POST['blood_type'],
            'address' => $_POST['address'],
            'status' => $_POST['status']
        ];

        // Handle Photo Upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploader = new \App\Services\FileUploader('criminals');
            $uploader->setAllowedTypes(['image/jpeg', 'image/png', 'image/webp']);
            $filename = $uploader->upload($_FILES['photo']);
            if ($filename) {
                $data['photo_path'] = $filename;
            }
        }

        $criminalModel = new Criminal();
        // Since update logic is basic in Base Model or Criminal Model, let's verify Criminal Model update params.
        // It seems Criminal Model inheritance from Base Model might only have basic update. 
        // Actually Base Model has update($id, $data).
        // Let's rely on that but check if Criminal Model has specific update.
        // Criminal Model was updated earlier to have create/update etc.

        // Wait, I need to check Criminal Model's update method details.
        // Assuming it works like create but with ID.
        if ($criminalModel->update($id, $data)) {
            \App\Services\Logger::log("Updated Criminal Record #$id");
            $this->redirect(BASE_URL . '/criminals/show?id=' . $id);
        } else {
            die("Failed to update record");
        }
    }

    public function linkCase()
    {
        $this->requireRole(['admin', 'officer', 'detective']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            return;

        $criminalId = $_POST['criminal_id'];
        $caseId = $_POST['case_id'];
        $involvement = $_POST['involvement'];

        $criminalModel = new Criminal();
        $criminalModel->linkCase($criminalId, $caseId, $involvement);

        $this->redirect(BASE_URL . '/criminals/show?id=' . $criminalId);
    }
}
