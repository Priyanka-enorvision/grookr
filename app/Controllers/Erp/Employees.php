<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;


use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\MainModel;
use App\Models\DesignationModel;
use App\Models\CountryModel;
use App\Models\StaffdetailsModel;
use App\Models\ContractModel;
use App\Models\UserdocumentsModel;
use App\Models\EmailtemplatesModel;
use App\Libraries\S3ClientLibrary;

class Employees extends BaseController
{

	public function index()
	{

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$s3Library = new S3ClientLibrary();
		$files = $s3Library->listObjects();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('staff2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');
		$data['files'] = $files;
		$data['subview'] = view('erp/employees/staff_list', $data);
		return view('erp/layout/layout_main', $data);
	}
	public function staff_grid()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$pager = \Config\Services::pager();
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
			if (!in_array('staff2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_employees') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employees_grid';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_employees');
		$data['subview'] = view('erp/employees/staff_grid', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function staff_dashboard()
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
		$data['title'] = lang('Dashboard.xin_hrm_dashboard') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'assets';
		$data['breadcrumbs'] = lang('Dashboard.xin_hrm_dashboard');

		$data['subview'] = view('erp/employees/staff_dashboard', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function staff_details()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
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
			if (!in_array('staff4', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$iuser_id = udecode($request->getUri()->getSegment(3));
		$user_val = $UsersModel->where('user_id', $iuser_id)->first();
		if (!$user_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Employees.xin_employee_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employee_details';
		$data['breadcrumbs'] = lang('Employees.xin_employee_details');

		$data['subview'] = view('erp/employees/staff_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// list

	public function employees_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$config = new \Config\Encryption();
		$config->key = 'aBigsecret_ofAtleast32Characters';
		$config->driver = 'OpenSSL';
		$encrypter = \Config\Services::encrypter($config);

		$RolesModel = new RolesModel();
		$SystemModel = new SystemModel();
		$CountryModel = new CountryModel();
		$DesignationModel = new DesignationModel();
		$StaffdetailsModel = new StaffdetailsModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$staff = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->orderBy('user_id', 'ASC')->findAll();
		} else {
			$staff = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->orderBy('user_id', 'ASC')->findAll();
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data = [];

		$s3Library = new S3ClientLibrary();
		$files = $s3Library->listObjects();

		// Fetch all uploaded files from S3

		foreach ($staff as $r) {
			if (in_array('staff4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/employee-details') . '/' . uencode($r['user_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-edit"></i></button></a></span>';
			} else {
				$edit = '';
			}
			if (in_array('staff5', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['user_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$role = $RolesModel->where('role_id', $r['user_role_id'])->first();
			if ($r['is_active'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Main.xin_employees_active') . '</span>';
			} else {
				$status = '<span class="badge badge-light-danger">' . lang('Main.xin_employees_inactive') . '</span>';
			}
			if ($r['gender'] == 1) {
				$gender = lang('Main.xin_gender_male');
			} else {
				$gender = lang('Main.xin_gender_female');
			}
			$role_name = $role['role_name'];
			$country_info = $CountryModel->where('country_id', $r['country'])->first();
			if (!empty($country_info)) {
				$country_name = $country_info['country_name'];
			} else {
				$country_name = '---';
			}
			$name = $r['first_name'] . ' ' . $r['last_name'];
			// Designation
			$employee_detail = $StaffdetailsModel->where('user_id', $r['user_id'])->first();
			$employee_id = $employee_detail['employee_id'];

			$idesignations = $DesignationModel->where('designation_id', $employee_detail['designation_id'])->first();
			if (!empty($idesignations)) {
				$designation_name = $idesignations['designation_name'];
			} else {
				$designation_name = '---';
			}

			$profile_photos = [];

			$default_photo = base_url() . 'uploads/users/default/default_profile.jpg';

			foreach ($files as $file) {
				$url = 'https://grookr.s3.us-west-2.amazonaws.com/' . $file['Key'];

				if (preg_match('/uploads\/(\d+)_/', $url, $matches)) {
					$numericId = $matches[1];
				} else {
					continue;
				}

				if ($numericId == $employee_id) {
					$profile_photos[] = $url;
				}
			}

			if (empty($profile_photos)) {
				$profile_photos[] = $default_photo;
			}

			$uname = '<a href="' . site_url('erp/employee-details') . '/' . uencode($r['user_id']) . '"><div class="d-inline-block align-middle">';

			foreach ($profile_photos as $photo) {
				$uname .= '<img src="' . $photo . '" alt="user image" class="img-radius align-top m-r-15" style="width:40px;height: 40px;">';
			}

			$uname .= '<div class="d-inline-block">
							<h6 class="m-b-0">' . $name . '</h6>
							<p class="m-b-0">' . $r['email'] . '</p>
						</div>
					</div></a>';
			$combhr = $edit . $delete;

			$links = $uname;

			$data[] = array(
				$links,
				$designation_name,
				$r['contact_number'],
				$gender,
				$country_name,
				$role_name,
				$status,
				$combhr
			);
		}

		$output = [
			//"draw" => $draw,
			"data" => $data
		];
		echo json_encode($output);
		exit();
	}



	// record list
	public function allowances_list($id = null)
	{
		$session = \Config\Services::session();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$ContractModel = new ContractModel();

		$usession = $session->get('sup_username');

		$get_data = $ContractModel
			->where('user_id', $id)
			->where('salay_type', 'allowances')
			->orderBy('contract_option_id', 'ASC')
			->findAll();

		$data = [];
		foreach ($get_data as $r) {
			$edit = '<span data-toggle="tooltip" title="' . lang('Main.xin_edit') . '">
						<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target=".view-modal-data" data-field_type="allowances" data-field_id="' . uencode($r['contract_option_id']) . '">
							<i class="feather icon-edit"></i>
						</button>
					</span>';

			$delete = '<span data-toggle="tooltip" title="' . lang('Main.xin_delete') . '">
						<button type="button" class="btn btn-sm btn-danger delete" data-toggle="modal" data-target=".delete-modal" data-field_type="all_allowances" data-record-id="' . uencode($r['contract_option_id']) . '">
							<i class="feather icon-trash-2"></i>
						</button>
					</span>';

			$is_fixed = $r['is_fixed'] == 1
				? lang('Employees.xin_title_tax_fixed')
				: lang('Employees.xin_title_tax_percent');

			$contract_tax_option = match ((int)$r['contract_tax_option']) {
				1 => lang('Employees.xin_salary_allowance_non_taxable'),
				2 => lang('Employees.xin_fully_taxable'),
				default => lang('Employees.xin_partially_taxable'),
			};

			$data[] = [
				$r['option_title'],
				$r['contract_amount'],
				$contract_tax_option,
				$is_fixed,
				$edit . $delete
			];
		}

		return $this->response->setJSON(['data' => $data]);
	}

	// record list
	public function commissions_list($id = null)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$ContractModel = new ContractModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $ContractModel->where('user_id', $id)->where('salay_type', 'commissions')->orderBy('contract_option_id', 'ASC')->findAll();
		$data = [];

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_type="commissions" data-field_id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-edit"></i></button></span>';
			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="all_commissions" data-record-id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-trash-2"></i></button></span>';

			if ($r['is_fixed'] == 0) {
				$is_fixed = lang('Employees.xin_title_tax_fixed');
			} else {
				$is_fixed = lang('Employees.xin_title_tax_percent');
			}
			if ($r['contract_tax_option'] == 0) {
				$contract_tax_option = lang('Employees.xin_salary_allowance_non_taxable');
			} else if ($r['contract_tax_option'] == 1) {
				$contract_tax_option = lang('Employees.xin_fully_taxable');
			} else {
				$contract_tax_option = lang('Employees.xin_partially_taxable');
			}

			$combhr = $edit . $delete;

			$salary_option = $r['option_title'];
			// $salary_option = '
			// 	'.$r['option_title'].'
			// 	<div class="overlay-edit">
			// 		'.$combhr.'
			// 	</div>
			// ';
			$data[] = array(
				$salary_option,
				$r['contract_amount'],
				$contract_tax_option,
				$is_fixed,
				$combhr
			);
		}
		$output = [
			//"draw" => $draw,
			"data" => $data
		];
		echo json_encode($output);
		exit();
	}
	// record list
	public function statutory_list($id = null)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$ContractModel = new ContractModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $ContractModel->where('user_id', $id)->where('salay_type', 'statutory')->orderBy('contract_option_id', 'ASC')->findAll();

		$data = [];

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_type="statutory" data-field_id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-edit"></i></button></span>';
			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="all_statutory_deductions" data-record-id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-trash-2"></i></button></span>';

			if ($r['is_fixed'] == 0) {
				$is_fixed = lang('Employees.xin_title_tax_fixed');
			} else {
				$is_fixed = lang('Employees.xin_title_tax_percent');
			}
			$combhr = $edit . $delete;
			$salary_option = $r['option_title'];
			// $salary_option = '
			// 	'.$r['option_title'].'
			// 	<div class="overlay-edit">
			// 		'.$combhr.'
			// 	</div>
			// ';
			$data[] = array(
				$salary_option,
				$r['contract_amount'],
				$is_fixed,
				$combhr
			);
		}
		$output = [
			//"draw" => $draw,
			"data" => $data
		];
		echo json_encode($output);
		exit();
	}
	// record list
	public function other_payments_list($id = null)
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		// $id = $request->getUri()->getSegment(4);
		$ContractModel = new ContractModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $ContractModel->where('user_id', $id)->where('salay_type', 'other_payments')->orderBy('contract_option_id', 'ASC')->findAll();
		$data = [];

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_type="other_payments" data-field_id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-edit"></i></button></span>';
			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="all_other_payments" data-record-id="' . uencode($r['contract_option_id']) . '"><i class="feather icon-trash-2"></i></button></span>';

			if ($r['is_fixed'] == 0) {
				$is_fixed = lang('Employees.xin_title_tax_fixed');
			} else {
				$is_fixed = lang('Employees.xin_title_tax_percent');
			}
			if ($r['contract_tax_option'] == 0) {
				$contract_tax_option = lang('Employees.xin_salary_allowance_non_taxable');
			} else if ($r['contract_tax_option'] == 1) {
				$contract_tax_option = lang('Employees.xin_fully_taxable');
			} else {
				$contract_tax_option = lang('Employees.xin_partially_taxable');
			}

			$combhr = $edit . $delete;

			$salary_option = $r['option_title'];
			// $salary_option = '
			// 	'.$r['option_title'].'
			// 	<div class="overlay-edit">
			// 		'.$combhr.'
			// 	</div>
			// ';
			$data[] = array(
				$salary_option,
				$r['contract_amount'],
				$contract_tax_option,
				$is_fixed,
				$combhr
			);
		}
		$output = [
			//"draw" => $draw,
			"data" => $data
		];
		echo json_encode($output);
		exit();
	}
	// record list
	public function user_documents_list($id = null)
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		// $id = $request->getUri()->getSegment(4);
		$UserdocumentsModel = new UserdocumentsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $UserdocumentsModel->where('user_id', $id)->orderBy('document_id', 'ASC')->findAll();
		$data = [];

		foreach ($get_data as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_type="document" data-field_id="' . uencode($r['document_id']) . '"><i class="feather icon-edit"></i></button></span>';
			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="document" data-record-id="' . uencode($r['document_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			$download_link = '<a href="' . site_url() . 'download?type=documents&filename=' . uencode($r['document_file']) . '">' . lang('Main.xin_download') . '</a>';
			$combhr = $edit . $delete;
			$salary_option = '
				' . $r['document_name'] . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>
			';
			$data[] = array(
				$salary_option,
				$r['document_type'],
				$download_link
			);
		}
		$output = [
			//"draw" => $draw,
			"data" => $data
		];
		echo json_encode($output);
		exit();
	}
	// |||add record|||

	public function add_employee()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			$UsersModel = new UsersModel();
			$SystemModel = new SystemModel();

			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

			// Validation Rules
			$validation->setRules([
				'first_name' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'last_name' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'employee_id' => 'required',
				'office_shift_id' => 'required',
				'email' => 'required|valid_email',
				'department_id' => 'required',
				'designation_id' => 'required',
				'username' => 'required|min_length[6]|is_unique[ci_erp_users.username]',
				'password' => 'required|min_length[6]',
				'contact_number' => 'required|min_length[6]|max_length[12]',
				'role' => 'required',
				'basic_salary' => 'required',
				'salay_type' => 'required', // Corrected from "salay_type"
			]);

			if (!$validation->withRequest($this->request)->run()) {
				foreach ($validation->getErrors() as $error) {
					$Return['error'] = $error;
					return  $this->response->setJSON($Return);
				}
			}

			// Fetch Form Data
			$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
			$first_name = $this->request->getPost('first_name', FILTER_SANITIZE_STRING);
			$last_name = $this->request->getPost('last_name', FILTER_SANITIZE_STRING);
			$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
			$username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
			$password = $this->request->getPost('password', FILTER_SANITIZE_STRING);
			$contact_number = $this->request->getPost('contact_number', FILTER_SANITIZE_STRING);
			$role = $this->request->getPost('role', FILTER_SANITIZE_STRING);
			$gender = $this->request->getPost('gender', FILTER_SANITIZE_STRING);
			$office_shift_id = $this->request->getPost('office_shift_id', FILTER_SANITIZE_STRING);
			$department_id = $this->request->getPost('department_id', FILTER_SANITIZE_STRING);
			$designation_id = $this->request->getPost('designation_id', FILTER_SANITIZE_STRING);
			$basic_salary = $this->request->getPost('basic_salary', FILTER_SANITIZE_STRING);
			$hourly_rate = $this->request->getPost('hourly_rate', FILTER_SANITIZE_STRING);
			$salary_type = $this->request->getPost('salay_type', FILTER_SANITIZE_STRING);

			// Password Hashing
			$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

			// **Optional File Upload**
			$file_name = '';
			$file = $this->request->getFile('file');

			if ($file && $file->isValid() && !$file->hasMoved()) {
				try {
					$file_name = $employee_id . '_' . $file->getClientName();
					$sanitized_file_name = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file_name);
					$tempFilePath = WRITEPATH . 'uploads/' . $sanitized_file_name;
					$file->move(WRITEPATH . 'uploads/', $sanitized_file_name);

					// Upload to S3
					$s3Library = new S3ClientLibrary();
					$s3Result = $s3Library->uploadFile($tempFilePath, 'uploads/' . $sanitized_file_name);

					if (file_exists($tempFilePath)) {
						unlink($tempFilePath);
					}

					if ($s3Result && isset($s3Result['url'])) {
						$file_name = $s3Result['url'];
						log_message('info', 'File uploaded to S3: ' . $file_name);
					}
				} catch (\Exception $e) {
					log_message('error', 'File upload error: ' . $e->getMessage());
				}
			}

			// Prepare Data for Insert
			$data = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'user_type' => 'staff',
				'username' => $username,
				'password' => $password_hash,
				'contact_number' => $contact_number,
				'country' => 99,
				'user_role_id' => $role,
				'profile_photo' => $file_name,
				'gender' => $gender,
				'company_name' => $user_info['company_name'],
				'is_active' => 1,
				'company_id' => $company_id,
				'created_at' => date('Y-m-d H:i:s'),
			];

			$UsersModel = new UsersModel();
			$StaffdetailsModel = new StaffdetailsModel();


			// Check if Email Exists
			if ($UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->where('email', $email)->first() === null) {
				$UsersModel->insert($data);
				$user_id = $UsersModel->insertID();

				// Employee Details Data
				$data2 = [
					'user_id' => $user_id,
					'employee_id' => $employee_id,
					'department_id' => $department_id,
					'designation_id' => $designation_id,
					'office_shift_id' => $office_shift_id,
					'date_of_joining' => date('Y-m-d'),
					'basic_salary' => $basic_salary,
					'hourly_rate' => $hourly_rate,
					'salay_type' => $salary_type,
					'role_description' => 'Enter role description here..',
					'bio' => 'Enter staff bio here..',
					'created_at' => date('Y-m-d H:i:s'),
				];
				$StaffdetailsModel->insert($data2);
				$EmailtemplatesModel = new EmailtemplatesModel();
				$xin_system = $SystemModel->where('setting_id', 1)->first();
				if ($xin_system['enable_email_notification'] == 1) {
					// Send mail start
					$itemplate = $EmailtemplatesModel->where('template_id', 5)->first();
					$isubject = $itemplate['subject'];
					$ibody = html_entity_decode($itemplate['message']);
					$fbody = str_replace(array("{site_name}", "{user_password}", "{user_username}", "{site_url}"), array($user_info['company_name'], $password, $username, site_url()), $ibody);
					timehrm_mail_data($user_info['email'], $user_info['company_name'], $email, $isubject, $fbody);
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				$Return['result'] = lang('Employees.xin_success_add_employee');
			} else {
				$Return['error'] = lang('Main.xin_already_exist_error_email');
			}

			return $this->response->setJSON($Return);
		}
	}

	// contract options
	// |||add record|||
	public function add_allowance()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'contract_tax_option' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'is_fixed' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'option_title' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'contract_amount' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"contract_tax_option" => $validation->getError('contract_tax_option'),
					"is_fixed" => $validation->getError('is_fixed'),
					"option_title" => $validation->getError('option_title'),
					"contract_amount" => $validation->getError('contract_amount')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$contract_tax_option = $this->request->getPost('contract_tax_option', FILTER_SANITIZE_STRING);
				$is_fixed = $this->request->getPost('is_fixed', FILTER_SANITIZE_STRING);
				$option_title = $this->request->getPost('option_title', FILTER_SANITIZE_STRING);
				$contract_amount = $this->request->getPost('contract_amount', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$UsersModel = new UsersModel();
				$ContractModel = new ContractModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'user_id' => $id,
					'salay_type' => 'allowances',
					'contract_tax_option' => $contract_tax_option,
					'is_fixed' => $is_fixed,
					'option_title' => $option_title,
					'contract_amount' => $contract_amount
				];
				$result = $ContractModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.employee_set_allowance_success');
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
	// |||edit record|||
	public function update_allowance()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if (!$this->request->isAJAX()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
		$Return['csrf_hash'] = csrf_hash();

		// Validate input data
		$rules = [
			'contract_tax_option' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),

				]
			],
			'is_fixed' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),
				]
			],
			'option_title' => [
				'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),
					'regex_match' => lang('Main.xin_error_field_text')
				]
			],
			'contract_amount' => [
				'rules' => 'required|numeric',
				'errors' => [
					'required' => lang('Main.xin_error_field_text'),
					'numeric' => lang('Main.xin_error_field_numeric')
				]
			]
		];

		if (!$this->validate($rules)) {
			$errors = $validation->getErrors();
			$Return['error'] = reset($errors); // Return first error
			return $this->response->setJSON($Return);
		}

		try {
			$id = udecode($this->request->getPost('token'));
			if (!$id) {
				throw new \RuntimeException('Invalid ID');
			}

			$data = [
				'salay_type' => 'allowances',
				'contract_tax_option' => $this->request->getPost('contract_tax_option'),
				'is_fixed' => $this->request->getPost('is_fixed'),
				'option_title' => $this->request->getPost('option_title'),
				'contract_amount' => $this->request->getPost('contract_amount')
			];

			$ContractModel = new ContractModel();
			if ($ContractModel->update($id, $data)) {
				$Return['result'] = lang('Success.employee_updated_allowance_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		} catch (\Exception $e) {
			$Return['error'] = $e->getMessage();
		}

		$Return['csrf_hash'] = csrf_hash();
		return $this->response->setJSON($Return);
	}
	// |||add record|||
	public function add_commissions()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'contract_tax_option' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'is_fixed' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'option_title' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'contract_amount' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"contract_tax_option" => $validation->getError('contract_tax_option'),
					"is_fixed" => $validation->getError('is_fixed'),
					"option_title" => $validation->getError('option_title'),
					"contract_amount" => $validation->getError('contract_amount')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$contract_tax_option = $this->request->getPost('contract_tax_option', FILTER_SANITIZE_STRING);
				$is_fixed = $this->request->getPost('is_fixed', FILTER_SANITIZE_STRING);
				$option_title = $this->request->getPost('option_title', FILTER_SANITIZE_STRING);
				$contract_amount = $this->request->getPost('contract_amount', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$UsersModel = new UsersModel();
				$ContractModel = new ContractModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'user_id' => $id,
					'salay_type' => 'commissions',
					'contract_tax_option' => $contract_tax_option,
					'is_fixed' => $is_fixed,
					'option_title' => $option_title,
					'contract_amount' => $contract_amount
				];
				$result = $ContractModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.employee_set_commission_success');
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
	// |||add record|||
	public function update_commission()
	{
		// Verify AJAX request
		if (!$this->request->isAJAX()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$response = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		$session = session();
		$usession = $session->get('sup_username');

		// Enhanced validation rules
		$rules = [
			'contract_tax_option' => 'required|in_list[1,2,3]',
			'is_fixed' => 'required|in_list[1,2]',
			'option_title' => 'required|min_length[2]|max_length[100]',
			'contract_amount' => 'required|numeric',
			'token' => 'required'
		];

		$messages = [
			'option_title' => [
				'required' => lang('Main.xin_error_field_text'),
				'min_length' => lang('Main.xin_error_min_length'),
				'max_length' => lang('Main.xin_error_max_length')
			],
			'contract_amount' => [
				'numeric' => lang('Main.xin_error_field_numeric')
			]
		];

		if (!$this->validate($rules, $messages)) {
			$response['error'] = implode(' ', $this->validator->getErrors());
			return $this->response->setJSON($response);
		}

		try {
			$id = udecode($this->request->getPost('token'));
			if (!$id) {
				throw new \RuntimeException(lang('Main.xin_error_invalid_id'));
			}

			$data = [
				'salay_type' => 'commissions',
				'contract_tax_option' => $this->request->getPost('contract_tax_option'),
				'is_fixed' => $this->request->getPost('is_fixed'),
				'option_title' => $this->request->getPost('option_title'),
				'contract_amount' => $this->request->getPost('contract_amount'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$ContractModel = new ContractModel();
			$result = $ContractModel->update($id, $data);

			if ($result) {
				$response['result'] = lang('Success.employee_update_commission_success');
				log_message('info', "Commission updated - ID: {$id} by User: {$usession['sup_user_id']}");
			} else {
				$response['error'] = lang('Main.xin_no_changes_made');
			}
		} catch (\Exception $e) {
			log_message('error', 'Commission update error: ' . $e->getMessage());
			$response['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($response);
	}
	// |||add record|||
	public function add_statutory()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'is_fixed' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'option_title' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'contract_amount' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"is_fixed" => $validation->getError('is_fixed'),
					"option_title" => $validation->getError('option_title'),
					"contract_amount" => $validation->getError('contract_amount')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$is_fixed = $this->request->getPost('is_fixed', FILTER_SANITIZE_STRING);
				$option_title = $this->request->getPost('option_title', FILTER_SANITIZE_STRING);
				$contract_amount = $this->request->getPost('contract_amount', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$UsersModel = new UsersModel();
				$ContractModel = new ContractModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'user_id' => $id,
					'salay_type' => 'statutory',
					'contract_tax_option' => 0,
					'is_fixed' => $is_fixed,
					'option_title' => $option_title,
					'contract_amount' => $contract_amount
				];
				$result = $ContractModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.employee_set_statutory_deduction_success');
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
	// |||add record|||
	public function update_statutory()
	{
		// Verify AJAX request
		if (!$this->request->isAJAX()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$response = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		$session = session();
		$usession = $session->get('sup_username');

		// Enhanced validation rules
		$rules = [
			'is_fixed' => 'required|in_list[1,2]',
			'option_title' => 'required|min_length[2]|max_length[100]',
			'contract_amount' => 'required|numeric',
			'token' => 'required'
		];

		$messages = [
			'option_title' => [
				'required' => lang('Main.xin_error_field_text'),
				'min_length' => lang('Main.xin_error_min_length'),
				'max_length' => lang('Main.xin_error_max_length')
			],
			'contract_amount' => [
				'numeric' => lang('Main.xin_error_field_numeric')
			]
		];

		if (!$this->validate($rules, $messages)) {
			$response['error'] = implode(' ', $this->validator->getErrors());
			return $this->response->setJSON($response);
		}

		try {
			$id = udecode($this->request->getPost('token'));
			if (!$id) {
				throw new \RuntimeException(lang('Main.xin_error_invalid_id'));
			}

			$data = [
				'salay_type' => 'statutory',
				'is_fixed' => $this->request->getPost('is_fixed'),
				'option_title' => $this->request->getPost('option_title'),
				'contract_amount' => $this->request->getPost('contract_amount'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$ContractModel = new ContractModel();
			$result = $ContractModel->update($id, $data);

			if ($result) {
				$response['result'] = lang('Success.employee_update_statutory_deduction_success');
				log_message('info', "Statutory deduction updated - ID: {$id} by User: {$usession['sup_user_id']}");
			} else {
				$response['error'] = lang('Main.xin_no_changes_made');
			}
		} catch (\Exception $e) {
			log_message('error', 'Statutory deduction update error: ' . $e->getMessage());
			$response['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($response);
	}
	// |||add record|||
	public function add_otherpayment()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'contract_tax_option' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'is_fixed' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'option_title' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'contract_amount' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"contract_tax_option" => $validation->getError('contract_tax_option'),
					"is_fixed" => $validation->getError('is_fixed'),
					"option_title" => $validation->getError('option_title'),
					"contract_amount" => $validation->getError('contract_amount')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$contract_tax_option = $this->request->getPost('contract_tax_option', FILTER_SANITIZE_STRING);
				$is_fixed = $this->request->getPost('is_fixed', FILTER_SANITIZE_STRING);
				$option_title = $this->request->getPost('option_title', FILTER_SANITIZE_STRING);
				$contract_amount = $this->request->getPost('contract_amount', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$UsersModel = new UsersModel();
				$ContractModel = new ContractModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'user_id' => $id,
					'salay_type' => 'other_payments',
					'contract_tax_option' => $contract_tax_option,
					'is_fixed' => $is_fixed,
					'option_title' => $option_title,
					'contract_amount' => $contract_amount
				];
				$result = $ContractModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.employee_set_reimbursement_success');
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
	// |||add record|||
	public function update_other_payments()
	{
		// Verify AJAX request
		if (!$this->request->isAJAX()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$response = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		$session = session();
		$usession = $session->get('sup_username');

		// Enhanced validation rules
		$rules = [
			'contract_tax_option' => 'required|in_list[1,2,3]',
			'is_fixed' => 'required|in_list[1,2]',
			'option_title' => 'required|min_length[2]|max_length[100]',
			'contract_amount' => 'required|numeric',
			'token' => 'required'
		];

		$messages = [
			'option_title' => [
				'required' => lang('Main.xin_error_field_text'),
				'min_length' => lang('Main.xin_error_min_length'),
				'max_length' => lang('Main.xin_error_max_length')
			],
			'contract_amount' => [
				'numeric' => lang('Main.xin_error_field_numeric')
			]
		];

		if (!$this->validate($rules, $messages)) {
			$response['error'] = implode(' ', $this->validator->getErrors());
			return $this->response->setJSON($response);
		}

		try {
			$id = udecode($this->request->getPost('token'));
			if (!$id) {
				throw new \RuntimeException(lang('Main.xin_error_invalid_id'));
			}

			$data = [
				'salay_type' => 'other_payments',
				'contract_tax_option' => $this->request->getPost('contract_tax_option'),
				'is_fixed' => $this->request->getPost('is_fixed'),
				'option_title' => $this->request->getPost('option_title'),
				'contract_amount' => $this->request->getPost('contract_amount'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			$ContractModel = new ContractModel();
			$result = $ContractModel->update($id, $data);

			if ($result) {
				$response['result'] = lang('Success.employee_update_reimbursement_success');
				log_message('info', "Other payment updated - ID: {$id} by User: {$usession['sup_user_id']}");
			} else {
				$response['error'] = lang('Main.xin_no_changes_made');
			}
		} catch (\Exception $e) {
			log_message('error', 'Other payment update error: ' . $e->getMessage());
			$response['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($response);
	}
	// |||add record|||
	public function add_document()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'add_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'document_name' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => 'documnent name invalid'
					]
				],
				'document_type' => [
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => 'documnent type invalid'
					]
				],
				'document_file' => [
					'rules' => 'uploaded[document_file]|mime_in[document_file,image/jpg,image/jpeg,image/gif,image/png]|max_size[document_file,5120]',
					'errors' => [
						'uploaded' => lang('Main.xin_error_field_text'),
						'mime_in' => 'wrong size'
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"document_name" => $validation->getError('document_name'),
					"document_type" => $validation->getError('document_type'),
					"document_file" => $validation->getError('document_file')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				// upload file
				$document_file = $this->request->getFile('document_file');
				$file_name = $document_file->getName();
				$document_file->move('public/uploads/documents/');

				$document_name = $this->request->getPost('document_name', FILTER_SANITIZE_STRING);
				$document_type = $this->request->getPost('document_type', FILTER_SANITIZE_STRING);
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
					'user_id' => $id,
					'document_name' => $document_name,
					'document_type' => $document_type,
					'document_file' => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$UserdocumentsModel = new UserdocumentsModel();
				$result = $UserdocumentsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.employee_set_document_success');
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
	// |||edit record|||
	public function update_document()
	{

		// Verify AJAX request
		if (!$this->request->isAJAX()) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}

		$response = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		$session = session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			$response['error'] = lang('Main.xin_session_expired');
			return $this->response->setJSON($response);
		}

		// Validation rules
		$rules = [
			'document_name' => 'required',
			'document_type' => 'required',
			'document_file' => [
				'rules' => 'max_size[document_file,5120]|mime_in[document_file,image/jpg,image/jpeg,image/png,image/gif,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]',
				'errors' => [
					'max_size' => lang('Employees.xin_error_file_too_large'),
					'mime_in' => lang('Employees.xin_error_invalid_file_type')
				]
			]
		];

		if (!$this->validate($rules)) {
			$response['error'] = implode(' ', $this->validator->getErrors());
			return $this->response->setJSON($response);
		}

		try {
			$id = udecode($this->request->getPost('token'));
			if (!$id) {
				throw new \RuntimeException(lang('Main.xin_error_invalid_id'));
			}

			$document_name = $this->request->getPost('document_name');
			$document_type = $this->request->getPost('document_type');
			$document_file = $this->request->getFile('document_file');

			$UserdocumentsModel = new UserdocumentsModel();
			$current_document = $UserdocumentsModel->find($id);

			$data = [
				'document_name' => $document_name,
				'document_type' => $document_type,
				'updated_at' => date('Y-m-d H:i:s')
			];

			// Handle file upload if present
			if ($document_file && $document_file->isValid() && !$document_file->hasMoved()) {
				// Delete old file if exists
				if ($current_document['document_file'] && file_exists('uploads/documents/' . $current_document['document_file'])) {
					unlink('uploads/documents/' . $current_document['document_file']);
				}

				// Generate unique filename
				$newName = $document_file->getRandomName();
				$document_file->move('uploads/documents/', $newName);
				$data['document_file'] = $newName;
			}

			$result = $UserdocumentsModel->update($id, $data);

			if ($result) {
				$response['result'] = lang('Success.employee_update_document_success');
				log_message('info', "Document updated - ID: {$id} by User: {$usession['sup_user_id']}");
			} else {
				$response['error'] = lang('Main.xin_no_changes_made');
			}
		} catch (\Exception $e) {
			log_message('error', 'Document update error: ' . $e->getMessage());
			$response['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($response);
	}
	// |||update record|||
	public function update_basic_info()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();

			// Set validation rules
			$validation->setRules([
				'first_name' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'last_name' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'contact_number' => 'required|numeric|min_length[6]|max_length[12]',
				'employee_id' => 'required',
				'role' => 'required',
				'state' => 'required|regex_match[/^[A-Za-z\s]+$/]',
				'city' => 'required|regex_match[/^[A-Za-z\s]+$/]',
			], [
				// Custom error messages
				'first_name' => [
					'required' => lang('Main.xin_employee_error_first_name'),
					'regex_match' => 'First name should only contain letters and spaces.',
				],
				'last_name' => [
					'required' => lang('Main.xin_employee_error_last_name'),
					'regex_match' => 'Last name should only contain letters and spaces.',
				],
				'contact_number' => [
					'required' => lang('Main.xin_error_contact_field'),
					'numeric' => 'Contact number should only contain numbers.',
					'min_length' => 'Contact number should be at least 10 digits.',
					'max_length' => 'Contact number should not exceed 15 digits.',
				],
				'employee_id' => [
					'required' => lang('Employees.xin_employee_error_employee_id'),
				],
				'role' => [
					'required' => lang('Employees.xin_employee_error_staff_role'),
				],
				'state' => [
					'regex_match' => 'State name should only contain letters and spaces.',
				],
				'city' => [
					'regex_match' => 'City name should only contain letters and spaces.',
				],
			]);

			// Run validation
			$validation->withRequest($this->request)->run();

			// Check for validation errors
			if ($validation->hasError('first_name')) {
				$Return['error'] = $validation->getError('first_name');
			} elseif ($validation->hasError('last_name')) {
				$Return['error'] = $validation->getError('last_name');
			} elseif ($validation->hasError('contact_number')) {
				$Return['error'] = $validation->getError('contact_number');
			} elseif ($validation->hasError('employee_id')) {
				$Return['error'] = $validation->getError('employee_id');
			} elseif ($validation->hasError('role')) {
				$Return['error'] = $validation->getError('role');
			} elseif ($validation->hasError('state')) {
				$Return['error'] = $validation->getError('state');
			} elseif ($validation->hasError('city')) {
				$Return['error'] = $validation->getError('city');
			}

			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}

			// Sanitize and retrieve form inputs
			$first_name = $this->request->getPost('first_name', FILTER_SANITIZE_STRING);
			$last_name = $this->request->getPost('last_name', FILTER_SANITIZE_STRING);
			$contact_number = $this->request->getPost('contact_number', FILTER_SANITIZE_STRING);
			$role = $this->request->getPost('role', FILTER_SANITIZE_STRING);
			$gender = $this->request->getPost('gender', FILTER_SANITIZE_STRING);
			$state = $this->request->getPost('state', FILTER_SANITIZE_STRING);
			$zipcode = $this->request->getPost('zipcode', FILTER_SANITIZE_STRING);
			$city = $this->request->getPost('city', FILTER_SANITIZE_STRING);
			$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
			$address_1 = $this->request->getPost('address_1', FILTER_SANITIZE_STRING);
			$address_2 = $this->request->getPost('address_2', FILTER_SANITIZE_STRING);
			$country = $this->request->getPost('country', FILTER_SANITIZE_STRING);

			// Staff details
			$date_of_birth = $this->request->getPost('date_of_birth', FILTER_SANITIZE_STRING);
			$marital_status = $this->request->getPost('marital_status', FILTER_SANITIZE_STRING);
			$religion = $this->request->getPost('religion', FILTER_SANITIZE_STRING);
			$blood_group = $this->request->getPost('blood_group', FILTER_SANITIZE_STRING);
			$citizenship_id = $this->request->getPost('citizenship_id', FILTER_SANITIZE_STRING);
			$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);

			if (empty($country)) {
				$country = 0;
			}
			if (empty($religion)) {
				$religion = 0;
			}
			if (empty($citizenship_id)) {
				$citizenship_id = 0;
			}

			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();

			// Update user data
			$data = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'contact_number' => $contact_number,
				'country' => $country,
				'user_role_id' => $role,
				'address_1' => $address_1,
				'address_2' => $address_2,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'gender' => $gender,
				'is_active' => $status
			];
			$result = $UsersModel->update($id, $data);

			// Update employee details
			$data2 = [
				'employee_id' => $employee_id,
				'date_of_birth' => $date_of_birth,
				'marital_status' => $marital_status,
				'religion_id' => $religion,
				'blood_group' => $blood_group,
				'citizenship_id' => $citizenship_id
			];

			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$MainModel = new MainModel();
				$MainModel->update_employee_record($data2, $id);
				$Return['result'] = lang('Employees.xin_success_update_employee');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	// |||update record|||
	public function update_bio()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			//staff details
			$bio = $this->request->getPost('bio', FILTER_SANITIZE_STRING);
			$experience = $this->request->getPost('experience', FILTER_SANITIZE_STRING);
			$reporting_manager = $this->request->getPost('reporting_manager', FILTER_SANITIZE_STRING);

			// set rules
			$validation->setRules(
				[
					'bio' => 'required',
					'reporting_manager' => 'required',
				],
				[   // Errors
					'bio' => [
						'required' => lang('Main.xin_error_field_text'),
					]
				],
				[
					'reporting_manager' => [
						'required' => lang('Main.xin_error_field_text'),
					]
				]
			);

			$validation->withRequest($this->request)->run();
			//check error
			if ($validation->hasError('bio')) {
				$Return['error'] = $validation->getError('bio');
			} elseif ($validation->hasError('reporting_manager')) {
				$Return['error'] = $validation->getError('reporting_manager');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}

			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			// employee details
			$data = [
				'bio' => $bio,
				'experience' => $experience,
				'reporting_manager' => $reporting_manager,

			];
			$MainModel = new MainModel();
			$result = $MainModel->update_employee_record($data, $id);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {

				$Return['result'] = lang('Success.employee_update_bio_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_social()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			//staff details
			$fb_profile = $this->request->getPost('fb_profile', FILTER_SANITIZE_STRING);
			$twitter_profile = $this->request->getPost('twitter_profile', FILTER_SANITIZE_STRING);
			$gplus_profile = $this->request->getPost('gplus_profile', FILTER_SANITIZE_STRING);
			$linkedin_profile = $this->request->getPost('linkedin_profile', FILTER_SANITIZE_STRING);

			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			// employee details
			$data = [
				'fb_profile' => $fb_profile,
				'twitter_profile' => $twitter_profile,
				'gplus_profile' => $gplus_profile,
				'linkedin_profile' => $linkedin_profile,
			];
			$MainModel = new MainModel();
			$result = $MainModel->update_employee_record($data, $id);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {

				$Return['result'] = lang('Success.employee_update_social_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_bankinfo()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			//staff details
			$account_title = $this->request->getPost('account_title', FILTER_SANITIZE_STRING);
			$account_number = $this->request->getPost('account_number', FILTER_SANITIZE_STRING);
			$bank_name = $this->request->getPost('bank_name', FILTER_SANITIZE_STRING);
			$iban = $this->request->getPost('iban', FILTER_SANITIZE_STRING);
			$swift_code = $this->request->getPost('swift_code', FILTER_SANITIZE_STRING);
			$bank_branch = $this->request->getPost('bank_branch', FILTER_SANITIZE_STRING);

			// Set rules with validation
			$validation->setRules(
				[
					'account_title' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'account_number' => 'required|numeric|min_length[16]|max_length[16]',
					'bank_name' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'iban' => 'required|regex_match[/^[A-Z0-9]{15,34}$/]',
					'swift_code' => 'required|regex_match[/^[A-Z0-9]{8,11}$/]',
					'bank_branch' => 'required'
				],
				[
					'account_title' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => "account title invalid",
					],
					'account_number' => [
						'required' => lang('Main.xin_error_field_text'),
						'numeric' => lang('Main.xin_error_account_number_numeric'),
						'min_length' => lang('Main.xin_error_account_number_length'),
						'max_length' => lang('Main.xin_error_account_number_length'),
					],
					'bank_name' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => lang('Main.xin_error_bank_name_invalid'),
					],
					'iban' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => lang('Main.xin_error_iban_invalid'),
					],
					'swift_code' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => lang('Main.xin_error_swift_code_invalid'),
					],
					'bank_branch' => [
						'required' => lang('Main.xin_error_field_text'),
					],
				]
			);

			if (!$validation->withRequest($this->request)->run()) {
				$errors = $validation->getErrors();
				$Return['error'] = reset($errors);

				if ($Return['error'] !== '') {
					return $this->response->setJSON($Return);
					return;
				}
			}


			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			$data = [
				'account_title' => $account_title,
				'account_number' => $account_number,
				'bank_name' => $bank_name,
				'iban' => $iban,
				'swift_code' => $swift_code,
				'bank_branch' => $bank_branch,
			];
			$MainModel = new MainModel();
			$result = $MainModel->update_employee_record($data, $id);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {

				$Return['result'] = lang('Success.employee_update_bankinfo_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_contact_info()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			//staff details
			$contact_full_name = $this->request->getPost('contact_full_name', FILTER_SANITIZE_STRING);
			$contact_phone_no = $this->request->getPost('contact_phone_no', FILTER_SANITIZE_STRING);
			$contact_email = $this->request->getPost('contact_email', FILTER_SANITIZE_STRING);
			$contact_address = $this->request->getPost('contact_address', FILTER_SANITIZE_STRING);
			// set rules
			$validation->setRules([
				'contact_full_name' => [
					'label' => 'Full Name',
					'rules' => 'required|regex_match[/^[A-Za-z\s]+$/]',
					'errors' => [
						'required' => lang('Main.xin_error_field_text'),
						'regex_match' => 'Contact name is invalid (letters and spaces only)',
					]
				],
				'contact_phone_no' => [
					'label' => 'Phone Number',
					'rules' => 'required|numeric|min_length[6]|max_length[12]',
					'errors' => [
						'required' => lang('Main.xin_error_contact_field'),
						'numeric' => lang('Main.xin_error_account_number_numeric'),
						'min_length' => lang('Main.xin_error_account_number_length'),
						'max_length' => lang('Main.xin_error_account_number_length'),
					]
				],
				'contact_email' => [
					'label' => 'Email',
					'rules' => 'required|valid_email',
					'errors' => [
						'required' => lang('Main.xin_error_field_text'),
						'valid_email' => 'The email format is invalid.',
					]
				],
				'contact_address' => [
					'label' => 'Address',
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			]);

			// Run the validation
			if (!$validation->withRequest($this->request)->run()) {
				// Collect all errors
				$Return['error'] = $validation->getErrors();
				return $this->response->setJSON($Return);
			}
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			// employee details
			$data = [
				'contact_full_name' => $contact_full_name,
				'contact_phone_no' => $contact_phone_no,
				'contact_email' => $contact_email,
				'contact_address' => $contact_address,
			];
			$MainModel = new MainModel();
			$result = $MainModel->update_employee_record($data, $id);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {

				$Return['result'] = lang('Success.employee_update_emergency_contact_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_company_info()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'first_name' => 'required',
					'last_name' => 'required',
					'email' => 'required|valid_email',
					'username' => 'required|min_length[6]',
					'department_id' => 'required',
					'designation_id' => 'required',
					'employee_id' => 'required',
					'contact_number' => 'required',
					'office_shift_id' => 'required',
					'role' => 'required'
				],
				[   // Errors
					'first_name' => [
						'required' => lang('Main.xin_employee_error_first_name'),
					],
					'last_name' => [
						'required' => lang('Main.xin_employee_error_last_name'),
					],
					'employee_id' => [
						'required' => lang('Employees.xin_employee_error_employee_id'),
					],
					'office_shift_id' => [
						'required' => lang('Employees.xin_office_shift_field_error'),
					],
					'email' => [
						'required' => lang('Main.xin_employee_error_email'),
						'valid_email' => lang('Main.xin_employee_error_invalid_email')
					],
					'department_id' => [
						'required' => lang('Employees.xin_employee_error_department'),
					],
					'designation_id' => [
						'required' => lang('Employees.xin_employee_error_designation'),
					],
					'username' => [
						'required' => lang('Main.xin_employee_error_username'),
						'min_length' => lang('Main.xin_min_error_username')
					],
					'contact_number' => [
						'required' => lang('Main.xin_error_contact_field'),
					],
					'role' => [
						'required' => lang('Employees.xin_employee_error_staff_role'),
					]
				]
			);

			$validation->withRequest($this->request)->run();
			//check error
			if ($validation->hasError('first_name')) {
				$Return['error'] = $validation->getError('first_name');
			} elseif ($validation->hasError('last_name')) {
				$Return['error'] = $validation->getError('last_name');
			} elseif ($validation->hasError('email')) {
				$Return['error'] = $validation->getError('email');
			} elseif ($validation->hasError('username')) {
				$Return['error'] = $validation->getError('username');
			} elseif ($validation->hasError('department_id')) {
				$Return['error'] = $validation->getError('department_id');
			} elseif ($validation->hasError('designation_id')) {
				$Return['error'] = $validation->getError('designation_id');
			} elseif ($validation->hasError('employee_id')) {
				$Return['error'] = $validation->getError('employee_id');
			} elseif ($validation->hasError('contact_number')) {
				$Return['error'] = $validation->getError('contact_number');
			} elseif ($validation->hasError('office_shift_id')) {
				$Return['error'] = $validation->getError('office_shift_id');
			} elseif ($validation->hasError('role')) {
				$Return['error'] = $validation->getError('role');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}
			//staff
			$first_name = $this->request->getPost('first_name', FILTER_SANITIZE_STRING);
			$last_name = $this->request->getPost('last_name', FILTER_SANITIZE_STRING);
			$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
			$username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
			$contact_number = $this->request->getPost('contact_number', FILTER_SANITIZE_STRING);
			$role = $this->request->getPost('role', FILTER_SANITIZE_STRING);
			$gender = $this->request->getPost('gender', FILTER_SANITIZE_STRING);
			$state = $this->request->getPost('state', FILTER_SANITIZE_STRING);
			$zipcode = $this->request->getPost('zipcode', FILTER_SANITIZE_STRING);
			$city = $this->request->getPost('city', FILTER_SANITIZE_STRING);
			$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
			$address_1 = $this->request->getPost('address_1', FILTER_SANITIZE_STRING);
			$address_2 = $this->request->getPost('address_2', FILTER_SANITIZE_STRING);
			$country_id = $this->request->getPost('country', FILTER_SANITIZE_STRING);

			// staff details
			$cat_ids = implode(',', $this->request->getPost('leave_categories', FILTER_SANITIZE_STRING));
			$employee_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
			$office_shift_id = $this->request->getPost('office_shift_id', FILTER_SANITIZE_STRING);
			$department_id = $this->request->getPost('department_id', FILTER_SANITIZE_STRING);
			$designation_id = $this->request->getPost('designation_id', FILTER_SANITIZE_STRING);
			$date_of_joining = $this->request->getPost('date_of_joining', FILTER_SANITIZE_STRING);
			$date_of_leaving = $this->request->getPost('date_of_leaving', FILTER_SANITIZE_STRING);
			$date_of_birth = $this->request->getPost('date_of_birth', FILTER_SANITIZE_STRING);
			$marital_status = $this->request->getPost('marital_status', FILTER_SANITIZE_STRING);
			$religion = $this->request->getPost('religion', FILTER_SANITIZE_STRING);
			$blood_group = $this->request->getPost('blood_group', FILTER_SANITIZE_STRING);
			$citizenship_id = $this->request->getPost('citizenship_id', FILTER_SANITIZE_STRING);

			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			$data = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'username' => $username,
				'contact_number' => $contact_number,
				'country' => $country_id,
				'user_role_id' => $role,
				'address_1' => $address_1,
				'address_2' => $address_2,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'gender' => $gender,
				'is_active' => $status
			];
			$result = $UsersModel->update($id, $data);
			// employee details
			$data2 = [
				'employee_id' => $employee_id,
				'department_id' => $department_id,
				'designation_id' => $designation_id,
				'office_shift_id' => $office_shift_id,
				'leave_categories' => $cat_ids,
				'date_of_joining' => $date_of_joining,
				'date_of_leaving' => $date_of_leaving,
				'date_of_birth' => $date_of_birth,
				'marital_status' => $marital_status,
				'religion_id' => $religion,
				'blood_group' => $blood_group,
				'citizenship_id' => $citizenship_id
			];

			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$MainModel = new MainModel();
				$MainModel->update_employee_record($data2, $id);
				$Return['result'] = lang('Employees.xin_success_update_employee');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_contract_info()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'contract_date' => 'required',
					'department_id' => 'required',
					'designation_id' => 'required',
					'basic_salary' => 'required',
					'salay_type' => 'required',
					'office_shift_id' => 'required',
					'role_description' => 'required'
				],
				[   // Errors
					'contract_date' => [
						'required' => lang('Main.xin_error_field_text'),
					],
					'department_id' => [
						'required' => lang('Employees.xin_employee_error_department'),
					],
					'designation_id' => [
						'required' => lang('Employees.xin_employee_error_designation'),
					],
					'basic_salary' => [
						'required' => lang('Main.xin_error_field_text'),
					],
					'salay_type' => [
						'required' => lang('Main.xin_error_field_text'),
					],
					'office_shift_id' => [
						'required' => lang('Employees.xin_office_shift_field_error'),
					],
					'role_description' => [
						'required' => lang('Main.xin_error_field_text'),
					]
				]
			);

			$validation->withRequest($this->request)->run();
			//check error
			if ($validation->hasError('contract_date')) {
				$Return['error'] = $validation->getError('contract_date');
			} elseif ($validation->hasError('department_id')) {
				$Return['error'] = $validation->getError('department_id');
			} elseif ($validation->hasError('designation_id')) {
				$Return['error'] = $validation->getError('designation_id');
			} elseif ($validation->hasError('basic_salary')) {
				$Return['error'] = $validation->getError('basic_salary');
			} elseif ($validation->hasError('salay_type')) {
				$Return['error'] = $validation->getError('salay_type');
			} elseif ($validation->hasError('office_shift_id')) {
				$Return['error'] = $validation->getError('office_shift_id');
			} elseif ($validation->hasError('role_description')) {
				$Return['error'] = $validation->getError('role_description');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}
			//staff details
			$date_of_joining = $this->request->getPost('contract_date', FILTER_SANITIZE_STRING);
			$department_id = $this->request->getPost('department_id', FILTER_SANITIZE_STRING);
			$designation_id = $this->request->getPost('designation_id', FILTER_SANITIZE_STRING);
			$basic_salary = $this->request->getPost('basic_salary', FILTER_SANITIZE_STRING);
			$hourly_rate = $this->request->getPost('hourly_rate', FILTER_SANITIZE_STRING);
			$salay_type = $this->request->getPost('salay_type', FILTER_SANITIZE_STRING);
			$office_shift_id = $this->request->getPost('office_shift_id', FILTER_SANITIZE_STRING);
			$role_description = $this->request->getPost('role_description', FILTER_SANITIZE_STRING);
			$date_of_leaving = $this->request->getPost('contract_end', FILTER_SANITIZE_STRING);

			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			// employee details
			$data2 = [
				'date_of_joining' => $date_of_joining,
				'department_id' => $department_id,
				'designation_id' => $designation_id,
				'office_shift_id' => $office_shift_id,
				'basic_salary' => $basic_salary,
				'hourly_rate' => $hourly_rate,
				'salay_type' => $salay_type,
				'date_of_leaving' => $date_of_leaving,
				'role_description' => $role_description,
			];
			$MainModel = new MainModel();
			$result = $MainModel->update_employee_record($data2, $id);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {

				$Return['result'] = lang('Success.employee_update_contract_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// |||update record|||
	public function update_account_info()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'email' => 'required|valid_email',
					'username' => 'required|min_length[6]',
				],
				[   // Errors
					'email' => [
						'required' => lang('Main.xin_employee_error_email'),
						'valid_email' => lang('Main.xin_employee_error_invalid_email')
					],
					'username' => [
						'required' => lang('Main.xin_employee_error_username'),
						'min_length' => lang('Main.xin_min_error_username')
					],
				]
			);

			$validation->withRequest($this->request)->run();
			//check error
			if ($validation->hasError('email')) {
				$Return['error'] = $validation->getError('email');
			} elseif ($validation->hasError('username')) {
				$Return['error'] = $validation->getError('username');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}
			//staff
			$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
			$username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$UsersModel = new UsersModel();
			$data = [
				'email' => $email,
				'username' => $username,
			];
			$result = $UsersModel->update($id, $data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_update_accountinfo_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// update record
	public function update_profile_photo()
	{
		$session = \Config\Services::session();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($this->request->getPost('type') !== 'edit_record') {
			$Return = ['error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
			return  $this->response->setJSON($Return);
		}

		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Validate the uploaded file
		$validation = $this->validate([
			'file' => [
				'uploaded[file]',
				'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
				'max_size[file,4096]',
			],
		]);

		if (!$validation) {
			$Return['error'] = lang('Main.xin_error_profile_picture_field');
			$Return['csrf_hash'] = csrf_hash();
			return  $this->response->setJSON($Return);
		}

		$key = $this->request->getPost('key');
		$employee_id = $this->request->getPost('employee_id');
		$file = $this->request->getFile('file');


		if ($file && $file->isValid() && !$file->hasMoved()) {
			try {
				$file_name = $employee_id . '_' . $file->getClientName();
				$sanitized_file_name = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $file_name);
				$filePath = WRITEPATH . 'uploads/' . $sanitized_file_name;

				// Move the file to a temporary local directory
				$file->move(WRITEPATH . 'uploads/', $sanitized_file_name);

				$s3Library = new S3ClientLibrary();

				// Delete the old file from S3
				if (!empty($key)) {
					$deleteResult = $s3Library->deleteFile($key);
					if ($deleteResult['status'] !== 'success') {
						log_message('error', 'Failed to delete existing file from S3: ' . $key);
						$Return['error'] = lang('Employees.xin_staff_picture_delete_error');
						$Return['csrf_hash'] = csrf_hash();
						return  $this->response->setJSON($Return);
					}
				}

				// Upload the new file to S3
				$uploadResult = $s3Library->uploadFile($filePath, 'uploads/' . $sanitized_file_name);

				// Ensure the file was uploaded successfully to S3
				if ($uploadResult && isset($uploadResult['url'])) {
					$file_name = $uploadResult['url'];
					log_message('info', 'File uploaded to S3 successfully: ' . $file_name);
				} else {
					throw new \Exception('Failed to upload file to S3.');
				}

				// Remove the temporary file
				if (file_exists($filePath)) {
					unlink($filePath);
				}

				if ($uploadResult['status'] !== 'success') {
					$Return['error'] = $uploadResult['message'];
					$Return['csrf_hash'] = csrf_hash();
					return $this->response->setJSON($Return);
				}

				// Update the user's profile photo in the database
				$UsersModel = new UsersModel();
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				$data = ['profile_photo' => $file_name];
				$UsersModel->update($id, $data);

				$Return['result'] = lang('Main.xin_profile_picture_success_updated');
				$Return['csrf_hash'] = csrf_hash();
				return $this->response->setJSON($Return);
			} catch (\Exception $e) {
				log_message('error', 'File upload error: ' . $e->getMessage());
				$Return['error'] = lang('Employees.xin_staff_picture_upload_error');
				$Return['csrf_hash'] = csrf_hash();
				return $this->response->setJSON($Return);
			}
		} else {
			$Return['error'] = lang('Main.xin_error_profile_picture_field');
			$Return['csrf_hash'] = csrf_hash();
			return $this->response->setJSON($Return);
		}
	}

	// update record
	public function update_password()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					//'current_password' => 'required|is_not_unique[xin_users.password]',
					'new_password' => 'required|min_length[6]',
					'confirm_password' => 'required|matches[new_password]',
				],
				[   // Errors
					'new_password' => [
						'required' => lang('Main.xin_error_new_password_field'),
						'min_length' => lang('Main.xin_error_new_password_short_field'),
					],
					'confirm_password' => [
						'required' => lang('Main.xin_error_confirm_password_field'),
						'matches' => lang('Main.xin_error_confirm_password_matches_field'),
					]
				]
			);
			$UsersModel = new UsersModel();
			$validation->withRequest($this->request)->run();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			//check error
			$new_password = $this->request->getPost('new_password', FILTER_SANITIZE_STRING);
			if ($validation->hasError('new_password')) {
				$Return['error'] = $validation->getError('new_password');
			} elseif ($validation->hasError('confirm_password')) {
				$Return['error'] = $validation->getError('confirm_password');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}


			$options = array('cost' => 12);
			$password_hash = password_hash($new_password, PASSWORD_BCRYPT, $options);
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$data = [
				'password' => $password_hash,
			];

			$result = $UsersModel->update($id, $data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Main.xin_success_new_password_field');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}


	public function is_designation($id = null)
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		// $id = $request->getUri()->getSegment(4);
		$data = [
			'department_id' => $id
		];
		if ($session->has('sup_username')) {
			return view('erp/employees/get_designations', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// read record
	public function dialog_user_data()
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
			return view('erp/employees/dialog_employee_details', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_all_allowances()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {

			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ContractModel = new ContractModel();
			$result = $ContractModel->where('contract_option_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_delete_allowance_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_all_commissions()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ContractModel = new ContractModel();
			$result = $ContractModel->where('contract_option_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_delete_commission_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_all_statutory_deductions()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ContractModel = new ContractModel();
			$result = $ContractModel->where('contract_option_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_delete_statutory_deduction_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_all_other_payments()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$ContractModel = new ContractModel();
			$result = $ContractModel->where('contract_option_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_delete_reimbursement_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_document()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = ['result' => '', 'error' => '', 'csrf_hash' => ''];

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$UserdocumentsModel = new UserdocumentsModel();
			$result = $UserdocumentsModel->where('document_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.employee_delete_document_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record

	public function delete_staff()
	{
		if ($this->request->getPost('type') == 'delete_record') {
			$Return = [
				'result' => '',
				'error' => '',
				'csrf_hash' => csrf_hash(),
			];

			$session = \Config\Services::session();
			$usession = $session->get('sup_username');
			$request = \Config\Services::request();

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			if (!$id) {
				$Return['error'] = lang('Main.xin_invalid_request');
				return $this->response->setJSON($Return);
			}

			$UsersModel = new UsersModel();
			$StaffdetailsModel = new StaffdetailsModel();
			$s3Library = new S3ClientLibrary();

			$user_info = $UsersModel->find($usession['sup_user_id']);
			if (!$user_info) {
				$Return['error'] = lang('Main.xin_error_msg');
				return $this->response->setJSON($Return);
			}

			$company_id = $user_info['user_type'] == 'staff' ? $user_info['company_id'] : $usession['sup_user_id'];

			// Fetch user's data to get profile photo key
			$user_data = $UsersModel->find($id);
			if (!$user_data) {
				$Return['error'] = lang('Main.xin_error_msg');
				return $this->response->setJSON($Return);
			}

			$url = $user_data['profile_photo'];

			$parsed_url = parse_url($url);
			$path = $parsed_url['path'];

			$file_path = ltrim($path, '/');

			// Delete profile photo from S3
			if (!empty($file_path)) {
				$deleteResult = $s3Library->deleteFile($file_path);
				if ($deleteResult['status'] !== 'success') {
					log_message('error', 'Failed to delete existing file from S3: ' . $file_path);
				}
			}

			// Delete user and their details
			$user_deleted = $UsersModel->where('user_id', $id)->where('company_id', $company_id)->delete();
			if ($user_deleted) {
				$StaffdetailsModel->where('user_id', $id)->delete(); // Clean up staff details
				$Return['result'] = lang('Success.employee_deleted_success');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}

			return $this->response->setJSON($Return);
		}
	}

	public function print_pattern()
	{
		// for ($i = 1; $i <= 5; $i++) {
		// 	for ($j = 0; $j < $i; $j++) {
		// 		echo "* ";
		// 	}
		// 	echo "<br>";
		// }
		$n = 5;

		for ($i = 1; $i <= $n; $i++) {
			for ($j = $i; $j < $n; $j++) {
				echo "&nbsp;&nbsp;";
			}
			// Print stars
			for ($k = 1; $k <= (2 * $i - 1); $k++) {
				echo "* ";
			}
			echo "<br>";
		}
	}
}
