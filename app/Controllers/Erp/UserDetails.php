<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use App\Models\UsersModel;


class UserDetails extends BaseController
{
    public function __construct()
    {
        helper('Language');
    }


    public function save_summary()
    {
        $UsersModel = new UsersModel();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        if (!$usession) {
            return redirect()->to('/auth/login')->with('error', 'Session expired. Please login.');
        }

        // Validate input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'about_job' => 'permit_empty|max_length[1000]',
            'professional_overview' => 'permit_empty|max_length[1000]',
            'achievements' => 'permit_empty|max_length[1000]',
            'strengths' => 'permit_empty|max_length[1000]',
            'current_projects' => 'permit_empty|max_length[1000]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if (!$user_info) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Prepare the data to be saved
        $data = [
            'company_id' => $user_info['company_id'],
            'user_id' => $user_info['user_id'],
            'about_job' => $this->request->getPost('about_job'),
            'pro_overview' => $this->request->getPost('professional_overview'),
            'achievements' => $this->request->getPost('achievements'),
            'strengths' => $this->request->getPost('strengths'),
            'current_projects' => $this->request->getPost('current_projects'),
        ];

        $db = \Config\Database::connect();
        $builder = $db->table('ci_user_summary');

        try {
            $existing_record = $builder->where('user_id', $user_info['user_id'])->countAllResults();

            if ($existing_record) {
                $result = $builder->where('user_id', $user_info['user_id'])->update($data);
            } else {
                $result = $builder->insert($data);
            }

            if ($result) {
                return redirect('erp/my-profile')->with('success', 'Summary Details successfully saved.');
            } else {
                return redirect()->back()->with('error', 'Failed to save details. No changes made.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
        }
    }



    public function delete($id)
    {
        $Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

        $session = \Config\Services::session();
        $usession = $session->get('sup_username');
        if (!$usession) {
            $Return['error'] = 'Session expired or user not logged in.';
            return $this->response->setJSON($Return);
        }
        $project_id = $this->request->getPost('project_id');
        $uri = service('uri');

        $firstSegment = $uri->getSegment(1);
        $secondSegment = $uri->getSegment(2);
        $thirdSegment = $uri->getSegment(3);

        $Model = new \App\Models\MilestonesModel();
        $result = $Model->where('id', $id)->delete();

        if ($result) {
            $Return['result'] = lang('Success.ci_project_deleted_msg');

            if ($firstSegment == 'erp' && $secondSegment == 'milestones-list') {
                $Return['redirect_url'] = base_url('erp/milestones-list');
            } elseif ($firstSegment == 'erp' && $secondSegment == 'project-detail' && uencode($project_id)) {
                $Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
            }
        } else {
            $Return['error'] = lang('Main.xin_error_msg');  // Error message if deletion fails
        }

        return $this->response->setJSON($Return);  // Send the response as JSON
    }


    public function getData($id)
    {
        $session = \Config\Services::session();
        $UsersModel = new UsersModel();
        $ProjectsModel = new \App\Models\ProjectsModel();
        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $Model = new \App\Models\MilestonesModel();
        $result = $Model->where('id', $id)->first();
        $project =  $ProjectsModel->orderBy('project_id', 'ASC')->findAll();

        // Get the list of users
        $data['users'] = $UsersModel->where(['company_id' => $user_info['company_id'], 'user_type' => 'staff', 'is_active' => 1])->findAll();

        if ($result) {
            // Pass both result and users data to the view
            return view('erp/milestones/edit_milestone', ['result' => $result, 'project' => $project]);
        } else {
            return redirect()->back()->with('error', 'No data found for the given ID');
        }
    }


    public function update($enc_id)
    {
        $milestones_id = base64_decode($enc_id);
        $session = session();
        $model = new \App\Models\MilestonesModel();
        $validation = \Config\Services::validation();

        // Define validation rules
        $validation->setRules([
            'name' => 'required|min_length[3]',
            'due_date' => 'required|valid_date',
        ]);

        // If validation passes
        if ($validation->withRequest($this->request)->run()) {
            $data = [
                'project_id' => $this->request->getPost('project_id'),
                'name' => $this->request->getPost('name'),
                'due_date' => $this->request->getPost('due_date'),
                'description' => $this->request->getPost('description'),
                'orders' => $this->request->getPost('milestone_order'),
            ];

            $current_url = $this->request->getServer('HTTP_REFERER') ?? base_url('erp/milestones-list');

            try {
                $result = $model->update($milestones_id, $data);

                if ($result) {
                    $session->setFlashdata('message', 'Milestone successfully updated.');
                    return redirect()->to($current_url);
                } else {
                    $session->setFlashdata('error', lang('Main.xin_error_msg'));
                }
            } catch (\Exception $e) {
                log_message('error', 'Error updating milestone: ' . $e->getMessage());
                $session->setFlashdata('error', 'An error occurred: ' . $e->getMessage());
            }
        } else {
            $session->setFlashdata('error', implode(", ", $validation->getErrors()));
        }

        return redirect()->back()->withInput();
    }
}
