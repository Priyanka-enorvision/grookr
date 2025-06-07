<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LeadConfigModel;
use App\Models\LeadOptions;


class Timelogs extends BaseController
{
    public function __construct()
    {
        helper('Language');
    }




    public function index()
    {
        $session = \Config\Services::session();
        $SystemModel = new SystemModel();
        $UsersModel = new UsersModel();
        $Model = new \App\Models\MilestonesModel();

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $data['title'] = "Work Management / Milestones";
        $data['path_url'] = 'items';
        $data['breadcrumbs'] = 'Work Management  / Milestones';
        $data['result'] = $Model->orderBy('id', 'ASC')->findAll();

        $data['subview'] = view('erp/milestones/milestones_list', $data);
        return view('erp/layout/layout_main', $data); //page load

    }

    public function save()
    {

        $UsersModel = new \App\Models\UsersModel();
        $Model = new \App\Models\TimelogsModel();
        $validation = \Config\Services::validation();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $validation->setRules([
            'project_id' => 'required|integer',
            'task_id' => 'required|integer',
            'member' => 'required',
            'start_date' => 'required|valid_date',
            'start_time' => 'required',
            'due_date' => 'required|valid_date',
            'due_time' => 'required',
            'note' => 'permit_empty',
        ]);

        if ($this->request->getMethod()) {

            // Calculate total hours based on start and end time
            $startDateTime = new \DateTime($this->request->getPost('start_date') . ' ' . $this->request->getPost('start_time'));
            $endDateTime = new \DateTime($this->request->getPost('due_date') . ' ' . $this->request->getPost('due_time'));
            $interval = $startDateTime->diff($endDateTime);
            $totalHours = $interval->h + ($interval->days * 24) + ($interval->i / 60); // Total hours with minutes converted to fractions
            $members = $this->request->getPost('member') ?? [];
            // Filter out empty values (like the empty option) and ensure all values are valid
            $members = array_filter($members, function($value) {
                return $value !== '' && $value !== null;
            });

            // If no members selected, you might want to handle this case
            if (empty($members)) {
                // Either set to NULL, empty string, or handle the error
                $employee_id = null;
            } else {
                $employee_id = implode(',', $members);
            }

            $data = [
                'company_id' => $user_info['company_id'],
                'project_id' => $this->request->getPost('project_id'),
                'task_id' => $this->request->getPost('task_id'),
                'employee_id' => $employee_id,
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('due_time'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('due_date'),
                'total_hours' => $totalHours,
                'timelogs_memo' => $this->request->getPost('note'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            
            if ($Model->insert($data)) {
                $session->setFlashdata('message', 'TimeLogs successfully added.');
            } else {
                $session->setFlashdata('error', 'Failed to add TimeLog.');
            }
        } else {
            $session->setFlashdata('error', implode(", ", $validation->getErrors()));
        }

        return redirect()->back()->withInput();
    }

    public function delete($id)
    {
        $Return = [
            'result' => '',
            'error' => '',
            'csrf_hash' => csrf_hash()
        ];

        $session = \Config\Services::session();
        $usession = $session->get('sup_username');
        if (!$usession) {
            $Return['error'] = 'Session expired or user not logged in.';
            return $this->response->setJSON($Return);
        }



        if (!is_numeric($id) || $id <= 0) {
            $Return['error'] = 'Invalid record ID.';
            return $this->response->setJSON($Return);
        }

        $Model = new \App\Models\TimelogsModel();
        try {
            $result = $Model->delete($id);

            if ($result) {
                $Return['result'] = 'Timelog deleted successfully.';
            } else {
                $Return['error'] = 'Failed to delete the timelog. Please try again.';
            }
        } catch (\Exception $e) {
            $Return['error'] = 'An error occurred: ' . $e->getMessage();
        }

        return $this->response->setJSON($Return);
    }





    public function edit_timelogs()
    {
        $UsersModel = new UsersModel();
        $SystemModel = new SystemModel();
        $Model = new \App\Models\TimelogsModel();
        $taskModel = new \App\Models\TasksModel();
        $session = \Config\Services::session();
        $request = \Config\Services::request();
        $usession = $session->get('sup_username');

        $segment_id = $request->getUri()->getSegment(3);
        $timelog_id = udecode($segment_id);
        $timelog_data = $Model->where('timelogs_id', $timelog_id)->first();
        $task  = $taskModel->where('project_id', $timelog_data['project_id'])->findAll();

        $xin_system = $SystemModel->where('setting_id', 1)->first();
        $usession = $session->get('sup_username');
        $data['title'] = 'Edit Timelogs ';
        $data['path_url'] = 'Timelogs';
        $data['breadcrumbs'] = 'Edit Timelogs';
        $data['timelog_data'] = $timelog_data;
        $data['task'] = $task;

        $data['subview'] = view('erp/projects/edit_timelogs', $data);
        return view('erp/layout/layout_main', $data);
    }

    public function update($timelog_id)
    {
        $session = session();
        $model = new \App\Models\TimelogsModel();

        $data = [
            'start_date' => $this->request->getPost('start_date'),
            'start_time' => $this->request->getPost('start_time'),
            'due_date' => $this->request->getPost('due_date'),
            'due_time' => $this->request->getPost('due_time'),
            'task_id' => $this->request->getPost('task_id'),
            'employee_id' => implode(',', $this->request->getPost('member')), // Members as comma-separated values
            'timelogs_memo' => $this->request->getPost('note'),
        ];
        $project_id = $this->request->getPost('project_id');

        try {
            $result = $model->update($timelog_id, $data);

            // Check if the update was successful
            if ($result) {
                $session->setFlashdata('message', 'Timelog successfully updated.');
                return redirect()->to(site_url('erp/project-detail/') . uencode($project_id));
            } else {
                $session->setFlashdata('error', 'Error updating the timelog. Please try again.');
            }
        } catch (\Exception $e) {
            // Log and display the error
            log_message('error', 'Error updating timelog: ' . $e->getMessage());
            $session->setFlashdata('error', 'An error occurred: ' . $e->getMessage());
        }

        return redirect()->back()->withInput();
    }
}
