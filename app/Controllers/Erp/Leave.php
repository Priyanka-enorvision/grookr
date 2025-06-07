<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\I18n\Time;

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\MainModel;
use App\Models\ConstantsModel;
use App\Models\LeaveModel;
use App\Models\EmailtemplatesModel;

class Leave extends BaseController
{

	public function index()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
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
			if (!in_array('leave2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_manage_leaves') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'leave';
		$data['breadcrumbs'] = lang('Dashboard.xin_manage_leaves');

		$data['subview'] = view('erp/leave/staff_leave_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function leave_status()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
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
			if (!in_array('leave2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Employees.xin_employee_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'leave';
		$data['breadcrumbs'] = lang('Employees.xin_employee_details');

		$data['subview'] = view('erp/leave/leave_status', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function leave_calendar()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

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
			if (!in_array('leave_calendar', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_acc_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'meetings';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_calendar');

		$data['subview'] = view('erp/leave/calendar_leave', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function view_leave()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$LeaveModel = new LeaveModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$ifield_id = udecode($request->getUri()->getSegment(3));
		$leave_val = $LeaveModel->where('leave_id', $ifield_id)->first();
		if (!$leave_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('/'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Leave.xin_leave_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'leave_details';
		$data['breadcrumbs'] = lang('Leave.xin_leave_details');

		$data['subview'] = view('erp/leave/leave_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	// record list
	public function leave_list()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Check session
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		// Load models
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ConstantsModel = new ConstantsModel();
		$LeaveModel = new LeaveModel();

		try {
			// Get user info with error handling
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if (!$user_info) {
				throw new \RuntimeException('User not found');
			}

			// Get leave data based on user type
			if ($user_info['user_type'] == 'staff') {
				$get_data = $LeaveModel->where('employee_id', $usession['sup_user_id'])
					->orderBy('leave_id', 'ASC')
					->findAll();
			} else {
				$get_data = $LeaveModel->where('company_id', $usession['sup_user_id'])
					->orderBy('leave_id', 'ASC')
					->findAll();
			}

			$data = [];

			foreach ($get_data as $r) {
				// Edit button logic
				$edit = '';
				if ($r['status'] == 1 && (in_array('leave4', staff_role_resource()) || $user_info['user_type'] == 'company')) {
					$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '">
                    <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" 
                        data-toggle="modal" data-target=".view-modal-data" 
                        data-field_id="' . uencode($r['leave_id']) . '">
                        <i class="feather icon-edit"></i>
                    </button>
                </span>';
				}

				// Delete button logic
				$delete = '';
				if (in_array('leave6', staff_role_resource()) || $user_info['user_type'] == 'company') {
					$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '">
                    <button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" 
                        data-toggle="modal" data-target=".delete-modal" 
                        data-record-id="' . uencode($r['leave_id']) . '">
                        <i class="feather icon-trash-2"></i>
                    </button>
                </span>';
				}

				// View button
				$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '">
                <a href="' . site_url() . 'erp/view-leave-info/' . uencode($r['leave_id']) . '">
                    <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light">
                        <span class="fa fa-arrow-circle-right"></span>
                    </button>
                </a>
            </span>';

				// Leave type
				$ltype = $ConstantsModel->where('constants_id', $r['leave_type_id'])
					->where('type', 'leave_type')
					->first();
				$itype_name = $ltype ? $ltype['category_name'] : 'N/A';

				// Applied on date
				$applied_on = set_date_format($r['created_at']);

				// Staff information with fallback for missing profile photo
				$staff = $UsersModel->where('user_id', $r['employee_id'])->first();
				if ($staff) {
					$name = ($staff['first_name'] ?? '') . ' ' . ($staff['last_name'] ?? '');
					$profile_photo = !empty($staff['profile_photo']) ?
						base_url('public/uploads/users/thumb/' . $staff['profile_photo']) :
						base_url('public/default-profile.jpg');

					$uname = '<div class="d-inline-block align-middle">
                    <img src="' . $profile_photo . '" alt="' . htmlspecialchars($name) . '" 
                        class="img-radius align-top m-r-15" style="width:40px;">
                    <div class="d-inline-block">
                        <h6 class="m-b-0">' . htmlspecialchars($name) . '</h6>
                        <p class="m-b-0">' . htmlspecialchars($staff['email'] ?? '') . '</p>
                    </div>
                </div>';
				} else {
					$uname = '<div class="d-inline-block align-middle">
                    <img src="' . base_url('public/default-profile.jpg') . '" alt="Unknown" 
                        class="img-radius align-top m-r-15" style="width:40px;">
                    <div class="d-inline-block">
                        <h6 class="m-b-0">Unknown Staff</h6>
                        <p class="m-b-0">N/A</p>
                    </div>
                </div>';
				}

				// Calculate leave duration
				$no_of_days = erp_date_difference($r['from_date'], $r['to_date']);
				$idays = ($r['is_half_day'] == 1) ?
					lang('Employees.xin_hr_leave_half_day') :
					$no_of_days . ' ' . lang('Leave.xin_leave_days');

				$duration = set_date_format($r['from_date']) . ' ' .
					lang('Employees.dashboard_to') . ' ' .
					set_date_format($r['to_date']);

				// Leave status
				switch ($r['status']) {
					case 1:
						$status = '<span class="badge badge-light-warning">' . lang('Main.xin_pending') . '</span>';
						break;
					case 2:
						$status = '<span class="badge badge-light-success">' . lang('Main.xin_approved') . '</span>';
						break;
					case 3:
						$status = '<span class="badge badge-light-danger">' . lang('Main.xin_rejected') . '</span>';
						break;
					default:
						$status = '<span class="badge badge-light-warning">' . lang('Main.xin_pending') . '</span>';
				}

				$combhr = $edit . $view . $delete;

				$data[] = [
					$uname,
					$itype_name,
					$duration,
					$idays,
					$applied_on,
					$status,
					$combhr
				];
			}

			return $this->response->setJSON([
				"data" => $data
			]);
		} catch (\Exception $e) {
			log_message('error', 'Error in leave_list: ' . $e->getMessage());
			return $this->response->setJSON([
				"error" => "An error occurred while processing your request"
			])->setStatusCode(500);
		}
	}
	// |||add record|||
	public function add_leave()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$Return['csrf_hash'] = csrf_hash();
		if ($this->request->getPost('type') === 'add_record') {
			// set rules
			$rules = [
				'leave_type' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'start_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'end_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'reason' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"leave_type" => $validation->getError('leave_type'),
					"start_date" => $validation->getError('start_date'),
					"end_date" => $validation->getError('end_date'),
					"reason" => $validation->getError('reason')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				$validated = $this->validate([
					'attachment' => [
						'rules'  => 'uploaded[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/gif,image/png]|max_size[attachment,3072]',
						'errors' => [
							'uploaded' => lang('Main.xin_error_field_text'),
							'mime_in' => 'wrong size'
						]
					],
				]);
				$leave_type = $this->request->getPost('leave_type', FILTER_SANITIZE_STRING);
				$start_date = $this->request->getPost('start_date', FILTER_SANITIZE_STRING);
				$end_date = $this->request->getPost('end_date', FILTER_SANITIZE_STRING);
				if (strtotime($end_date) < strtotime($start_date)) {
					$Return['error'] = 'End date cannot be earlier than start date.';
					return $this->response->setJSON($Return);
					
				}
				$reason = $this->request->getPost('reason', FILTER_SANITIZE_STRING);
				$leave_half_day = $this->request->getPost('leave_half_day', FILTER_SANITIZE_STRING);
				$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
				$luser_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
				
				$UsersModel = new UsersModel();
				$ConstantsModel = new ConstantsModel();
				$leave_user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($leave_user_info['user_type'] == 'staff') {
					$luser_id = $leave_user_info['user_id'];
					$leave_types = $ConstantsModel->where('company_id', $leave_user_info['company_id'])->where('type', 'leave_type')->first();
					
				} else {
					$leave_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->first();
				}
				// check half leave
				$no_of_days = erp_date_difference($start_date, $end_date);

				$tinc = count_employee_leave($luser_id, $leave_type);
				$days_per_year = $leave_types['field_one'];
				$rem_leave = $days_per_year - $tinc;

				if ($rem_leave == 0) {
					$Return['error'] = lang('Main.xin_hr_cant_appply_leave_quota_completed');
				} else if ($no_of_days > $rem_leave) {
					$Return['error'] = lang('Main.xin_hr_cant_appply_morethan') . $rem_leave . ' ' . lang('Main.xin_day');
				}
				if ($Return['error'] != '') {
					$Return['csrf_hash'] = csrf_hash();
					return $this->response->setJSON($Return);
				}
				if ($leave_half_day == 1 && $no_of_days > 1) {
					$Return['error'] = lang('Success.xin_hr_cant_appply_morethan') . ' 1 ' . lang('Main.xin_day');
				}
				if ($Return['error'] != '') {
					$Return['csrf_hash'] = csrf_hash();
					return $this->response->setJSON($Return);
				}
				if ($leave_half_day == 1) {
					$leave_half_day_opt = 1;
				} else {
					$leave_half_day_opt = 0;
				}

				$SystemModel = new SystemModel();
				$xin_system = $SystemModel->where('setting_id', 1)->first();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$staff_id = $usession['sup_user_id'];
					$company_id = $user_info['company_id'];
					$company_info = $UsersModel->where('company_id', $company_id)->first();
				} else {
					$staff_id = $this->request->getPost('employee_id', FILTER_SANITIZE_STRING);
					$company_id = $usession['sup_user_id'];
					$company_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				}

				$require_info = $ConstantsModel->where('constants_id', $leave_type)->where('type', 'leave_type')->first();
				if ($require_info['field_two'] == 0) {
					$status = 2;
				} else {
					$status = 1;
				}
				if ($validated) {
					$attachment = $this->request->getFile('attachment');
					$file_name = $attachment->getName();
					$attachment->move('uploads/leave/');
					$data = [
						'company_id' => $company_id,
						'employee_id'  => $staff_id,
						'leave_type_id'  => $leave_type,
						'from_date'  => $start_date,
						'to_date'  => $end_date,
						'reason'  => $reason,
						'remarks'  => $remarks,
						'status'  => $status,
						'is_half_day'  => $leave_half_day_opt,
						'leave_attachment'  => $file_name,
						'created_at'  => date('d-m-Y h:i:s'),
					];
				} else {
					$data = [
						'company_id' => $company_id,
						'employee_id'  => $staff_id,
						'leave_type_id'  => $leave_type,
						'from_date'  => $start_date,
						'to_date'  => $end_date,
						'reason'  => $reason,
						'remarks'  => $remarks,
						'status'  => $status,
						'is_half_day'  => $leave_half_day_opt,
						'leave_attachment'  => '',
						'created_at'  => date('d-m-Y h:i:s'),
					];
				}
				$LeaveModel = new LeaveModel();
				$existing_leave = $LeaveModel->where('employee_id', $luser_id)
					->where('leave_type_id', $leave_type)
					->where('from_date', $start_date)
					->where('to_date', $end_date)
					->first();

				if ($existing_leave) {
					$Return['error'] = 'This employee has already applied for the same leave type for the selected date range.';
					return $this->response->setJSON($Return);
					
				}
				$EmailtemplatesModel = new EmailtemplatesModel();
				$result = $LeaveModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_leave_created__msg');
					if($xin_system['enable_email_notification'] == 1){
						// Send mail start
						$itemplate = $EmailtemplatesModel->where('template_id', 13)->first();
						$istaff_info = $UsersModel->where('user_id', $staff_id)->first();
						$full_name = $istaff_info['first_name'].' '.$istaff_info['last_name'];
						// leave type
						$ltype = $ConstantsModel->where('constants_id', $leave_type)->where('type','leave_type')->first();
						$category_name = $ltype['category_name'];	
						$isubject = $itemplate['subject'];
						$ibody = html_entity_decode($itemplate['message']);
						$fbody = str_replace(array("{site_name}","{employee_name}","{leave_type}"),array($company_info['company_name'],$full_name,$category_name),$ibody);
						timehrm_mail_data($istaff_info['email'],$company_info['company_name'],$company_info['email'],$isubject,$fbody);

					}
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
			}
			return $this->response->setJSON($Return);
			
		}
	}


	public function leave_type_chart()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ConstantsModel = new ConstantsModel();
		$LeaveModel = new LeaveModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
		} else {
			$get_data = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
		}
		$data = array();
		$Return = array('iseries' => '', 'ilabels' => '');
		$title_info = array();
		$series_info = array();
		foreach ($get_data as $r) {
			$leave_info = $LeaveModel->where('leave_type_id', $r['constants_id'])->first();
			$leave_count = $LeaveModel->where('leave_type_id', $r['constants_id'])->countAllResults();
			if ($leave_count > 0) {
				$title_info[] = $r['category_name'];
				$series_info[] = $leave_count;
			}
		}
		$Return['iseries'] = $series_info;
		$Return['ilabels'] = $title_info;
		return $this->response->setJSON($Return);
		exit;
	}
	public function leave_status_chart()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$ConstantsModel = new ConstantsModel();
		$LeaveModel = new LeaveModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$leave_pending = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
			$total_accepted = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
			$total_rejected = $LeaveModel->where('employee_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
		} else {
			$leave_pending = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
			$total_accepted = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
			$total_rejected = $LeaveModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
		}
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('accepted' => '', 'accepted_count' => '', 'pending' => '', 'pending_count' => '', 'rejected' => '', 'rejected_count' => '');

		//accepted
		$Return['accepted'] = lang('Main.xin_approved');
		$Return['accepted_count'] = $total_accepted;
		// pending
		$Return['pending'] = lang('Main.xin_pending');
		$Return['pending_count'] = $leave_pending;
		// rejected
		$Return['rejected'] = lang('Main.xin_rejected');
		$Return['rejected_count'] = $total_rejected;
		return $this->response->setJSON($Return);
		
	}

	public function update_leave()
	{

		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Initialize response array
		$Return = [
			'result' => '',
			'error' => '',
			'csrf_hash' => csrf_hash()
		];

		// Check if user is authorized
		if (!in_array($user_info['user_type'], ['company', 'staff'])) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		// Check if it's a POST request
		if (!$this->request->is('post')) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		// Set validation rules
		$rules = [
			'remarks' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			],
			'reason' => [
				'rules' => 'required',
				'errors' => ['required' => lang('Main.xin_error_field_text')]
			]
		];

		if (!$this->validate($rules)) {
			$ruleErrors = [
				"remarks" => $validation->getError('remarks'),
				"reason" => $validation->getError('reason')
			];

			foreach ($ruleErrors as $err) {
				if (!empty($err)) {
					$Return['error'] = $err;
					return $this->response->setJSON($Return);
				}
			}
		}

		// Process valid data
		$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
		$reason = $this->request->getPost('reason', FILTER_SANITIZE_STRING);
		$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

		$data = [
			'remarks' => $remarks,
			'reason' => $reason,
			'updated_at' => date('Y-m-d H:i:s') // Add update timestamp
		];



		$LeaveModel = new LeaveModel();
		$result = $LeaveModel->update($id, $data);
		$Return['csrf_hash'] = csrf_hash();

		if ($result) {
			$Return['result'] = lang('Success.ci_leave_updated_msg');
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
		}

		return $this->response->setJSON($Return);
	}

	// |||update record|||
	// public function update_leave_status()
	// {
	// 	$validation =  \Config\Services::validation();
	// 	$session = \Config\Services::session();
	// 	$request = \Config\Services::request();
	// 	$usession = $session->get('sup_username');
	// 	if ($this->request->getPost('type') === 'edit_record') {
	// 		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
	// 		$Return['csrf_hash'] = csrf_hash();
	// 		// set rules
	// 		$rules = [
	// 			'status' => [
	// 				'rules'  => 'required',
	// 				'errors' => [
	// 					'required' => lang('Main.xin_error_field_text')
	// 				]
	// 			],
	// 			'remarks' => [
	// 				'rules'  => 'required',
	// 				'errors' => [
	// 					'required' => lang('Main.xin_error_field_text')
	// 				]
	// 			]
	// 		];
	// 		if (!$this->validate($rules)) {
	// 			$ruleErrors = [
	// 				"remarks" => $validation->getError('remarks'),
	// 				"status" => $validation->getError('status')
	// 			];
	// 			foreach ($ruleErrors as $err) {
	// 				$Return['error'] = $err;
	// 				if ($Return['error'] != '') {
	// 					$this->output($Return);
	// 				}
	// 			}
	// 		} else {
	// 			$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
	// 			$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
	// 			$id = udecode($this->request->getPost('token_status', FILTER_SANITIZE_STRING));
	// 			$data = [
	// 				'remarks' => $remarks,
	// 				'status'  => $status
	// 			];
	// 			$UsersModel = new UsersModel();
	// 			$LeaveModel = new LeaveModel();
	// 			$ConstantsModel = new ConstantsModel();
	// 			$EmailtemplatesModel = new EmailtemplatesModel();
	// 			$result = $LeaveModel->update($id, $data);
	// 			$SystemModel = new SystemModel();
	// 			$xin_system = $SystemModel->where('setting_id', 1)->first();
	// 			$Return['csrf_hash'] = csrf_hash();
	// 			if ($result == TRUE) {
	// 				$Return['result'] = lang('Success.ci_leave_status_updated_msg');
	// 				if ($xin_system['enable_email_notification'] == 1) {
	// 					$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
	// 					if ($user_info['user_type'] == 'staff') {
	// 						$company_info = $UsersModel->where('company_id', $user_info['company_id'])->first();
	// 					} else {
	// 						$company_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
	// 					}
	// 					$leave_result = $LeaveModel->where('leave_id', $id)->first();
	// 					if ($status  == 2) { //approve
	// 						// Send mail start

	// 						$itemplate = $EmailtemplatesModel->where('template_id', 14)->first();

	// 						$istaff_info = $UsersModel->where('user_id', $leave_result['employee_id'])->first();

	// 						// leave type
	// 						$ltype = $ConstantsModel->where('constants_id', $leave_result['leave_type_id'])->where('type', 'leave_type')->first();
	// 						$category_name = $ltype['category_name'];
	// 						$isubject = $itemplate['subject'];
	// 						$ibody = html_entity_decode($itemplate['message']);
	// 						$fbody = str_replace(array("{site_name}", "{leave_type}", "{start_date}", "{end_date}", "{remarks}"), array($company_info['company_name'], $category_name, $leave_result['from_date'], $leave_result['to_date'], $remarks), $ibody);

	// 						// timehrm_mail_data($company_info['email'],$company_info['company_name'],$istaff_info['email'],$isubject,$fbody);
	// 						$Return = timehrm_mail_data($company_info['email'], $company_info['company_name'], $istaff_info['email'], $isubject, $fbody);

	// 						if ($Return['result'] != '') {
	// 							$Return['result'];
	// 						} else {
	// 							$Return['error'];
	// 						}
	// 					} elseif ($status == 3) {
	// 						// Send mail start
	// 						$itemplate = $EmailtemplatesModel->where('template_id', 15)->first();
	// 						$istaff_info = $UsersModel->where('user_id', $leave_result['employee_id'])->first();
	// 						// leave type
	// 						$ltype = $ConstantsModel->where('constants_id', $leave_result['leave_type_id'])->where('type', 'leave_type')->first();
	// 						$category_name = $ltype['category_name'];
	// 						$isubject = $itemplate['subject'];
	// 						$ibody = html_entity_decode($itemplate['message']);
	// 						$fbody = str_replace(array("{site_name}", "{leave_type}", "{start_date}", "{end_date}", "{remarks}"), array($company_info['company_name'], $category_name, $leave_result['from_date'], $leave_result['to_date'], $remarks), $ibody);
	// 						// timehrm_mail_data($company_info['email'],$company_info['company_name'],$istaff_info['email'],$isubject,$fbody);
	// 						$Return = timehrm_mail_data($company_info['email'], $company_info['company_name'], $istaff_info['email'], $isubject, $fbody);
	// 						if ($Return['result'] != '') {
	// 							$Return['result'];
	// 						} else {
	// 							// Error
	// 							$Return['error'];
	// 						}
	// 					} else {
	// 					}
	// 				}
	// 			} else {
	// 				$Return['error'] = lang('Main.xin_error_msg');
	// 			}
	// 			return $this->response->setJSON($Return);
	// 			exit;
	// 		}
	// 	} else {
	// 		$Return['error'] = lang('Main.xin_error_msg');
	// 		return $this->response->setJSON($Return);
	// 		exit;
	// 	}
	// }

	public function update_leave_status()
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

		// Check if it's a POST request
		if (!$this->request->is('post')) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		// Validation rules
		$rules = [
			'status' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Main.xin_error_field_text')
				]
			],
			'remarks' => [
				'rules' => 'required',
				'errors' => [
					'required' => lang('Main.xin_error_field_text')
				]
			]
		];

		if (!$this->validate($rules)) {
			$ruleErrors = [
				"remarks" => $validation->getError('remarks'),
				"status" => $validation->getError('status')
			];

			foreach ($ruleErrors as $err) {
				if (!empty($err)) {
					$Return['error'] = $err;
					break;
				}
			}
			return $this->response->setJSON($Return);
		}

		// Process valid data
		$remarks = $this->request->getPost('remarks', FILTER_SANITIZE_STRING);
		$status = $this->request->getPost('status', FILTER_SANITIZE_STRING);
		$id = udecode($this->request->getPost('token_status', FILTER_SANITIZE_STRING));

		$data = [
			'remarks' => $remarks,
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		];

		// Load models
		$UsersModel = new \App\Models\UsersModel();
		$LeaveModel = new \App\Models\LeaveModel();
		$ConstantsModel = new \App\Models\ConstantsModel();
		$EmailtemplatesModel = new \App\Models\EmailtemplatesModel();
		$SystemModel = new \App\Models\SystemModel();

		// Update leave status
		$result = $LeaveModel->update($id, $data);

		if (!$result) {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
		}

		$Return['result'] = lang('Success.ci_leave_status_updated_msg');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		// Skip email if notifications are disabled
		if ($xin_system['enable_email_notification'] != 'yes') {
			return $this->response->setJSON($Return);
		}

		// Prepare email notification
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_info = ($user_info['user_type'] == 'staff')
			? $UsersModel->where('company_id', $user_info['company_id'])->first()
			: $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$leave_result = $LeaveModel->where('leave_id', $id)->first();

		// Skip if status doesn't require notification (only for approved=2 or rejected=3)
		if (!in_array($status, [2, 3])) {
			return $this->response->setJSON($Return);
		}

		// Determine template based on status
		$template_id = ($status == 2) ? 14 : 15; // 14=approved, 15=rejected
		$itemplate = $EmailtemplatesModel->where('template_id', $template_id)->first();

		if (!$itemplate) {
			return $this->response->setJSON($Return);
		}

		$istaff_info = $UsersModel->where('user_id', $leave_result['employee_id'])->first();
		$ltype = $ConstantsModel->where('constants_id', $leave_result['leave_type_id'])
			->where('type', 'leave_type')
			->first();

		$category_name = $ltype['category_name'] ?? '';

		// Prepare email content
		$isubject = $itemplate['subject'];
		$ibody = html_entity_decode($itemplate['message']);

		// Format dates if needed
		$from_date = date('d M Y', strtotime($leave_result['from_date']));
		$to_date = date('d M Y', strtotime($leave_result['to_date']));

		$fbody = str_replace(
			["{site_name}", "{leave_type}", "{start_date}", "{end_date}", "{remarks}"],
			[
				$company_info['company_name'],
				$category_name,
				$from_date,
				$to_date,
				$remarks
			],
			$ibody
		);

		// Send email
		$emailResult = timehrm_mail_data(
			$company_info['email'],
			$company_info['company_name'],
			$istaff_info['email'],
			$isubject,
			$fbody
		);

		if ($emailResult['result'] != '') {
			$Return['result'] .= ' ' . $emailResult['result'];
		} elseif (isset($emailResult['error'])) {
			$Return['error'] .= ' ' . $emailResult['error'];
		}

		return $this->response->setJSON($Return);
	}
	// read record
	public function read_leave()
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
			return view('erp/leave/dialog_leave', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_leave()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$LeaveModel = new LeaveModel();
			$result = $LeaveModel->where('leave_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_leave_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
}
