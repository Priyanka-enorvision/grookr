<?php

namespace App\Controllers\Erp;

use CodeIgniter\I18n\Time;
use App\Controllers\BaseController;


use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\TasksModel;
use App\Models\ProjectsModel;
use App\Models\ProjectbugsModel;
use App\Models\ProjectnotesModel;
use App\Models\ProjectfilesModel;
use App\Models\ProjecttimelogsModel;
use App\Models\ProjectdiscussionModel;
use App\Models\InvoicesModel;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Projects extends BaseController
{

	public function projects_dashboard()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_dashboard', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function show_projects()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {

			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {

			if (!in_array('project1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = lang('Dashboard.left_projects') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'projects';
		$data['breadcrumbs'] = lang('Dashboard.left_projects');
		$data['subview'] = view('erp/projects/projects_list', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function create_project()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('project2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$data['title'] = 'Create Project ';
		$data['path_url'] = 'Create Project';
		$data['breadcrumbs'] = 'Create Project';

		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['subview'] = view('erp/projects/add_project', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function projects_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$ProjectsModel = new ProjectsModel();
		$UsersModel = new UsersModel();

		$xin_system = erp_company_settings();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$status = $this->request->getGet('status');
		$assigned_to = $this->request->getGet('assigned_to');
		$expert_to = $this->request->getGet('expert_to');

		if ($status !== null || $assigned_to !== null || $expert_to !== null) {
			$session_data = [
				'project_status' => $status,
				'project_user' => $assigned_to,
				'project_expert' => $expert_to,
			];
			$session->set('project_data', $session_data);
		} else {
			$get_session_data = $session->get('project_data');
			if ($get_session_data) {
				$status = $get_session_data['project_status'];
				$assigned_to = $get_session_data['project_user'];
				$expert_to = $get_session_data['project_expert'];
			}
		}

		$draw = intval($this->request->getGet('draw'));
		$start = intval($this->request->getGet('start'));
		$length = intval($this->request->getGet('length'));

		$builder = $ProjectsModel->where('company_id', $user_info['company_id']);

		if ($status !== null && $status !== '') {
			$builder->where('status', $status);
		}

		if (!empty($expert_to)) {
			$builder->like('expert_to', $expert_to, 'both');
		}

		if (!empty($assigned_to)) {
			$builder->like('assigned_to', $assigned_to, 'both');
		}

		if ($user_info['user_type'] == 'staff') {
			$user_id = $user_info['user_id'];

			$curl = curl_init();
			$url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id";
			curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_URL => $url,
				CURLOPT_HTTPGET => true,
				CURLOPT_TIMEOUT => 10,
			]);

			$response = curl_exec($curl);

			if ($response === false) {
				$error_msg = curl_error($curl);
				curl_close($curl);
				die("cURL Error: " . $error_msg);
			}
			$expert_user_detail = json_decode($response, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				curl_close($curl);
				die("JSON Decoding Error: " . json_last_error_msg());
			}

			$expert_id = isset($expert_user_detail['detail']['id']) ? $expert_user_detail['detail']['id'] : null;
			curl_close($curl);

			$builder->groupStart()
				->where('added_by', $usession['sup_user_id'])
				->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
				->groupEnd();

			if ($expert_id !== null) {
				$builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
			}
		}

		$recordsTotal = $builder->countAllResults(false);
		$builder->orderBy('project_id', 'ASC');
		$get_data = $builder->findAll($length, $start);
		$recordsFiltered = count($get_data);

		$data = array();

		foreach ($get_data as $r) {
			// Action Icons
			$viewIcon = '<span data-toggle="tooltip" title="View Project">
                        <a href="' . site_url('') . '/' . uencode($r['project_id']) . '">
                            <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light">
                                <i class="feather icon-eye"></i>
                            </button>
                        </a>
                    </span>';

			$copyIcon = '<span data-toggle="tooltip" title="Copy Project">
					<button type="button" class="btn icon-btn btn-sm btn-light-secondary waves-effect waves-light copy-project" 
							data-record-id="' . uencode($r['project_id']) . '" 
							data-project-id="' . uencode($r['project_id']) . '">
						<i class="feather icon-copy"></i>
					</button>
				</span>';


			$editIcon = '<span data-toggle="tooltip" title="Edit Project">
                        <a href="' . site_url('erp/project-detail') . '/' . uencode($r['project_id']) . '">
                            <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                <i class="feather icon-edit"></i>
                            </button>
                        </a>
                    </span>';

			if (in_array('project4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['project_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $viewIcon . $copyIcon . $editIcon . $delete;

			$assigned_to = explode(',', $r['assigned_to']);
			$multi_users = multi_user_profile_photo($assigned_to);

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			$progress_class = $r['project_progress'] <= 20 ? 'bg-danger' : ($r['project_progress'] <= 50 ? 'bg-warning' : ($r['project_progress'] <= 75 ? 'bg-info' : 'bg-success'));

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['project_progress'] . '%;" aria-valuenow="' . $r['project_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['project_progress'] . '%</div></div>';

			$status_labels = [
				0 => '<span class="label label-warning">' . lang('Projects.xin_not_started') . '</span>',
				1 => '<span class="label label-primary">' . lang('Projects.xin_in_progress') . '</span>',
				2 => '<span class="label label-success">' . lang('Projects.xin_completed') . '</span>',
				3 => '<span class="label label-danger">' . lang('Projects.xin_project_cancelled') . '</span>',
			];

			$priority_labels = [
				1 => '<span class="badge badge-light-danger">' . lang('Projects.xin_highest') . '</span>',
				2 => '<span class="badge badge-light-danger">' . lang('Projects.xin_high') . '</span>',
				3 => '<span class="badge badge-light-primary">' . lang('Projects.xin_normal') . '</span>',
				4 => '<span class="badge badge-light-success">' . lang('Projects.xin_low') . '</span>',
			];

			$status_label = $status_labels[$r['status']] ?? '<span class="label label-danger">' . lang('Projects.xin_project_hold') . '</span>';
			$priority = $priority_labels[$r['priority']] ?? '<span class="badge badge-light-success">' . lang('Projects.xin_low') . '</span>';

			$project_summary = '<a href="' . site_url('erp/project-detail/' . uencode($r['project_id'])) . '">' . $r['title'] . '</a>';
			$created_by = $UsersModel->where('user_id', $r['added_by'])->first();
			$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];

			$project_revenue = $r['revenue'] ?? 0;
			$project_revenue = number_to_currency($project_revenue, $xin_system['default_currency'], null, 2);

			$client_info = $UsersModel->where('user_id', $r['client_id'])->where('user_type', 'customer')->first();
			$iclient = $client_info['first_name'] . ' ' . $client_info['last_name'];

			$data[] = array(
				$project_summary,
				$iclient,
				$start_date,
				$end_date,
				$multi_users,
				$priority,
				$progress_bar,
				$u_name,
				$project_revenue,
				$combhr
			);
		}

		$output = array(
			"draw" => $draw,
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => $data
		);

		return $this->response->setJSON($output);
	}

	public function projects_grid()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('project1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_projects') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'projects_grid';
		$data['breadcrumbs'] = lang('Dashboard.left_projects');

		$data['subview'] = view('erp/projects/projects_grid', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function projects_client()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] !== 'customer') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_projects') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'projects_client';
		$data['breadcrumbs'] = lang(line: 'Dashboard.left_projects');

		$data['subview'] = view('erp/projects/clients_projects_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function project_details()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ProjectsModel = new ProjectsModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$segment_id = $request->getUri()->getSegment(3);
		$ifield_id = udecode($segment_id);
		$project_val = $ProjectsModel->where('project_id', $ifield_id)->first();
		if (!$project_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] == 'staff') {
			$project_data = $ProjectsModel->where('company_id', $user_info['company_id'])->where('project_id', $ifield_id)->first();
		} else {
			$project_data = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $ifield_id)->first();
		}
		$data['progress'] = $project_data['project_progress'];
		$data['project_data'] = $project_val;
		$data['title'] = lang('Projects.xin_project_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'project_details';
		$data['breadcrumbs'] = lang('Projects.xin_project_details');

		$data['subview'] = view('erp/projects/project_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function client_project_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ProjectsModel = new ProjectsModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'customer') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$segment_id = $request->getUri()->getSegment(3);
		$ifield_id = udecode($segment_id);
		$isegment_val = $ProjectsModel->where('project_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		//$data['progress'] = $project_data['project_progress'];
		$data['title'] = lang('Projects.xin_project_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'project_details';
		$data['breadcrumbs'] = lang('Projects.xin_project_details');

		$data['subview'] = view('erp/projects/client_project_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function projects_calendar()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('projects_calendar', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'projects';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_calendar');

		$data['subview'] = view('erp/projects/calendar_projects', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function project_timelogs()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_timelogs', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function projects_scrum_board()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('projects_sboard', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_projects_scrm_board') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'projects_scrum_board';
		$data['breadcrumbs'] = lang('Dashboard.xin_projects_scrm_board');

		$data['subview'] = view('erp/projects/projects_scrum_board', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function invoices()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_invoices', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function payments_history()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_payments_history', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function invoice_taxes()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_invoice_taxes', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function quotes()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/projects_quotes', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// record list
	public function timelogs_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$ProjecttimelogsModel = new ProjecttimelogsModel();
		$segment_id = $this->request->getVar('project_val', FILTER_SANITIZE_STRING);
		$ifield_id = udecode($segment_id);
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $ProjecttimelogsModel->where('company_id', $user_info['company_id'])->where('project_id', $ifield_id)->orderBy('timelogs_id', 'ASC')->findAll();
		} else {
			$get_data = $ProjecttimelogsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $ifield_id)->orderBy('timelogs_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('hr_event3', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_id="' . uencode($r['timelogs_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('hr_event4', staff_role_resource()) || $user_info['user_type'] == 'company') { //delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete_timelog" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['timelogs_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			//assigned user
			$iuser = $UsersModel->where('user_id', $r['employee_id'])->first();
			$employee_name = $iuser['first_name'] . ' ' . $iuser['last_name'];

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);
			$total_hours = $r['total_hours'];
			$combhr = $edit . $delete;
			if (in_array('hr_event3', staff_role_resource()) || in_array('hr_event4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$iemployee_name = '
				' . $employee_name . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			} else {
				$iemployee_name = $employee_name;
			}
			$data[] = array(
				$iemployee_name,
				$start_date,
				$end_date,
				$total_hours
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// record list
	public function project_tasks_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$request = \Config\Services::request();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TasksModel = new TasksModel();
		$segment_id = $this->request->getVar('project_val', FILTER_SANITIZE_STRING);
		$ifield_id = udecode($segment_id);

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $TasksModel->where('company_id', $user_info['company_id'])->where('project_id', $ifield_id)->orderBy('task_id', 'ASC')->findAll();
		} else if ($user_info['user_type'] == 'customer') {
			$get_data = $TasksModel->where('company_id', $user_info['company_id'])->where('project_id', $ifield_id)->orderBy('task_id', 'ASC')->findAll();
		} else {
			$get_data = $TasksModel->where('company_id', $usession['sup_user_id'])->where('project_id', $ifield_id)->orderBy('task_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('project4', staff_role_resource()) || $user_info['user_type'] == 'company') { //delete
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['task_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}
			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a target="_blank" href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			//assigned user
			if ($r['assigned_to'] == '') {
				$ol = lang('xin_not_assigned');
			} else {
				$ol = '';
				foreach (explode(',', $r['assigned_to']) as $emp_id) {
					$assigned_to = $UsersModel->where('user_id', $emp_id)->where('user_type', 'staff')->first();
					if ($assigned_to) {

						$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];

						if ($assigned_to['profile_photo'] != '' && $assigned_to['profile_photo'] != 'no file') {
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . base_url() . '/public/uploads/users/thumb/' . $assigned_to['profile_photo'] . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						} else {
							if ($assigned_to['gender'] == 'Male') {
								$de_file = base_url() . '/public/uploads/profile/default_male.jpg';
							} else {
								$de_file = base_url() . '/public/uploads/profile/default_female.jpg';
							}
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . $de_file . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						}
					} ////
					else {
						$ol .= '';
					}
				}
				$ol .= '';
			}

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			// task progress
			if ($r['task_progress'] <= 20) {
				$progress_class = 'bg-danger';
			} else if ($r['task_progress'] > 20 && $r['task_progress'] <= 50) {
				$progress_class = 'bg-warning';
			} else if ($r['task_progress'] > 50 && $r['task_progress'] <= 75) {
				$progress_class = 'bg-info';
			} else {
				$progress_class = 'bg-success';
			}

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['task_progress'] . '%;" aria-valuenow="' . $r['task_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['task_progress'] . '%</div></div>';
			// task status			
			if ($r['task_status'] == 0) {
				$status = '<span class="badge badge-light-warning">' . lang('Projects.xin_not_started') . '</span>';
			} else if ($r['task_status'] == 1) {
				$status = '<span class="badge badge-light-primary">' . lang('Projects.xin_in_progress') . '</span>';
			} else if ($r['task_status'] == 2) {
				$status = '<span class="badge badge-light-success">' . lang('Projects.xin_completed') . '</span>';
			} else if ($r['task_status'] == 3) {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_cancelled') . '</span>';
			} else {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_hold') . '</span>';
			}
			$created_by = $UsersModel->where('user_id', $r['created_by'])->first();
			$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];
			$ttask_date = lang('xin_start_date') . ': ' . $start_date . '<br>' . lang('xin_end_date') . ': ' . $end_date;
			$combhr = $view . $delete;
			$overall_progress = $progress_bar . $status;
			if (in_array('erp9', staff_role_resource()) || in_array('erp10', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$itask_name = '
				' . $r['task_name'] . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			} else {
				$itask_name = $r['task_name'];
			}
			$data[] = array(
				$itask_name,
				$ol,
				$start_date,
				$end_date,
				$overall_progress
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// record list
	public function client_projects_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $ProjectsModel->where('company_id', $user_info['company_id'])->where('client_id', $usession['sup_user_id'])->orderBy('project_id', 'ASC')->findAll();
		$data = array();

		foreach ($get_data as $r) {

			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['project_id']) . '"><i class="feather icon-trash-2"></i></button></span>';

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/project-details') . '/' . uencode($r['project_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';

			//assigned user
			if ($r['assigned_to'] == '') {
				$ol = lang('xin_not_assigned');
			} else {
				$ol = '';
				foreach (explode(',', $r['assigned_to']) as $emp_id) {
					$assigned_to = $UsersModel->where('user_id', $emp_id)->where('user_type', 'staff')->first();
					if ($assigned_to) {

						$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];

						if ($assigned_to['profile_photo'] != '' && $assigned_to['profile_photo'] != 'no file') {
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . base_url() . '/public/uploads/users/thumb/' . $assigned_to['profile_photo'] . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						} else {
							if ($assigned_to['gender'] == 'Male') {
								$de_file = base_url() . 'uploads/profile/default_male.jpg';
							} else {
								$de_file = base_url() . 'uploads/profile/default_female.jpg';
							}
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . $de_file . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						}
					} ////
					else {
						$ol .= '';
					}
				}
				$ol .= '';
			}

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			// project progress
			if ($r['project_progress'] <= 20) {
				$progress_class = 'bg-danger';
			} else if ($r['project_progress'] > 20 && $r['project_progress'] <= 50) {
				$progress_class = 'bg-warning';
			} else if ($r['project_progress'] > 50 && $r['project_progress'] <= 75) {
				$progress_class = 'bg-info';
			} else {
				$progress_class = 'bg-success';
			}

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['project_progress'] . '%;" aria-valuenow="' . $r['project_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['project_progress'] . '%</div></div>';

			// project status			
			if ($r['status'] == 0) {
				$status = '<span class="label bg-warning">' . lang('xin_not_started') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="label bg-primary">' . lang('xin_in_progress') . '</span>';
			} else if ($r['status'] == 2) {
				$status = '<span class="label bg-success">' . lang('xin_completed') . '</span>';
			} else if ($r['status'] == 3) {
				$status = '<span class="label bg-danger">' . lang('xin_project_cancelled') . '</span>';
			} else {
				$status = '<span class="label bg-danger">' . lang('xin_project_hold') . '</span>';
			}
			// priority
			if ($r['priority'] == 1) {
				$priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_highest') . '</span>';
			} else if ($r['priority'] == 2) {
				$priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_high') . '</span>';
			} else if ($r['priority'] == 3) {
				$priority = '<span class="badge badge-light-primary">' . lang('Projects.xin_normal') . '</span>';
			} else {
				$priority = '<span class="badge badge-light-success">' . lang('Projects.xin_low') . '</span>';
			}

			$created_by = $UsersModel->where('user_id', $r['added_by'])->first();
			$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];
			$combhr = $view . $delete;

			$ititle = '
			' . $r['title'] . '
			<div class="overlay-edit">
				' . $combhr . '
			</div>';
			$data[] = array(
				$ititle,
				$start_date,
				$end_date,
				$ol,
				$priority,
				$progress_bar
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// record list
	public function client_profile_projects_list($client_id)
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$request = \Config\Services::request();
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$ProjectsModel = new ProjectsModel();
		// $client_id = udecode($this->request->getVar('client_id', FILTER_SANITIZE_STRING));
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		$get_data = $ProjectsModel->where('company_id', $company_id)->where('client_id', $client_id)->orderBy('project_id', 'ASC')->findAll();
		$data = array();

		foreach ($get_data as $r) {

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/project-detail') . '/' . uencode($r['project_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';

			//assigned user
			if ($r['assigned_to'] == '') {
				$ol = lang('xin_not_assigned');
			} else {
				$ol = '';
				foreach (explode(',', $r['assigned_to']) as $emp_id) {
					$assigned_to = $UsersModel->where('user_id', $emp_id)->where('user_type', 'staff')->first();
					if ($assigned_to) {

						$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];

						if ($assigned_to['profile_photo'] != '' && $assigned_to['profile_photo'] != 'no file') {
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . base_url() . '/public/uploads/users/thumb/' . $assigned_to['profile_photo'] . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						} else {
							if ($assigned_to['gender'] == 'Male') {
								$de_file = base_url() . '/public/uploads/profile/default_male.jpg';
							} else {
								$de_file = base_url() . '/public/uploads/profile/default_female.jpg';
							}
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . $de_file . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
						}
					} ////
					else {
						$ol .= '';
					}
				}
				$ol .= '';
			}

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			// project progress
			if ($r['project_progress'] <= 20) {
				$progress_class = 'bg-danger';
			} else if ($r['project_progress'] > 20 && $r['project_progress'] <= 50) {
				$progress_class = 'bg-warning';
			} else if ($r['project_progress'] > 50 && $r['project_progress'] <= 75) {
				$progress_class = 'bg-info';
			} else {
				$progress_class = 'bg-success';
			}

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['project_progress'] . '%;" aria-valuenow="' . $r['project_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['project_progress'] . '%</div></div>';
			// task status			
			if ($r['status'] == 0) {
				$status = '<span class="label label-warning">' . lang('xin_not_started') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="label label-primary">' . lang('xin_in_progress') . '</span>';
			} else if ($r['status'] == 2) {
				$status = '<span class="label label-success">' . lang('xin_completed') . '</span>';
			} else if ($r['status'] == 3) {
				$status = '<span class="label label-danger">' . lang('xin_project_cancelled') . '</span>';
			} else {
				$status = '<span class="label label-danger">' . lang('xin_project_hold') . '</span>';
			}
			// priority
			if ($r['priority'] == 1) {
				$priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_highest') . '</span>';
			} else if ($r['priority'] == 2) {
				$priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_high') . '</span>';
			} else if ($r['priority'] == 3) {
				$priority = '<span class="badge badge-light-primary">' . lang('Projects.xin_normal') . '</span>';
			} else {
				$priority = '<span class="badge badge-light-success">' . lang('Projects.xin_low') . '</span>';
			}

			$project_summary = '<a href="' . site_url() . 'erp/project/detail/' . $r['project_id'] . '">' . $r['title'] . '</a>';

			$created_by = $UsersModel->where('user_id', $r['added_by'])->first();
			$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];
			$combhr = $view;
			if (in_array('erp9', staff_role_resource()) || in_array('erp10', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$ititle = '
				' . $project_summary . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			} else {
				$ititle = $project_summary;
			}
			$data[] = array(
				$ititle,
				$priority,
				$ol,
				$start_date,
				$end_date,
				$progress_bar
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}


	public function add_project()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if ($this->request->getPost()) {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			$rules = [
				'title' => [
					'rules' => 'required|max_length[255]',
					'errors' => [
						'required' => 'Project title is required',
						'max_length' => 'Project title cannot exceed 255 characters'
					]
				],
				'client_id' => [
					'rules' => 'required|numeric',
					'errors' => [
						'required' => 'Please select a client',
						'numeric' => 'Invalid client selection'
					]
				],
				'start_date' => [
					'rules' => 'required|valid_date',
					'errors' => [
						'required' => 'Start date is required',
						'valid_date' => 'Please enter a valid start date'
					]
				],
				'end_date' => [
					'rules' => 'required|valid_date',
					'errors' => [
						'required' => 'End date is required',
						'valid_date' => 'Please enter a valid end date'
					]
				],
				'summary' => [
					'rules' => 'required',
					'errors' => [
						'required' => 'Project summary is required',

					]
				],

			];

			if (!$this->validate($rules)) {
				// Store all validation errors in session
				$session->setFlashdata('errors', $this->validator->getErrors());
				return redirect()->back()->withInput();
			}

			$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
			$entities_id = $this->request->getPost('entities_id', FILTER_SANITIZE_STRING);
			$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
			$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
			$client_id = $this->request->getPost('client_id', FILTER_SANITIZE_STRING);
			$summary = $this->request->getPost('summary', FILTER_SANITIZE_STRING);
			$revenue = $this->request->getPost('revenue', FILTER_SANITIZE_STRING) ?: 0;
			$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
			$priority = $this->request->getPost('priority', FILTER_SANITIZE_STRING);
			$billing_type = $this->request->getPost('billing_type', FILTER_SANITIZE_STRING);
			$tags = $this->request->getPost('tags', FILTER_SANITIZE_STRING);
			$budget_hours = $this->request->getPost('budget_hours', FILTER_SANITIZE_STRING);

			$assigned_ids = $this->request->getPost('assigned_to', FILTER_SANITIZE_STRING);
			$assigned_ids_string = implode(',', $assigned_ids);

			$experts_ids = implode(',', $this->request->getPost('expert_to', FILTER_SANITIZE_STRING));
			$send_email = $this->request->getPost('send_email') === 'on' ? true : false;

			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			$company_id = ($user_info['user_type'] === 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

			$companies_ID = $this->request->getPost('company_id');
			$employe_ID = $this->request->getPost('employe_id');

			$data = [
				'company_id' => $company_id,
				'client_id' => $client_id,
				'title' => $title,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'assigned_to' => $assigned_ids_string,
				'expert_to' => $experts_ids,
				'priority' => $priority,
				'summary' => $summary,
				'revenue' => $revenue,
				'entities_id' => (int) $entities_id,
				'budget_hours' => $budget_hours,
				'description' => $description,
				'project_no' => '',
				'project_progress' => 0,
				'status' => 0,
				'project_note' => '',
				'billing_type' => $billing_type,
				'tags' => $tags,

				'companies_ID' => (int) $companies_ID,
				'employe_ID' => (int) $employe_ID,

				'send_email' => $send_email,
				'added_by' => $usession['sup_user_id'],
				'created_at' => date('Y-m-d H:i:s')
			];

			$ProjectsModel = new ProjectsModel();

			if ($ProjectsModel->insert($data)) {
				if ($send_email === true) {

					require_once APPPATH . '/ThirdParty/phpmailer/vendor/autoload.php';
					require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/PHPMailer.php';
					require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/Exception.php';
					require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/SMTP.php';

					$mail = new PHPMailer(true);
					try {
						$mail->isSMTP();
						$mail->Host = 'smtp.gmail.com';
						$mail->SMTPAuth = true;
						$mail->Username = '2001priyankagupta@gmail.com';
						$mail->Password = 'hmohvrigwvuvieqp';
						$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
						$mail->Port = 587;

						$mail->setFrom('2001priyankagupta@gmail.com', 'Grookr');

						foreach ($assigned_ids as $assign_id) {
							$user = $UsersModel->where('user_id', $assign_id)->first();
							if ($user) {
								$mail->addAddress($user['email']);
							}
						}
						$mail->isHTML(true);
						$mail->Subject = 'New Project Added';
						$mail->Body = view('erp/project_sendmail', ['title' => $title]);

						$mail->send();
						$session->setFlashdata('message', 'Project added successfully and Notification email sent to assigned users.');
					} catch (Exception $e) {
						log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);
						$session->setFlashdata('email_error', "Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
					}
				} else {
					$session->setFlashdata('message', 'Project added successfully');
				}
			} else {
				$session->setFlashdata('error', lang('Main.xin_error_msg'));
			}
		}

		return redirect()->to(base_url('erp/projects-list'));
	}

	

	// |||update record|||
	public function update_project()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'title' => [
					'rules' => 'required|max_length[255]',
					'errors' => [
						'required' => 'Project title is required',
						'max_length' => 'Project title cannot exceed 255 characters'
					]
				],
				'client_id' => [
					'rules' => 'required|numeric',
					'errors' => [
						'required' => 'Please select a client',
						'numeric' => 'Invalid client selection'
					]
				],
				'start_date' => [
					'rules' => 'required|valid_date',
					'errors' => [
						'required' => 'Start date is required',
						'valid_date' => 'Please enter a valid start date'
					]
				],
				'end_date' => [
					'rules' => 'required|valid_date',
					'errors' => [
						'required' => 'End date is required',
						'valid_date' => 'Please enter a valid end date'
					]
				],
				'summary' => [
					'rules' => 'required',
					'errors' => [
						'required' => 'Project summary is required'
					]
				],

				'status' => [
					'rules' => 'required',
					'errors' => [
						'required' => 'Please select status'
					]
				]
			];

			if (!$this->validate($rules)) {

				$session->setFlashdata('errors', $this->validator->getErrors());
				return redirect()->back()->withInput();
			} else {

				$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
				$entities_id = $this->request->getPost('entities_id');
				$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
				$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
				$client_id = $this->request->getPost('client_id', FILTER_SANITIZE_STRING);
				$summary = $this->request->getPost('summary', FILTER_SANITIZE_STRING);
				$revenue = $this->request->getPost('revenue', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$budget_hours = $this->request->getPost('budget_hours', FILTER_SANITIZE_STRING);

				$assigned_ids = implode(',', $this->request->getPost('assigned_to', FILTER_SANITIZE_STRING));
				$expert_ids = implode(',', $this->request->getPost('expert_to', FILTER_SANITIZE_STRING));
				$associated_goals = implode(',', $this->request->getPost('associated_goals', FILTER_SANITIZE_STRING));

				$companies_ID = $this->request->getPost('company_id');
				$employe_ID = $this->request->getPost('employe_id');

				$employee_ids = $assigned_ids;
				$id = udecode($this->request->getPost('token'));
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}

				$data = [
					'client_id' => $client_id,
					'title' => $title,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'assigned_to' => $employee_ids,
					'associated_goals' => $associated_goals,
					'summary' => $summary,
					'entities_id' => (int) $entities_id,
					'revenue' => $revenue,
					'budget_hours' => $budget_hours,
					'description' => $description,
					'companies_ID' => (int) $companies_ID,
					'employe_ID' => (int) $employe_ID,
					'expert_to' => $expert_ids,
					'status' => $this->request->getPost('status', FILTER_SANITIZE_STRING),
					'project_progress' => $this->request->getPost('progres_val', FILTER_SANITIZE_STRING),

				];

				$ProjectsModel = new ProjectsModel();
				$result = $ProjectsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result) {
					$session->setFlashdata('message', 'Project Updated successfully');
					return redirect()->to(site_url('erp/projects-list'));
				} else {
					$session->setFlashdata('error', lang('Main.xin_error_msg'));
					return redirect()->to(site_url('erp/projects-list'));
				}
				return $this->response->setJSON($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
			exit;
		}
	}
	// |||update record|||
	public function update_project_progress()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'progres_val' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_progress_field_error')
					]
				],
				'status' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'priority' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"progres_val" => $validation->getError('progres_val'),
					"status" => $validation->getError('status'),
					"priority" => $validation->getError('priority'),
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$progres_val = $this->request->getPost('progres_val', FILTER_SANITIZE_STRING);
				$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
				$priority = $this->request->getPost('priority', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$data = [
					'project_progress' => $progres_val,
					'status' => $status,
					'priority' => $priority
				];
				$ProjectsModel = new ProjectsModel();
				$result = $ProjectsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_project_status_updated_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	// |||add record|||
	public function add_note()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getPost('type') === 'add_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();

			// set rules
			$rules = [
				'description' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_note_field_error')
					]
				]
			];

			if (!$this->validate($rules)) {
				$ruleErrors = [
					"description" => $validation->getError('description')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				$ProjectnotesModel = new ProjectnotesModel();
				$existingNote = $ProjectnotesModel->where('company_id', $company_id)
					->where('project_id', $id)
					->first();

				if ($existingNote) {
					$data = [
						'project_note' => $description
					];
					$result = $ProjectnotesModel->update($existingNote['project_note_id'], $data);
					if ($result) {
						$Return['result'] = "Notes Updated Successfully ";
						$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($id);
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				} else {
					// Insert new note if no existing note is found
					$data = [
						'company_id' => $company_id,
						'project_id' => $id,
						'employee_id' => $usession['sup_user_id'],
						'project_note' => $description,
						'created_at' => date('d-m-Y h:i:s')
					];
					$result = $ProjectnotesModel->insert($data);
					if ($result) {
						$Return['result'] = lang('Success.ci_project_note_added_msg');
						$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($id);
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				}

				$Return['csrf_hash'] = csrf_hash();
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	public function add_client_project_note()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getPost('type') === 'add_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();

			// set rules
			$rules = [
				'description' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_note_field_error')
					]
				]
			];

			if (!$this->validate($rules)) {
				$ruleErrors = [
					"description" => $validation->getError('description')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				$ProjectnotesModel = new ProjectnotesModel();
				$existingNote = $ProjectnotesModel->where('company_id', $company_id)
					->where('project_id', $id)
					->first();

				if ($existingNote) {
					$data = [
						'project_note' => $description
					];
					$result = $ProjectnotesModel->update($existingNote['project_note_id'], $data);
					if ($result) {
						$Return['result'] = "Notes Updated Successfully ";
						$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($id);
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				} else {
					// Insert new note if no existing note is found
					$data = [
						'company_id' => $company_id,
						'project_id' => $id,
						'employee_id' => $usession['sup_user_id'],
						'project_note' => $description,
						'created_at' => date('d-m-Y h:i:s')
					];
					$result = $ProjectnotesModel->insert($data);
					if ($result) {
						$Return['result'] = lang('Success.ci_project_note_added_msg');
						$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($id);
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				}

				$Return['csrf_hash'] = csrf_hash();
				return $this->response->setJSON($Return);
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	// |||add record|||
	public function add_bug()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getMethod()) {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			// Validation rules
			$rules = [
				'bug_description' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_bug_field_error'),
					],
				],
			];

			if (!$this->validate($rules)) {
				$Return['error'] = $validation->getError('bug_description');
				return $this->response->setJSON($Return);
			}

			// Get user info
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			$company_id = $user_info['user_type'] === 'staff'
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Retrieve and sanitize input
			$description = $this->request->getPost('bug_description');
			$project_id = udecode($this->request->getPost('token'));

			$data = [
				'company_id' => $company_id,
				'project_id' => $project_id,
				'employee_id' => $usession['sup_user_id'],
				'bug_note' => $description,
				'created_at' => date('Y-m-d H:i:s'),
			];

			// Insert into database
			$ProjectbugsModel = new ProjectbugsModel();
			if ($ProjectbugsModel->insert($data)) {
				$Return['result'] = lang('Success.ci_project_bug_added_msg');
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}

			$Return['csrf_hash'] = csrf_hash();
			return $this->response->setJSON($Return);
		}

		$Return = ['error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
		return $this->response->setJSON($Return);
	}
	public function add_client_project_bug()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getMethod()) {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			// Validation rules
			$rules = [
				'bug_description' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_bug_field_error'),
					],
				],
			];

			if (!$this->validate($rules)) {
				$Return['error'] = $validation->getError('bug_description');
				return $this->response->setJSON($Return);
			}

			// Get user info
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			$company_id = $user_info['user_type'] === 'staff'
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Retrieve and sanitize input
			$description = $this->request->getPost('bug_description');
			$project_id = udecode($this->request->getPost('token'));

			$data = [
				'company_id' => $company_id,
				'project_id' => $project_id,
				'employee_id' => $usession['sup_user_id'],
				'bug_note' => $description,
				'created_at' => date('Y-m-d H:i:s'),
			];

			// Insert into database
			$ProjectbugsModel = new ProjectbugsModel();
			if ($ProjectbugsModel->insert($data)) {
				$Return['result'] = lang('Success.ci_project_bug_added_msg');
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}

			$Return['csrf_hash'] = csrf_hash();
			return $this->response->setJSON($Return);
		}

		$Return = ['error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
		return $this->response->setJSON($Return);
	}

	// |||add record|||
	public function add_timelogs()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_timelogs') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'start_time' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'end_time' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'start_date' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'end_date' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'memo' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"start_time" => $validation->getError('start_time'),
					"end_time" => $validation->getError('end_time'),
					"start_date" => $validation->getError('start_date'),
					"end_date" => $validation->getError('end_date'),
					"memo" => $validation->getError('memo')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
					$employee_id = $usession['sup_user_id'];
				} else {
					$company_id = $usession['sup_user_id'];
					$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
				}
				$start_time = $this->request->getPost('start_time', FILTER_SANITIZE_STRING);
				$end_time = $this->request->getPost('end_time', FILTER_SANITIZE_STRING);
				$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
				$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
				$memo = $this->request->getPost('memo', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				// total hours
				$start_time_opt = Time::parse($start_date . ' ' . $start_time);
				$end_time_opt = Time::parse($end_date . ' ' . $end_time);
				$diff = $start_time_opt->difference($end_time_opt);
				$getHours = $diff->getHours();
				$getMinutes = $diff->getMinutes();
				$hours = floor($getMinutes / 60);
				$min = $getMinutes - ($hours * 60);
				$total_hours = $hours . ":" . $min;
				$data = [
					'company_id' => $company_id,
					'project_id' => $id,
					'employee_id' => $employee_id,
					'start_time' => $start_time,
					'end_time' => $end_time,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'total_hours' => $total_hours,
					'timelogs_memo' => $memo,
					'created_at' => date('d-m-Y h:i:s')
				];
				$ProjecttimelogsModel = new ProjecttimelogsModel();
				$result = $ProjecttimelogsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_timelog_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	// |||update record|||
	public function update_timelog()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'start_time' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'end_time' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'start_date' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'end_date' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'memo' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"start_time" => $validation->getError('start_time'),
					"end_time" => $validation->getError('end_time'),
					"start_date" => $validation->getError('start_date'),
					"end_date" => $validation->getError('end_date'),
					"memo" => $validation->getError('memo')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
					$employee_id = $usession['sup_user_id'];
				} else {
					$company_id = $usession['sup_user_id'];
					$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
				}
				$start_time = $this->request->getPost('start_time', FILTER_SANITIZE_STRING);
				$end_time = $this->request->getPost('end_time', FILTER_SANITIZE_STRING);
				$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
				$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
				$memo = $this->request->getPost('memo', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				// total hours
				$start_time_opt = Time::parse($start_date . ' ' . $start_time);
				$end_time_opt = Time::parse($end_date . ' ' . $end_time);
				$diff = $start_time_opt->difference($end_time_opt);
				$getHours = $diff->getHours();
				$getMinutes = $diff->getMinutes();
				$hours = floor($getMinutes / 60);
				$min = $getMinutes - ($hours * 60);
				$total_hours = $hours . ":" . $min;
				$data = [
					'employee_id' => $employee_id,
					'start_time' => $start_time,
					'end_time' => $end_time,
					'start_date' => $start_date,
					'end_date' => $end_date,
					'total_hours' => $total_hours,
					'timelogs_memo' => $memo
				];
				$ProjecttimelogsModel = new ProjecttimelogsModel();
				$result = $ProjecttimelogsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_timelog_updated_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$this->output($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
	// |||add record|||
	public function add_discussion()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		$Return = [
			'result' => '',
			'error' => '',
			'csrf_hash' => csrf_hash()
		];

		$rules = [
			'description' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Success.xin_discussion_field_error')]
			],
			'subject' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Success.xin_subject_field_error')]
			]
		];

		if (!$this->validate($rules)) {
			$errors = $validation->getErrors();
			$Return['error'] = reset($errors);
			return $this->output($Return);
		}

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_id = ($user_info['user_type'] === 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

		$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
		$subject = $this->request->getPost('subject', FILTER_SANITIZE_STRING);
		$project_id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
		$discussion_id = $this->request->getPost('discussion_id');

		$data = [
			'company_id' => $company_id,
			'project_id' => $project_id,
			'employee_id' => $usession['sup_user_id'],
			'discussion_text' => $description,
			'subject' => $subject
		];

		$ProjectdiscussionModel = new ProjectdiscussionModel();

		if (!empty($discussion_id)) {
			$result = $ProjectdiscussionModel->update($discussion_id, $data);

			if ($result) {
				$Return['result'] = "Discussion Updated Successgully";
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		} else {
			// Insert new discussion
			$data['created_at'] = date('Y-m-d H:i:s'); // Add created_at only for new entries
			$result = $ProjectdiscussionModel->insert($data);

			if ($result) {
				$Return['result'] = lang('Success.ci_project_discussion_added_msg');
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		}

		$Return['csrf_hash'] = csrf_hash();
		return $this->output($Return);
	}
	public function add_project_client_discussion()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		$Return = [
			'result' => '',
			'error' => '',
			'csrf_hash' => csrf_hash()
		];

		$rules = [
			'description' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Success.xin_discussion_field_error')]
			],
			
		];

		if (!$this->validate($rules)) {
			$errors = $validation->getErrors();
			$Return['error'] = reset($errors);
			return $this->response->setJSON($Return);
		}

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_id = ($user_info['user_type'] === 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

		$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
		$project_id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
		$discussion_id = $this->request->getPost('discussion_id');

		$data = [
			'company_id' => $company_id,
			'project_id' => $project_id,
			'employee_id' => $usession['sup_user_id'],
			'discussion_text' => $description,
		];

		$ProjectdiscussionModel = new ProjectdiscussionModel();

		if (!empty($discussion_id)) {
			$result = $ProjectdiscussionModel->update($discussion_id, $data);

			if ($result) {
				$Return['result'] = "Discussion Updated Successgully";
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		} else {
			// Insert new discussion
			$data['created_at'] = date('Y-m-d H:i:s'); // Add created_at only for new entries
			$result = $ProjectdiscussionModel->insert($data);

			if ($result) {
				$Return['result'] = lang('Success.ci_project_discussion_added_msg');
				$Return['redirect_url'] = base_url('erp/project-detail/') . uencode($project_id);
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		}

		$Return['csrf_hash'] = csrf_hash();
		return $this->response->setJSON($Return);
	}


	public function add_attachment()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = csrf_hash();

		if ($this->request->getPost('file_name')) {
			// Set validation rules
			$rules = [
				'file_name' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'attachment_file' => [
					'rules' => 'uploaded[attachment_file]|mime_in[attachment_file,image/jpg,image/jpeg,image/gif,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|max_size[attachment_file,3072]',
					'errors' => [
						'uploaded' => lang('Success.xin_file_field_error'),
						'mime_in' => 'Unsupported file format or wrong size',
					],
				],
			];

			if (!$this->validate($rules)) {
				$ruleErrors = [
					"file_name" => $validation->getError('file_name'),
					"attachment_file" => $validation->getError('attachment_file')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						echo json_encode($Return);
						return;
					}
				}
			} else {
				// File Upload
				$attachment = $this->request->getFile('attachment_file');
				if ($attachment->isValid() && !$attachment->hasMoved()) {
					$file_name = $attachment->getRandomName(); // Get a random file name to avoid conflicts
					$attachment->move('uploads/project_files/', $file_name); // Move file to desired folder

					// Retrieve other form inputs
					$file_title = $this->request->getPost('file_name', FILTER_SANITIZE_STRING);
					$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

					// Fetch user info from session
					$UsersModel = new UsersModel();
					$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
					$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

					// Prepare data to store in the database
					$data = [
						'company_id' => $company_id,
						'project_id' => $id,
						'employee_id' => $usession['sup_user_id'],
						'file_title' => $file_title,
						'attachment_file' => $file_name,
						'created_at' => date('Y-m-d H:i:s') // Use current date and time
					];

					// Insert data into the database
					$ProjectfilesModel = new ProjectfilesModel();
					$result = $ProjectfilesModel->insert($data);

					if ($result) {
						$Return['result'] = lang('Success.ci_project_file_added_msg');
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				} else {
					$Return['error'] = lang('Success.xin_file_field_error');
				}
			}
		} else {
			$Return['error'] = "First Add a Title name Then upload a document file.";
		}

		echo json_encode($Return);
		return;
	}
	public function add_client_project_attachment()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = csrf_hash();

		if ($this->request->getPost('file_name')) {
			// Set validation rules
			$rules = [
				'file_name' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'attachment_file' => [
					'rules' => 'uploaded[attachment_file]|mime_in[attachment_file,image/jpg,image/jpeg,image/gif,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|max_size[attachment_file,3072]',
					'errors' => [
						'uploaded' => lang('Success.xin_file_field_error'),
						'mime_in' => 'Unsupported file format or wrong size',
					],
				],
			];

			if (!$this->validate($rules)) {
				$ruleErrors = [
					"file_name" => $validation->getError('file_name'),
					"attachment_file" => $validation->getError('attachment_file')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);	
					}
				}
			} else {
				// File Upload
				$attachment = $this->request->getFile('attachment_file');
				if ($attachment->isValid() && !$attachment->hasMoved()) {
					$file_name = $attachment->getRandomName(); // Get a random file name to avoid conflicts
					$attachment->move('uploads/project_files/', $file_name); // Move file to desired folder

					// Retrieve other form inputs
					$file_title = $this->request->getPost('file_name', FILTER_SANITIZE_STRING);
					$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

					// Fetch user info from session
					$UsersModel = new UsersModel();
					$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
					$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

					// Prepare data to store in the database
					$data = [
						'company_id' => $company_id,
						'project_id' => $id,
						'employee_id' => $usession['sup_user_id'],
						'file_title' => $file_title,
						'attachment_file' => $file_name,
						'created_at' => date('Y-m-d H:i:s') // Use current date and time
					];

					// Insert data into the database
					$ProjectfilesModel = new ProjectfilesModel();
					$result = $ProjectfilesModel->insert($data);

					if ($result) {
						$Return['result'] = lang('Success.ci_project_file_added_msg');
					} else {
						$Return['error'] = lang('Main.xin_error_msg');
					}
				} else {
					$Return['error'] = lang('Success.xin_file_field_error');
				}
			}
		} else {
			$Return['error'] = "First Add a Title name Then upload a document file.";
		}

		return $this->response->setJSON($Return);
	}
	// update record
	public function update_project_status()
	{

		if ($this->request->getVar('xfieldid')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = $this->request->getVar('xfieldid', FILTER_SANITIZE_STRING);
			$status = $this->request->getVar('xfieldst', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}
			$data = [
				'status' => $status,
			];
			$ProjectsModel = new ProjectsModel();
			$result = $ProjectsModel->update($id, $data);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_status_updated_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	public function project_status_chart()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Fetch data from the database
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$get_projects = $ProjectsModel->where('company_id', $company_id)->countAllResults();
		$not_started = $ProjectsModel->where('company_id', $company_id)->where('status', 0)->countAllResults();
		$in_progress = $ProjectsModel->where('company_id', $company_id)->where('status', 1)->countAllResults();
		$completed = $ProjectsModel->where('company_id', $company_id)->where('status', 2)->countAllResults();
		$cancelled = $ProjectsModel->where('company_id', $company_id)->where('status', 3)->countAllResults();
		$hold = $ProjectsModel->where('company_id', $company_id)->where('status', 4)->countAllResults();

		$Return = array(
			'not_started' => '',
			'in_progress' => '',
			'completed' => '',
			'cancelled' => '',
			'hold' => '',
			'not_started_lb' => '',
			'in_progress_lb' => '',
			'completed_lb' => '',
			'cancelled_lb' => '',
			'hold_lb' => ''
		);

		$total = $not_started + $in_progress + $completed + $cancelled + $hold;

		$not_started = ($not_started > 0) ? number_format(($not_started / $get_projects) * 100, 1, '.', '') : $not_started;
		$in_progress = ($in_progress > 0) ? number_format(($in_progress / $get_projects) * 100, 1, '.', '') : $in_progress;
		$completed = ($completed > 0) ? number_format(($completed / $get_projects) * 100, 1, '.', '') : $completed;
		$cancelled = ($cancelled > 0) ? number_format(($cancelled / $get_projects) * 100, 1, '.', '') : $cancelled;
		$hold = ($hold > 0) ? number_format(($hold / $get_projects) * 100, 1, '.', '') : $hold;

		$Return['not_started_lb'] = lang('Projects.xin_not_started');
		$Return['not_started'] = $not_started;
		$Return['in_progress_lb'] = lang('Projects.xin_in_progress');
		$Return['in_progress'] = $in_progress;
		$Return['completed_lb'] = lang('Projects.xin_completed');
		$Return['completed'] = $completed;
		$Return['cancelled_lb'] = lang('Projects.xin_project_cancelled');
		$Return['cancelled'] = $cancelled;
		$Return['hold_lb'] = lang('Projects.xin_project_hold');
		$Return['hold'] = $hold;
		$Return['total'] = $total;
		$Return['total_label'] = lang('Main.xin_total');

		return $this->response->setJSON($Return);
	}

	public function staff_project_status_chart()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$ConstantsModel = new ConstantsModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_projects = $ProjectsModel->where('company_id', $user_info['company_id'])->countAllResults();
			$not_started = $ProjectsModel->where('company_id', $user_info['company_id'])->where('status', 0)->countAllResults();
			$in_progress = $ProjectsModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
			$completed = $ProjectsModel->where('company_id', $user_info['company_id'])->where('status', 2)->countAllResults();
			$cancelled = $ProjectsModel->where('company_id', $user_info['company_id'])->where('status', 3)->countAllResults();
			$hold = $ProjectsModel->where('company_id', $user_info['company_id'])->where('status', 4)->countAllResults();
		} else {
			$get_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
			$not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
			$in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
			$completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
			$cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
			$hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
		}
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('not_started' => '', 'in_progress' => '', 'completed' => '', 'cancelled' => '', 'hold' => '', 'not_started_lb' => '', 'in_progress_lb' => '', 'completed_lb' => '', 'cancelled_lb' => '', 'hold_lb' => '',);
		// not_started
		$Return['not_started_lb'] = lang('Projects.xin_not_started');
		$Return['not_started'] = $not_started;
		// in_progress
		$Return['in_progress_lb'] = lang('Projects.xin_in_progress');
		$Return['in_progress'] = $in_progress;
		// completed
		$Return['completed_lb'] = lang('Projects.xin_completed');
		$Return['completed'] = $completed;
		// cancelled
		$Return['cancelled_lb'] = lang('Projects.xin_project_cancelled');
		$Return['cancelled'] = $cancelled;
		// hold
		$Return['hold_lb'] = lang('Projects.xin_project_hold');
		$Return['hold'] = $hold;
		$Return['total_label'] = lang('Main.xin_total');
		return $this->response->setJSON($Return);
		
	}
	public function client_project_status_chart()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$ConstantsModel = new ConstantsModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_projects = $ProjectsModel->where('client_id', $usession['sup_user_id'])->countAllResults();
		$not_started = $ProjectsModel->where('client_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
		$in_progress = $ProjectsModel->where('client_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
		$completed = $ProjectsModel->where('client_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
		$cancelled = $ProjectsModel->where('client_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
		$hold = $ProjectsModel->where('client_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('not_started' => '', 'in_progress' => '', 'completed' => '', 'cancelled' => '', 'hold' => '', 'not_started_lb' => '', 'in_progress_lb' => '', 'completed_lb' => '', 'cancelled_lb' => '', 'hold_lb' => '',);
		// not_started
		$Return['not_started_lb'] = lang('Projects.xin_not_started');
		$Return['not_started'] = $not_started;
		// in_progress
		$Return['in_progress_lb'] = lang('Projects.xin_in_progress');
		$Return['in_progress'] = $in_progress;
		// completed
		$Return['completed_lb'] = lang('Projects.xin_completed');
		$Return['completed'] = $completed;
		// cancelled
		$Return['cancelled_lb'] = lang('Projects.xin_project_cancelled');
		$Return['cancelled'] = $cancelled;
		// hold
		$Return['hold_lb'] = lang('Projects.xin_project_hold');
		$Return['hold'] = $hold;
		$Return['total_label'] = lang('Main.xin_total');
		return $this->response->setJSON($Return);
		
	}
	public function projects_priority_chart()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$highest = $ProjectsModel->where('company_id', $user_info['company_id'])->where('priority', 1)->countAllResults();
			$high = $ProjectsModel->where('company_id', $user_info['company_id'])->where('priority', 2)->countAllResults();
			$normal = $ProjectsModel->where('company_id', $user_info['company_id'])->where('priority', 3)->countAllResults();
			$low = $ProjectsModel->where('company_id', $user_info['company_id'])->where('priority', 4)->countAllResults();
		} else {
			$highest = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('priority', 1)->countAllResults();
			$high = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('priority', 2)->countAllResults();
			$normal = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('priority', 3)->countAllResults();
			$low = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('priority', 4)->countAllResults();
		}
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('highest' => '', 'high' => '', 'normal' => '', 'low' => '', 'highest_lb' => '', 'high_lb' => '', 'normal_lb' => '', 'low_lb' => '');

		// highest
		$Return['highest_lb'] = lang('Projects.xin_highest');
		$Return['highest'] = $highest;
		// high
		$Return['high_lb'] = lang('Projects.xin_high');
		$Return['high'] = $high;
		// normal
		$Return['normal_lb'] = lang('Projects.xin_normal');
		$Return['normal'] = $normal;
		// low
		$Return['low_lb'] = lang('Projects.xin_low');
		$Return['low'] = $low;
		$this->output($Return);
		exit;
	}
	// read record||read_timelog
	public function read_timelog()
	{
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$id = $request->getGet('field_id');
		$data = [
			'field_id' => $id,
		];
		if ($session->has('sup_username')) {
			return view('erp/projects/dialog_timelog', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_project()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ProjectsModel = new ProjectsModel();
			$result = $ProjectsModel->where('project_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_deleted_msg');
				$Return['redirect_url'] = base_url('erp/projects-list');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_project_note()
	{

		if ($this->request->getVar('field_id')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$ProjectnotesModel = new ProjectnotesModel();
			$result = $ProjectnotesModel->where('project_note_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_note_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	// delete record
	public function delete_project_bug($id)
	{

		if ($id) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			// $id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$ProjectbugsModel = new ProjectbugsModel();
			$result = $ProjectbugsModel->where('project_bug_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_bug_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_project_discussion($id)
	{

		if ($id) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			
			// $id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$ProjectdiscussionModel = new ProjectdiscussionModel();
			$result = $ProjectdiscussionModel->where('project_discussion_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_discussion_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_timelog()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ProjecttimelogsModel = new ProjecttimelogsModel();
			$result = $ProjecttimelogsModel->where('timelogs_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_timelog_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	// delete record
	public function delete_project_file($id)
	{

		if ($id) {
			
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			// $id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$ProjectfilesModel = new ProjectfilesModel();
			$result = $ProjectfilesModel->where('project_file_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_project_file_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
		
	}

	public function edit_project()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ProjectsModel = new ProjectsModel();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$segment_id = $request->getUri()->getSegment(3);
		$project_id = udecode($segment_id);

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$usession = $session->get('sup_username');
		$data['title'] = 'Edit Project ';
		$data['path_url'] = 'Edit Project';
		$data['breadcrumbs'] = 'Edit Project';
		$data['project_id'] = $project_id;

		$data['subview'] = view('erp/projects/edit_project', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function copy_project()
	{
		$ProjectsModel = new ProjectsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$project_id = $this->request->getVar('project_id');

		$project_data = $ProjectsModel->where('project_id', $project_id)->first();

		// Check if project data exists
		if (!$project_data) {
			$session->setFlashdata('error', 'Project not found.');
			return redirect()->back()->withInput();
		}

		$data = [
			'company_id' => $project_data['company_id'],
			'client_id' => $project_data['client_id'],
			'title' => $project_data['title'],
			'start_date' => $this->request->getPost('startDate'),
			'end_date' => $this->request->getPost('deadline'),
			'assigned_to' => $project_data['assigned_to'], // Assuming this is a comma-separated string
			'expert_to' => $project_data['expert_to'],
			'priority' => $project_data['priority'],
			'summary' => $project_data['summary'],
			'revenue' => $project_data['revenue'],
			'budget_hours' => $project_data['budget_hours'],
			'description' => $project_data['description'],
			'project_no' => '', // Adjust this if project number needs a value
			'project_progress' => 0,
			'status' => $this->request->getPost('taskStatus'),
			'project_note' => '',
			'billing_type' => $project_data['billing_type'],
			'tags' => $project_data['tags'],
			'send_email' => $project_data['send_email'],
			'send_email' => $project_data['send_email'],
			'added_by' => $usession['sup_user_id'],
			'created_at' => date('Y-m-d H:i:s')
		];

		// $db      = \Config\Database::connect();
		// $builder = $db->table('ci_projects');
		// var_dump($builder->insert($data));
		// die;
		// $builder->insert($data);


		if ($ProjectsModel->insert($data)) {
			$session->setFlashdata('message', 'Project copied successfully.');
		} else {
			$session->setFlashdata('error', 'Failed to copy project.');
		}

		return redirect()->back()->withInput();
	}


	public function project_invoice($project_id)
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ProjectsModel = new ProjectsModel();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('invoice3', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$project_data = $ProjectsModel->where('project_id', $project_id)->first();
		$data['title'] = 'Project Invoice';
		$data['path_url'] = 'create_invoice';
		$data['breadcrumbs'] = 'Project Invoice';
		$data['project'] = $project_data;

		$data['subview'] = view('erp/projects/project_invoice', $data);
		return view('erp/layout/layout_main', $data); //page load
	}


	public function edit_projectInvoice()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$InvoicesModel = new InvoicesModel();

		$request = \Config\Services::request();
		$invoice_id = udecode($request->getUri()->getSegment(3));
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$invoice_data = $InvoicesModel->where('invoice_id', $invoice_id)->first();
		$data['title'] = 'Edit Project Invoice';
		$data['path_url'] = 'create_invoice';

		$data['breadcrumbs'] = 'Edit Project Invoice';
		$data['invoice'] = $invoice_data;

		$data['subview'] = view('erp/projects/editProject_invoice', $data);
		return view('erp/layout/layout_main', $data); //page load
	}




	// public function set_entity_id_session()
	// {
	// 	$session = \Config\Services::session();
	// 	$entityId = $this->request->getPost('entityId');

	// 	$session->set('entityId', $entityId);

	// 	return json_encode(['status' => 'success']);
	// }

	public function set_entity_id_session()
	{
		$session = \Config\Services::session();
		$entityId = $this->request->getPost('entityId');

		if (!$entityId) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'Entity ID required']);
		}

		$session->set('entityId', $entityId);

		return $this->response->setJSON(['status' => 'success']);
	}
	public function get_employe()
	{
		$company_id = $this->request->getVar('company_id');
		if (!$company_id) {
			return $this->response->setJSON([]);
		}

		$UsersModel = new UsersModel();
		$employees = $UsersModel->where('user_type', 'staff')
			->where('company_id', $company_id)
			->findAll();


		$formattedEmployees = [];
		foreach ($employees as $employee) {
			$formattedEmployees[] = [
				'id' => $employee['user_id'],
				'name' => trim($employee['first_name'] . ' ' . $employee['last_name'])
			];
		}

		return $this->response->setJSON($formattedEmployees);
	}
}
