<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\SystemModel;
use App\Models\UsersModel;

use App\Models\PlanningEntityModel;
use App\Models\YearPlanningModel;
use App\Models\MonthlyPlanningModel;
use App\Models\MonthlyAchivedModel;
use App\Models\Form_model;
use App\Models\CompanysettingsModel;
use App\Models\MonthlyPlanReviewModel;
use App\Models\ProjectsModel;
use PhpParser\Node\Stmt\Else_;

class Dashboard extends BaseController
{

	protected $cache;
	public function __construct()
	{
		$this->cache = \Config\Services::config();
	}
	public function index()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_title') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'dashboard';
		$UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$data['subview'] = view('erp/dashboard/index', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// set new language
	public function language($real_language = "")
	{

		$session = session();
		$request = \Config\Services::request();
		if (empty($_SERVER['HTTP_REFERER'])) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$session->remove('lang');
		$session->set('lang', $real_language);
		return redirect()->to($_SERVER['HTTP_REFERER']);
	}

	public function web_leads()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$data['title'] = "Web Leads | " . $xin_system['application_name'];
		$data['path_url'] = 'web-leads';
		$data['subview'] = view('erp/web_lead/web_lead', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function read_web_lead()
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
			return view('erp/web_lead/change_to_company', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	public function read_web_leads()
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
			return view('erp/web_lead/change_to_client', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}

	public function delete_web_lead()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Sanitize and decode the ID
		$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
		$formModel = new Form_model();
		$result = $formModel->delete($id);

		// Prepare the response array
		$response = [
			'success' => false,
			'result' => '',
			'error' => ''
		];

		if ($result) {
			$response['result'] = lang('Success.ci_lead_deleted_msg');
			$response['success'] = true;
		} else {
			$response['error'] = lang('Main.xin_error_msg');
		}

		return $this->output($response);
	}

	public function convert_company()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type', FILTER_SANITIZE_STRING) === 'edit_record') {

			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$data = [
				'lead_status' => 1,
			];

			$UsersModel = new UsersModel();
			$Form_model = new Form_model();
			$result = $Form_model->update($id, $data);
			$web_lead_info = $Form_model->where('id', $id)->first();
			$name_parts = explode(' ', trim($web_lead_info['name']), 2);
			$first_name = $name_parts[0];
			$last_name = isset($name_parts[1]) ? $name_parts[1] : '';
			$iusername = explode('@', $web_lead_info['email']);
			$username = $iusername[0];
			$options = array('cost' => 12);
			$password_hash = password_hash($username, PASSWORD_BCRYPT, $options);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$data2 = [
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $web_lead_info['email'],
					'user_type' => 'company',
					'username' => $username,
					'password' => $password_hash,
					'contact_number' => $web_lead_info['contact'],
					'country' => 99,
					'user_role_id' => 0,
					'address_1' => '',
					'address_2' => '',
					'city' => '',
					'profile_photo' => '',
					'state' => '',
					'zipcode' => '',
					'gender' => '',
					'company_name' => '',
					'trading_name' => '',
					'registration_no' => '',
					'government_tax' => '',
					'company_type_id' => 0,
					'last_login_date' => '0',
					'last_logout_date' => '0',
					'last_login_ip' => '0',
					'is_logged_in' => '0',
					'is_active' => 1,
					'created_at' => date('Y-m-d H:i:s'),
				];

				$result2 = $UsersModel->insert($data2);
				$user_id = $UsersModel->insertID();

				if ($result2) {
					$UsersModel->update($user_id, ['company_id' => $user_id]);

					$CompanysettingsModel = new CompanysettingsModel();
					$newData = [
						'company_id' => $user_id,
						'default_currency' => 'INR',
						'default_currency_symbol' => 'INR',
						// 'default_currency_symbol' => '₹',
						'notification_position' => 'toast-top-center',
						'notification_close_btn' => '0',
						'notification_bar' => 'true',
						'date_formate_xi' => 'Y.m.d'

					];
					$CompanysettingsModel->insert($newData);

					// $xin_system = new SystemModel();
					// $xin_system_data = $xin_system->where('setting_id', 1)->first();

					// if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1) {
					// 	$EmailtemplatesModel = new EmailtemplatesModel();
					// 	$itemplate = $EmailtemplatesModel->where('template_id', 5)->first();

					// 	if ($itemplate) {
					// 		$isubject = $itemplate['subject'];
					// 		$ibody = html_entity_decode($itemplate['message']);
					// 		$fbody = str_replace(
					// 			array("{site_name}", "{user_password}", "{user_username}", "{site_url}"),
					// 			array($xin_system_data['company_name'], $request->getPost('password'), $request->getPost('username'), site_url()),
					// 			$ibody
					// 		);
					// 		timehrm_mail_data($xin_system_data['email'], $xin_system_data['company_name'], $data['email'], $isubject, $fbody);
					// 	}
					// }

					$session->setFlashdata('result', 'Company Added Successfully!');
					return redirect()->to(site_url('erp/web-leads'));
				} else {
					$session->setFlashdata('error', lang('Main.xin_error_msg'));
					return redirect()->to(site_url('erp/web-leads'));
				}
				$Return['result'] = lang('Success.ci_lead_changed_to_client_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
			exit;
		} else {

			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}


	public function convert_client()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type', FILTER_SANITIZE_STRING) === 'edit_record') {

			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$data = [
				'lead_status' => 1,
			];


			$UsersModel = new UsersModel();
			$Form_model = new Form_model();
			$result = $Form_model->update($id, $data);
			var_dump($result);
			die;
			$web_lead_info = $Form_model->where('id', $id)->first();
			$name_parts = explode(' ', trim($web_lead_info['name']), 2);
			$first_name = $name_parts[0];
			$last_name = isset($name_parts[1]) ? $name_parts[1] : '';
			$iusername = explode('@', $web_lead_info['email']);
			$username = $iusername[0];
			$options = array('cost' => 12);
			$password_hash = password_hash($username, PASSWORD_BCRYPT, $options);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$data2 = [
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $web_lead_info['email'],
					'user_type' => 'client',
					'username' => $username,
					'password' => $password_hash,
					'contact_number' => $web_lead_info['contact'],
					'country' => 99,
					'user_role_id' => 0,
					'address_1' => '',
					'address_2' => '',
					'city' => '',
					'profile_photo' => '',
					'state' => '',
					'zipcode' => '',
					'gender' => '',
					'company_name' => '',
					'trading_name' => '',
					'registration_no' => '',
					'government_tax' => '',
					'company_type_id' => 0,
					'last_login_date' => '0',
					'last_logout_date' => '0',
					'last_login_ip' => '0',
					'is_logged_in' => '0',
					'is_active' => 1,
					'created_at' => date('Y-m-d H:i:s'),
				];

				$result2 = $UsersModel->insert($data2);
				$user_id = $UsersModel->insertID();

				if ($result2) {
					$UsersModel->update($user_id, ['company_id' => $user_id]);

					$CompanysettingsModel = new CompanysettingsModel();
					$newData = [
						'company_id' => $user_id,
						'default_currency' => 'INR',
						'default_currency_symbol' => 'INR',
						// 'default_currency_symbol' => '₹',
						'notification_position' => 'toast-top-center',
						'notification_close_btn' => '0',
						'notification_bar' => 'true',
						'date_formate_xi' => 'Y.m.d'

					];
					$CompanysettingsModel->insert($newData);

					// $xin_system = new SystemModel();
					// $xin_system_data = $xin_system->where('setting_id', 1)->first();

					// if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1) {
					// 	$EmailtemplatesModel = new EmailtemplatesModel();
					// 	$itemplate = $EmailtemplatesModel->where('template_id', 5)->first();

					// 	if ($itemplate) {
					// 		$isubject = $itemplate['subject'];
					// 		$ibody = html_entity_decode($itemplate['message']);
					// 		$fbody = str_replace(
					// 			array("{site_name}", "{user_password}", "{user_username}", "{site_url}"),
					// 			array($xin_system_data['company_name'], $request->getPost('password'), $request->getPost('username'), site_url()),
					// 			$ibody
					// 		);
					// 		timehrm_mail_data($xin_system_data['email'], $xin_system_data['company_name'], $data['email'], $isubject, $fbody);
					// 	}
					// }

					$session->setFlashdata('result', 'Client Added Successfully!');
					return redirect()->to(site_url('erp/web-leads-list'));
				} else {
					$session->setFlashdata('error', lang('Main.xin_error_msg'));
					return redirect()->to(site_url('erp/web-leads-list'));
				}
				$Return['result'] = lang('Success.ci_lead_changed_to_client_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
			exit;
		} else {

			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}


	public function web_lead_detail()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$Form_model = new Form_model();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}


		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$segment_id = $request->uri->getSegment(3);
		$ifield_id = udecode($segment_id);

		$isegment_val = $Form_model->where('id', $ifield_id)->first();

		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$data['title'] = 'Web Lead Detail' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'web-lead-detail';
		$data['breadcrumbs'] = 'web-lead-detail';
		$data['subview'] = view('erp/web_lead/web_lead_details', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function update_web_lead()
	{
		// Initialize session and check user login status
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Session data not found.'
			]);
		}

