<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\OpportunityModel;

class Opportunity extends BaseController
{
    public function __construct()
    {
        // Load language
        helper('Language');
        $this->lang = \Config\Services::language();
    }

    public function index()
    {
        $session = \Config\Services::session();
        $UsersModel = new UsersModel();
        $opportunityModel = new OpportunityModel();

        $usession = $session->get('sup_username');

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $data['title'] = "Client Management / Opportunity";
        $data['path_url'] = 'opportunity';
        $data['breadcrumbs'] = 'Client Management / Opportunity';

        $data['result'] = $opportunityModel
        ->where(['company_id' => $user_info['company_id']])
        // ->orWhere('company_id IS NULL')
        ->orderBy('id', 'ASC')->findAll();
        
        $data['users'] = $UsersModel->where(['company_id' => $user_info['company_id'], 'user_type' => 'staff', 'is_active' => 1])->findAll();

        $data['subview'] = view('erp/opportunity/list', $data);

        return view('erp/layout/layout_main', $data);
    }
    public function save()
    {
        $UsersModel = new UsersModel();
        $OpportunityModel = new OpportunityModel();
        $validation = \Config\Services::validation();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $validation->setRules([
            'opportunity_name' => 'required|min_length[3]',
            'user_id' => 'required|numeric',
            'opportunity_stage' => 'required',
            'expected_closing_date' => 'required|valid_date[Y-m-d]',
            'value' => 'required|decimal',
            'probability' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'comments' => 'permit_empty|string',
        ]);

        if ($this->request->getMethod() === 'POST' && $validation->withRequest($this->request)->run()) {
            $opportunity_name = $this->request->getPost('opportunity_name');

            $existing_data = $OpportunityModel->where(['opportunity_name' => $opportunity_name, 'company_id' => $user_info['company_id']])->first();

            if ($existing_data) {
                $session->setFlashdata('error', lang('Language.xin_error_exists_opportunityName'));
                return redirect()->back()->withInput();
            }

            $data = [
                'company_id' => $user_info['company_id'],
                'user_id' => $this->request->getPost('user_id'),
                'opportunity_name' => $this->request->getPost('opportunity_name'),
                'opportunity_stage' => $this->request->getPost('opportunity_stage'),
                'expected_closing_date' => $this->request->getPost('expected_closing_date'),
                'value' => $this->request->getPost('value'),
                'probability' => $this->request->getPost('probability'),
                'comments' => $this->request->getPost('comments'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            log_message('debug', 'Data to be inserted: ' . json_encode($data));

            try {
                if ($OpportunityModel->insert($data)) {
                    $session->setFlashdata('message', lang('Language.xin_opportunity_add'));
                    log_message('debug', 'Opportunity data inserted successfully.');
                } else {
                    log_message('error', 'Failed to insert opportunity data: ' . json_encode($OpportunityModel->errors()));
                    $session->setFlashdata('error', 'Failed to insert data.');
                }
            } catch (\Exception $e) {
                log_message('error', 'Database Error: ' . $e->getMessage());
                $session->setFlashdata('error', 'An error occurred while saving the opportunity: ' . $e->getMessage());
            }
        } else {
            $validationErrors = $validation->getErrors();
            if (!empty($validationErrors)) {
                $session->setFlashdata('error', implode(", ", $validationErrors));
            } else {
                $session->setFlashdata('error', 'Form submission failed. Please check your input and try again.');
            }
        }

        return redirect()->back()->withInput();
    }


    public function delete($enc_id)
    {
        $id = $enc_id;
        $session = \Config\Services::session();
        $request = \Config\Services::request();

        $Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

        $opportunityModel = new OpportunityModel();

        try {
            $opportunity = $opportunityModel->find($id);
            if (!$opportunity) {
                $session->setFlashdata('error', lang('Membership.xin_error_no_record_found'));
                return redirect()->back();
            }
            $result = $opportunityModel->delete($id);

            if ($result) {
                $session->setFlashdata('message', lang('Language.xin_lead_delete'));
            } else {
                $session->setFlashdata('error', lang('Membership.xin_error_msg'));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error in deleting record: ' . $e->getMessage());
            $session->setFlashdata('error', 'An error occurred: ' . $e->getMessage());
        }
        return redirect()->back();
    }



    public function updateStatus($enc_id, $status)
    {
        // Initialize services
        $session = \Config\Services::session();
        $opportunityModel = new OpportunityModel();
        
        try {
            // Validate status input
            $validStatuses = ['true', 'false', '1', '0'];
            if (!in_array($status, $validStatuses)) {
                throw new \InvalidArgumentException('Invalid status value');
            }

            // Convert status to boolean/int for database
            $dbStatus = ($status === 'true' || $status === '1') ? 1 : 0;
            
            // Update the record
            $data = ['status' => $dbStatus];
            $result = $opportunityModel->update($enc_id, $data);
            
            if ($result) {
                $session->setFlashdata('message', lang('Language.xin_success_update_status'));
            } else {
                // Check if the record exists
                $exists = $opportunityModel->find($enc_id);
                if (!$exists) {
                    $session->setFlashdata('error', lang('Language.xin_error_record_not_found'));
                } else {
                    $session->setFlashdata('error', lang('Main.xin_error_msg'));
                    log_message('error', 'Failed to update opportunity status. ID: ' . $enc_id);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating opportunity status: ' . $e->getMessage());
            $session->setFlashdata('error', lang('Main.xin_error_msg'));
        }
        
        return redirect()->back();
    }

    public function getData($id)
    {
        $session = \Config\Services::session();
        $UsersModel = new UsersModel();
        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $opportunityModel = new OpportunityModel();
        $result = $opportunityModel->where('id', $id)->first();

        // Get the list of users
        $data['users'] = $UsersModel->where(['company_id' => $user_info['company_id'], 'user_type' => 'staff', 'is_active' => 1])->findAll();

        if ($result) {
            // Pass both result and users data to the view
            return view('erp/opportunity/edit', ['result' => $result, 'users' => $data['users']]);
        } else {
            return redirect()->back()->with('error', 'No data found for the given ID');
        }
    }


    public function update($enc_id)
    {
        $session = session();
        $opportunityModel = new OpportunityModel();
        $validation = \Config\Services::validation();

        // Set validation rules for all fields
        $validation->setRules([
            'opportunity_name' => 'required|min_length[3]',
            'user_id' => 'required|numeric',
            'opportunity_stage' => 'required',
            'expected_closing_date' => 'required|valid_date',
            'value' => 'required|numeric',
            'probability' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'comments' => 'permit_empty'
        ]);

        // Log incoming data for debugging
        log_message('debug', 'Update data: ' . print_r($this->request->getPost(), true));
        log_message('debug', 'Opportunity ID: ' . $enc_id);

        if (!$validation->withRequest($this->request)->run()) {
            $errors = implode(", ", $validation->getErrors());
            log_message('error', 'Validation failed: ' . $errors);
            $session->setFlashdata('error', $errors);
            return redirect()->back()->withInput();
        }

        try {
            // Verify opportunity exists
            $opportunity = $opportunityModel->find($enc_id);
            if (!$opportunity) {
                throw new \RuntimeException('Opportunity not found');
            }

            $data = [
                'user_id' => $this->request->getPost('user_id'),
                'opportunity_name' => $this->request->getPost('opportunity_name'),
                'opportunity_stage' => $this->request->getPost('opportunity_stage'),
                'expected_closing_date' => $this->request->getPost('expected_closing_date'),
                'value' => $this->request->getPost('value'),
                'probability' => $this->request->getPost('probability'),
                'comments' => $this->request->getPost('comments'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            log_message('debug', 'Data to update: ' . print_r($data, true));

            if ($opportunityModel->update($enc_id, $data)) {
                $session->setFlashdata('message', lang('Language.xin_update'));
            } else {
                $error = $opportunityModel->errors() ? implode(', ', $opportunityModel->errors()) : 'Unknown database error';
                log_message('error', 'Update failed: ' . $error);
                $session->setFlashdata('error', $error);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating opportunity: ' . $e->getMessage());
            $session->setFlashdata('error', 'An error occurred: ' . $e->getMessage());
        }

        return redirect()->to(base_url('erp/opportunity-list'));
    }


    public function leads_list($opp_id)
    {
        $id = base64_decode($opp_id);
        $session = \Config\Services::session();
        $SystemModel = new SystemModel();
        $UsersModel = new UsersModel();
        $opportunityModel = new OpportunityModel();

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $data['title'] = "";
        $data['path_url'] = 'opportunity Under Leads';
        $data['breadcrumbs'] = 'opportunity - Leads';

        $data['result'] = $opportunityModel->where(['company_id' => $user_info['company_id']])->orderBy('id', 'ASC')->findAll();
        $data['users'] = $UsersModel->where(['company_id' => $user_info['company_id'], 'user_type' => 'staff', 'is_active' => 1])->findAll();

        $data['subview'] = view('erp/opportunity/view_lead', $data);

        return view('erp/layout/layout_main', $data);
    }
}
