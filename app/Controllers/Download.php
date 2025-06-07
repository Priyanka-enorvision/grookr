<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class Download extends BaseController {
    
    public function index() {    
        $type = $this->request->getGet('type');
        $filename = $this->request->getGet('filename');
        
        if (!$type || !$filename) {
            return $this->response->setStatusCode(400)->setBody('Invalid request');
        }

        $filename = urldecode($filename);
        $filepath = FCPATH . 'uploads/' . $type . '/' . $filename;
        
        // Verify file exists and is readable
        if (!is_file($filepath) || !is_readable($filepath)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        // Use CI4's built-in download function
        return $this->response->download($filepath, null, true);
    }

    // Alternative method if you need custom handling
    public function force_download($filepath, $filename = null) {
        if (!is_file($filepath) || !is_readable($filepath)) {
            return false;
        }

        $filename = $filename ?? basename($filepath);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $mimes = new \Config\Mimes();
        $mime = $mimes->getMimeType($extension) ?? 'application/octet-stream';

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Pragma: no-cache');
        header('Content-Length: ' . filesize($filepath));

        readfile($filepath);
        exit;
    }
}