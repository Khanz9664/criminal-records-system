<?php

namespace App\Services;

class FileUploader
{
    private $targetDir;
    private $allowedTypes;

    public function __construct($subDir = '')
    {
        // Base uploads folder
        $this->targetDir = dirname(__DIR__, 2) . '/public/uploads/';
        if (!empty($subDir)) {
            $this->targetDir .= trim($subDir, '/') . '/';
        }

        // Create if not exists
        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir, 0755, true);
        }

        // Default allowed types (Images + PDF)
        $this->allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    }

    public function setAllowedTypes($types)
    {
        $this->allowedTypes = $types;
    }

    /**
     * Uploads a file
     * @param array $file The $_FILES['input_name'] array
     * @return string|false The filename if successful, false otherwise
     */
    public function upload($file)
    {
        if (!isset($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Validate Type
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);

        if (!in_array($mime, $this->allowedTypes)) {
            // error_log("Invalid file type: " . $mime);
            return false;
        }

        // Generate Secure Filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $this->targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $filename; // Return just the filename, storing relative path logic in controller/view is better for DB
        }

        return false;
    }
}
