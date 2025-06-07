<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\EmailtemplatesModel;

class Auth extends BaseController
{

	protected $request;

	public function login()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$UsersModel = new UsersModel();

		if (!$this->request->isAJAX()) {
			return $this->response->setJSON([
				'error' => lang('Main.xin_error_msg'),
				'csrf_hash' => csrf_hash()
			]);
		}

		$Return = [
			'result' => '',
			'error' => '',
			'csrf_hash' => csrf_hash(),
			'redirect' => '' // Add redirect URL to response
		];

		// set rules
		$rules = [
			'iusername' => [
				'rules'  => 'required',
				'errors' => [
					'required' => lang('Login.xin_employee_error_username')
				]
			],
			'password' => [
				'rules'  => 'required|min_length[6]',
				'errors' => [
					'required' => lang('Main.xin_employee_error_password'),
					'min_length' => lang('Login.xin_min_error_password')
				]
			]
		];

		if (!$this->validate($rules)) {
			$ruleErrors = [
				"iusername" => $validation->getError('iusername'),
				"password" => $validation->getError('password'),
			];
			foreach ($ruleErrors as $err) {
				if (!empty($err)) {
					$Return['error'] = $err;
					break;
				}
			}
			return $this->response->setJSON($Return);
		}

		$username = $this->request->getPost('iusername', FILTER_SANITIZE_STRING);
		$password = $this->request->getPost('password', FILTER_SANITIZE_STRING);
		$throttler = \Config\Services::throttler();

		// Check throttling
		if (!$throttler->check('auth', 5, MINUTE)) {
			$Return['error'] = lang('Login.xin_error_max_attempts');
			return $this->response->setJSON($Return);
		}

		$iuser = $UsersModel->where('username', $username)
			->where('is_active', 1)
			->first();

		if (!$iuser || !password_verify($password, $iuser['password'])) {
			$Return['error'] = lang('Login.xin_error_invalid_credentials');
			return $this->response->setJSON($Return);
		}

		// Create session data
		$session_data = [
			'sup_user_id' => $iuser['user_id'],
			'sup_username' => $iuser['username'],
			'sup_email' => $iuser['email'],
			'is_logged_in' => true
		];

		$session->set('sup_username', $session_data);
		$session->set('sup_user_id', $session_data);

		// Update last login info
		$ipaddress = $request->getIPAddress();
		$UsersModel->update($iuser['user_id'], [
			'last_login_date' => date('d-m-Y H:i:s'),
			'last_login_ip' => $ipaddress,
			'is_logged_in' => '1'
		]);

		$Return['result'] = lang('Login.xin_success_logged_in');
		$Return['redirect'] = base_url('dashboard'); // Set explicit redirect URL
		$Return['csrf_hash'] = csrf_hash();

