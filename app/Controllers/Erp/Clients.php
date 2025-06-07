<?php

namespace App\Controllers\Erp;


use App\Controllers\BaseController;

use CodeIgniter\I18n\Time;

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\LeadsModel;
use App\Models\CountryModel;
use App\Models\LeadsfollowupModel;
use App\Models\EmailtemplatesModel;
use App\Models\LeadConfigModel;
use App\Models\AccountDetailModel;

class Clients extends BaseController
{

	public function index()
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
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('client1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Projects.xin_manage_clients') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'clients';
		$data['breadcrumbs'] = lang('Projects.xin_manage_clients');

		$data['subview'] = view('erp/clients/clients_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function leads_index()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$LeadConfig = new LeadConfigModel();

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
			if (!in_array('leads1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_leads') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'leads';
		$data['breadcrumbs'] = lang('Dashboard.xin_leads');

		$data['subview'] = view('erp/clients/leads_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function web_leads_index()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$LeadConfig = new LeadConfigModel();

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
			if (!in_array('leads1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = "Web Leads" . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'web_leads';
		$data['breadcrumbs'] = "Web Leads";

		$data['subview'] = view('erp/clients/web_leads_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}


	public function clients_grid()
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
			if (!in_array('client1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Projects.xin_manage_clients') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'clients_grid';
		$data['breadcrumbs'] = lang('Projects.xin_manage_clients');

		$data['subview'] = view('erp/clients/clients_grid', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function client_details($id=null)
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		//$AssetsModel = new AssetsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$request = \Config\Services::request();
		// $ifield_id = udecode($request->uri->getSegment(3));
		
		$isegment_val = $UsersModel->where('user_id', $id)->first();
		
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
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
			if (!in_array('client1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Main.xin_client_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'client_details';
		$data['breadcrumbs'] = lang('Main.xin_client_details');
		$data['client_id'] = $id;
		$data['subview'] = view('erp/clients/client_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	// list
	public function clients_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		
		// Check session
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		// Load models
		$UsersModel = new UsersModel();
		$RolesModel = new RolesModel();
		$SystemModel = new SystemModel();
		$CountryModel = new CountryModel();
		
		try {
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if (!$user_info) {
				throw new \Exception('User not found');
			}

			// Get clients based on user type
			$query = $UsersModel->where('user_type', 'customer')->orderBy('user_id', 'ASC');
			
			if ($user_info['user_type'] == 'staff') {
				$clients = $query->where('company_id', $user_info['company_id'])->findAll();
			} else {
				$clients = $query->where('company_id', $usession['sup_user_id'])->findAll();
			}

			$xin_system = $SystemModel->where('setting_id', 1)->first();
			$data = [];

			foreach ($clients as $client) {
				try {
					// Edit button
					$edit = '';
					if (in_array('client3', staff_role_resource()) || $user_info['user_type'] == 'company') {
						$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" 
							title="' . lang('Main.xin_view_details') . '">
							<a href="' . site_url('erp/view-client-info') . '/' .$client['user_id']. '">
								<button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light">
									<i class="feather icon-arrow-right"></i>
								</button>
							</a>
						</span>';
					}

					// Delete button
					$delete = '';
					if (in_array('client4', staff_role_resource()) || $user_info['user_type'] == 'company') {
						$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" 
							title="' . lang('Main.xin_delete') . '">
							<button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" 
								data-toggle="modal" data-target=".delete-modal" 
								data-record-id="' .$client['user_id']. '">
								<i class="feather icon-trash-2"></i>
							</button>
						</span>';
					}

					// Status
					$status = match ((int)$client['is_active']) {
						1 => '<span class="badge badge-light-success">' . lang('Main.xin_employees_active') . '</span>',
						2 => '<span class="badge badge-light-danger">' . lang('Main.xin_employees_inactive') . '</span>',
						default => '<span class="badge badge-light-secondary">' . lang('Main.xin_unknown') . '</span>',
					};

					// Gender
					$gender = match ((int)$client['gender']) {
						1 => lang('Main.xin_gender_male'),
						default => lang('Main.xin_gender_female'),
					};

					// Country
					$country_name = '';
					if (!empty($client['country'])) {
						$country_info = $CountryModel->where('country_id', $client['country'])->first();
						$country_name = $country_info['country_name'] ?? '';
					}

					// Client name and photo
					$name = htmlspecialchars(($client['first_name'] ?? '')) . ' ' . htmlspecialchars(($client['last_name'] ?? ''));
					$profile_photo = !empty($client['profile_photo']) ? $client['profile_photo'] : 'default.png';
					
					$uname = '<div class="d-inline-block align-middle">
						<img src="' . base_url('uploads/clients/thumb/' . $profile_photo) . '" 
							alt="user image" class="img-radius align-top m-r-15" style="width:40px;">
						<div class="d-inline-block">
							<h6 class="m-b-0">' . $name . '</h6>
							<p class="m-b-0">' . htmlspecialchars($client['email'] ?? '') . '</p>
						</div>
					</div>';

					// Combine buttons
					$combhr = $edit . $delete;
					
					// Final links column
					if (in_array('client3', staff_role_resource()) || 
						in_array('client4', staff_role_resource()) || 
						$user_info['user_type'] == 'company') {
						$links = $uname . '<div class="overlay-edit">' . $combhr . '</div>';
					} else {
						$links = $uname;
					}

					$data[] = [
						$links,
						htmlspecialchars($client['username'] ?? ''),
						htmlspecialchars($client['contact_number'] ?? ''),
						$gender,
						$country_name,
						$status
					];

				} catch (\Exception $e) {
					log_message('error', 'Error processing client record: ' . $e->getMessage());
					continue; // Skip this client but continue with others
				}
			}

			return $this->response->setJSON([
				"data" => $data
			]);

		} catch (\Exception $e) {
			log_message('error', 'Error in clients_list: ' . $e->getMessage());
			return $this->response->setStatusCode(500)->setJSON([
				'error' => 'An error occurred while processing your request',
				'details' => ENVIRONMENT === 'development' ? $e->getMessage() : null
			]);
		}
	}

	// list
	public function leads_followup_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$request = \Config\Services::request();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$UsersModel = new UsersModel();
		$LeadsfollowupModel = new LeadsfollowupModel();
		$RolesModel = new RolesModel();
		$SystemModel = new SystemModel();
		$CountryModel = new CountryModel();

		$lead_id = udecode($this->request->getVar('xlead_id', FILTER_SANITIZE_STRING));

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$followup = $LeadsfollowupModel->where('company_id', $user_info['company_id'])->where('lead_id', $lead_id)->orderBy('followup_id', 'ASC')->findAll();
		} else {
			$followup = $LeadsfollowupModel->where('company_id', $usession['sup_user_id'])->where('lead_id', $lead_id)->orderBy('followup_id', 'ASC')->findAll();
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data = array();

		foreach ($followup as $r) {

			$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_id="' . uencode($r['followup_id']) . '"><i class="feather icon-edit"></i></button></span>';

			$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['followup_id']) . '"><i class="feather icon-trash-2"></i></button></span>';

			$combhr = $edit . $delete;
			$created_at = set_date_format($r['created_at']);
			$next_followup = set_date_format($r['next_followup']);
			$inext_followup = '
			' . $next_followup . '
			<div class="overlay-edit">
				' . $combhr . '
			</div>';


			$data[] = array(
				$inext_followup,
				$r['description'],
				$created_at,
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
	public function add_followup()
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
				'next_follow_up' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'description' => [
					'rules' => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"next_follow_up" => $validation->getError('next_follow_up'),
					"description" => $validation->getError('description')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$lead_id = $this->request->getPost('token', FILTER_SANITIZE_STRING);
				$next_follow_up = $this->request->getPost('next_follow_up', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'lead_id' => $lead_id,
					'company_id' => $company_id,
					'next_followup' => $next_follow_up,
					'description' => $description,
					'created_at' => date('d-m-Y h:i:s')
				];
				$LeadsfollowupModel = new LeadsfollowupModel();
				$result = $LeadsfollowupModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_lead_followup_added_msg');
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
	public function update_followup($follow_id)
	{
		$session = \Config\Services::session();
		$LeadsfollowupModel = new LeadsfollowupModel();

		// Check if this is a POST request
		if (!$this->request->is('post')) {
			return redirect()->back()->with('error', 'Invalid request method');
		}

		// Validate input
		$validation = \Config\Services::validation();
		$validation->setRules([
			'next_follow_up' => [
				'rules' => 'required|valid_date[Y-m-d]',
				'errors' => [
					'required' => 'Follow up date is required',
					'valid_date' => 'Please enter a valid date'
				]
			],
			'description' => [
				'rules' => 'required|min_length[5]|max_length[500]',
				'errors' => [
					'required' => 'Description is required',
					'min_length' => 'Description must be at least 5 characters',
					'max_length' => 'Description cannot exceed 500 characters'
				]
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			$errors = implode("<br>", $validation->getErrors());
			return redirect()->back()->with('error', $errors)->withInput();
		}

		try {
			// Get lead_id before update in case update fails
			$followup = $LeadsfollowupModel->find($follow_id);
			if (!$followup) {
				throw new \RuntimeException('Follow-up record not found');
			}
			$lead_id = $followup['lead_id'];

			$data = [
				'next_followup' => $this->request->getPost('next_follow_up'),
				'description' => $this->request->getPost('description'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($LeadsfollowupModel->update($follow_id, $data)) {
				$session->setFlashdata('message', 'Follow-up updated successfully');
			} else {
				throw new \RuntimeException('Failed to update follow-up');
			}

		} catch (\Exception $e) {
			log_message('error', 'Follow-up update error: ' . $e->getMessage());
			$lead_id = $lead_id ?? $LeadsfollowupModel->select('lead_id')
													->where('followup_id', $follow_id)
													->first()['lead_id'] ?? null;
			
			$session->setFlashdata('error', $e->getMessage());
			
			if (!$lead_id) {
				return redirect()->to(base_url('erp/leads'))->with('error', 'Could not determine lead');
			}
		}

		return redirect()->to(base_url('erp/view-lead-info/' . $lead_id));
	}

	// |||add record|||
	public function add_client()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'add_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'first_name' => 'required',
					'last_name' => 'required',
					'email' => 'required|valid_email|is_unique[ci_erp_users.email]',
					'username' => 'required|min_length[6]|is_unique[ci_erp_users.username]',
					'password' => 'required|min_length[6]',
					'contact_number' => 'required'
				],
				[   // Errors
					'first_name' => [
						'required' => lang('Main.xin_employee_error_first_name'),
					],
					'last_name' => [
						'required' => lang('Main.xin_employee_error_last_name'),
					],
					'email' => [
						'required' => lang('Main.xin_employee_error_email'),
						'valid_email' => lang('Main.xin_employee_error_invalid_email'),
						'is_unique' => lang('Main.xin_already_exist_error_email'),
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
			} elseif ($validation->hasError('password')) {
				$Return['error'] = $validation->getError('password');
			} elseif ($validation->hasError('contact_number')) {
				$Return['error'] = $validation->getError('contact_number');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}

			$file_name = '';
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
					$this->output($Return);
				}

				$avatar = $this->request->getFile('file');
				$file_name = $avatar->getName();
				$avatar->move('uploads/clients/');

				$image->withFile('uploads/clients/' . $file_name)
					->fit(100, 100, 'center')
					->save('uploads/clients/thumb/' . $file_name);
			}

			$first_name = $this->request->getPost('first_name', FILTER_SANITIZE_STRING);
			$last_name = $this->request->getPost('last_name', FILTER_SANITIZE_STRING);
			$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
			$username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
			$password = $this->request->getPost('password', FILTER_SANITIZE_STRING);
			$contact_number = $this->request->getPost('contact_number', FILTER_SANITIZE_STRING);
			$gender = $this->request->getPost('gender', FILTER_SANITIZE_STRING);
			$options = array('cost' => 12);
			$password_hash = password_hash($password, PASSWORD_BCRYPT, $options);

			$UsersModel = new UsersModel();
			$SystemModel = new SystemModel();
			$EmailtemplatesModel = new EmailtemplatesModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
				$iuser_info = $UsersModel->where('company_id', $company_id)->first();
			} else {
				$company_id = $usession['sup_user_id'];
				$iuser_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			}
			$xin_system = $SystemModel->where('setting_id', 1)->first();
			$data = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'user_type' => 'customer',
				'username' => $username,
				'password' => $password_hash,
				'contact_number' => $contact_number,
				'country' => 0,
				'user_role_id' => 0,
				'address_1' => '',
				'address_2' => '',
				'city' => '',
				'profile_photo' => $file_name,
				'state' => '',
				'zipcode' => '',
				'gender' => $gender,
				'company_name' => $iuser_info['company_name'],
				'trading_name' => '',
				'registration_no' => '',
				'government_tax' => '',
				'company_type_id' => 0,
				'last_login_date' => '0',
				'last_logout_date' => '0',
				'last_login_ip' => '0',
				'is_logged_in' => '0',
				'is_active' => 1,
				'company_id' => $company_id,
				'created_at' => date('d-m-Y h:i:s')
			];
			$result = $UsersModel->insert($data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_client_added_msg');
				if ($xin_system['enable_email_notification'] == 1) {
					// Send mail start
					$itemplate = $EmailtemplatesModel->where('template_id', 5)->first();
					$isubject = $itemplate['subject'];
					$ibody = html_entity_decode($itemplate['message']);
					$fbody = str_replace(array("{site_name}", "{user_password}", "{user_username}", "{site_url}"), array($user_info['company_name'], $password, $username, site_url()), $ibody);
					timehrm_mail_data($user_info['email'], $user_info['company_name'], $email, $isubject, $fbody);
					// Send mail end
				}
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	public function insertLead()
	{
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$LeadConfig = new LeadConfigModel();
		$cache = \Config\Services::cache();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
		$table_name = 'leads_' . $company_name;

		// Fetch lead fields
		$leadFields = $LeadConfig->groupStart()
			->where('company_id', $user_info['company_id'])
			->orWhere('company_id', null)
			->groupEnd()
			->orderBy('id', 'ASC')
			->findAll();

		$leadData = [];

		foreach ($leadFields as $field) {
			$fieldName = rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_');

			// Set opportunity_id from POST
			$leadData['opportunity_id'] = $this->request->getPost('opportunity_id');
			$leadData['lead_status'] = $this->request->getPost('lead_status');
			$leadData['sources_name'] = $this->request->getPost('source_name');

			// Check for file upload based on dynamic field name
			if (!empty($_FILES[$fieldName]['name'])) {
				// Validate the uploaded file
				$image = \Config\Services::image();
				$validated = $this->validate([
					$fieldName => [
						'uploaded[' . $fieldName . ']',
						'mime_in[' . $fieldName . ',image/jpg,image/jpeg,image/gif,image/png]',
						'max_size[' . $fieldName . ',4096]',
					],
				]);

				if (!$validated) {
					$errors = $this->validator->getErrors();
					$session->setFlashdata('message', 'Error uploading image for ' . $fieldName . ': ' . implode(', ', $errors));
					return redirect()->back();
				}

				// Move the uploaded file and resize it
				$avatar = $this->request->getFile($fieldName);
				$file_name = $avatar->getRandomName();
				$avatar->move('uploads/leads/', $file_name);

				// Resize and save the image
				$image->withFile('uploads/leads/' . $file_name)
					->fit(100, 100, 'center')
					->save('uploads/' . $file_name);

				// Save the file name in the leadData array
				$leadData[$fieldName] = $file_name;
			} else {
				// Get the post data for other fields
				$fieldValue = $this->request->getPost($fieldName);
				if ($fieldValue !== null && $fieldValue !== '') {
					$leadData[$fieldName] = $fieldValue;
				}
			}
		}

		try {
			$db = \Config\Database::connect();
			$builder = $db->table($table_name);

			log_message('debug', 'Lead data being inserted: ' . json_encode($leadData));

			// Insert lead data into the database
			if ($builder->insert($leadData)) {
				$cacheKey = 'lead_data_' . $user_info['user_id'];
				$cache->delete($cacheKey);
				$session->setFlashdata('message', 'Lead successfully added');
			} else {
				$errors = $db->error();
				log_message('error', 'Database insert error: ' . json_encode($errors));
				$session->setFlashdata('message', 'Error occurred while adding lead: ' . $errors['message']);
			}
		} catch (\Exception $e) {
			log_message('error', 'Database error: ' . $e->getMessage());
			$session->setFlashdata('message', 'An error occurred: ' . $e->getMessage());
		}

		return redirect()->back();
	}

	public function insertLeadApi()
	{
		$UsersModel = new UsersModel();
		$LeadConfig = new LeadConfigModel();
		$cache = \Config\Services::cache();
		$db = \Config\Database::connect();

		try {
			$username = $this->request->getPost('username');

			if (empty($username)) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Username parameter is required'
				])->setStatusCode(400);
			}

			$user = $UsersModel->where(['username' => $username, 'user_type' => 'company'])->first();
			if (!$user) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Company not found'
				])->setStatusCode(404);
			}

			$company_id = $user['company_id'] ?? null;
			if (!$company_id) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Invalid company ID'
				])->setStatusCode(400);
			}

			$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user['company_name']));
			$table_name = 'leads_' . $company_name;

			// Fetch lead configuration fields
			$leadFields = $LeadConfig->groupStart()
				->where('company_id', null)
				->orWhere('company_id', $company_id)
				->groupEnd()
				->orderBy('id', 'ASC')
				->findAll();

			if (empty($leadFields)) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'No lead configuration fields found.'
				])->setStatusCode(400);
			}

			$leadData = [];
			$leadData['opportunity_id'] = $this->request->getPost('opportunity_id');
			$leadData['lead_status'] = $this->request->getPost('lead_status');
			$leadData['sources_name'] = $this->request->getPost('source_name');

			// Process dynamic fields
			foreach ($leadFields as $field) {
				$fieldName = rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_');

				if (!empty($_FILES[$fieldName]['name'])) {
					$avatar = $this->request->getFile($fieldName);

					if ($avatar->isValid() && !$avatar->hasMoved()) {
						$file_name = $avatar->getRandomName();
						$avatar->move('uploads/leads/', $file_name);
						$leadData[$fieldName] = $file_name;
					} else {
						return $this->response->setJSON([
							'status' => 'error',
							'message' => 'Error uploading file: ' . $avatar->getErrorString()
						])->setStatusCode(400);
					}
				} else {
					$fieldValue = $this->request->getPost($fieldName);
					if ($fieldValue !== null && $fieldValue !== '') {
						$leadData[$fieldName] = $fieldValue;
					}
				}
			}

			// Insert lead data
			$builder = $db->table($table_name);
			if ($builder->insert($leadData)) {
				$cacheKey = 'lead_data_' . $company_id;
				$cache->delete($cacheKey);

				return $this->response->setJSON([
					'status' => 'success',
					'message' => 'Lead successfully added.',
					'lead_id' => $db->insertID()
				])->setStatusCode(201);
			} else {
				$errors = $db->error();
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Database insert error: ' . json_encode($errors)
				])->setStatusCode(500);
			}
		} catch (\Exception $e) {
			log_message('error', 'Lead Insert API Error: ' . $e->getMessage());
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}


	public function lead_details($segment_id)
	{
		// $lead_id = base64_decode($enc_id)
		$request = \Config\Services::request();

		// $segment_id = $request->uri->getSegment(3);
		// $lead_id = udecode($segment_id);
		$lead_id = $segment_id;
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$LeadConfig = new LeadConfigModel();
		$SystemModel = new SystemModel();
		$LeadsfollowupModel = new LeadsfollowupModel();
		$db = \Config\Database::connect();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
		$table_name = 'leads_' . $company_name;

		$builder = $db->table($table_name);
		$leadData = $builder->where('id', $lead_id)->get()->getRowArray();

		$followup = $LeadsfollowupModel->where('company_id', $user_info['company_id'])->where('lead_id', $lead_id)->orderBy('followup_id', 'ASC')->findAll();


		if (!$leadData) {
			$session->setFlashdata('message', 'Lead not found');
			return redirect()->back();
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = lang('Main.xin_lead_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'lead_details';
		$data['breadcrumbs'] = lang('Main.xin_lead_details');
		$data['leadData'] = $leadData;
		$data['followup'] = $followup;
		$data['lead_id'] = $lead_id;

		$data['subview'] = view('erp/clients/lead_details', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function follow_up_view($id)
	{
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$LeadsfollowupModel = new LeadsfollowupModel();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$followup = $LeadsfollowupModel->where('followup_id', $id)->orderBy('followup_id', 'ASC')->findAll();

		if ($followup) {
			return view('erp/clients/dialog_followup', ['result' => $followup[0]]);
		} else {
			return redirect()->back()->with('error', 'No data found for the given ID');
		}
	}

	public function update_lead()
	{
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$LeadConfig = new LeadConfigModel();
		$SystemModel = new SystemModel();
		$db = \Config\Database::connect();
		$cache = \Config\Services::cache();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$lead_id = $this->request->getPost('token', FILTER_SANITIZE_STRING);

		$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
		$table_name = 'leads_' . $company_name;

		$leadFields = $LeadConfig->groupStart()
			->where('company_id', $user_info['company_id'])
			->orWhere('company_id', null)
			->groupEnd()
			->orderBy('id', 'ASC')
			->findAll();

		$leadData = [];

		if ($this->request->getPost('type') === 'edit_record') {
			foreach ($leadFields as $field) {
				$fieldName = rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_');
				$leadData['opportunity_id'] = $this->request->getPost('opportunity_id');
				$leadData['lead_status'] = $this->request->getPost('lead_status');
				$leadData['sources_name'] = $this->request->getPost('source_name');

				if (!empty($_FILES[$fieldName]['name'])) {
					$image = \Config\Services::image();
					$validated = $this->validate([
						$fieldName => [
							'uploaded[' . $fieldName . ']',
							'mime_in[' . $fieldName . ',image/jpg,image/jpeg,image/gif,image/png]',
							'max_size[' . $fieldName . ',4096]',
						],
					]);

					if (!$validated) {
						$errors = $this->validator->getErrors();
						return $this->response->setJSON([
							'error' => 'Error uploading image for ' . $fieldName . ': ' . implode(', ', $errors),
							'csrf_hash' => csrf_hash() // Regenerate CSRF token
						]);
					}

					$avatar = $this->request->getFile($fieldName);
					$file_name = $avatar->getRandomName();
					$avatar->move('uploads/leads/', $file_name);

					$image->withFile('uploads/leads/' . $file_name)
						->fit(100, 100, 'center')
						->save('uploads/leads/' . $file_name);

					$leadData[$fieldName] = $file_name;
				} else {
					$old_image = $this->request->getPost('old_image');
					if (!empty($old_image)) {
						$leadData[$fieldName] = $old_image;
					}
				}
				$fieldValue = $this->request->getPost($fieldName);
				if ($fieldValue !== null && $fieldValue !== '') {
					$leadData[$fieldName] = $fieldValue;
				}
			}

			unset($leadData['ciapp_check']);

			try {
				$builder = $db->table($table_name);
				$builder->where('id', $lead_id);

				log_message('debug', 'Lead data to update: ' . json_encode($leadData)); // Log data

				if ($builder->update($leadData)) {
					$cacheKey = 'lead_data_' . $user_info['user_id'];
					$cache->delete($cacheKey);
					return $this->response->setJSON([
						'result' => 'Lead successfully updated',
						'redirect' => base_url('erp/leads-list'), // Provide redirect URL
						'csrf_hash' => csrf_hash() // Regenerate CSRF token
					]);
				} else {
					$errors = $db->error();
					log_message('error', 'Database update error: ' . json_encode($errors));
					return $this->response->setJSON([
						'error' => 'Error occurred while updating lead: ' . $errors['message'],
						'csrf_hash' => csrf_hash() // Regenerate CSRF token
					]);
				}
			} catch (\Exception $e) {
				log_message('error', 'Database error: ' . $e->getMessage());
				return $this->response->setJSON([
					'error' => 'An error occurred: ' . $e->getMessage(),
					'csrf_hash' => csrf_hash() // Regenerate CSRF token
				]);
			}
		}
	}


	// Helper method to determine company ID
	private function determineCompanyId($usession)
	{
		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		return ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];
	}

	// bulk data record
	// |||add record|||

	public function add_bulk_lead()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		if ($this->request->getPost()) {
			$bulk_file = $this->request->getFile('bulk_file');
			$file_extension = $bulk_file->getClientExtension();

			if (!in_array($file_extension, ['csv', 'txt', 'xlsx', 'xls'])) {
				$session->setFlashdata('error', 'Invalid file format.');
				return redirect()->to(site_url('erp/leads-list'));
			}

			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
			$table_name = 'leads_' . $company_name;

			$db = \Config\Database::connect();

			try {
				if (in_array($file_extension, ['csv', 'txt'])) {
					$message = $this->processCsvFile($bulk_file, $table_name, $db);
				} else {
					$message = $this->processExcelFile($bulk_file, $table_name, $db);
				}

				$session->setFlashdata('message', $message);
			} catch (\Exception $e) {
				$session->setFlashdata('error', 'Error processing file: ' . $e->getMessage());
			}

			return redirect()->to(site_url('erp/leads-list'));
		}

		$session->setFlashdata('error', 'Invalid request type.');
		return redirect()->to(site_url('erp/leads-list'));
	}


	private function processCsvFile($bulk_file, $table_name, $db)
	{
		$csvFile = fopen($bulk_file->getTempName(), 'r');
		$header = fgetcsv($csvFile); // header row

		$inserted = 0;
		$updated = 0;

		while (($row = fgetcsv($csvFile)) !== FALSE) {
			$data = array_combine($header, $row);

			if (isset($data['email']) && !empty($data['email'])) {
				if ($this->isDuplicateEntry($table_name, $data['email'], $db)) {
					$db->table($table_name)->where('email', $data['email'])->update($data);
					$updated++;
				} else {
					$db->table($table_name)->insert($data);
					$inserted++;
				}
			}
		}

		fclose($csvFile);
		return "CSV Import Complete: Inserted $inserted, Updated $updated";
	}

	private function processExcelFile($bulk_file, $table_name, $db)
	{

		require_once APPPATH . '/ThirdParty/vendor/autoload.php';

		$inserted = 0;
		$updated = 0;

		try {
			if (!$bulk_file->isValid()) throw new \Exception('Invalid file upload.');
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($bulk_file->getTempName());
			$sheet = $spreadsheet->getActiveSheet();

			$header = [];

			foreach ($sheet->getRowIterator() as $rowIndex => $row) {
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
				$rowData = [];

				if ($rowIndex == 1) {
					foreach ($cellIterator as $cell) {
						$header[] = strtolower(trim(str_replace(' ', '_', $cell->getValue())));
					}
				} else {
					foreach ($cellIterator as $cell) {
						$rowData[] = $cell->getValue();
					}

					$data = array_combine($header, $rowData);

					if (isset($data['email']) && !empty($data['email'])) {
						if ($this->isDuplicateEntry($table_name, $data['email'], $db)) {
							$db->table($table_name)->where('email', $data['email'])->update($data);
							$updated++;
						} else {
							$db->table($table_name)->insert($data);
							$inserted++;
						}
					}
				}
			}

			return "Excel Import Complete: Inserted $inserted, Updated $updated";
		} catch (\Exception $e) {
			return 'Error: ' . $e->getMessage();
		}
	}


	private function isDuplicateEntry($table_name, $email, $db)
	{
		return $db->table($table_name)->where('email', $email)->countAllResults() > 0;
	}



	// update record
	public function update_client()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'first_name' => 'required',
					'last_name' => 'required',
					'email' => 'required|valid_email',
					'username' => 'required|min_length[6]',
					'contact_number' => 'required',
					'country' => 'required',
					'status' => 'required'
				],
				[   // Errors
					'first_name' => [
						'required' => lang('Main.xin_employee_error_first_name'),
					],
					'last_name' => [
						'required' => lang('Main.xin_employee_error_last_name'),
					],
					'email' => [
						'required' => lang('Main.xin_employee_error_email'),
						'valid_email' => lang('Main.xin_employee_error_invalid_email')
					],
					'username' => [
						'required' => lang('Main.xin_employee_error_username'),
						'min_length' => lang('Main.xin_min_error_username')
					],
					'contact_number' => [
						'required' => lang('Main.xin_error_subscription_field'),
					],
					'country' => [
						'required' => lang('Main.xin_error_country_field'),
					],
					'status' => [
						'required' => lang('Main.xin_error_field_text'),
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
			} elseif ($validation->hasError('status')) {
				$Return['error'] = $validation->getError('status');
			} elseif ($validation->hasError('contact_number')) {
				$Return['error'] = $validation->getError('contact_number');
			} elseif ($validation->hasError('country')) {
				$Return['error'] = $validation->getError('country');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}

			$first_name = $this->request->getPost('first_name', FILTER_SANITIZE_STRING);
			$last_name = $this->request->getPost('last_name', FILTER_SANITIZE_STRING);
			$email = $this->request->getPost('email', FILTER_SANITIZE_STRING);
			$username = $this->request->getPost('username', FILTER_SANITIZE_STRING);
			$contact_number = $this->request->getPost('contact_number', FILTER_SANITIZE_STRING);
			$country = $this->request->getPost('country', FILTER_SANITIZE_STRING);
			$gender = $this->request->getPost('gender', FILTER_SANITIZE_STRING);
			$address_1 = $this->request->getPost('address_1', FILTER_SANITIZE_STRING);
			$address_2 = $this->request->getPost('address_2', FILTER_SANITIZE_STRING);
			$city = $this->request->getPost('city', FILTER_SANITIZE_STRING);
			$state = $this->request->getPost('state', FILTER_SANITIZE_STRING);
			$zipcode = $this->request->getPost('zipcode', FILTER_SANITIZE_STRING);
			$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
			$id = $this->request->getPost('token', FILTER_SANITIZE_STRING);
			$data = [
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'username' => $username,
				'contact_number' => $contact_number,
				'country' => $country,
				'user_role_id' => 0,
				'address_1' => $address_1,
				'address_2' => $address_2,
				'city' => $city,
				'state' => $state,
				'zipcode' => $zipcode,
				'gender' => $gender,
				'is_active' => $status,
			];
			$UsersModel = new UsersModel();
			$result = $UsersModel->update($id, $data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_client_updated_msg');
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
	public function update_client_status()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$validation->setRules(
				[
					'status' => 'required'
				],
				[   // Errors
					'status' => [
						'required' => lang('Main.xin_error_field_text'),
					]
				]
			);

			$validation->withRequest($this->request)->run();
			//check error
			if ($validation->hasError('status')) {
				$Return['error'] = $validation->getError('status');
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}
			$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			$data = [
				'is_active' => $status,
			];
			$UsersModel = new UsersModel();
			$result = $UsersModel->update($id, $data);
			$Return['csrf_hash'] = csrf_hash();
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_client_status_updated_msg');
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

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			$image = service('image');
			$validated = $this->validate([
				'file' => [
					'uploaded[file]',
					'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
					'max_size[file,4096]',
				],
			]);
			if (!$validated) {
				$Return['error'] = lang('Main.xin_error_profile_picture_field');
			} else {
				$avatar = $this->request->getFile('file');
				$file_name = $avatar->getName();
				$avatar->move('uploads/clients/');
				$image->withFile(filesrc($file_name))
					->fit(100, 100, 'center')
					->save('uploads/clients/thumb/' . $file_name);
			}
			if ($Return['error'] != '') {
				$this->output($Return);
			}
			$id = $this->request->getPost('token', FILTER_SANITIZE_STRING);
			// $id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			if ($validated) {
				$UsersModel = new UsersModel();
				$Return['result'] = lang('Main.xin_profile_picture_success_updated');
				$data = [
					'profile_photo' => $file_name
				];
				$result = $UsersModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
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
	public function update_lead_profile_photo()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			$image = service('image');
			// set rules
			$validated = $this->validate([
				'file' => [
					'uploaded[file]',
					'mime_in[file,image/jpg,image/jpeg,image/gif,image/png]',
					'max_size[file,4096]',
				],
			]);
			if (!$validated) {
				$Return['error'] = lang('Main.xin_error_profile_picture_field');
			} else {
				$avatar = $this->request->getFile('file');
				$file_name = $avatar->getName();
				$avatar->move('uploads/clients/');
				$image->withFile(filesrc($file_name))
					->fit(100, 100, 'center')
					->save('uploads/clients/thumb/' . $file_name);
			}
			if ($Return['error'] != '') {
				return $this->response->setJSON($Return);
			}
			$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
			if ($validated) {
				$LeadsModel = new LeadsModel();
				$Return['result'] = lang('Main.xin_profile_picture_success_updated');
				$data = [
					'profile_photo' => $file_name
				];
				$result = $LeadsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
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
	public function update_password_opt()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
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
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
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
				$Return['result'] = lang('Main.xin_success_user_password_field');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	// |||add record|||
	public function convert_lead()
	{
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');

		if ($this->request->getPost('type') === 'edit_record') {

			$Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

			$id = $this->request->getPost('token', FILTER_SANITIZE_STRING);

			$data = ['status' => 2];

			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			
			$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
			$table_name = 'leads_' . $company_name;

			$db = \Config\Database::connect();
			$builder = $db->table($table_name);
			$builder->where('id', $id);
			$result = $builder->update($data);

			$lead_info = $db->table($table_name)->where('id', $id)->get()->getFirstRow();
			
			if (!$lead_info) {
				$Return['error'] = lang('Main.xin_error_msg');
				return $this->response->setJSON($Return);
			}

			$full_name = trim($lead_info->name);
			$name_parts = explode(' ', $full_name);
			$first_name = $name_parts[0];
			$last_name = isset($name_parts[1]) ? $name_parts[1] : '';

			$iusername = explode('@', $lead_info->email);
			$username = $iusername[0];
			$options = ['cost' => 12];
			$password_hash = password_hash($username, PASSWORD_BCRYPT, $options);

			if ($result) {
				$data2 = [
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $lead_info->email,
					'user_type' => 'customer',
					'username' => $username,
					'password' => $password_hash,
					'contact_number' => '',
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
					'company_id' => $user_info['company_id'],
					'created_at' => date('d-m-Y H:i:s')
				];
				

				$result2 = $UsersModel->insert($data2);
				

				if (!$result2) {
					$db = \Config\Database::connect();
					$error = $db->error(); // Retrieve any error
					log_message('error', 'Insert Error: ' . print_r($error, true));
					log_message('error', 'SQL Query: ' . $db->getLastQuery());

					$Return['error'] = 'Insert failed: ' . $error['message'];
				} else {

					$Return['result'] = lang('Success.ci_lead_changed_to_client_msg');
				}
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			// Return the response
			return $this->response->setJSON($Return);
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}
	}

	// read record
	public function read_followup()
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
			return view('erp/clients/dialog_followup', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// read record
	public function read_lead()
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
			return view('erp/clients/change_to_client', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_client()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = $this->request->getPost('_token', FILTER_SANITIZE_STRING);
			$Return['csrf_hash'] = csrf_hash();
			$UsersModel = new UsersModel();
			$result = $UsersModel->where('user_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_client_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_leads($id)
	{
		// $id = base64_decode($enc_id);
		$session = \Config\Services::session();
		$db = \Config\Database::connect();
		$UsersModel = new UsersModel();
		$cache = \Config\Services::cache();

		$usession = $session->get('sup_username');
		if (!$usession || !isset($usession['sup_user_id'])) {
			$session->setFlashdata('error', 'Invalid session');
			return redirect()->back();
		}
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$user_info) {
			$session->setFlashdata('error', 'User not found');
			return redirect()->back();
		}
		$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
		$table_name = 'leads_' . $company_name;

		if (!$db->tableExists($table_name)) {
			$session->setFlashdata('error', 'Lead table does not exist for this company');
			return redirect()->back();
		}
		$builder = $db->table($table_name);
		$builder->where('id', $id);
		$result = $builder->delete();

		if ($result) {
			$cacheKey = 'lead_data_' . $user_info['user_id'];
			$cache->delete($cacheKey);
			$session->setFlashdata('message', 'Lead Deleted Successfully');
		} else {
			$session->setFlashdata('error', 'Failed to delete lead');
		}

		return redirect()->back();
	}


	public function delete_follow($id)
	{
		// $id = base64_decode($enc_id);
		$session = \Config\Services::session();
		$request = \Config\Services::request();

		$Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());
		$LeadsfollowupModel = new LeadsfollowupModel();
		$result = $LeadsfollowupModel->where('followup_id', $id)->delete($id);
		if ($result) {
			$session->setFlashdata('message', 'Follow-Up Deleted Successfully');
		} else {
			$session->setFlashdata('error', 'Failed to delete lead');
		}
		return redirect()->back()->withInput();
	}


	public function save_accountDetails()
	{

		$UsersModel = new UsersModel();
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Define validation rules
		$validation->setRules([
			'account_name' => 'required|min_length[3]',
			'office_address' => 'required|min_length[3]',
			'pincode' => 'required|numeric|min_length[6]|max_length[6]',
			'gst_no' => 'required|alpha_numeric|min_length[15]|max_length[15]',
			'pan_no' => 'required|alpha_numeric|min_length[10]|max_length[10]',
		]);

		if ($this->request->getMethod() === 'POST' && $validation->withRequest($this->request)->run()) {
			$data = [
				'lead_id' => $this->request->getPost('lead_id'),
				'company_id' => $user_info['company_id'],
				'account_name' => $this->request->getPost('account_name'),
				'office_address' => $this->request->getPost('office_address'),
				'pincode' => $this->request->getPost('pincode'),
				'gst_no' => $this->request->getPost('gst_no'),
				'pan_card_no' => $this->request->getPost('pan_no'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			log_message('debug', 'Data to be inserted: ' . json_encode($data));

			try {
				// Use database connection to perform raw SQL insert
				$db = \Config\Database::connect();
				$builder = $db->table('ci_account_details');
				$insert = $builder->insert($data);

				if (!$insert) {
					$error = $db->error();
					log_message('error', 'Insert Error: ' . print_r($error, true));
					$session->setFlashdata('error', 'Failed to insert data.');
				} else {
					$session->setFlashdata('message', 'Account Details inserted successfully.');
					log_message('debug', 'Account Details inserted successfully. ID: ' . $db->insertID());
				}
			} catch (\Exception $e) {
				log_message('error', 'Database Error: ' . $e->getMessage());
				$session->setFlashdata('error', 'An error occurred while saving the account details: ' . $e->getMessage());
			}
		} else {
			$validationErrors = $validation->getErrors();
			if (!empty($validationErrors)) {
				$session->setFlashdata('error', implode(", ", $validationErrors));
			} else {
				$session->setFlashdata('error', 'Form submission failed. Please check your input and try again.');
			}
		}

		return redirect()->back()->withInput();
	}


	public function delete_accountRecord($account_id)
	{
		// Initialize services
		$session = \Config\Services::session();
		$accountDetails = new AccountDetailModel();
		
		// Check user session first
		if (!$session->has('sup_username')) {
			return redirect()->to('/')->with('error', 'Session expired');
		}

		try {
			// 1. Validate account_id is numeric
			if (!is_numeric($account_id)) {
				throw new \InvalidArgumentException('Invalid account ID');
			}

			// 2. Get the record first to ensure it exists and get lead_id
			$record = $accountDetails->find($account_id);
			if (!$record) {
				throw new \RuntimeException('Account record not found');
			}

			// Store lead_id for redirect before deletion
			$lead_id = $record['lead_id'];

			// 3. Attempt to delete
			$deleted = $accountDetails->delete($account_id);
			
			if (!$deleted) {
				// Check for database errors
				$error = $accountDetails->errors() ? implode(', ', $accountDetails->errors()) : 'Unknown database error';
				throw new \RuntimeException($error);
			}

			// Success
			$session->setFlashdata('message', 'Account details deleted successfully');
			log_message('info', "Account ID {$account_id} deleted successfully");

		} catch (\Exception $e) {
			log_message('error', 'Delete account error: ' . $e->getMessage());
			
			// Try to get lead_id even if deletion failed
			if (!isset($lead_id)) {
				$leadRecord = $accountDetails->select('lead_id')
										->where('account_id', $account_id)
										->first();
				$lead_id = $leadRecord['lead_id'] ?? null;
			}

			$session->setFlashdata('error', 'Failed to delete: ' . $e->getMessage());
			
			// If we can't determine lead_id, redirect to leads list
			if (!$lead_id) {
				return redirect()->to(base_url('erp/leads'))->with('error', 'Could not determine lead');
			}
		}

		// Redirect back to lead info page
		return redirect()->to(base_url('erp/view-lead-info/' . $lead_id));
	}


	public function account_view_details($id)
	{
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$AccountDetailModel = new AccountDetailModel();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$account = $AccountDetailModel->where('account_id', $id)->first();

		if ($account) {
			return view('erp/clients/view_accountdetail', ['result' => $account]);
		} else {
			return redirect()->back()->with('error', 'No data found for the given ID.');
		}
	}



	public function update_account($account_id)
	{
		$session = session();
		$accountDetails = new AccountDetailModel();

		$validation = \Config\Services::validation();
		$validation->setRules([
			'account_name' => 'required|min_length[3]',
			'office_address' => 'required|min_length[3]',
			'pincode' => 'required|numeric|min_length[6]|max_length[6]',
			'gst_no' => 'required|alpha_numeric|min_length[15]|max_length[15]',
			'pan_no' => 'required|alpha_numeric|min_length[10]|max_length[10]',
		]);

		if ($validation->withRequest($this->request)->run()) {
			$data = [
				'account_name' => $this->request->getPost('account_name'),
				'office_address' => $this->request->getPost('office_address'),
				'pincode' => $this->request->getPost('pincode'),
				'gst_no' => $this->request->getPost('gst_no'),
				'pan_card_no' => $this->request->getPost('pan_no'),
				'updated_at' => date('Y-m-d H:i:s')
			];

			try {
				$result = $accountDetails->update($account_id, $data);

				if ($result) {
					$session->setFlashdata('message', 'Account Details updated successfully');
				} else {
					$session->setFlashdata('error', lang('Main.xin_error_msg'));
				}
			} catch (\Exception $e) {
				log_message('error', 'Error updating account details: ' . $e->getMessage());
				$session->setFlashdata('error', 'An error occurred: ' . $e->getMessage());
			}
		} else {
			$session->setFlashdata('error', implode(", ", $validation->getErrors()));
			return redirect()->back()->withInput();
		}

		$lead_id = $accountDetails->select('lead_id')->where('account_id', $account_id)->first();

		return redirect()->to(base_url('erp/view-lead-info/' . $lead_id['lead_id']));
	}


	public function overview()
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

		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = '';
		$data['path_url'] = 'Client Management / Overview';
		$data['breadcrumbs'] = 'Client Management / Overview';

		$data['subview'] = view('erp/clients/overview', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function filter_leads()
	{

		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$LeadConfig = new LeadConfigModel();
		$cache = \Config\Services::cache();
		$usession = $session->get('sup_username');

		// Get user info securely
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
		$table_name = 'leads_' . $company_name;
		$opportunity_id = $this->request->getVar('opportunity_id');
		$status = $this->request->getVar('status');

		$db = \Config\Database::connect();
		$builder = $db->table($table_name);

		if (!empty($opportunity_id)) {
			$builder->where('opportunity_id', $opportunity_id);
		}
		if (!empty($status)) {
			$builder->where('lead_status', $status);
		}

		$query = $builder->get();
		
		$cacheKey = 'lead_data_' . $user_info['user_id'];
		$cache->delete($cacheKey);
		$data['get_leadList'] = $query->getResultArray();
		$leadFields = $LeadConfig->groupStart()
			->where('company_id', $user_info['company_id'])
			->orWhere('company_id', null)
			->groupEnd()
			->orderBy('id', 'ASC')
			->findAll();


		// Prepare the HTML header
		$html = '<thead>
        <tr>
            <th>#</th>
            <th>Opportunity</th>';

		$columnNames = array_column($leadFields, 'column_name');
		foreach ($columnNames as $columnName) {
			$html .= '<th>' . esc($columnName) . '</th>';
		}

		$html .= '<th>Lead Status</th>
              <th>Sources</th>
              <th>Status</th>
              <th>Action</th>
        </tr>
    </thead>';
		$html .= '<tbody>';

		if (!empty($data['get_leadList'])) {
			$i = 1;
			foreach ($data['get_leadList'] as $list) {
				$html .= '<tr>';
				$html .= '<td>' . $i++ . '</td>'; // Serial Number
				$html .= '<td>' . esc(getOpportunityName($list['opportunity_id'])) . '</td>'; // Opportunity Name

				foreach ($columnNames as $field) {
					$field_name = strtolower(str_replace(' ', '_', trim($field)));
					$html .= '<td>'; // Start a new cell

					if (isset($list[$field_name])) {
						if (strpos($field_name, 'image') !== false) {
							$image_path = 'uploads/leads/' . esc($list[$field_name]);

							if (!empty($list[$field_name]) && file_exists($image_path)) {
								$html .= '<img src="' . base_url($image_path) . '" alt="Profile Image" width="50" height="50">';
							} else {
								$html .= '<img src="' . base_url('uploads/leads/dummy-image.jpg') . '" alt="Dummy Image" width="50" height="50">';
							}
						} elseif (strpos($field_name, 'date') !== false) {
							$html .= date('d M Y', strtotime($list[$field_name]));
						} else {
							$html .= esc($list[$field_name]);
						}
					} else {
						$html .= '-'; // Fallback for missing values
					}

					$html .= '</td>'; // Close cell
				}

				// Lead status badge
				$html .= '<td>';
				$html .= ($list['lead_status'] == 'hot') ? '<span class="badge badge-light-primary">Hot</span>' : '<span class="badge badge-light-success">Cold</span>';
				$html .= '</td>';

				// Sources name
				$html .= '<td>' . esc($list['sources_name']) . '</td>';

				// Client or Lead status
				$html .= '<td>';
				$html .= ($list['status'] == 1) ? '<span class="badge badge-light-primary">Lead</span>' : '<span class="badge badge-light-success">Client</span>';
				$html .= '</td>';

				// Action buttons
				$html .= '<td>';
				$html .= '<a href="' . base_url('erp/view-lead-info/' . uencode($list['id'])) . '" class="btn btn-primary" style="background-color: blue !important;" data-toggle="tooltip" title="View Details">';
				$html .= '<i class="feather icon-edit-2 text-white"></i>';
				$html .= '</a>';

				if ($list['status'] == 1) {
					$html .= '<button type="button" class="btn btn-info" title="Change to Client" data-toggle="modal" data-target=".view-modal-data" data-field_id="' . uencode($list['id']) . '">';
					$html .= '<i class="feather icon-shuffle"></i>';
					$html .= '</button>';
				}

				$html .= '<a href="' . base_url('erp/delete-leads/' . base64_encode($list['id'])) . '" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this item?\');" data-toggle="tooltip" title="Delete Item">';
				$html .= '<i class="feather icon-trash-2"></i>';
				$html .= '</a>';
				$html .= '</td>'; // Close action buttons cell
				$html .= '</tr>'; // Close table row
			}
		} else {
			// No data found row
			$html .= '<tr><td colspan="' . (count($columnNames) + 6) . '" style="text-align: center;">No data found</td></tr>';
		}

		$html .= '</tbody>'; // Close tbody

		return $html;
	}


	public function set_opportunity_session()
	{
		$session = \Config\Services::session();
		$opportunity_id = $this->request->getPost('opportunity_id');

		$session->set('opportunity_id', $opportunity_id);

		return json_encode(['status' => 'success']);
	}
}