		// Load user info
		$UsersModel = new UsersModel();
		$user_info = $UsersModel->find($usession['sup_user_id']);

		if (!$user_info) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'User not found.'
			]);
		}

		// Define validation rules
		$rules = [
			'name' => 'required',
			'email' => 'required',
			'contact' => 'required',
			'description' => 'required',
			'status' => 'required',
		];

		// Validate form input
		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Validation error',
				'errors' => $errors
			]);
		}

		// Fetch POST data and sanitize inputs
		$postData = $this->request->getPost([
			'name',
			'email',
			'contact',
			'description',
			'status',
			'web_lead_id'
		], FILTER_SANITIZE_STRING);

		// Prepare data array for update
		$data = [
			'name' => $postData['name'],
			'email' => $postData['email'],
			'contact' => $postData['contact'],
			'description' => $postData['description'],
			'status' => (int)$postData['status'],
			'remark' => $postData['remark'],
			'updated_at'  => date('Y-m-d H:i:s')
		];



		// Initialize Form model
		$Form_model = new Form_model();

		// Verify if record exists
		$record = $Form_model->where('id', $postData['web_lead_id'])->first();

		if (!$record) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'This record is not found.'
			]);
		}

		$db = \Config\Database::connect();
		$db->transStart();

		try {

			$updateSuccessful = $Form_model->update($postData['web_lead_id'], $data);
			$db->transComplete();

			if ($db->transStatus() === FALSE || !$updateSuccessful) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Failed to update the entity.',
					'details' => $Form_model->errors()
				]);
			}

			// Return success response
			return $this->response->setJSON([
				'status' => 'success',
				'message' => 'Web Lead updated successfully!'
			]);
		} catch (\Exception $e) {
			// Rollback on error
			$db->transRollback();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred during the update.',
				'details' => $e->getMessage()
			]);
		}
	}

	public function planning_configuration()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$data['title'] = 'Planning Configuration' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'planning_configuration';
		$data['subview'] = view('erp/planning/planning_entity', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function add_planning_entities()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new UsersModel();

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'Session data not found.']);
		}

		// Get company information
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		// Validation rules
		$rules = [
			'entity' => 'required',
			'type' => 'required|in_list[text,number]',
		];

		// Validate input
		if (!$this->validate($rules)) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'Validation error', 'errors' => $this->validator->getErrors()]);
		}

		// Sanitize input
		$entity = $this->request->getPost('entity', FILTER_SANITIZE_STRING);
		$type = $this->request->getPost('type', FILTER_SANITIZE_STRING);
		$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);

		$data = [
			'entity' => $entity,
			'type' => $type,
			'description' => $description,
			'company_id' => $company_id,
			'user_type' => $user_type,
			'valid' => 1
		];

		$PlanningEntityModel = new PlanningEntityModel();

		// Check for existing entity in this company
		$existing_record = $PlanningEntityModel->where('company_id', $company_id)->where('entity', $entity)->first();

		if ($existing_record) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'This entity name already exists for the company.']);
		}

		try {
			$inserted = $PlanningEntityModel->insert($data);
			if ($inserted) {
				log_message('info', 'Insert ID: ' . $inserted);
				return $this->response->setJSON(['status' => 'success', 'message' => 'Form submitted successfully!']);
			} else {
				return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit form. Database error occurred.', 'details' => $PlanningEntityModel->errors()]);
			}
		} catch (\Exception $e) {
			log_message('error', 'Insert failed: ' . $e->getMessage());
			return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to submit form. Database error occurred.', 'details' => $e->getMessage()]);
		}
	}
	public function planning_configuration_details($ifield_id)
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$PlanningEntityModel = new PlanningEntityModel();
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
		// $segment_id = $request->uri->getSegment(3);
		// $ifield_id = udecode($segment_id);
		$isegment_val = $PlanningEntityModel->where('id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] == 'staff') {
			$planning_data = $PlanningEntityModel->where('company_id', $user_info['company_id'])->where('id', $ifield_id)->first();
		} else {
			$planning_data = $PlanningEntityModel->where('company_id', $usession['sup_user_id'])->where('id', $ifield_id)->first();
		}


		$data['title'] = 'planning_entities_details' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'planning_configuration';
		$data['breadcrumbs'] = 'planning_entities_details';
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/planning/planning_entities_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}


	public function update_planning_entity()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Session data not found.'
			]);
		}

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->find($usession['sup_user_id']);

		if (!$user_info) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'User not found.'
			]);
		}

		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$rules = [
			'entity' => 'required',
			'type' => 'required',
			'planning_entity_id' => 'required|integer'
		];

		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Validation error',
				'errors' => $errors
			]);
		}

		$postData = $this->request->getPost([
			'entity',
			'type',
			'description',
			'planning_entity_id'
		], FILTER_SANITIZE_STRING);

		$valid = (bool) $this->request->getPost('valid');


		$data = [
			'entity' => $postData['entity'],
			'type' => $postData['type'],
			'description' => $postData['description'],
			'valid' => $valid,
			'company_id' => $company_id,
			'user_type' => $user_type,
		];
		$PlanningEntityModel = new PlanningEntityModel();
		$record = $PlanningEntityModel
			->where(['company_id' => $company_id, 'user_type' => $user_type])
			->where('id', $postData['planning_entity_id'])
			->first();

		if (!$record) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Entity not found or you do not have permission to edit it.'
			]);
		}

		$db = \Config\Database::connect();
		$db->transStart();

		try {
			$updateSuccessful = $PlanningEntityModel->update($postData['planning_entity_id'], $data);
			$db->transComplete();

			if ($db->transStatus() === FALSE || !$updateSuccessful) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Failed to update entity.',
					'details' => $PlanningEntityModel->errors()
				]);
			}
			return $this->response->setJSON([
				'status' => 'success',
				'message' => 'planning entities updated successfully!',
			]);
		} catch (\Exception $e) {
			$db->transRollback();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred during the update.',
				'details' => $e->getMessage()
			]);
		}
	}



	public function delete_planning_entity()
	{
		if ($this->request->getPost()) {
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');
			$id = $this->request->getPost('_token', FILTER_SANITIZE_STRING);

			if (!$id) {
				$session->setFlashdata('error', 'Invalid ID.');
				return redirect()->back();
			}

			$YearPlanningModel = new YearPlanningModel();
			$PlanningEntityModel = new PlanningEntityModel();
			$data = $PlanningEntityModel->where('id', $id)->first();

			if (!$data) {
				$session->setFlashdata('error', 'Entity not found.');
				return redirect()->back();
			}

			// Delete related records first
			$YearPlanningModel->where('entities_id', $data['id'])->delete();

			// Delete main entity
			$result = $PlanningEntityModel->delete($id);

			if ($result) {
				$session->setFlashdata('success', 'Entity deleted Successfully.');
			} else {
				$session->setFlashdata('error', 'Failed to delete entity.');
			}

			// Redirect back to the same page
			return redirect()->back();
		}
	}

	public function year_planning()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$data['title'] = 'Year Plannning' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'yearplanningform';
		$data['subview'] = view('erp/planning/yearplanningform', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function year_planning_submit()
	{

		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$YearPlanningModel = new YearPlanningModel();

		$entities_data = $this->request->getPost('entities');
		$year = $this->request->getPost('year');
		$revenue = $this->request->getPost('revenue');

		$record = $YearPlanningModel->where([
			'company_id' => $company_id,
			'user_type' => $user_type,
			'year' => $year
		])->first();

		if ($record) {
			return $this->response->setJSON(['status' => 'error', 'message' => 'This Year Data Already Exists.']);
		}

		$errors = [];
		if (!empty($entities_data)) {
			foreach ($entities_data as $entity_data) {
				if (!empty($entity_data['entities_id']) && !empty($entity_data['entity_value'])) {
					$data = [
						'entities_id' => $entity_data['entities_id'],
						'entity_value' => $entity_data['entity_value'],
						'year' => $year,
						'company_id' => $company_id,
						'user_type' => $user_type
					];

					if (!$YearPlanningModel->insert($data)) {
						$errors[] = $YearPlanningModel->errors();
					}
				} else {
					$missingFields = [];
					if (empty($entity_data['entities_id']))
						$missingFields[] = 'entities_id';
					if (empty($entity_data['entity_value']))
						$missingFields[] = 'entity_value';

					$errors[] = 'Missing required fields: ' . implode(', ', $missingFields);
				}
			}
		} else {
			$errors[] = 'No data was received';
		}

		if (!empty($errors)) {
			return $this->response->setJSON(['status' => 'error', 'errors' => $errors]);
		}


		return $this->response->setJSON(['status' => 'success', 'message' => 'Form submitted successfully!']);
	}


	public function year_planning_detail($ifield_id)
	{

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$YearPlanningModel = new YearPlanningModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$user_id = $user_info['user_id'];
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$isegment_val = $YearPlanningModel->where('id', $ifield_id)->first();

		$data['title'] = 'year_planning_details' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'year_planning_details';
		$data['breadcrumbs'] = 'year_planning_details' . $user_id;
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/planning/year_planning_details', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function update_year_planning_entity()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Session expired. Please login again.'
			]);
		}

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->find($usession['sup_user_id']);

		if (!$user_info) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'User not found.'
			]);
		}

		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$rules = [
			'entity_value' => 'required',
			'year' => 'required',
			'year_planning_id' => 'permit_empty'
		];

		if (!$this->validate($rules)) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Validation error',
				'errors' => $this->validator->getErrors()
			]);
		}

		$postData = $this->request->getPost([
			'entities_id',
			'entity_value',
			'year_planning_id',
			'year',
		], FILTER_SANITIZE_STRING);

		$data = [
			'entities_id' => $postData['entities_id'],
			'entity_value' => $postData['entity_value'],
			'year' => $postData['year'],
			'company_id' => $company_id,
			'user_type' => $user_type,
			'updated_at' => date('Y-m-d H:i:s')
		];

		$data2 = [
			'entities_id' => $postData['year_planning_id'],
			'entity_value' => $postData['entity_value'],
			'year' => $postData['year'],
			'company_id' => $company_id,
			'user_type' => $user_type,
			'created_at' => date('Y-m-d H:i:s')
		];

		$YearPlanningModel = new YearPlanningModel();
		$db = \Config\Database::connect();
		$db->transStart();

		try {
			if (empty($postData['entities_id'])) {
				$insertID = $YearPlanningModel->insert($data2);
				if (!$insertID) {
					throw new \Exception('Failed to insert record');
				}
				$message = 'Year planning created successfully!';
			} else {
				if (!$YearPlanningModel->update($postData['year_planning_id'], $data)) {
					throw new \Exception('Failed to update record');
				}
				$message = 'Year planning updated successfully!';
			}

			$db->transComplete();

			// Clear cache
			$cache = \Config\Services::cache();
			$cacheKey = 'year_planning_' . $company_id . '_' . $postData['year'];
			$cache->delete($cacheKey);

			return $this->response->setJSON([
				'status' => 'success',
				'message' => $message
			]);
		} catch (\Exception $e) {
			$db->transRollback();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred: ' . $e->getMessage()
			]);
		}
	}

	public function delete_year_planning()
	{
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			$UsersModel = new UsersModel();
			$user_info = $UsersModel->find($usession['sup_user_id']);

			if (!$user_info) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'User not found.'
				]);
			}


			$id = $this->request->getPost('_token', FILTER_SANITIZE_STRING);


			if (!$id) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Invalid ID.'
				]);
			}

			$company_id = $user_info['company_id'];
			$user_type = $user_info['user_type'];

			$YearPlanningModel = new YearPlanningModel();
			$data = $YearPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'id' => $id])->first();


			if (!$data) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'No data found for the provided ID.'
				]);
			}

			$result = $YearPlanningModel->where('id', $id)->delete();

			if ($result) {
				$session->setFlashdata('success', 'Year planning data deleted successfully.');
				return redirect()->to(site_url('erp/year-planning'));
			} else {
				$session->setFlashdata('error', 'Failed to delete year planning data or insufficient permissions.');
				return redirect()->to(site_url('erp/year-planning'));
			}
		}
	}


	public function monthly_planning()
	{

		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$data['title'] = 'Annual Planning' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'dashboard';
		$data['subview'] = view('erp/planning/testing', $data);
		// $data['subview'] = view('erp/planning/monthly_planning', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function delete_project()
	{

		if ($this->request->getPost('type') == 'delete_record') {
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
				$Return['redirect_url'] = base_url('erp/monthly-planning');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			$this->output($Return);
		}
	}

	public function monthly_planning_list()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$data['title'] = 'Monthly Plannning' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'monthly_plannning';
		$data['subview'] = view('erp/planning/monthly_planning_list', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function monthly_plan_submit()
	{

		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$MonthlyPlanningModel = new MonthlyPlanningModel();
		$YearPlanningModel = new YearPlanningModel();

		// Get the posted data
		$entities_data = $this->request->getPost('entities');
		$year = $this->request->getPost('year');
		$month = $this->request->getPost('month'); // Changed from getVar to getPost for consistency

		$month = strtolower($year);

		// echo $month;
		// echo $year;
		// echo $entities_data;
		// die;

		$yearData = $YearPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $year])->first();

		if (!empty($yearData)) {
			$existingYear = $yearData['year'];

			if ($existingYear) {

				$record = $MonthlyPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $year, 'month' => $month])->first();

				if ($record == null) {
					$errors = [];

					if (!empty($entities_data)) {
						foreach ($entities_data as $entity_data) {
							if (
								isset($entity_data['entities_id']) && !empty($entity_data['entities_id']) &&
								isset($entity_data['entity_value']) && !empty($entity_data['entity_value'])
							) {
								$data = [
									'entities_id' => $entity_data['entities_id'],
									'entity_value' => $entity_data['entity_value'],
									'year' => $year,
									'month' => $month,
									'company_id' => $company_id,
									'user_type' => $user_type,
								];

								if (!$MonthlyPlanningModel->insert($data)) {
									$errors[] = $MonthlyPlanningModel->errors();
								}
							} else {
								$errors[] = 'Missing required fields for entity ID ' . (isset($entity_data['entities_id']) ? $entity_data['entities_id'] : 'unknown');
							}
						}
					} else {
						$errors[] = 'No data was received';
					}

					if (!empty($errors)) {
						return $this->response->setJSON([
							'status' => 'error',
							'errors' => $errors
						]);
					}
					return $this->response->setJSON([
						'status' => 'success',
						'message' => 'Form submitted successfully!'
					]);
				} else {
					return $this->response->setJSON(['message' => 'This Year Month Data Already Exists.']);
				}
			} else {
				return $this->response->setJSON(['message' => 'First Plan This Year Data']);
			}
		} else {
			return $this->response->setJSON(['message' => 'First Plan This Year Data']);
		}
	}


	public function monthly_planning_detail($ifield_id)
	{

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$MonthlyPlanningModel = new MonthlyPlanningModel();
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
		// $segment_id = $request->uri->getSegment(3);
		// $ifield_id = udecode($segment_id);
		$isegment_val = $MonthlyPlanningModel->where('id', $ifield_id)->first();

		// if (!$isegment_val) {
		// 	$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
		// 	return redirect()->to(site_url('erp/desk'));
		// }


		$data['title'] = 'monthly_planning_details' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'monthly_planning_details';
		$data['breadcrumbs'] = 'monthly_planning_details' . $usession['sup_user_id'];
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/planning/monthly_planning_details', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function monthly_achive_submit()
	{

		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$MonthlyAchivedModel = new MonthlyAchivedModel();
		$YearPlanningModel = new YearPlanningModel();
		$MonthlyPlanningModel = new MonthlyPlanningModel();

		$entities_data = $this->request->getPost('entities');
		$year = $this->request->getPost('year');
		$month = $this->request->getPost('month');
		$month = strtolower($month);

		$yearData = $YearPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $year])->first();
		$existingYear = $yearData['year'];

		if ($existingYear) {

			$monthly_planned = $MonthlyPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $year, 'month' => $month])->first();
			$existingMonth = $monthly_planned['month'];
			if ($existingMonth) {
				$record = $MonthlyAchivedModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $year, 'month' => $month])->first();
				if ($record == null) {
					$errors = [];

					if (!empty($entities_data)) {
						foreach ($entities_data as $entity_data) {
							if (
								isset($entity_data['entities_id']) && !empty($entity_data['entities_id']) &&
								isset($entity_data['entity_value']) && !empty($entity_data['entity_value'])
							) {
								$data = [
									'entities_id' => $entity_data['entities_id'],
									'entity_value' => $entity_data['entity_value'],
									'year' => $year,
									'month' => $month,
									'company_id' => $company_id,
									'user_type' => $user_type,
								];

								if (!$MonthlyAchivedModel->insert($data)) {
									$errors[] = $MonthlyAchivedModel->errors();
								}
							} else {
								$errors[] = 'Missing required fields for entity ID ' . (isset($entity_data['entities_id']) ? $entity_data['entities_id'] : 'unknown');
							}
						}
					} else {
						$errors[] = 'No data was received';
					}

					if (!empty($errors)) {
						return $this->response->setJSON([
							'status' => 'error',
							'errors' => $errors
						]);
					}
					return $this->response->setJSON([
						'status' => 'success',
						'message' => 'Form submitted successfully!'
					]);
				} else {
					return $this->response->setJSON(['message' => 'This Year Month Data Already Exists.']);
				}
			} else {
				return $this->response->setJSON(['message' => 'FIrst Plan This  Month Data']);
			}
		} else {
			return $this->response->setJSON(['message' => 'First Plan This Year Data']);
		}
	}

	public function monthly_planning_review($ifield_id)
	{

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$MonthlyPlanningModel = new MonthlyPlanningModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$user_id = $user_info['user_id'];
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			return redirect()->to(site_url('erp/desk'));
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		// $segment_id = $request->uri->getSegment(3);
		// $ifield_id = udecode($segment_id);
		$isegment_val = $MonthlyPlanningModel->where('id', $ifield_id)->first();

		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}


		$data['title'] = 'monthly_planning_review' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'monthly_planning_review';
		$data['breadcrumbs'] = 'monthly_planning_review' . $user_id;
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/planning/monthly_planning_review', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function update_monthly_planning_entity()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Session expired. Please login again.'
			]);
		}

		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if (!$user_info) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'User not found.'
			]);
		}

		$company_id = $user_info['company_id'];
		$user_type = $user_info['user_type'];

		$rules = [
			'year' => 'required',
			'month' => 'required',
			'monthly_planning_id' => 'required|numeric',
			'entity_value' => 'required'
		];

		$validation = \Config\Services::validation();
		if (!$validation->setRules($rules)->run($this->request->getPost())) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Validation failed',
				'errors' => $validation->getErrors()
			]);
		}

		$postData = $this->request->getPost([
			'entity_value',
			'monthly_planning_id',
			'year',
			'month'
		]);

		$MonthlyPlanningModel = new MonthlyPlanningModel();
		$record = $MonthlyPlanningModel
			->where('company_id', $company_id)
			->where('user_type', $user_type)
			->where('id', $postData['monthly_planning_id'])
			->first();

		$updateData = [
			'entity_value' => $postData['entity_value'],
			'year' => $postData['year'],
			'month' => $postData['month'],
			'updated_at' => date('Y-m-d H:i:s')
		];

		$insertData = [
			'entities_id' => $postData['monthly_planning_id'],
			'entity_value' => $postData['entity_value'],
			'year' => $postData['year'],
			'month' => $postData['month'],
			'company_id' => $company_id,
			'user_type' => $user_type,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		$db = \Config\Database::connect();
		$db->transStart();

		try {
			if (empty($record)) {
				$insertID = $MonthlyPlanningModel->insert($insertData);

				if (!$insertID) {
					throw new \Exception('Failed to insert record');
				}

				$returnId = $insertID;
				$action = 'inserted';
			} else {
				if (!$MonthlyPlanningModel->update($postData['monthly_planning_id'], $updateData)) {
					throw new \Exception('Failed to update record');
				}

				$returnId = $postData['monthly_planning_id'];
				$action = 'updated';
			}

			$db->transComplete();

			if ($db->transStatus() === FALSE) {
				throw new \RuntimeException('Transaction failed');
			}

			return $this->response->setJSON([
				'status' => 'success',
				'message' => "Record {$action} successfully!",
				'data' => [
					'id' => $returnId,
					'entity_value' => $postData['entity_value']
				]
			]);
		} catch (\Exception $e) {
			$db->transRollback();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Operation failed',
				'system_error' => $e->getMessage(),
				'model_errors' => $MonthlyPlanningModel->errors() ?? null
			]);
		}
	}

	public function review_monthly_planning_entity()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Session data not found.'
			]);
		}

		$UsersModel = new UsersModel();
		$MonthlyPlanReviewModel = new MonthlyPlanReviewModel();
		$user_info = $UsersModel->find($usession['sup_user_id']);

		if (!$user_info) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'User not found.'
			]);
		}

		$company_id = $user_info['company_id'];

		$user_type = $user_info['user_type'];

		$rules = [
			'monthly_planning_id' => 'required',
			'status' => 'required',
			'comment' => 'required',
			'real_value' => 'required',
			'expected_value' => 'required'
		];

		if (!$this->validate($rules)) {
			$errors = $this->validator->getErrors();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Validation error',
				'errors' => $errors
			]);
		}

		$postData = $this->request->getPost([
			'monthly_planning_id',
			'status',
			'comment',
			'real_value',
			'expected_value'
		], FILTER_SANITIZE_STRING);

		$data = [
			'company_id' => (int) $company_id,
			'user_type' => $user_type,
			'monthly_plan_id' => (int) $postData['monthly_planning_id'],
			'status' => $postData['status'],
			'comment' => $postData['comment'],
			'real_value' => (int) $postData['real_value'],
			'expected_value' => (int) $postData['expected_value'],
		];
		$db = \Config\Database::connect();
		$db->transStart();

		try {
			$Successful = $MonthlyPlanReviewModel->insert($data);
			$db->transComplete();

			if ($db->transStatus() === FALSE || !$Successful) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Failed to save review entity.',
					'details' => $MonthlyPlanReviewModel->errors()
				]);
			}

			return $this->response->setJSON([
				'status' => 'success',
				'message' => 'Monthly Planning Review saved successfully!'
			]);
		} catch (\Exception $e) {
			$db->transRollback();
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred during the save operation.',
				'details' => $e->getMessage()
			]);
		}
	}

	public function delete_monthly_planning()
	{
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

			$session = \Config\Services::session();
			$usession = $session->get('sup_username');


			$id = $this->request->getPost('_token', FILTER_SANITIZE_STRING);

			if (!$id) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Invalid ID.'
				]);
			}

			$MonthlyPlanningModel = new MonthlyPlanningModel();
			$data = $MonthlyPlanningModel->where('id', $id)->first();
			if (!$data) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'No data found for the provided ID.'
				]);
			}

			$result = $MonthlyPlanningModel->where('id', $id)->delete();

			if ($result) {
				$session->setFlashdata('success', 'Monthly planning data deleted successfully.');
				return redirect()->to(site_url('erp/monthly-planning-list'));
			} else {
				$session->setFlashdata('error', 'Failed to delete Monthly planning data or insufficient permissions.');
				return redirect()->to(site_url('erp/monthly-planning-list'));
			}
		}
	}
}
