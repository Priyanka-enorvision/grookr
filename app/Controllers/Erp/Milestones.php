<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;


use App\Models\SystemModel;
use App\Models\UsersModel;


class Milestones extends BaseController
{
    public function __construct()
    {
        helper('Language');
    }



    public function index()
    {
        $session = \Config\Services::session();
        $UsersModel = new UsersModel();
        $Model = new \App\Models\MilestonesModel();
        $ProjectsModel = new \App\Models\ProjectsModel();

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $data['title'] = "Work Management / Milestones";
        $data['path_url'] = 'items';
        $data['breadcrumbs'] = 'Work Management  / Milestones';
        $data['result'] = $Model->orderBy('id', 'ASC')->findAll();
        $data['project'] =  $ProjectsModel->orderBy('project_id', 'ASC')->findAll();

        $data['subview'] = view('erp/milestones/milestones_list', $data);
        return view('erp/layout/layout_main', $data); //page load

    }

    public function save()
    {
        $UsersModel = new \App\Models\UsersModel();
        $Model = new \App\Models\MilestonesModel();
        $validation = \Config\Services::validation();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        // Check if session exists
        if (!$usession || !isset($usession['sup_user_id'])) {
            return redirect()->to('/')->with('error', 'Please login first.');
        }

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        // Check if user exists
        if (!$user_info) {
            return redirect()->to('/')->with('error', 'User not found.');
        }

        $validation->setRules([
            'name' => 'required|min_length[3]',
            'due_date' => 'required|valid_date',
            'description' => 'permit_empty',
            'milestone_order' => 'required|integer',
            'project_id' => 'required',
        ]);

        if ($this->request->getMethod()) {
            if ($validation->withRequest($this->request)->run()) {
                $milestone_name = $this->request->getPost('name');

                // Check if milestone already exists
                $existing_data = $Model->where([
                    'name' => $milestone_name,
                    'company_id' => $user_info['company_id'],
                    'project_id' => $this->request->getPost('project_id')
                ])->first();

                if ($existing_data) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Milestone name already exists for this project. Please enter a different name.');
                }

                // Prepare data for insertion
                $data = [
                    'company_id' => $user_info['company_id'],
                    'project_id' => $this->request->getPost('project_id'),
                    'name' => $milestone_name,
                    'due_date' => $this->request->getPost('due_date'),
                    'description' => $this->request->getPost('description'),
                    'status' => 1, // Default status
                    'orders' => $this->request->getPost('milestone_order'),
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                try {
                    if ($Model->insert($data)) {
                        return redirect()->back()
                            ->with('message', 'Milestone successfully added.');
                    } else {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to add milestone.');
                    }
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Database error: ' . $e->getMessage());
                }
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('errors', $validation->getErrors());
            }
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Invalid request method.');
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
        $project =  $ProjectsModel->where(['company_id' => $user_info['company_id']])->orderBy('project_id', 'ASC')->findAll();

        // Get the list of users
        $data['users'] = $UsersModel->where(['company_id' => $user_info['company_id'], 'user_type' => 'staff', 'is_active' => 1])->findAll();

        if ($result) {
            // Pass both result and users data to the view
            return view('erp/milestones/edit_milestone', ['result' => $result, 'project' => $project]);
        } else {
            return redirect()->back()->with('error', 'No data found for the given ID');
        }
    }


    public function update($milestones_id)
    {
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