		return $this->response->setJSON($Return);
	}
	// unlock user.	
	public function forgot_password()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$session->remove('sup_username');
		$data['title'] = lang('Dashboard.xin_lock_user');
		$Return['result'] = 'Locked User.';
		return view('erp/auth/forgot_password', $data);
	}


	//forgot password
	public function check_password()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_user_id');
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$EmailtemplatesModel = new EmailtemplatesModel();

		if ($this->request->getPost('add_type') === 'forgot_password') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'email' => [
					'rules'  => 'required|valid_email',
					'errors' => [
						'required' => lang('Main.xin_employee_error_email'),
						'valid_email' => lang('Main.xin_employee_error_invalid_email')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"email" => $validation->getError('email'),
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
				$check_user = $UsersModel->where(['email' => $email,'is_active' => 1])->countAllResults();
				if ($check_user > 0) {
					$Return['result'] = lang('Main.xin_error_msg__available');
					$iuser = $UsersModel->where(['email'=>$email, 'is_active'=>1])->first();
					$username = $iuser['username'];
					$password = $iuser['password'];

					$xin_system = $SystemModel->where('setting_id', 1)->first();
					$template = $EmailtemplatesModel->where('template_id', 1)->first();

					$subject = $template['subject'];
					$body = html_entity_decode($template['message']);
					$body = str_replace(array("{site_name}", "{site_url}", "{user_id}"), array($xin_system['company_name'], site_url(), uencode($email)), $body);
					timehrm_mail_data($xin_system['email'], $xin_system['company_name'], $email, $subject, $body);
					return $this->response->setJSON($Return);
				} else {
					$Return['error'] = lang('Main.xin_error_msg_not');
					return $this->response->setJSON($Return);
				}
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}
	// verified_password
	public function verified_password()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$EmailtemplatesModel = new EmailtemplatesModel();
		$email = udecode($_GET['v']);

		$check_user = $UsersModel->where('email', $email)->countAllResults();
		if ($check_user > 0) {
			$iuser = $UsersModel->where(['email'=>$email, 'is_active'=>1])->first();
			$username = $iuser['username'];
			$options = array('cost' => 12);
			$password = 'Hu2k4JHik42ol4hH32';
			$password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

			$xin_system = $SystemModel->where('setting_id', 1)->first();
			$data = [
				'password' => $password_hash,
				'title' => lang('Verified'),
			];
			$UsersModel->update($iuser['user_id'], $data);
			// Send mail start
			$template = $EmailtemplatesModel->where('template_id', 2)->first();
			$subject = $template['subject'];
			$body = html_entity_decode($template['message']);
			$body = str_replace(array("{site_name}", "{password}", "{username}"), array($xin_system['company_name'], $password, $username), $body);
			timehrm_mail_data($xin_system['email'], $xin_system['company_name'], $email, $subject, $body);
			// Send mail end
		}

		return view('erp/auth/verified_password', $data);
	}
	//unlock user.
	public function unlock()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_user_id');
		$UsersModel = new UsersModel();

		if ($this->request->getPost('type') === 'unlock_user') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'password' => [
					'rules'  => 'required|min_length[6]',
					'errors' => [
						'required' => lang('Main.xin_employee_error_password'),
						'min_length' => lang('Login.xin_min_error_password')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"password" => $validation->getError('password'),
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$throttler = \Config\Services::throttler();
				$is_allow = $throttler->check('auth', 5, MINUTE);
				$iuser = $UsersModel->where('user_id', $usession['sup_user_id'], 'is_active', 1)->first();
				$password = $this->request->getPost('password', FILTER_SANITIZE_STRING);
				$username = $iuser['username'];
				$user_info = $UsersModel->where('username', $username)->where('is_active', 1)->first();
				$data = array(
					'username' => $username,
					'password' => $password
				);
				if ($is_allow) {
					if (password_verify($password, $user_info['password'])) {
						$session_data = array(
							'sup_user_id' => $user_info['user_id'],
							'sup_username' => $iuser['username'],
							'sup_email' => $user_info['email'],
						);
						// Add user data in session
						$session->set('sup_username', $session_data);
						$session->set('sup_user_id', $session_data);
						$Return['result'] = lang('Login.xin_success_logged_in');

						// get user session record
						$ipaddress = $request->getIPAddress();
						$last_data = array(
							'last_login_date' => date('d-m-Y H:i:s'),
							'last_login_ip' => $ipaddress,
							'is_logged_in' => '1'
						);

						$id = $user_info['user_id']; // user id
						// update last login record
						$UsersModel->update($id, $last_data);
						$Return['csrf_hash'] = csrf_hash();
						//$this->session->set_flashdata('expire_official_document', 'expire_official_document');
						$this->output($Return);
					} else {
						$Return['error'] = lang('Login.xin_error_invalid_credentials');
						/*Return*/
						$Return['csrf_hash'] = csrf_hash();
						$this->output($Return);
					}
				} else {
					$session->setFlashdata('xin_error_max_attempts', lang('Login.xin_error_max_attempts'));
					$Return['error'] = lang('Login.xin_error_max_attempts');
					/*Return*/
					$Return['csrf_hash'] = csrf_hash();
					$this->output($Return);
				}
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			$this->output($Return);
			exit;
		}
	}
}
