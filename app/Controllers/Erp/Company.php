<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\CompanysettingsModel;
use App\Models\EmailtemplatesModel;


class Company extends BaseController
{

	public function companies_list()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Projects.xin_manage_companies') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'companies_list';
		$data['breadcrumbs'] = lang('Projects.xin_manage_companies');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$data['subview'] = view('erp/company/companies_list', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function monthly_planning()
	{

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$segment_id = $request->getUri()->getSegment(3);
		$isegment_val = $UsersModel->where('user_id', $segment_id)->first();

		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$data['title'] = 'Annual Planning' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'dashboard';
		$data['breadcrumbs'] = 'Company Annual Planning';
		$data['isegment_val'] = $isegment_val;
		$data['subview'] = view('erp/planning/company_annual_planning', $data);
		return view('erp/layout/layout_main', $data); 
	}

	public function updateStatus($enc_id, $status)
	{
		$company_id = base64_decode($enc_id);
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$data = array('is_active' => $status);
		$UsersModel = new UsersModel();
		$result = $UsersModel->update($company_id, $data);
		if ($result) {
			$session->setFlashdata('success', 'Status Updated successfully.');
		} else {
			$session->setFlashdata('error', 'Failed to updated Status.');
		}
		return redirect()->back();
	}
	public function companies_grid()
	{

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
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Projects.xin_manage_companies') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'companies_grid';
		$data['breadcrumbs'] = lang('Projects.xin_manage_companies');
		$data['subview'] = view('erp/company/companies_grid', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function add_company()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();

			// Add file validation to your rules
			$validation->setRules([
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|valid_email',
				'username' => 'required|min_length[6]|is_unique[ci_erp_users.username]',
				'password' => 'required|min_length[6]',
				'contact_number' => 'required',

			], [
				'first_name' => [
					'required' => lang('Main.xin_employee_error_first_name'),
				],
				'last_name' => [
					'required' => lang('Main.xin_employee_error_last_name'),
				],
				'email' => [
					'required' => lang('Main.xin_employee_error_email'),
					'valid_email' => lang('Main.xin_employee_error_invalid_email'),
				],
				'username' => [
					'required' => lang('Main.xin_employee_error_username'),
					'min_length' => lang('Main.xin_min_error_username'),
					'is_unique' => lang('Main.xin_already_exist_error_username')
				],
				'password' => [
					'required' => lang('Main.xin_employee_error_password'),
					'min_length' => lang('Login.xin_min_error_password')
				],
				'contact_number' => [
					'required' => lang('Main.xin_error_contact_field'),
				],

			]);

			if (!$validation->withRequest($request)->run()) {
				$session->setFlashdata('error', $validation->getErrors());
				return redirect()->to(site_url('erp/companies-list'));
			}

			$file_name = '';
			$avatar = $request->getFile('file');

			// Make sure the file is valid
			if ($avatar->isValid() && !$avatar->hasMoved()) {
				$newName = $avatar->getRandomName();

				// Ensure directories exist
				if (!is_dir(ROOTPATH . 'uploads/companies')) {
					mkdir(ROOTPATH . 'uploads/companies', 0755, true);
				}
				if (!is_dir(ROOTPATH . 'uploads/companies/thumb')) {
					mkdir(ROOTPATH . 'uploads/companies/thumb', 0755, true);
				}

				// Move the file
				$avatar->move(ROOTPATH . 'uploads/companies', $newName);
				$file_name = $newName;

				// Create thumbnail
				$image = \Config\Services::image()
					->withFile(ROOTPATH . 'uploads/companies/' . $newName)
					->fit(100, 100, 'center')
					->save(ROOTPATH . 'uploads/companies/thumb/' . $newName);
			}

			$data = [
				'first_name' => $request->getPost('first_name', FILTER_SANITIZE_STRING),
				'last_name' => $request->getPost('last_name', FILTER_SANITIZE_STRING),
				'email' => $request->getPost('email', FILTER_SANITIZE_EMAIL),
				'user_type' => 'company',
				'username' => $request->getPost('username', FILTER_SANITIZE_STRING),
				'password' => password_hash($request->getPost('password'), PASSWORD_BCRYPT),
				'contact_number' => $request->getPost('contact_number', FILTER_SANITIZE_STRING),
				'gender' => $request->getPost('gender', FILTER_SANITIZE_STRING),
				'profile_photo' => $file_name,
				'country'  => 99,
				'user_role_id' => 0,
				'address_1'  => '',
				'address_2'  => '',
				'city'  => '',
				'state'  => '',
				'zipcode' => '',
				'company_name' => $request->getPost('company_name', FILTER_SANITIZE_STRING),
				'trading_name' => '',
				'registration_no' => '',
				'government_tax' => '',
				'company_type_id'  => 0,
				'last_login_date' => '0',
				'last_logout_date' => '0',
				'last_login_ip' => '0',
				'is_logged_in' => '0',
				'is_active'  => 1,
				'created_at' => date('Y-m-d H:i:s'),
			];

			$UsersModel = new UsersModel();

			$check = $UsersModel->where('user_type', 'company')->where('email', $data['email'])->first();

			if (!$check) {
				$result = $UsersModel->insert($data);
				$user_id = $UsersModel->insertID();

				if ($result) {
					$UsersModel->update($user_id, ['company_id' => $user_id]);

					$CompanysettingsModel = new CompanysettingsModel();
					$newData = [
						'company_id' => $user_id,
						'default_currency' => 'INR',
						'default_currency_symbol' => 'INR',
						'notification_position' => 'toast-top-center',
						'notification_close_btn' => '0',
						'notification_bar' => 'true',
						'date_formate_xi' => 'Y.m.d'
					];
					$CompanysettingsModel->insert($newData);

					$xin_system = new SystemModel();
					$xin_system_data = $xin_system->where('setting_id', 1)->first();

					if ($xin_system_data && $xin_system_data['enable_email_notification'] == 1) {
						$EmailtemplatesModel = new EmailtemplatesModel();
						$itemplate = $EmailtemplatesModel->where('template_id', 5)->first();

						if ($itemplate) {
							$isubject = $itemplate['subject'];
							$ibody = html_entity_decode($itemplate['message']);
							$fbody = str_replace(
								array("{site_name}", "{user_password}", "{user_username}", "{site_url}"),
								array($xin_system_data['company_name'], $request->getPost('password'), $request->getPost('username'), site_url()),
								$ibody
							);
							timehrm_mail_data($xin_system_data['email'], $xin_system_data['company_name'], $data['email'], $isubject, $fbody);
						}
					}

					$session->setFlashdata('success', 'Company Added Successfully!');
					return redirect()->to(site_url('erp/companies-list'));
				} else {
					$session->setFlashdata('error', lang('Main.xin_error_msg'));
					return redirect()->to(site_url('erp/companies-list'));
				}
			} else {
				$session->setFlashdata('error', 'Email ID already exists');
				return redirect()->to(site_url('erp/companies-list'));
			}
		} else {
			$session->setFlashdata('error', lang('Main.xin_error_msg'));
			return redirect()->to(site_url('erp/companies-list'));
		}
	}

	public function company_details()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$segment_id = $request->getUri()->getSegment(3);
		$ifield_id = udecode($segment_id);
		$isegment_val = $UsersModel->where('user_id', $ifield_id)->first();
		$user_id = $isegment_val['user_id'];
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$data['title'] = 'company_details' . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'company-detail';
		$data['breadcrumbs'] = 'company-detail' . $user_id;
		$data['subview'] = view('erp/company/company_detail', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function update_company()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();

			$user_id = $request->getPost('user_id');

			$validation->setRules([
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|valid_email',
				'username' => 'required',
				'contact_number' => 'required',
				'company_name' => 'required',
			], [
				'first_name' => ['required' => lang('Main.xin_employee_error_first_name')],
				'last_name' => ['required' => lang('Main.xin_employee_error_last_name')],
				'email' => [
					'required' => lang('Main.xin_employee_error_email'),
					'valid_email' => lang('Main.xin_employee_error_invalid_email'),
				],
				'username' => [
					'required' => lang('Main.xin_employee_error_username'),
					'min_length' => lang('Main.xin_min_error_username'),
					'is_unique' => lang('Main.xin_already_exist_error_username')
				],
				'contact_number' => ['required' => lang('Main.xin_error_contact_field')],
				'company_name' => ['required' => lang('Main.dashboard_companyname_error')],
			]);

			if (!$validation->withRequest($request)->run()) {
				$session->setFlashdata('error', $Return['error']);
				return redirect()->to(site_url('erp/companies-list'));
			}

			$file_name = $request->getPost('old_file');
			if (!empty($_FILES['file']['name'])) {
				$image = \Config\Services::image();
				$validated = $this->validate([
					'file' => [
						'uploaded[file]',
						'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
						'max_size[file,4096]',
					],
				]);

				if (!$validated) {
					$Return['error'] = lang('Employees.xin_staff_picture_field_error');
					$session->setFlashdata('error', $Return['error']);
					return redirect()->to(site_url('erp/companies-list'));
				}

				$avatar = $request->getFile('file');
				$file_name = $avatar->getName();
				$avatar->move('uploads/companies/');

				$image->withFile('uploads/companies/' . $file_name)
					->fit(100, 100, 'center')
					->save('uploads/companies/thumb/' . $file_name);
			}

			// Prepare data for update
			$data = [
				'first_name' => $request->getPost('first_name', FILTER_SANITIZE_STRING),
				'last_name' => $request->getPost('last_name', FILTER_SANITIZE_STRING),
				'email' => $request->getPost('email', FILTER_SANITIZE_EMAIL),
				'username' => $request->getPost('username', FILTER_SANITIZE_STRING),
				'contact_number' => $request->getPost('contact_number', FILTER_SANITIZE_STRING),
				'gender' => $request->getPost('gender', FILTER_SANITIZE_STRING),
				'profile_photo' => $file_name,
				'company_name' => $request->getPost('company_name', FILTER_SANITIZE_STRING),
			];

			$UsersModel = new UsersModel();
			$result = $UsersModel->update($user_id, $data);

			if ($result) {
				$session->setFlashdata('success', 'Company Updated Successfully!');
				return redirect()->to(site_url('erp/companies-list'));
			} else {
				$session->setFlashdata('error', lang('Main.xin_error_msg'));
				return redirect()->to(site_url('erp/companies-list'));
			}
		} else {
			$session->setFlashdata('error', lang('Main.xin_error_msg'));
			return redirect()->to(site_url('erp/companies-list'));
		}
	}


	public function delete_company()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));

		$UsersModel = new UsersModel();

		$company = $UsersModel->find($id);
		$response = [
			'result' => '',
			'error' => ''
		];

		if (!$company) {
			$session->setFlashdata('error', lang('Main.xin_company_not_found'));
			return redirect()->to(site_url('erp/companies-list'));
		}

		$updated = $UsersModel->delete($id);	

		if ($updated) {
			$session->setFlashdata('success', 'Company Deleted Successfully!');
			return redirect()->to(site_url('erp/companies-list'));
		} else {
			$session->setFlashdata('error', lang('Main.xin_error_msg'));
			return redirect()->to(site_url('erp/companies-list'));
		}

		return $this->output($response);
	}
}
