<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\Form_model;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


class FormController extends Controller
{
    protected $formModel;

    public function __construct()
    {
        $this->formModel = new Form_model();
    }


    public function get_form()
    {
        $data['title'] = 'Form Submission';
        return view('form_view', $data);
    }


    // public function submit_form()
    // {
    //     log_message('info', 'submit_form method called');

    //     $validation = \Config\Services::validation();
    //     $validation->setRules([
    //         'name'        => 'required',
    //         'contact'     => 'required',
    //         'email'       => 'required|valid_email',
    //         'description' => 'required'
    //     ]);

    //     if (!$validation->withRequest($this->request)->run()) {
    //         $errors = $validation->getErrors();
    //         log_message('error', 'Validation errors: ' . json_encode($errors));

    //         return $this->response->setJSON(['status' => 'error', 'errors' => $errors])->setStatusCode(400);
    //     }

    //     $data = [
    //         'name'        => $this->request->getPost('name'),
    //         'contact'     => $this->request->getPost('contact'),
    //         'email'       => $this->request->getPost('email'),
    //         'description' => $this->request->getPost('description')
    //     ];

    //     if ($this->formModel->insert($data)) {
    //         log_message('info', 'Data inserted successfully');
    //         return $this->response->setJSON(['status' => 'success', 'message' => 'Data submitted successfully.'])->setStatusCode(200);
    //     } else {
    //         log_message('error', 'Failed to insert data');
    //         return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit data.'])->setStatusCode(500);
    //     }
    // }


    public function submit_form()
    {
        log_message('info', 'submit_form method called');

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'        => 'required',
            'contact'     => 'required',
            'email'       => 'required|valid_email',
            'description' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = $validation->getErrors();
            log_message('error', 'Validation errors: ' . json_encode($errors));

            return $this->response->setJSON(['status' => 'error', 'errors' => $errors])->setStatusCode(400);
        }

        $referer = $this->request->getServer('HTTP_REFERER') ?? 'Direct Access';
        // $ipAddress = $this->request->getIPAddress();

        $company  = $this->request->getPost('company'); 
        $industry = $this->request->getPost('industry');

        if (!empty($industry) && is_array($industry)) {
            $industry = json_encode($industry);
        } else {
            $industry = null;
        }

        
        $category = $this->request->getPost('category');

        if (!empty($category) && is_array($category)) {
            $category = json_encode($category);
        } else {
            $category = null;
        }
        $participate = $this->request->getPost('participate');

        if (!empty($participate) && is_array($participate)) {
            $participate = json_encode($participate);
        } else {
            $participate = null;
        }

        $data = [
            'name'        => $this->request->getPost('name'),
            'contact'     => $this->request->getPost('contact'),
            'email'       => $this->request->getPost('email'),
            'description' => $this->request->getPost('description'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'zip_code' => $this->request->getPost('zip_code'),
            'subject' => $this->request->getPost('subject'),
            'industry'    => $industry, 
            'category'    => $category, 
            'participate'    => $participate, 
            'source'    => $this->request->getPost('source'), 
            'company'     => !empty($company) ? $company : null,
            'request' => $referer,
            // 'request' => $ipAddress,
            'created_at'  => date('Y-m-d H:i:s')
        ];

        if ($this->formModel->insert($data)) {
            log_message('info', 'Data inserted successfully');
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data submitted successfully.'])->setStatusCode(200);
        } else {
            log_message('error', 'Failed to insert data');
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit data.'])->setStatusCode(500);
        }
    }
}
?>