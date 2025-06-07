<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the TimeHRM License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.timehrm.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to timehrm.official@gmail.com so we can send you a copy immediately.
 *
 * @author   TimeHRM
 * @author-email  timehrm.official@gmail.com
 * @copyright  Copyright © timehrm.com All Rights Reserved
 */

namespace App\Controllers\Erp;

use App\Controllers\BaseController;


use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\MainModel;
use App\Models\TasksModel;
use App\Models\ProjectsModel;
use App\Models\TasknotesModel;
use App\Models\TaskfilesModel;
use App\Models\TaskdiscussionModel;
use App\Models\EmailtemplatesModel;

class Tasks extends BaseController
{

	public function index()
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
			if (!in_array('task1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_tasks') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'tasks';
		$data['breadcrumbs'] = lang('Dashboard.left_tasks');

		$data['subview'] = view('erp/projects/projects_tasks', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function tasks_grid()
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
			if (!in_array('task1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_tasks') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'tasks_grid';
		$data['breadcrumbs'] = lang('Dashboard.left_tasks');

		$data['subview'] = view('erp/projects/projects_tasks_grid', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function task_client()
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
		if ($user_info['user_type'] != 'customer') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_tasks') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'task_client';
		$data['breadcrumbs'] = lang('Dashboard.left_tasks');
		$data['subview'] = view('erp/projects/projects_task_client', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function task_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TasksModel = new TasksModel();
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
		$isegment_val = $TasksModel->where('task_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] == 'staff') {
			$task_data = $TasksModel->where('company_id', $user_info['company_id'])->where('task_id', $ifield_id)->first();
		} else {
			$task_data = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_id', $ifield_id)->first();
		}
		$data['progress'] = $task_data['task_progress'];
		$data['title'] = lang('Projects.xin_task_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'task_details';
		$data['breadcrumbs'] = lang('Projects.xin_task_details');

		$data['subview'] = view('erp/projects/task_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function client_task_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TasksModel = new TasksModel();
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
		$isegment_val = $TasksModel->where('task_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		//$data['progress'] = $task_data['task_progress'];
		$data['title'] = lang('Projects.xin_task_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'task_details';
		$data['breadcrumbs'] = lang('Projects.xin_task_details');

		$data['subview'] = view('erp/projects/client_task_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function tasks_summary()
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
		$data['path_url'] = 'tasks';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');

		$data['subview'] = view('erp/projects/tasks_summary', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function tasks_calendar()
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
			if (!in_array('tasks_calendar', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_calendar');

		$data['subview'] = view('erp/projects/calendar_tasks', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function tasks_scrum_board()
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
			if (!in_array('tasks_sboard', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_projects_scrm_board') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'tasks_scrum_board';
		$data['breadcrumbs'] = lang('Dashboard.xin_projects_scrm_board');

		$data['subview'] = view('erp/projects/projects_tasks_scrum_board', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// record list
	public function tasks_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$TasksModel = new TasksModel();
		$ProjectsModel = new ProjectsModel();

		$user_info = $UsersModel->find($usession['sup_user_id']);

		$status = $this->request->getGet('status');
		$assigned_to = $this->request->getGet('assigned_to');
		$project = $this->request->getGet('project');
		$expert_to = $this->request->getGet('expert_to');


		if ($status !== null || $assigned_to !== null || $project !== null || $expert_to !== null) {
			$session_data = [
				'task_status' => $status,
				'task_user' => $assigned_to,
				'project' => $project,
				'task_expert' => $expert_to,
			];
			$session->set('task_data', $session_data);
		} else {

			$get_session_data = $session->get('task_data');
			if ($get_session_data) {
				$status = $get_session_data['task_status'];
				$assigned_to = $get_session_data['task_user'];
				$project = $get_session_data['project'];
				$expert_to = $get_session_data['expert_to'];
			}
		}

		$draw = intval($this->request->getGet('draw'));
		$start = intval($this->request->getGet('start'));
		$length = intval($this->request->getGet('length'));

		$builder = $TasksModel->where('company_id', $user_info['company_id']);

		if ($status !== null && $status !== '') {
			$builder->where('task_status', $status);
		}
		if ($assigned_to !== null && $assigned_to !== '') {
			$builder->like('assigned_to', $assigned_to, 'both');
		}
		if ($expert_to !== null && $expert_to !== '') {
			$builder->like('expert_to', $expert_to, 'both');
		}
		if ($project !== null && $project !== '') {
			$builder->where('project_id', $project);
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
				->where('created_by', $usession['sup_user_id'])
				->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
				->groupEnd();

			if ($expert_id !== null) {
				$builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
			}
		}


		$recordsTotal = $builder->countAllResults(false);
		$builder->orderBy('task_id', 'ASC');
		$get_data = $builder->findAll($length, $start);
		$recordsFiltered = count($get_data);

		$data = [];
		foreach ($get_data as $r) {
			$delete = '';
			if (in_array('task4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['task_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			}

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';


			$assigned_to = explode(',', $r['assigned_to']);
			$multi_users = multi_user_profile_photo($assigned_to);

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			$progress_class = $this->getProgressClass($r['task_progress']);
			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['task_progress'] . '%;" aria-valuenow="' . $r['task_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['task_progress'] . '%</div></div>';

			$status = $this->getStatusLabel($r['task_status']);

			$project_details = $ProjectsModel->find($r['project_id']);
			$project_name = $project_details['title'];
			$created_by = $UsersModel->find($r['created_by']);
			$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];

			$combhr = $view . $delete;
			$itask_name = '<a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '">' . $r['task_name'] . '</a>';
			// $itask_name = $r['task_name'].'<div class="overlay-edit">'.$combhr.'</div>';

			$data[] = [
				$itask_name,
				$multi_users,
				$start_date,
				$end_date,
				$status,
				$progress_bar,
				$u_name,
				$project_name,
				$combhr
			];
		}

		$output = [
			"draw" => $draw,
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsFiltered,
			"data" => $data
		];

		return $this->response->setJSON($output);
	}
	// public function tasks_lists()
	// {
	// 	$session = \Config\Services::session();
	// 	$usession = $session->get('sup_username');

	// 	if (!$session->has('sup_username')) {
	// 		return redirect()->to(site_url('/'));
	// 	}

	// 	$UsersModel = new UsersModel();
	// 	$TasksModel = new TasksModel();
	// 	$ProjectsModel = new ProjectsModel();

	// 	$user_info = $UsersModel->find($usession['sup_user_id']);

	// 	$status = $this->request->getGet('status');
	// 	$assigned_to = $this->request->getGet('assigned_to');
	// 	$project = $this->request->getGet('project');
	// 	$expert_to = $this->request->getGet('expert_to');


	// 	if ($status !== null || $assigned_to !== null || $project !== null || $expert_to !== null) {
	// 		$session_data = [
	// 			'task_status' => $status,
	// 			'task_user' => $assigned_to,
	// 			'project' => $project,
	// 			'task_expert' => $expert_to,
	// 		];
	// 		$session->set('task_data', $session_data);
	// 	} else {

	// 		$get_session_data = $session->get('task_data');
	// 		if ($get_session_data) {
	// 			$status = $get_session_data['task_status'];
	// 			$assigned_to = $get_session_data['task_user'];
	// 			$project = $get_session_data['project'];
	// 			$expert_to = $get_session_data['expert_to'];
	// 		}
	// 	}

	// 	$draw = intval($this->request->getGet('draw'));
	// 	$start = intval($this->request->getGet('start'));
	// 	$length = intval($this->request->getGet('length'));
	// 	$searchValue = $this->request->getGet('search')['value'] ?? '';

	// 	$builder = $TasksModel->where('company_id', $user_info['company_id']);

	// 	if (!empty($status)) $builder->where('task_status', $status);
	// 	if (!empty($assigned_to)) $builder->like('assigned_to', $assigned_to, 'both');
	// 	if (!empty($expert_to)) $builder->like('expert_to', $expert_to, 'both');
	// 	if (!empty($project)) $builder->where('project_id', $project);
	// 	if ($user_info['user_type'] == 'staff') {
	// 		$user_id = $user_info['user_id'];

	// 		$curl = curl_init();

	// 		$url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id";


	// 		curl_setopt_array($curl, [
	// 			CURLOPT_RETURNTRANSFER => true,
	// 			CURLOPT_URL => $url,
	// 			CURLOPT_HTTPGET => true,
	// 			CURLOPT_TIMEOUT => 10,
	// 		]);

	// 		$response = curl_exec($curl);

	// 		if ($response === false) {
	// 			$error_msg = curl_error($curl);
	// 			curl_close($curl);
	// 			die("cURL Error: " . $error_msg);
	// 		}

	// 		$expert_user_detail = json_decode($response, true);

	// 		if (json_last_error() !== JSON_ERROR_NONE) {
	// 			curl_close($curl);
	// 			die("JSON Decoding Error: " . json_last_error_msg());
	// 		}

	// 		$expert_id = isset($expert_user_detail['detail']['id']) ? $expert_user_detail['detail']['id'] : null;

	// 		curl_close($curl);

	// 		$builder->groupStart()
	// 			->where('created_by', $usession['sup_user_id'])
	// 			->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
	// 			->groupEnd();

	// 		if ($expert_id !== null) {
	// 			$builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
	// 		}
	// 	}


	// 	if (!empty($searchValue)) {
	// 		$builder->groupStart()
	// 			->like('task_name', $searchValue)
	// 			->orLike('assigned_to', $searchValue)
	// 			->orLike('expert_to', $searchValue)
	// 			->groupEnd();
	// 	}


	// 	$totalBuilder = clone $builder;
	// 	$recordsTotal = $totalBuilder->countAllResults(false);

	// 	$builder->orderBy('task_id', 'ASC');
	// 	$get_data = $builder->findAll($length, $start);

	// 	$data = [];
	// 	foreach ($get_data as $r) {
	// 		$delete = '';
	// 		if (in_array('task4', staff_role_resource()) || $user_info['user_type'] == 'company') {
	// 			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['task_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
	// 		}

	// 		$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';


	// 		$assigned_to = explode(',', $r['assigned_to']);
	// 		$multi_users = multi_user_profile_photo($assigned_to);

	// 		$start_date = set_date_format($r['start_date']);
	// 		$end_date = set_date_format($r['end_date']);

	// 		$progress_class = $this->getProgressClass($r['task_progress']);
	// 		$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['task_progress'] . '%;" aria-valuenow="' . $r['task_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['task_progress'] . '%</div></div>';

	// 		$status = $this->getStatusLabel($r['task_status']);

	// 		$project_details = $ProjectsModel->find($r['project_id']);
	// 		$project_name = $project_details['title'];
	// 		$created_by = $UsersModel->find($r['created_by']);
	// 		$u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];

	// 		$combhr = $view . $delete;
	// 		$itask_name = '<a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '">' . $r['task_name'] . '</a>';
	// 		// $itask_name = $r['task_name'].'<div class="overlay-edit">'.$combhr.'</div>';

	// 		$data[] = [
	// 			$itask_name,
	// 			$multi_users,
	// 			$start_date,
	// 			$end_date,
	// 			$status,
	// 			$progress_bar,
	// 			$u_name,
	// 			$project_name,
	// 			$combhr
	// 		];
	// 	}

	// 	$output = [
	// 		"draw" => $draw,
	// 		"recordsTotal" => $recordsTotal,
	// 		"recordsFiltered" => $recordsTotal,
	// 		"data" => $data
	// 	];

	// 	return $this->response->setJSON($output);
	// }

	public function tasks_data_lists()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$TasksModel = new TasksModel();
		$ProjectsModel = new ProjectsModel();

		$user_info = $UsersModel->find($usession['sup_user_id']);

		$status = $this->request->getGet('status');
		$assigned_to = $this->request->getGet('assigned_to');
		$project = $this->request->getGet('project');
		$expert_to = $this->request->getGet('expert_to');

		if ($status !== null || $assigned_to !== null || $project !== null || $expert_to !== null) {
			$session_data = [
				'task_status' => $status,
				'task_user' => $assigned_to,
				'project' => $project,
				'task_expert' => $expert_to,
			];
			$session->set('task_data', $session_data);
		} else {
			$get_session_data = $session->get('task_data');
			if ($get_session_data) {
				$status = $get_session_data['task_status'] ?? null;
				$assigned_to = $get_session_data['task_user'] ?? null;
				$project = $get_session_data['project'] ?? null;
				$expert_to = $get_session_data['task_expert'] ?? null; // Fixed key name here
			}
		}

		$draw = intval($this->request->getGet('draw'));
		$start = intval($this->request->getGet('start'));
		$length = intval($this->request->getGet('length'));
		$searchValue = $this->request->getGet('search')['value'] ?? '';

		$builder = $TasksModel->where('company_id', $user_info['company_id']);

		if (!empty($status)) $builder->where('task_status', $status);
		if (!empty($assigned_to)) $builder->like('assigned_to', $assigned_to, 'both');
		if (!empty($expert_to)) $builder->like('expert_to', $expert_to, 'both');
		if (!empty($project)) $builder->where('project_id', $project);

		if ($user_info['user_type'] == 'staff') {
			$user_id = $user_info['user_id'];
			$expert_id = null;

			try {
				$curl = curl_init();
				$url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id";

				curl_setopt_array($curl, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_URL => $url,
					CURLOPT_HTTPGET => true,
					CURLOPT_TIMEOUT => 5, // Reduced timeout
					CURLOPT_CONNECTTIMEOUT => 5,
				]);

				$response = curl_exec($curl);

				if ($response !== false) {
					$expert_user_detail = json_decode($response, true);
					if (json_last_error() === JSON_ERROR_NONE && isset($expert_user_detail['detail']['id'])) {
						$expert_id = $expert_user_detail['detail']['id'];
					}
				}
			} catch (\Exception $e) {
				// Silently handle any errors
			} finally {
				if (is_resource($curl)) {
					curl_close($curl);
				}
			}

			$builder->groupStart()
				->where('created_by', $usession['sup_user_id'])
				->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
				->groupEnd();

			if ($expert_id !== null) {
				$builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
			}
		}

		if (!empty($searchValue)) {
			$builder->groupStart()
				->like('task_name', $searchValue)
				->orLike('assigned_to', $searchValue)
				->orLike('expert_to', $searchValue)
				->groupEnd();
		}

		$totalBuilder = clone $builder;
		$recordsTotal = $totalBuilder->countAllResults(false);

		$builder->orderBy('task_id', 'ASC');
		$get_data = $builder->findAll($length, $start);

		$data = [];
		foreach ($get_data as $r) {
			$delete = '';
			if (in_array('task4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['task_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			}

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';

			$assigned_to = explode(',', $r['assigned_to']);
			$multi_users = multi_user_profile_photo($assigned_to);

			$start_date = set_date_format($r['start_date']);
			$end_date = set_date_format($r['end_date']);

			$progress_class = $this->getProgressClass($r['task_progress']);
			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['task_progress'] . '%;" aria-valuenow="' . $r['task_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['task_progress'] . '%</div></div>';

			$status = $this->getStatusLabel($r['task_status']);

			$project_name = '';
			if (!empty($r['project_id'])) {
				$project_details = $ProjectsModel->find($r['project_id']);
				$project_name = $project_details['title'] ?? '';
			}

			$u_name = '';
			if (!empty($r['created_by'])) {
				$created_by = $UsersModel->find($r['created_by']);
				$u_name = ($created_by['first_name'] ?? '') . ' ' . ($created_by['last_name'] ?? '');
			}

			$combhr = $view . $delete;
			$itask_name = '<a href="' . site_url('erp/task-detail') . '/' . uencode($r['task_id']) . '">' . $r['task_name'] . '</a>';

			$data[] = [
				$itask_name,
				$multi_users,
				$start_date,
				$end_date,
				$status,
				$progress_bar,
				$u_name,
				$project_name,
				$combhr
			];
		}

		$output = [
			"draw" => $draw,
			"recordsTotal" => $recordsTotal,
			"recordsFiltered" => $recordsTotal,
			"data" => $data
		];

		return $this->response->setJSON($output);
	}

	private function getProgressClass($progress)
	{
		if ($progress <= 20) {
			return 'bg-danger';
		} else if ($progress > 20 && $progress <= 50) {
			return 'bg-warning';
		} else if ($progress > 50 && $progress <= 75) {
			return 'bg-info';
		} else {
			return 'bg-success';
		}
	}

	private function getStatusLabel($task_status)
	{
		$status_labels = [
			0 => '<span class="badge badge-light-warning">' . lang('Projects.xin_not_started') . '</span>',
			1 => '<span class="badge badge-light-primary">' . lang('Projects.xin_in_progress') . '</span>',
			2 => '<span class="badge badge-light-success">' . lang('Projects.xin_completed') . '</span>',
			3 => '<span class="badge badge-light-danger">' . lang('Projects.xin_project_cancelled') . '</span>',
			4 => '<span class="badge badge-light-danger">' . lang('Projects.xin_project_hold') . '</span>'
		];
		return $status_labels[$task_status] ?? '';
	}


	public function client_tasks_list()
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
		$TasksModel = new TasksModel();
		$MainModel = new MainModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $MainModel->get_client_tasks($usession['sup_user_id']);
		//->where('project_id',$project_data['project_id']
		$data = array();

		foreach ($get_data as $r) {

			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r->task_id) . '"><i class="feather icon-trash-2"></i></button></span>';

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/task-details') . '/' . uencode($r->task_id) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			//assigned user
			if ($r->assigned_to == '') {
				$ol = lang('xin_not_assigned');
			} else {
				$ol = '';
				foreach (explode(',', $r->assigned_to) as $emp_id) {
					$assigned_to = $UsersModel->where('user_id', $emp_id)->where('user_type', 'staff')->first();
					if ($assigned_to) {

						$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];

						if ($assigned_to['profile_photo'] != '' && $assigned_to['profile_photo'] != 'no file') {
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . base_url() . 'uploads/users/thumb/' . $assigned_to['profile_photo'] . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
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

			$start_date = set_date_format($r->start_date);
			$end_date = set_date_format($r->end_date);
			// get project
			$_project = $ProjectsModel->where('project_id', $r->project_id)->first();

			// task progress
			if ($r->task_progress <= 20) {
				$progress_class = 'bg-danger';
			} else if ($r->task_progress > 20 && $r->task_progress <= 50) {
				$progress_class = 'bg-warning';
			} else if ($r->task_progress > 50 && $r->task_progress <= 75) {
				$progress_class = 'bg-info';
			} else {
				$progress_class = 'bg-success';
			}

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r->task_progress . '%;" aria-valuenow="' . $r->task_progress . '" aria-valuemin="0" aria-valuemax="100">' . $r->task_progress . '%</div></div>';

			// task status			
			if ($r->task_status == 0) {
				$status = '<span class="badge badge-light-warning">' . lang('Projects.xin_not_started') . '</span>';
			} else if ($r->task_status == 1) {
				$status = '<span class="badge badge-light-primary">' . lang('Projects.xin_in_progress') . '</span>';
			} else if ($r->task_status == 2) {
				$status = '<span class="badge badge-light-success">' . lang('Projects.xin_completed') . '</span>';
			} else if ($r->task_status == 3) {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_cancelled') . '</span>';
			} else {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_hold') . '</span>';
			}
			$overall_progress = $progress_bar . $status;
			$combhr = $view . $delete;
			$itask_name = '
				' . $r->task_name . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			$data[] = array(
				$itask_name,
				$ol,
				$start_date,
				$end_date,
				$_project['title'],
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
	public function client_profile_tasks_list($client_id)
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
		$ProjectsModel = new ProjectsModel();
		$MainModel = new MainModel();
		$TasksModel = new TasksModel();
		// $client_id = udecode($this->request->getVar('client_id', FILTER_SANITIZE_STRING));
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		//	$project_data = $ProjectsModel->where('company_id',$company_id)->where('client_id',$client_id)->first();
		$get_data = $MainModel->get_client_tasks($client_id);
		//$get_data = $TasksModel->where('company_id',$company_id)->where('project_id',$project_data['project_id'])->orderBy('task_id', 'ASC')->findAll();

		$data = array();

		foreach ($get_data as $r) {

			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r->task_id) . '"><i class="feather icon-trash-2"></i></button></span>';

			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/task-detail') . '/' . uencode($r->task_id) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			//assigned user
			if ($r->assigned_to == '') {
				$ol = lang('xin_not_assigned');
			} else {
				$ol = '';
				foreach (explode(',', $r->assigned_to) as $emp_id) {
					$assigned_to = $UsersModel->where('user_id', $emp_id)->where('user_type', 'staff')->first();
					if ($assigned_to) {

						$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];

						if ($assigned_to['profile_photo'] != '' && $assigned_to['profile_photo'] != 'no file') {
							$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" data-state="primary" title="' . $assigned_name . '"><span class="mb-1"><img src="' . base_url() . 'uploads/users/thumb/' . $assigned_to['profile_photo'] . '" class="img-fluid img-radius wid-30" alt=""></span></a>';
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

			$start_date = set_date_format($r->start_date);
			$end_date = set_date_format($r->end_date);
			// get project
			$_project = $ProjectsModel->where('project_id', $r->project_id)->first();

			// task progress
			if ($r->task_progress <= 20) {
				$progress_class = 'bg-danger';
			} else if ($r->task_progress > 20 && $r->task_progress <= 50) {
				$progress_class = 'bg-warning';
			} else if ($r->task_progress > 50 && $r->task_progress <= 75) {
				$progress_class = 'bg-info';
			} else {
				$progress_class = 'bg-success';
			}

			$progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r->task_progress . '%;" aria-valuenow="' . $r->task_progress . '" aria-valuemin="0" aria-valuemax="100">' . $r->task_progress . '%</div></div>';

			// task status			
			if ($r->task_status == 0) {
				$status = '<span class="badge badge-light-warning">' . lang('Projects.xin_not_started') . '</span>';
			} else if ($r->task_status == 1) {
				$status = '<span class="badge badge-light-primary">' . lang('Projects.xin_in_progress') . '</span>';
			} else if ($r->task_status == 2) {
				$status = '<span class="badge badge-light-success">' . lang('Projects.xin_completed') . '</span>';
			} else if ($r->task_status == 3) {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_cancelled') . '</span>';
			} else {
				$status = '<span class="badge badge-light-danger">' . lang('Projects.xin_project_hold') . '</span>';
			}
			$overall_progress = $progress_bar . $status;
			$combhr = $view . $delete;
			$itask_name = '
				' . $r->task_name . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			$data[] = array(
				$itask_name,
				$_project['title'],
				$ol,
				$overall_progress,
				$end_date,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// |||add record|||
	public function add_gridTask()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if (!$this->request->getPost()) {
			return $this->response->setJSON(['error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()]);
		}

		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Validation rules
		$rules = [
			'task_name' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'start_date' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'end_date' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'project_id' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Success.xin_project_field_error')]
			],
			'summary' => [
				'rules' => 'required|min_length[10]',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),
					'min_length' => lang('Main.xin_error_field_min_length')
				]
			]
		];

		if (!$this->validate($rules)) {
			$errors = [
				'task_name' => $validation->getError('task_name'),
				'start_date' => $validation->getError('start_date'),
				'end_date' => $validation->getError('end_date'),
				'project_id' => $validation->getError('project_id'),
				'summary' => $validation->getError('summary')
			];

			foreach ($errors as $error) {
				if (!empty($error)) {
					$Return['error'] = $error;
					break;
				}
			}

			return $this->response->setJSON($Return);
		}

		// Process valid data
		try {
			$post = $this->request->getPost();

			$task_name = filter_var($post['task_name'], FILTER_SANITIZE_STRING);
			$start_date = filter_var($post['start_date'], FILTER_SANITIZE_STRING);
			$end_date = filter_var($post['end_date'], FILTER_SANITIZE_STRING);
			$project_id = filter_var($post['project_id'], FILTER_SANITIZE_STRING);
			$task_hour = filter_var($post['task_hour'] ?? '', FILTER_SANITIZE_STRING);
			$summary = filter_var($post['summary'], FILTER_SANITIZE_STRING);
			$description = filter_var($post['description'] ?? '', FILTER_SANITIZE_STRING);
			$employe_id = filter_var($post['employee_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

			$assigned_ids = !empty($post['assigned_to']) ? implode(',', array_map('intval', $post['assigned_to'])) : '';
			$expert_ids = !empty($post['expert_to']) ? implode(',', array_map('intval', $post['expert_to'])) : '';
			$milestone_id = filter_var($post['milestone_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if (!$user_info) {
				throw new \RuntimeException('User not found');
			}

			$company_id = match ($user_info['user_type']) {
				'staff', 'customer' => $user_info['company_id'],
				default => $usession['sup_user_id']
			};

			$data = [
				'company_id' => $company_id,
				'task_name' => $task_name,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'project_id' => $project_id,
				'milestones_id' => $milestone_id,
				'summary' => $summary,
				'task_hour' => $task_hour,
				'description' => $description,
				'assigned_to' => $assigned_ids,
				'expert_to' => $expert_ids,
				'employe_ID' => $employe_id,
				'task_progress' => 0,
				'task_status' => 0,
				'task_note' => '',
				'created_by' => $usession['sup_user_id'],
				'created_at' => date('Y-m-d H:i:s') // Fixed date format
			];

			$TasksModel = new TasksModel();
			$result = $TasksModel->insert($data);

			if (!$result) {
				throw new \RuntimeException('Failed to insert task');
			}

			// Handle email notifications
			$xin_system = new SystemModel();
			$xin_system_data = $xin_system->where('setting_id', 1)->first();

			if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1 && !empty($post['assigned_to'])) {
				$EmailtemplatesModel = new EmailtemplatesModel();
				$itemplate = $EmailtemplatesModel->where('template_id', 10)->first();

				if ($itemplate) {
					$isubject = $itemplate['subject'];
					$ibody = html_entity_decode($itemplate['message']);
					$fbody = str_replace(
						['{site_name}', '{task_name}', '{task_due_date}'],
						[$user_info['company_name'], $task_name, $end_date],
						$ibody
					);

					foreach ($post['assigned_to'] as $_staff_id) {
						$staff_info = $UsersModel->where('user_id', $_staff_id)->first();
						if ($staff_info) {
							timehrm_mail_data(
								$user_info['email'],
								$user_info['company_name'],
								$staff_info['email'],
								$isubject,
								$fbody
							);
						}
					}
				}
			}

			$current_url = $this->request->getServer('HTTP_REFERER') ?? base_url();
			$session->setFlashdata('success_message', 'Task successfully created.');

			$Return['result'] = lang('Success.ci_task_added_msg');
			$Return['redirect_url'] = $current_url;

			return $this->response->setJSON($Return);
		} catch (\Exception $e) {
			log_message('error', 'Error in add_gridTask: ' . $e->getMessage());
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	public function add_task()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if (!$this->request->getPost()) {
			return $session->setFlashdata('error', lang('Main.xin_error_msg'));
		}

		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = csrf_hash();

		// set rules
		$rules = [
			'task_name' => [
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
			'project_id' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Success.xin_project_field_error')
				]
			],
			'summary' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Main.xin_error_field_text')
				]
			]
		];

		if (!$this->validate($rules)) {
			$ruleErrors = [
				"task_name" => $validation->getError('task_name'),
				"start_date" => $validation->getError('start_date'),
				"end_date" => $validation->getError('end_date'),
				"project_id" => $validation->getError('project_id'),
				"summary" => $validation->getError('summary')
			];

			foreach ($ruleErrors as $err) {
				if (!empty($err)) {
					$Return['error'] = $err;
					return $this->response->setJSON($Return);
				}
			}
		}

		// Process the form data
		$task_name = $this->request->getPost('task_name', FILTER_SANITIZE_STRING);
		$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
		$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
		$project_id = $this->request->getPost('project_id', FILTER_SANITIZE_STRING);
		$task_hour = $this->request->getPost('task_hour', FILTER_SANITIZE_STRING);
		$summary = $this->request->getPost('summary', FILTER_SANITIZE_STRING);
		$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
		$employe_id = $this->request->getPost('employee_id');

		$assigned_ids = implode(',', $this->request->getPost('assigned_to', FILTER_SANITIZE_STRING));
		$employee_ids = $assigned_ids;

		$expert_ids = implode(',', $this->request->getPost('expert_to', FILTER_SANITIZE_STRING));
		$milestone_id = $this->request->getPost('milestone_id') ? $this->request->getPost('milestone_id') : 0;

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($user_info['user_type'] == 'staff' || $user_info['user_type'] == 'customer') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$data = [
			'company_id' => $company_id,
			'task_name' => $task_name,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'project_id' => $project_id,
			'milestones_id' => $milestone_id,
			'summary' => $summary,
			'task_hour' => $task_hour,
			'description' => $description,
			'assigned_to' => $employee_ids,
			'expert_to' => $expert_ids,
			'employe_ID' => (int)$employe_id,
			'task_progress' => 0,
			'task_status' => 0,
			'task_note' => '',
			'created_by' => $usession['sup_user_id'],
			'created_at' => date('Y-m-d H:i:s') // Fixed date format
		];

		$TasksModel = new TasksModel();
		$result = $TasksModel->insert($data);

		if ($result === false) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		// Handle email notifications
		$xin_system = new SystemModel();
		$xin_system_data = $xin_system->where('setting_id', 1)->first();

		if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1) {
			$EmailtemplatesModel = new EmailtemplatesModel();
			$itemplate = $EmailtemplatesModel->where('template_id', 10)->first();

			if ($itemplate) {
				$isubject = $itemplate['subject'];
				$ibody = html_entity_decode($itemplate['message']);
				$fbody = str_replace(
					["{site_name}", "{task_name}", "{task_due_date}"],
					[$user_info['company_name'], $task_name, $end_date],
					$ibody
				);

				foreach ($this->request->getPost('assigned_to', FILTER_SANITIZE_STRING) as $_staff_id) {
					$staff_info = $UsersModel->where('user_id', $_staff_id)->first();
					if ($staff_info) {
						timehrm_mail_data(
							$user_info['email'],
							$user_info['company_name'],
							$staff_info['email'],
							$isubject,
							$fbody
						);
					}
				}
			}
		}

		$current_url = $this->request->getServer('HTTP_REFERER');
		$Return['result'] = lang('Success.ci_task_added_msg');
		$Return['redirect_url'] = $current_url;
		$session->setFlashdata('message', 'Task successfully added.');

		$this->output($Return);
		return redirect()->to($current_url);
	}

	public function add_tasks()
	{
		// Initialize services
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = service('request');
		$usession = $session->get('sup_username');

		// Prepare response array
		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Only process POST requests
		if (!$request->is('post')) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		// Set validation rules
		$rules = [
			'task_name' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'start_date' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'end_date' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'project_id' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Success.xin_project_field_error')]
			],
			'summary' => [
				'rules' => 'required|min_length[10]',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),
					'min_length' => lang('Main.xin_error_field_minlength')
				]
			]
		];

		// Validate input
		if (!$this->validate($rules)) {
			$errors = $validation->getErrors();
			$Return['error'] = implode("\n", $errors);
			return $this->response->setJSON($Return);
		}

		try {
			// Get sanitized input data
			$data = [
				'task_name' => $request->getPost('task_name', FILTER_SANITIZE_STRING),
				'start_date' => $request->getPost('start_date', FILTER_SANITIZE_STRING),
				'end_date' => $request->getPost('end_date', FILTER_SANITIZE_STRING),
				'project_id' => $request->getPost('project_id', FILTER_SANITIZE_NUMBER_INT),
				'task_hour' => $request->getPost('task_hour', FILTER_SANITIZE_STRING),
				'summary' => $request->getPost('summary', FILTER_SANITIZE_STRING),
				'description' => $request->getPost('description', FILTER_SANITIZE_STRING),
				'assigned_to' => implode(',', $request->getPost('assigned_to') ?? []),
				'expert_to' => implode(',', $request->getPost('expert_to') ?? []),
				'milestones_id' => $request->getPost('milestone_id', FILTER_SANITIZE_NUMBER_INT) ?? 0,
				'task_progress' => 0,
				'task_status' => 0,
				'task_note' => '',
				'created_at' => date('Y-m-d H:i:s')
			];

			// Get user info and set company_id
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if (!$user_info) {
				throw new \RuntimeException('User not found');
			}

			if (in_array($user_info['user_type'], ['staff', 'customer'])) {
				$data['company_id'] = $user_info['company_id'];
			} else {
				$data['company_id'] = $usession['sup_user_id'];
			}

			$data['created_by'] = $usession['sup_user_id'];

			// Save to database
			$TasksModel = new TasksModel();
			if (!$TasksModel->insert($data)) {
				throw new \RuntimeException('Failed to save task');
			}

			// Send email notifications if enabled
			$xin_system = new SystemModel();
			$xin_system_data = $xin_system->where('setting_id', 1)->first();

			if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1) {
				$EmailtemplatesModel = new EmailtemplatesModel();
				$itemplate = $EmailtemplatesModel->where('template_id', 10)->first();

				if ($itemplate) {
					$isubject = $itemplate['subject'];
					$ibody = html_entity_decode($itemplate['message']);
					$fbody = str_replace(
						["{site_name}", "{task_name}", "{task_due_date}"],
						[$user_info['company_name'], $data['task_name'], $data['end_date']],
						$ibody
					);

					$assigned_to = $request->getPost('assigned_to') ?? [];
					foreach ($assigned_to as $_staff_id) {
						$staff_info = $UsersModel->where('user_id', $_staff_id)->first();
						if ($staff_info) {
							timehrm_mail_data(
								$user_info['email'],
								$user_info['company_name'],
								$staff_info['email'],
								$isubject,
								$fbody
							);
						}
					}
				}
			}

			$Return['result'] = lang('Success.ci_task_added_msg');
			$Return['csrf_hash'] = csrf_hash();
		} catch (\Exception $e) {
			$Return['error'] = lang('Main.xin_error_msg') . ': ' . $e->getMessage();
		}

		return $this->response->setJSON($Return);
	}




	public function update_task()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if (!$usession) {
			return $this->response->setJSON([
				'error' => 'Session expired, please login again',
				'redirect' => site_url('login')
			]);
		}

		if ($this->request->getPost('type') === 'edit_record') {
			$Return = [
				'result' => '',
				'error' => '',
				'csrf_hash' => csrf_hash(),
				'redirect' => ''
			];

			// set validation rules
			$rules = [
				'task_name' => [
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
				'project_id' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Success.xin_project_field_error')
					]
				],
				'summary' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];

			if (!$this->validate($rules)) {
				$ruleErrors = [
					"task_name" => $validation->getError('task_name'),
					"start_date" => $validation->getError('start_date'),
					"end_date" => $validation->getError('end_date'),
					"project_id" => $validation->getError('project_id'),
					"summary" => $validation->getError('summary')
				];

				foreach ($ruleErrors as $err) {
					if (!empty($err)) {
						$Return['error'] = $err;
						break;
					}
				}

				return $this->response->setJSON($Return);
			}

			// Sanitize inputs
			$task_name = $this->request->getPost('task_name', FILTER_SANITIZE_STRING);
			$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
			$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
			$project_id = $this->request->getPost('project_id', FILTER_SANITIZE_NUMBER_INT);
			$task_hour = $this->request->getPost('task_hour', FILTER_SANITIZE_NUMBER_INT);
			$summary = $this->request->getPost('summary', FILTER_SANITIZE_STRING);
			$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

			$assigned_to = $this->request->getPost('assigned_to') ? $this->request->getPost('assigned_to') : [];
			$expert_to = $this->request->getPost('expert_to') ? $this->request->getPost('expert_to') : [];
			$associated_goals = $this->request->getPost('associated_goals') ? $this->request->getPost('associated_goals') : [];

			$assigned_ids = !empty($assigned_to) ? implode(',', array_map('intval', $assigned_to)) : '';
			$expert_ids = !empty($expert_to) ? implode(',', array_map('intval', $expert_to)) : '';
			$associated_goals_ids = !empty($associated_goals) ? implode(',', array_map('intval', $associated_goals)) : '';

			$milestone_id = $this->request->getPost('milestone_id') ? (int)$this->request->getPost('milestone_id') : 0;

			$data = [
				'task_name' => $task_name,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'milestones_id' => $milestone_id,
				'summary' => $summary,
				'task_hour' => $task_hour,
				'description' => $description,
				'project_id' => $project_id,
				'assigned_to' => $assigned_ids,
				'expert_to' => $expert_ids,
				'associated_goals' => $associated_goals_ids
			];

			$TasksModel = new TasksModel();
			$UsersModel = new UsersModel();
			$SystemModel = new SystemModel();
			$EmailtemplatesModel = new EmailtemplatesModel();

			$xin_system = $SystemModel->where('setting_id', 1)->first();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if (!$user_info) {
				return $this->response->setJSON([
					'error' => 'User not found',
					'redirect' => site_url('dashboard')
				]);
			}

			$company_info = ($user_info['user_type'] == 'company')
				? $user_info
				: $UsersModel->where('company_id', $user_info['company_id'])->first();

			try {
				$result = $TasksModel->update($id, $data);

				if ($result) {
					$Return['result'] = lang('Success.ci_task_updated_msg');

					if ($xin_system['enable_email_notification'] == 1) {
						$itemplate = $EmailtemplatesModel->where('template_id', 10)->first();
						if ($itemplate) {
							$isubject = $itemplate['subject'];
							$ibody = html_entity_decode($itemplate['message']);
							$fbody = str_replace(
								["{site_name}", "{task_name}", "{task_due_date}"],
								[$user_info['company_name'], $task_name, $end_date],
								$ibody
							);

							foreach ($assigned_to as $_staff_id) {
								$staff_info = $UsersModel->where('user_id', $_staff_id)->first();
								if ($staff_info) {
									timehrm_mail_data(
										$user_info['email'],
										$user_info['company_name'],
										$staff_info['email'],
										$isubject,
										$fbody
									);
								}
							}
						}
					}
				} else {
					$db = \Config\Database::connect();
					$error = $db->error();
					log_message('error', 'Database update failed. Error: ' . json_encode($error));
					$Return['error'] = 'Database update failed. Please try again.';
				}
			} catch (\Exception $e) {
				log_message('error', 'Database exception: ' . $e->getMessage());
				$Return['error'] = 'An error occurred while updating the task.';
			}

			return $this->response->setJSON($Return);
		} else {
			return $this->response->setJSON([
				'error' => lang('Main.xin_error_msg'),
				'redirect' => site_url('dashboard')
			]);
		}
	}
	// |||update record|||
	public function update_task_progress()
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
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"progres_val" => $validation->getError('progres_val'),
					"status" => $validation->getError('status'),
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
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$data = [
					'task_progress' => $progres_val,
					'task_status' => $status
				];
				$TasksModel = new TasksModel();
				$result = $TasksModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_task_status_updated_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
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

	public function taskUpdate($id)
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();

		if ($this->request->getMethod()) {

			// CSRF hash for security
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			// Set validation rules
			$rules = [
				'task_name' => 'required',
				'start_date' => 'required',
				'end_date' => 'required',
				'summary' => 'required',
			];

			if (!$this->validate($rules)) {
				// Collect all validation errors
				$Return['error'] = implode(', ', $validation->getErrors());
				return $this->response->setJSON($Return);
			}

			// Process the form input data
			$task_name = $this->request->getPost('task_name');
			$start_date = $this->request->getPost('start_date');
			$end_date = $this->request->getPost('end_date');
			$project_id = $this->request->getPost('project_id');
			$task_hour = $this->request->getPost('task_hour');
			$summary = $this->request->getPost('summary');
			$description = $this->request->getPost('description');
			$assigned_ids = implode(',', $this->request->getPost('assigned_to'));
			$expert_ids = implode(',', $this->request->getPost('expert_to'));

			$employe_id = $this->request->getPost('employee_id');
			$milestone_id = $this->request->getPost('milestone_id') ? $this->request->getPost('milestone_id') : 0;

			// Prepare data for update
			$data = [
				'task_name' => $task_name,
				'milestones_id' => $milestone_id,
				'start_date' => $start_date,
				'end_date' => $end_date,
				'task_hour' => $task_hour,
				'summary' => $summary,
				'description' => $description,
				'assigned_to' => $assigned_ids,
				'expert_to' => $expert_ids,
				'employe_ID' => (int)$employe_id,
				'task_progress' => $this->request->getPost('progres_val'),
				'task_status' => $this->request->getPost('status'),
			];


			$TasksModel = new TasksModel();
			$result = $TasksModel->update($id, $data);

			// Set CSRF hash again
			$Return['csrf_hash'] = csrf_hash();

			if ($result) {
				$session->setFlashdata('message', 'Task updated successfully');
				return redirect()->to(site_url('erp/project-detail/' . uencode($project_id)));
			} else {
				$session->setFlashdata('error', lang('Main.xin_error_msg'));
				return redirect()->to(site_url('erp/tasks-list'));
			}
		} else {

			// Handle the case if the request is not a POST request
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
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
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$data = [
					'company_id' => $company_id,
					'task_id' => $id,
					'employee_id' => $usession['sup_user_id'],
					'task_note' => $description,
					'created_at' => date('d-m-Y h:i:s')
				];
				$TasknotesModel = new TasknotesModel();
				$result = $TasknotesModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_task_note_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
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
	// update record
	public function update_task_status()
	{
		if ($this->request->getVar('xfieldid')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$task_id = $this->request->getVar('xfieldid', FILTER_SANITIZE_STRING);
			//$task_id = $request->uri->getSegment(4);
			$task_status = $this->request->getVar('xfieldst', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}
			$data = [
				'task_status' => $task_status,
			];
			$TasksModel = new TasksModel();
			$result = $TasksModel->update($task_id, $data);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_status_updated_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	// |||add record|||
	public function add_discussion()
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
						'required' => lang('Success.xin_discussion_field_error')
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
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$data = [
					'company_id' => $company_id,
					'task_id' => $id,
					'employee_id' => $usession['sup_user_id'],
					'discussion_text' => $description,
					'created_at' => date('d-m-Y h:i:s')
				];
				$TaskdiscussionModel = new TaskdiscussionModel();
				$result = $TaskdiscussionModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_task_discussion_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
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
	// |||add record|||
	public function add_attachment()
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
						$this->output($Return);
					}
				}
			} else {
				// upload file
				$attachment = $this->request->getFile('attachment_file');
				$file_name = $attachment->getName();
				$attachment->move('uploads/task_files/');

				$file_title = $this->request->getPost('file_name', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'company_id' => $company_id,
					'task_id' => $id,
					'employee_id' => $usession['sup_user_id'],
					'file_title' => $file_title,
					'attachment_file' => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$TaskfilesModel = new TaskfilesModel();
				$result = $TaskfilesModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_task_file_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				return $this->response->setJSON($Return);

			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	public function task_status_chart()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TasksModel = new TasksModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Fetch data from the database
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$get_tasks = $TasksModel->where('company_id', $company_id)->countAllResults();
		$not_started = $TasksModel->where('company_id', $company_id)->where('task_status', 0)->countAllResults();
		$in_progress = $TasksModel->where('company_id', $company_id)->where('task_status', 1)->countAllResults();
		$completed = $TasksModel->where('company_id', $company_id)->where('task_status', 2)->countAllResults();
		$cancelled = $TasksModel->where('company_id', $company_id)->where('task_status', 3)->countAllResults();
		$hold = $TasksModel->where('company_id', $company_id)->where('task_status', 4)->countAllResults();

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

		$not_started = ($not_started > 0) ? number_format(($not_started / $get_tasks) * 100, 1, '.', '') : $not_started;
		$in_progress = ($in_progress > 0) ? number_format(($in_progress / $get_tasks) * 100, 1, '.', '') : $in_progress;
		$completed = ($completed > 0) ? number_format(($completed / $get_tasks) * 100, 1, '.', '') : $completed;
		$cancelled = ($cancelled > 0) ? number_format(($cancelled / $get_tasks) * 100, 1, '.', '') : $cancelled;
		$hold = ($hold > 0) ? number_format(($hold / $get_tasks) * 100, 1, '.', '') : $hold;

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

	public function staff_task_status_chart()
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
		$TasksModel = new TasksModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_tasks = $TasksModel->where('company_id', $user_info['company_id'])->countAllResults();
			$not_started = $TasksModel->where('company_id', $user_info['company_id'])->where('task_status', 0)->countAllResults();
			$in_progress = $TasksModel->where('company_id', $user_info['company_id'])->where('task_status', 1)->countAllResults();
			$completed = $TasksModel->where('company_id', $user_info['company_id'])->where('task_status', 2)->countAllResults();
			$cancelled = $TasksModel->where('company_id', $user_info['company_id'])->where('task_status', 3)->countAllResults();
			$hold = $TasksModel->where('company_id', $user_info['company_id'])->where('task_status', 4)->countAllResults();
		} else {
			$get_tasks = $TasksModel->where('company_id', $usession['sup_user_id'])->countAllResults();
			$not_started = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 0)->countAllResults();
			$in_progress = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 1)->countAllResults();
			$completed = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 2)->countAllResults();
			$cancelled = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 3)->countAllResults();
			$hold = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 4)->countAllResults();
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
		//$Return['total'] = $total;
		$this->output($Return);
		exit;
	}
	public function client_task_status_chart()
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
		$TasksModel = new TasksModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$_project = $ProjectsModel->where('client_id', $usession['sup_user_id'])->first();
		$not_started = $TasksModel->where('project_id', $_project['project_id'])->where('task_status', 0)->countAllResults();
		$in_progress = $TasksModel->where('project_id', $_project['project_id'])->where('task_status', 1)->countAllResults();
		$completed = $TasksModel->where('project_id', $_project['project_id'])->where('task_status', 2)->countAllResults();
		$cancelled = $TasksModel->where('project_id', $_project['project_id'])->where('task_status', 3)->countAllResults();
		$hold = $TasksModel->where('project_id', $_project['project_id'])->where('task_status', 4)->countAllResults();
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
		//$Return['total'] = $total;
		return $this->response->setJSON($Return);
	}
	public function tasks_by_projects_chart()
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
		$TasksModel = new TasksModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_projects = $ProjectsModel->where('company_id', $user_info['company_id'])->orderBy('project_id', 'ASC')->findAll();
		} else {
			$get_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->orderBy('project_id', 'ASC')->findAll();
		}
		$data = array();
		$Return = array('iseries' => '', 'ilabels' => '');
		$title_info = array();
		$series_info = array();
		foreach ($get_projects as $r) {
			$task_info = $TasksModel->where('project_id', $r['project_id'])->first();
			$task_count = $TasksModel->where('project_id', $r['project_id'])->countAllResults();
			if ($task_count > 0) {
				$title_info[] = $r['title'];
				$series_info[] = $task_count;
			}
		}
		$Return['iseries'] = $series_info;
		$Return['ilabels'] = $title_info;
		$Return['total_label'] = lang('Main.xin_total');
		$this->output($Return);
		exit;
	}
	// delete record
	public function delete_gridtask()
	{
		if ($this->request->getPost('type') == 'delete_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$TasksModel = new TasksModel();
			$result = $TasksModel->where('task_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	public function delete_projecttask()
	{
		if ($this->request->getPost('type') == 'delete_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$TasksModel = new TasksModel();
			$result = $TasksModel->where('task_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}

	public function delete_tasks()
	{
		if ($this->request->getPost('type') == 'delete_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$TasksModel = new TasksModel();
			$result = $TasksModel->where('task_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_task_note()
	{

		if ($this->request->getVar('field_id')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$TasknotesModel = new TasknotesModel();
			$result = $TasknotesModel->where('task_note_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_note_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_task_discussion()
	{

		
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$this->request->getVar('field_id')) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		$id = $this->request->getVar('field_id');
		$Return['csrf_hash'] = csrf_hash();

		$TaskdiscussionModel = new TaskdiscussionModel();
		$result = $TaskdiscussionModel->where('task_discussion_id', $id)->delete($id);

		if ($result) {
			$Return['result'] = lang('Success.ci_task_discussion_deleted_msg');
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($Return);
	}
	// delete record
	public function delete_task_file()
	{

		if ($this->request->getVar('field_id')) {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = $this->request->getVar('field_id', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$TaskfilesModel = new TaskfilesModel();
			$result = $TaskfilesModel->where('task_file_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_task_file_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}


	public function edit_task()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$Model = new TasksModel();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$segment_id = $request->getUri()->getSegment(3);
		$task_id = udecode($segment_id);
		$task_data = $Model->where('task_id', $task_id)->first();

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$usession = $session->get('sup_username');
		$data['title'] = 'Edit Task ';
		$data['path_url'] = 'Edit Task';
		$data['breadcrumbs'] = 'Edit Task';
		$data['task_data'] = $task_data;

		$data['subview'] = view('erp/projects/edit_task', $data);
		return view('erp/layout/layout_main', $data);
	}
}
