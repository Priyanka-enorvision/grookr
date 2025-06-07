<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\MainModel;
use App\Models\KpiModel;
use App\Models\KpaModel;
use App\Models\ConstantsModel;
use App\Models\KpioptionsModel;
use App\Models\KpaoptionsModel;
use App\Models\DesignationModel;

class Talent extends BaseController
{

	public function performance_indicator()
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
			if (!in_array('indicator1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_performance_indicator') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'kpi';
		$data['breadcrumbs'] = lang('Dashboard.left_performance_indicator');

		$data['subview'] = view('erp/talent/performance_indicator', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function indicator_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$KpiModel = new KpiModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $KpiModel->where('performance_indicator_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Performance.xin_performance_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'kpi_details';
		$data['breadcrumbs'] = lang('Performance.xin_performance_details');

		$data['subview'] = view('erp/talent/indicator_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function appraisal_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$KpaModel = new KpaModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $KpaModel->where('performance_appraisal_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Performance.xin_performance_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'kpa_details';
		$data['breadcrumbs'] = lang('Performance.xin_performance_details');

		$data['subview'] = view('erp/talent/appraisal_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function performance_appraisal()
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
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('appraisal1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.left_performance_appraisal') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'kpa';
		$data['breadcrumbs'] = lang('Dashboard.left_performance_appraisal');

		$data['subview'] = view('erp/talent/performance_appraisal', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// record list
	public function indicator_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$KpiModel = new KpiModel();
		$ConstantsModel = new ConstantsModel();
		$KpioptionsModel = new KpioptionsModel();
		$DesignationModel = new DesignationModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $KpiModel->where('company_id', $user_info['company_id'])->orderBy('performance_indicator_id', 'ASC')->findAll();
			$count_competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->countAllResults();
			$count_competencies2 = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies2')->countAllResults();
		} else {
			$get_data = $KpiModel->where('company_id', $usession['sup_user_id'])->orderBy('performance_indicator_id', 'ASC')->findAll();
			$count_competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->countAllResults();
			$count_competencies2 = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies2')->countAllResults();
		}
		$data = array();

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url() . 'erp/kpi-details/' . uencode($r['performance_indicator_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><span class="fa fa-arrow-circle-right"></span></button></a></span>';
			if (in_array('indicator4', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['performance_indicator_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$created_at = set_date_format($r['created_at']);
			$designations = $DesignationModel->where('designation_id', $r['designation_id'])->first();
			$added_by = $UsersModel->where('user_id', $r['added_by'])->first();
			$combhr = $edit . $delete;
			$kpi_count_val = $KpioptionsModel->where('indicator_id', $r['performance_indicator_id'])->findAll();
			$star_value = 0;

			foreach ($kpi_count_val as $nw_starval) {
				$star_value += $nw_starval['indicator_option_value'];
			}
			$total_comp = $count_competencies + $count_competencies2;
			$total_val = $total_comp * 5;
			///
			if ($total_val < 1) {
				$rating_val = 0;
			} else {
				$rating_val = $star_value / $total_val * 5;
				$rating_val = number_format((float)$rating_val, 1, '.', '');
			}
			$total_stars = '<span class="overall-stars">';
			for ($i = 1; $i <= 5; $i++) {
				if (round($rating_val - .49) >= $i) {
					$total_stars .= "<i class='fa fa-star'></i>"; //fas fa-star for v5
				} elseif (round($rating_val + .49) >= $i) {
					$total_stars .= "<i class='fas fa-star-half-alt'></i>"; //fas fa-star-half-alt for v5
				} else {
					$total_stars .= "<i class='far fa-star'></i>"; //far fa-star for v5
				}
			}
			$total_stars .= '</span> ' . $rating_val;
			$ititle = '
					' . $r['title'] . '
					<div class="overlay-edit">
						' . $combhr . '
					</div>
				';
			//$combhr = $edit.$delete;	
			$data[] = array(
				$ititle,
				$designations['designation_name'],
				$total_stars,
				$added_by['first_name'] . ' ' . $added_by['last_name'],
				$created_at
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
	public function appraisal_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$KpaModel = new KpaModel();
		$ConstantsModel = new ConstantsModel();
		$KpaoptionsModel = new KpaoptionsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $KpaModel->where('company_id', $user_info['company_id'])->orderBy('performance_appraisal_id', 'ASC')->findAll();
			// count
			$count_competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->countAllResults();
			$count_competencies2 = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies2')->countAllResults();
		} else {
			$get_data = $KpaModel->where('company_id', $usession['sup_user_id'])->orderBy('performance_appraisal_id', 'ASC')->findAll();
			// count
			$count_competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->countAllResults();
			$count_competencies2 = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies2')->countAllResults();
		}
		$data = array();

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url() . 'erp/kpa-details/' . uencode($r['performance_appraisal_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><span class="fa fa-arrow-circle-right"></span></button></a></span>';

			if (in_array('appraisal4', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['performance_appraisal_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$created_at = set_date_format($r['created_at']);
			$user_assign = $UsersModel->where('user_id', $r['employee_id'])->first();
			$full_name = $user_assign['first_name'] . ' ' . $user_assign['last_name'];
			$added_by = $UsersModel->where('user_id', $r['added_by'])->first();
			$combhr = $edit . $delete;
			$kpa_count_val = $KpaoptionsModel->where('appraisal_id', $r['performance_appraisal_id'])->findAll();
			$star_value = 0;

			foreach ($kpa_count_val as $nw_starval) {
				$star_value += $nw_starval['appraisal_option_value'];
			}
			$total_comp = $count_competencies + $count_competencies2;
			$total_val = $total_comp * 5;
			///
			if ($total_val < 1) {
				$rating_val = 0;
			} else {
				$rating_val = $star_value / $total_val * 5;
				$rating_val = number_format((float)$rating_val, 1, '.', '');
			}
			$total_stars = '<span class="overall-stars">';
			for ($i = 1; $i <= 5; $i++) {
				if (round($rating_val - .49) >= $i) {
					$total_stars .= "<i class='fa fa-star'></i>"; //fas fa-star for v5
				} elseif (round($rating_val + .49) >= $i) {
					$total_stars .= "<i class='fas fa-star-half-alt'></i>"; //fas fa-star-half-alt for v5
				} else {
					$total_stars .= "<i class='far fa-star'></i>"; //far fa-star for v5
				}
			}
			$total_stars .= '</span> ' . $rating_val;
			$ititle = '
				' . $r['title'] . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>
			';
			//$combhr = $edit.$delete;	
			$data[] = array(
				$ititle,
				$full_name,
				$r['appraisal_year_month'],
				$added_by['first_name'] . ' ' . $added_by['last_name'],
				$total_stars,
				$created_at
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
	// public function add_indicator()
	// {
	// 	$validation = \Config\Services::validation();
	// 	$session = \Config\Services::session();
	// 	$request = \Config\Services::request();
	// 	$usession = $session->get('sup_username');

	// 	if ($this->request->getPost()) {
	// 		// Validation rules
	// 		$rules = [
	// 			'title' => [
	// 				'rules' => 'required',
	// 				'errors' => ['required' => lang('Main.xin_error_field_text')]
	// 			],
	// 			'designation_id' => [
	// 				'rules' => 'required',
	// 				'errors' => ['required' => lang('Employees.xin_employee_error_designation')]
	// 			]
	// 		];

	// 		if (!$this->validate($rules)) {
	// 			$errors = $validation->getErrors();
	// 			$session->setFlashdata('error', implode('<br>', $errors));
	// 			return redirect()->back()->withInput();
	// 		}

	// 		// Gather basic data
	// 		$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
	// 		$designation_id = $this->request->getPost('designation_id', FILTER_SANITIZE_STRING);
	// 		$review_period = $this->request->getPost('review_period', FILTER_SANITIZE_STRING);

	// 		$UsersModel = new UsersModel();
	// 		$ConstantsModel = new ConstantsModel();

	// 		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
	// 		$competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();

	// 		$company_id = $user_info['user_type'] === 'staff' ? $user_info['company_id'] : $usession['sup_user_id'];

	// 		// Prepare indicator data
	// 		$data = [
	// 			'company_id' => $company_id,
	// 			'user_id' => $user_info['user_id'],
	// 			'title' => $title,
	// 			'designation_id' => $designation_id,
	// 			'review_period' => $review_period,
	// 			'year' => date('Y'),
	// 			'added_by' => $usession['sup_user_id'],
	// 			'created_at' => date('Y-m-d H:i:s'),
	// 			'emp_total_rating' => $this->request->getPost('employee_total_rating'),
	// 			'mang_total_rating' => $this->request->getPost('manager_total_rating'),
	// 		];

	// 		$db = \Config\Database::connect();
	// 		$builder = $db->table('ci_performance_indicator');

	// 		if ($builder->insert($data)) {
	// 			$indicator_id = $db->insertID();
	// 			$builderOptions = $db->table('ci_performance_indicator_options');

	// 			// Handle employee data
	// 			$employeeData = $this->request->getPost('employee_data', FILTER_SANITIZE_STRING);
	// 			if (!empty($employeeData)) {
	// 				foreach ($employeeData as $key => $emp_value) {
	// 					$remarks = $this->request->getPost('employee_remarks')[$key];
	// 					$totalRating = $this->request->getPost('employee_total_rating');

	// 					$empData = [
	// 						'company_id' => $company_id,
	// 						'indicator_id' => $indicator_id,
	// 						'indicator_type' => 'employee',
	// 						'indicator_option_id' => $key,
	// 						'indicator_option_value' => $emp_value,
	// 						'remarks' => $remarks,
	// 						'total_rating' => $totalRating
	// 					];

	// 					$builderOptions->insert($empData);
	// 				}
	// 			}

	// 			$managerData = $this->request->getPost('manager_data', FILTER_SANITIZE_STRING);
	// 			if (empty($managerData)) {
	// 				$managerData = [];
	// 				foreach ($competencies as $competency) {
	// 					$managerData[$competency['constants_id']] = '1'; // Default value
	// 				}
	// 			}

	// 			foreach ($managerData as $key => $mang_value) {
	// 				$remarks = $this->request->getPost('manager_remarks')[$key] ?? '';

	// 				$totalRating = $this->request->getPost('manager_total_rating') ?? 0;
	// 				$totalRating = is_numeric($totalRating) ? $totalRating : 0;

	// 				$mangData = [
	// 					'company_id' => $company_id,          // Make sure $company_id is defined
	// 					'indicator_id' => $indicator_id,      // Make sure $indicator_id is defined
	// 					'indicator_type' => 'manager',
	// 					'indicator_option_id' => $key,
	// 					'indicator_option_value' => $mang_value,
	// 					'remarks' => $remarks,
	// 					'total_rating' => $totalRating,
	// 				];

	// 				// Insert data into the database
	// 				try {
	// 					$builderOptions->insert($mangData);
	// 				} catch (\Exception $e) {
	// 					error_log('Failed to insert manager data: ' . $e->getMessage());
	// 					return redirect()->back()->with('error', 'Failed to save manager data.');
	// 				}
	// 			}

	// 			// Success response
	// 			$current_url = $this->request->getServer('HTTP_REFERER') ?? base_url('erp/performance-indicator-list');
	// 			$session->setFlashdata('message', 'Performance saved successfully.');
	// 			return redirect()->to($current_url);
	// 		} else {
	// 			$session->setFlashdata('error', 'Failed to save performance.');
	// 			return redirect()->back()->withInput();
	// 		}
	// 	}

	// 	$session->setFlashdata('error', lang('Main.xin_error_msg'));
	// 	return redirect()->back();
	// }

	public function add_indicator()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getPost()) {
			// Validation rules
			$rules = [
				'title' => [
					'rules' => 'required',
					'errors' => ['required' => lang('Main.xin_error_field_text')]
				],
			];

			if (!$this->validate($rules)) {
				$errors = $validation->getErrors();
				$session->setFlashdata('error', implode('<br>', $errors));
				return redirect()->back()->withInput();
			}

			// Gather basic data
			$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
			$designation_id = $this->request->getPost('designation_id', FILTER_SANITIZE_STRING);
			$review_period = $this->request->getPost('review_period', FILTER_SANITIZE_STRING);

			$UsersModel = new UsersModel();
			$ConstantsModel = new ConstantsModel();

			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			$competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();

			$company_id = $user_info['user_type'] === 'staff' ? $user_info['company_id'] : $usession['sup_user_id'];

			// Prepare indicator data
			$data = [
				'company_id' => $company_id,
				'user_id' => $user_info['user_id'],
				'title' => $title,
				'designation_id' => $designation_id,
				'review_period' => $review_period,
				'year' => date('Y'),
				'added_by' => $usession['sup_user_id'],
				'created_at' => date('Y-m-d H:i:s'),
				'emp_total_rating' => $this->request->getPost('employee_total_rating'),
				'manager_overallRemark' => $this->request->getPost('manager_total_rating'),
			];

			$db = \Config\Database::connect();
			$builder = $db->table('ci_performance_indicator');

			try {
				if ($builder->insert($data)) {
					$indicator_id = $db->insertID();
					$builderOptions = $db->table('ci_performance_indicator_options');

					// Handle employee data
					$employeeData = $this->request->getPost('employee_data');
					if (!empty($employeeData)) {
						foreach ($employeeData as $key => $emp_value) {
							$remarks = $this->request->getPost('employee_remarks')[$key] ?? '';
							$totalRating = $this->request->getPost('employee_total_rating') ?? 0;

							$empData = [
								'company_id' => $company_id,
								'indicator_id' => $indicator_id,
								'indicator_type' => 'employee',
								'indicator_option_id' => $key,
								'indicator_option_value' => $emp_value,
								'remarks' => $remarks,
								'total_rating' => $totalRating
							];

							$builderOptions->insert($empData);
						}
					}

					// Handle manager data
					$managerData = $this->request->getPost('manager_data');
					if (empty($managerData)) {
						foreach ($competencies as $competency) {
							$managerData[$competency['constants_id']] = '1'; // Default value
						}
					}

					foreach ($managerData as $key => $mang_value) {
						$remarks = $this->request->getPost('manager_remarks')[$key] ?? '';
						$totalRating = $this->request->getPost('manager_total_rating') ?? 0;

						$mangData = [
							'company_id' => $company_id,
							'indicator_id' => $indicator_id,
							'indicator_type' => 'manager',
							'indicator_option_id' => $key,
							'indicator_option_value' => $mang_value,
							'remarks' => $remarks,
							'total_rating' => $totalRating,
						];

						$builderOptions->insert($mangData);
					}

					// Success response
					$current_url = $this->request->getServer('HTTP_REFERER') ?? base_url('erp/performance-indicator-list');
					$session->setFlashdata('message', 'Performance saved successfully.');
					return redirect()->to($current_url);
				} else {
					throw new \Exception('Failed to insert indicator data.');
				}
			} catch (\Exception $e) {
				error_log($e->getMessage());
				$session->setFlashdata('error', 'An error occurred while saving performance data.');
				return redirect()->back()->withInput();
			}
		}

		$session->setFlashdata('error', lang('Main.xin_error_msg'));
		return redirect()->back();
	}





	public function update_indicator()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($request->getPost()) {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			// Validation rules
			$rules = [
				'title' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text'),
					],
				],
				'designation_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Employees.xin_employee_error_designation'),
					],
				],
			];

			if (!$this->validate($rules)) {
				$errors = $validation->getErrors();
				$Return['error'] = implode(', ', $errors);
				return $this->output($Return);
			}

			// Sanitize inputs
			$title = filter_var($request->getPost('title'), FILTER_SANITIZE_STRING);
			$designation_id = filter_var($request->getPost('designation_id'), FILTER_SANITIZE_STRING);
			$id = $request->getPost('token');
			$data = [
				'title' => $title,
				'designation_id' => $designation_id,
				'emp_total_rating' => $request->getPost('employee_total_rating'),
				'mang_total_rating' => $request->getPost('manager_total_rating'),
				'manager_overallRemark' => $request->getPost('manager_overall_remark'),
				'updated_by' => $user_info['user_id'],
			];

			$KpiModel = new KpiModel();
			$KpioptionsModel = new KpioptionsModel();

			if ($KpiModel->update($id, $data)) {
				$db = \Config\Database::connect();
				$builderOptions = $db->table('ci_performance_indicator_options');

				// Update employee data
				$employee_data = $request->getPost('employee_data');
				$employee_remarks = $request->getPost('employee_remarks');

				if (is_array($employee_data)) {
					foreach ($employee_data as $key => $tech_value) {
						$empData = [
							'indicator_option_value' => $tech_value,
							'remarks' => $employee_remarks[$key] ?? '',
							'total_rating' => $request->getPost('employee_total_rating'),
						];
						$builderOptions
							->where('indicator_id', $id)
							->where('indicator_type', 'employee')
							->where('indicator_option_id', $key)
							->update($empData);
					}
				}

				// Update manager data
				$manager_data = $request->getPost('manager_data');
				$manager_remarks = $request->getPost('manager_remarks');

				if (is_array($manager_data)) {
					foreach ($manager_data as $key => $org_value) {
						$managerData = [
							'indicator_option_value' => $org_value,
							'remarks' => $manager_remarks[$key] ?? '',
							'total_rating' => $request->getPost('manager_total_rating'),
						];
						$builderOptions
							->where('indicator_id', $id)
							->where('indicator_type', 'manager')
							->where('indicator_option_id', $key)
							->update($managerData);
					}
				}

				$current_url = $request->getServer('HTTP_REFERER') ?? base_url('erp/performance-indicator-list');
				$session->setFlashdata('message', 'Performance Updated successfully.');
				return redirect()->to($current_url);
			} else {
				$session->setFlashdata('error', 'Failed to save performance.');
				return redirect()->back()->withInput();
			}
		} else {
			$session->setFlashdata('error', lang('Main.xin_error_msg'));
			return redirect()->back();
		}
	}


	public function add_appraisal()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'title' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'employee_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_employee_field_error')
					]
				],
				'month_year' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_select_month_field_error')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"title" => $validation->getError('title'),
					"employee_id" => $validation->getError('employee_id'),
					"month_year" => $validation->getError('month_year')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
				$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
				$month_year = $this->request->getPost('month_year', FILTER_SANITIZE_STRING);
				$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'company_id'  => $company_id,
					'title' => $title,
					'employee_id' => $employee_id,
					'remarks' => $remarks,
					'appraisal_year_month' => $month_year,
					'added_by' => $usession['sup_user_id'],
					'created_at' => date('d-m-Y h:i:s')
				];
				$KpaModel = new KpaModel();
				$KpaoptionsModel = new KpaoptionsModel();
				$result = $KpaModel->insert($data);
				$appraisal_id = $KpaModel->insertID();
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					foreach ($this->request->getPost('technical_competencies_value', FILTER_SANITIZE_STRING) as $key => $tech_value) {
						$data_ind = array(
							'company_id' => $company_id,
							'appraisal_id' => $appraisal_id,
							'appraisal_type' => 'technical',
							'appraisal_option_id' => $key,
							'appraisal_option_value' => $tech_value,
						);
						$KpaoptionsModel->insert($data_ind);
					}
					foreach ($this->request->getPost('organizational_competencies_value', FILTER_SANITIZE_STRING) as $ikey => $org_value) {
						$data_org = array(
							'company_id' => $company_id,
							'appraisal_id' => $appraisal_id,
							'appraisal_type' => 'organizational',
							'appraisal_option_id' => $ikey,
							'appraisal_option_value' => $org_value,
						);
						$KpaoptionsModel->insert($data_org);
					}

					$Return['result'] = lang('Success.ci_appraisal_added_msg');
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
	public function update_appraisal()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'title' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'employee_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_employee_field_error')
					]
				],
				'month_year' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_select_month_field_error')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"title" => $validation->getError('title'),
					"employee_id" => $validation->getError('employee_id'),
					"month_year" => $validation->getError('month_year')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$title = $this->request->getPost('title', FILTER_SANITIZE_STRING);
				$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
				$month_year = $this->request->getPost('month_year', FILTER_SANITIZE_STRING);
				$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				$data = [
					'title' => $title,
					'employee_id' => $employee_id,
					'remarks' => $remarks,
					'appraisal_year_month' => $month_year,
				];
				$KpaModel = new KpaModel();
				$KpaoptionsModel = new KpaoptionsModel();
				$result = $KpaModel->update($id, $data);

				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					foreach ($this->request->getPost('technical_competencies_value', FILTER_SANITIZE_STRING) as $key => $tech_value) {
						foreach ($tech_value as $option_id => $star_data) {
							$data_ind = array(
								'appraisal_option_id' => $option_id,
								'appraisal_option_value' => $star_data,
							);
							$KpaoptionsModel->update($key, $data_ind);
						}
					}
					foreach ($this->request->getPost('organizational_competencies_value', FILTER_SANITIZE_STRING) as $ikey => $org_value) {
						foreach ($org_value as $org_option_id => $star_data_org) {
							$data_org = array(
								'appraisal_option_id' => $org_option_id,
								'appraisal_option_value' => $star_data_org,
							);
							$KpaoptionsModel->update($ikey, $data_org);
						}
					}
					$Return['result'] = lang('Success.ci_appraisal_updated_msg');
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
	// delete record
	public function delete_indicator()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session($config);
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$KpiModel = new KpiModel();
			$result = $KpiModel->where('performance_indicator_id', $id)->delete($id);
			if ($result == TRUE) {
				$MainModel = new MainModel();
				$MainModel->delete_indicator_options($id);
				$Return['result'] = lang('Success.ci_indicator_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}
	// delete record
	public function delete_appraisal()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session($config);
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$KpaModel = new KpaModel();
			$result = $KpaModel->where('performance_appraisal_id', $id)->delete($id);
			if ($result == TRUE) {
				$MainModel = new MainModel();
				$MainModel->delete_appraisal_options($id);
				$Return['result'] = lang('Success.ci_appraisal_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function apply_performance()
	{
		$session = \Config\Services::session($config);
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$invoice_id = udecode($request->uri->getSegment(3));
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$data['title'] = 'Apply Performances';

		$data['breadcrumbs'] = 'Apply Performances';

		$data['subview'] = view('erp/constants/apply_performance', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function view_performance($performances_id)
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$KpiModel = new KpiModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$kpi_performance = $KpiModel->where('performance_indicator_id', $performances_id)->first();

		$data['title'] = 'Edit Performances';
		$data['breadcrumbs'] = 'Edit Performances';
		$data['path_url'] = '';
		$data['performances'] = $kpi_performance;

		$data['subview'] = view('erp/talent/view_performance', $data);
		// $data['subview'] = view('erp/talent/indicator_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}



	public function filter_performance()
	{

		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$KpiModel = new KpiModel();
		$cache = \Config\Services::cache();
		$db = \Config\Database::connect();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$year = $this->request->getGet('year_id');
		$period = $this->request->getGet('period');
		$employee_id = $this->request->getGet('employee_id');

		// Get all performance indicators
		$get_data = $KpiModel->where('company_id', $user_info['company_id'])->orderBy('performance_indicator_id', 'ASC')->findAll();

		// Filter data based on the conditions
		if ($year) {
			$get_data = array_filter($get_data, function ($item) use ($year) {
				return $item['year'] == $year;
			});
		}

		if ($period) {
			$get_data = array_filter($get_data, function ($item) use ($period) {
				return $item['review_period'] == $period;
			});
		}
		if ($employee_id) {
			$get_data = array_filter($get_data, function ($item) use ($employee_id) {
				return $item['user_id'] == $employee_id;
			});
		}

		if ($user_info['user_type'] == 'staff') {
			$user_id = $user_info['user_id'];
			$builder = $db->table('ci_erp_users_details');
			$builder->select('ci_erp_users.first_name, ci_erp_users.last_name, ci_erp_users.user_id');
			$builder->join('ci_erp_users', 'ci_erp_users.user_id = ci_erp_users_details.user_id');
			$builder->where('ci_erp_users_details.reporting_manager', $user_id);
			$query = $builder->get();
			$result = $query->getResultArray();
			$user_ids = array_column($result, 'user_id');
			$user_ids[] = $user_info['user_id'];

			$get_data = array_filter($get_data, function ($item) use ($user_ids) {
				return in_array($item['user_id'], $user_ids);
			});
		}

		// Initialize HTML table
		$html = '<thead>
                <tr>
                    <th>S. No</th>
                    <th>Year</th>
                    <th>Review Period</th>
                    <th>Rating</th>
                    <th>Manager Rating</th>
                    <th>Manager Remark</th>
                    <th>Updated By</th>
                    <th>Edit</th>
                </tr>
            </thead><tbody>';

		if (!empty($get_data)) {
			$i = 1;
			foreach ($get_data as $value) {
				$rating = $value['emp_total_rating'];
				$ManagerRating = isset($value['mang_total_rating']) ? $value['mang_total_rating'] : null;
				$managerRemark = isset($value['manager_overallRemark']) ? $value['manager_overallRemark'] : '-';
				$updatedBy = getClientname($value['updated_by']);

				// Rating logic for employee and manager
				$ratingLabel = $this->getRatingLabel($rating);
				$ManagerRatingLabel = $this->getRatingLabel($ManagerRating);

				// Calculate end date for the review period
				$createdDate = date('Y-m-d', strtotime($value['created_at']));
				$endDate = $this->calculateEndDate($value['review_period'], $createdDate);
				$currentDate = date('Y-m-d');
				$showButton = ($user_info['user_type'] == 'company' || ((strtotime($endDate) - strtotime($currentDate)) <= (2 * 24 * 60 * 60) && strtotime($currentDate) <= strtotime($endDate)));

				// Append the row to HTML
				$html .= '<tr>
                        <td>' . $i++ . '</td>
                        <td>' . $value['year'] . '</td>
                        <td>' . $value['review_period'] . '</td>
                        <td>' . $rating . ' ' . $ratingLabel . '</td>
                        <td>' . ($ManagerRating === null ? '-' : $ManagerRating . ' ' . $ManagerRatingLabel) . '</td>
                        <td>' . $managerRemark . '</td>
                        <td>' . $updatedBy . '</td>
                        <td>
                            <a href="' . ($showButton ? base_url('erp/view-performances/' . $value['performance_indicator_id']) : '#') . '"
                               data-bs-toggle="' . ($showButton ? 'tooltip' : '') . '"
                               title="' . ($showButton ? 'View Details' : '') . '">
                                <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" ' . (!$showButton ? 'disabled' : '') . '>
                                    <i class="feather icon-eye"></i>
                                </button>
                            </a>
                        </td>
                    </tr>';
			}
		} else {
			// No data found row
			$html .= '<tr><td colspan="8" style="text-align: center;">No data found</td></tr>';
		}

		$html .= '</tbody>'; // Close tbody

		return $html;
	}


	// Helper function to get rating label
	private function getRatingLabel($rating)
	{
		if ($rating >= 0 && $rating < 2) {
			return '<span class="project-status-2" style="color: red;">Poor</span>';
		} elseif ($rating >= 2 && $rating < 3) {
			return '<span class="project-status-2" style="color: orange;">Average</span>';
		} elseif ($rating >= 3 && $rating < 4) {
			return '<span class="project-status-2" style="color: blue;">Good</span>';
		} elseif ($rating >= 4 && $rating <= 5) {
			return '<span class="project-status-2" style="color: green;">Excellent</span>';
		} else {
			return '<span class="project-status-2" style="color: gray;">Invalid</span>';
		}
	}

	// Helper function to calculate the end date for a review period
	private function calculateEndDate($reviewPeriod, $createdDate)
	{
		switch ($reviewPeriod) {
			case 'week':
				return date('Y-m-d', strtotime($createdDate . ' +6 days'));
			case 'month':
				return date('Y-m-d', strtotime($createdDate . ' +1 month -1 day'));
			case 'quarter':
				return date('Y-m-d', strtotime($createdDate . ' +3 months -1 day'));
			case 'half_yearly':
				return date('Y-m-d', strtotime($createdDate . ' +6 months -1 day'));
			case 'yearly':
				return date('Y-m-d', strtotime($createdDate . ' +1 year -1 day'));
			default:
				return $createdDate;
		}
	}
}
