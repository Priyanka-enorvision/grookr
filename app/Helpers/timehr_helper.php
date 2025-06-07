<?php

use CodeIgniter\I18n\Time;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//TimeHRM - Helper
if (!function_exists('dashboard_profile_completeness')) {

	function dashboard_profile_completeness()
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$ShiftModel = new \App\Models\ShiftModel();
		$RolesModel = new \App\Models\RolesModel();
		$ConstantsModel = new \App\Models\ConstantsModel();
		$DesignationModel = new \App\Models\DesignationModel();
		$DepartmentModel = new \App\Models\DepartmentModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$departments = $DepartmentModel->where('company_id', $usession['sup_user_id'])->countAllResults();
		$designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->countAllResults();
		$office_shifts = $ShiftModel->where('company_id', $usession['sup_user_id'])->countAllResults();
		$roles = $RolesModel->where('company_id', $usession['sup_user_id'])->countAllResults();
		$competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->countAllResults();
		$competencies2 = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies2')->countAllResults();
		$fcompetencies = $competencies + $competencies2;
		if ($roles > 0):
			$roles = 1;
		endif;
		if ($fcompetencies > 1):
			$fcompetencies = 1;
		else:
			$fcompetencies = 0;
		endif;
		if ($departments > 0):
			$departments = 1;
		endif;
		if ($designations > 0):
			$designations = 1;
		endif;
		if ($office_shifts > 0):
			$office_shifts = 1;

		endif;

		$val = $roles + $fcompetencies + $departments + $designations + $office_shifts;
		$total_val = $val * 20;
		$data = array('departments' => $departments, 'designations' => $designations, 'office_shifts' => $office_shifts, 'roles' => $roles, 'competencies' => $fcompetencies, 'percent' => $total_val);
		return $data;
	}
}
if (!function_exists('attendance_time_checks')) {

	function attendance_time_checks()
	{

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$today_date = date('Y-m-d');
		$UsersModel = new \App\Models\UsersModel();
		$TimesheetModel = new \App\Models\TimesheetModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $TimesheetModel->where('company_id', $user_info['company_id'])->where('employee_id', $usession['sup_user_id'])->where('attendance_date', $today_date)->where('clock_out', '')->orderBy('time_attendance_id', 'DESC')->countAllResults();
		if ($get_data > 0):
			$get_data = 1;
		else:
			$get_data = 0;

		endif;
		return $get_data;
	}
}
if (!function_exists('attendance_time_checks_value')) {
	// attendance_time_checks_value
	function attendance_time_checks_value()
	{
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$UsersModel = new \App\Models\UsersModel();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$builder = $db->table('ci_timesheet');
		$builder->where('company_id', $user_info['company_id']);
		$builder->where('employee_id', $usession['sup_user_id']);
		$builder->where('clock_out', '');
		$builder->orderBy('time_attendance_id', 'DESC');
		$builder->limit(1);
		$query = $builder->get();
		return $query->getResult();
	}
}
if (!function_exists('check_user_attendance')) {

	function check_user_attendance()
	{

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$today_date = date('Y-m-d');
		$UsersModel = new \App\Models\UsersModel();
		$TimesheetModel = new \App\Models\TimesheetModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $TimesheetModel->where('company_id', $user_info['company_id'])->where('employee_id', $usession['sup_user_id'])->where('attendance_date', $today_date)->orderBy('time_attendance_id', 'DESC')->countAllResults();
		if ($get_data > 0):
			$get_data = 1;
		else:
			$get_data = 0;

		endif;
		return $get_data;
	}
}
if (!function_exists('check_user_attendance_value')) {
	/**
	 * Checks the current user's attendance record for today
	 * 
	 * @return array|null Returns attendance records or null if error occurs
	 * @throws \Exception If database query fails
	 */
	function check_user_attendance_value()
	{
		// Initialize session service
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Validate session data
		if (empty($usession) || !isset($usession['sup_user_id'])) {
			log_message('error', 'Invalid user session in attendance check');
			return null;
		}

		
		$UsersModel = new \App\Models\UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Validate user data
		if (empty($user_info) || !isset($user_info['company_id'])) {
			log_message('error', 'User not found or missing company ID');
			return null;
		}

		try {
			// Initialize database connection
			$db = \Config\Database::connect();
			$today_date = date('Y-m-d');

			// Build query
			$builder = $db->table('ci_timesheet');
			$builder->where('company_id', $user_info['company_id']);
			$builder->where('employee_id', $usession['sup_user_id']);
			$builder->where('attendance_date', $today_date);
			$builder->orderBy('time_attendance_id', 'DESC');
			$builder->limit(1);

			// Execute query and return results
			return $builder->get()->getResultArray();
		} catch (\Exception $e) {
			log_message('error', 'Attendance check error: ' . $e->getMessage());
			return null;
		}
	}
}
if (!function_exists('check_user_attendance_clockout_value')) {
	/**
	 * Checks if user has an open attendance clock-out for today
	 * 
	 * @return array|null Query results or null if error occurs
	 */
	function check_user_attendance_clockout_value()
	{
		// Initialize services
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Validate session data
		if (empty($usession) || !isset($usession['sup_user_id'])) {
			log_message('error', 'Invalid user session in attendance check');
			return null;
		}

		// Get user info
		$UsersModel = new \App\Models\UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		// Validate user data
		if (empty($user_info) || !isset($user_info['company_id'])) {
			log_message('error', 'User not found or missing company ID');
			return null;
		}

		try {
			$db = \Config\Database::connect();
			$today_date = date('Y-m-d');

			$builder = $db->table('ci_timesheet');
			$builder->where('company_id', $user_info['company_id']);
			$builder->where('employee_id', $usession['sup_user_id']);
			$builder->where('attendance_date', $today_date);
			$builder->where('clock_out', '');
			$builder->orderBy('time_attendance_id', 'DESC');
			$builder->limit(1);

			return $builder->get()->getResult();
		} catch (\Exception $e) {
			log_message('error', 'Attendance check error: ' . $e->getMessage());
			return null;
		}
	}
}
if (!function_exists('user_attendance_monthly_value')) {
	// user_attendance_monthly_value
	function user_attendance_monthly_value($attendance_date, $user_id)
	{

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new \App\Models\UsersModel();
		$TimesheetModel = new \App\Models\TimesheetModel();
		$user_info = $UsersModel->where('user_id', $user_id)->first();
		$get_data = $TimesheetModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('attendance_date', $attendance_date)->orderBy('time_attendance_id', 'DESC')->countAllResults();
		if ($get_data > 0):
			$get_data = 1;
		else:
			$get_data = 0;

		endif;
		return $get_data;
	}
}
// single user
if (!function_exists('staff_profile_photo')) {
	function staff_profile_photo($user_id)
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$user_info = $UsersModel->where('user_id', $user_id)->first();
		if ($user_info['user_type'] != 'customer') {
			if ($user_info['profile_photo'] == '' || $user_info['profile_photo'] == 'no') {
				if ($user_info['gender'] == 1) {
					$user_img = base_url() . 'uploads/users/default/default_profile.jpg';
				} else {
					$user_img = base_url() . 'uploads/users/default/default_profile.jpg';
				}
			} else {
				$user_img = $user_info['profile_photo'];
			}
		} else {
			if ($user_info['profile_photo'] == '' || $user_info['profile_photo'] == 'no') {
				if ($user_info['gender'] == 1) {
					$user_img = base_url() . 'uploads/users/default/default_profile.jpg';
				} else {
					$user_img = base_url() . 'uploads/users/default/default_profile.jpg';
				}
			} else {
				$user_img = $user_info['profile_photo'];
			}
		}
		return $user_img;
	}
}
// multi users with profile photo
if (!function_exists('multi_user_profile_photo')) {
	function multi_user_profile_photo($multi_user)
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();

		$defaultMalePhoto = base_url() . 'uploads/users/default/images.jpg';
		$defaultFemalePhoto = base_url() . 'uploads/users/default/download.jpg';

		$ol = '';

		// Ensure $multi_user is an array
		if (!is_array($multi_user)) {
			return $ol;
		}

		foreach ($multi_user as $user_id) {
			if (empty($user_id)) {
				continue;
			}

			$assigned_to = $UsersModel->where('user_id', $user_id)->first();

			if (empty($assigned_to)) {
				$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="Unknown User">';
				$ol .= '<span class="mb-1"><img src="' . $defaultMalePhoto . '" class="img-fluid img-radius wid-30 mr-1" alt=""></span>';
				$ol .= '</a>';
				continue;
			}

			// Set default name if empty
			$assigned_name = !empty($assigned_to['first_name'])
				? $assigned_to['first_name'] . ' ' . ($assigned_to['last_name'] ?? '')
				: 'Unknown User';

			// Determine profile photo
			$gender = $assigned_to['gender'] ?? 1; // Default to male if gender not set
			$profile_photo = $assigned_to['profile_photo'] ?? null;

			if (empty($profile_photo)) {
				$user_img = ($gender == 1) ? $defaultMalePhoto : $defaultFemalePhoto;
			} else {
				$user_img_path = FCPATH . 'uploads/users/thumb/' . $profile_photo;
				$user_img = file_exists($user_img_path)
					? base_url() . 'uploads/users/thumb/' . $profile_photo
					: (($gender == 1) ? $defaultMalePhoto : $defaultFemalePhoto);
			}

			$ol .= '<a href="javascript:void(0);" data-toggle="tooltip" data-placement="top" title="' . $assigned_name . '">';
			$ol .= '<span class="mb-1"><img src="' . $user_img . '" class="img-fluid img-radius wid-30 mr-1" alt=""></span>';
			$ol .= '</a>';
		}

		return $ol;
	}
}

// multi users only names
if (!function_exists('multi_users_info')) {
	function multi_users_info($multi_user)
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();

		$ol = '';
		$i = 0;
		foreach ($multi_user as $user_id) {
			if ($user_id != 0) {
				// get staff info
				$assigned_to = $UsersModel->where('user_id', $user_id)->first();
				if ($assigned_to) {
					$assigned_name = $assigned_to['first_name'] . ' ' . $assigned_to['last_name'];
					$ol .= $i . ': ' . $assigned_name . '<br>';
				} else {
					$ol .= $i . ': Unknown User (ID: ' . $user_id . ')<br>';
				}
			}
			$i++;
		}
		return $ol;
	}

}

// company membership || expiry
if (!function_exists('user_company_info')) {
	function user_company_info()
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$UsersModel = new \App\Models\UsersModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'company') {
			$company_id = $usession['sup_user_id'];
		} else if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else if ($user_info['user_type'] == 'customer') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		return $company_id;
	}
}
// company membership || expiry
if (!function_exists('company_membership_details')) {
	function company_membership_details()
	{
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		//use CodeIgniter\I18n\Time;
		//new \App\I18n\Time();
		$UsersModel = new \App\Models\UsersModel();
		$MembershipModel = new \App\Models\MembershipModel();
		$CompanymembershipModel = new \App\Models\CompanymembershipModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_membership = $CompanymembershipModel->where('company_id', $usession['sup_user_id'])->first();
		$subs_plan = $MembershipModel->where('membership_id', $company_membership['membership_id'])->first();

		if ($subs_plan['plan_duration'] == 1) {
			if ($subs_plan['membership_id'] == 1) {
				$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
				$add_time = $time->addMonths(1);
				$now = Time::now('Asia/Karachi');
				$itime = Time::parse($add_time, 'Asia/Karachi');
				$diff_days = $now->difference($itime);
				$plan_msg = lang('Membership.xin_trial_is_expiring') . ' ' . $diff_days->getDays() . ' ' . lang('Leave.xin_leave_days');
				$remaing_days = $diff_days->getDays();
			} else {
				$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
				$add_time = $time->addMonths(1);
				$now = Time::now('Asia/Karachi');
				$itime = Time::parse($add_time, 'Asia/Karachi');
				$diff_days = $now->difference($itime);
				$plan_msg = lang('Membership.xin_plan_is_expiring') . ' ' . $diff_days->getDays() . ' ' . lang('Leave.xin_leave_days');
				$remaing_days = $diff_days->getDays();
			}
			$plan_duration = lang('Membership.xin_subscription_monthly');
		} else if ($subs_plan['plan_duration'] == 2) {
			if ($subs_plan['membership_id'] == 1) {
				$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
				$add_time = $time->addYears(1);
				$now = Time::now('Asia/Karachi');
				$itime = Time::parse($add_time, 'Asia/Karachi');
				$diff_days = $now->difference($itime);
				$plan_msg = lang('Membership.xin_trial_is_expiring') . ' ' . $diff_days->getDays() . ' ' . lang('Leave.xin_leave_days');
				$remaing_days = $diff_days->getDays();
			} else {
				$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
				$add_time = $time->addYears(1);
				$now = Time::now('Asia/Karachi');
				$itime = Time::parse($add_time, 'Asia/Karachi');
				$diff_days = $now->difference($itime);
				$plan_msg = lang('Membership.xin_plan_is_expiring') . ' ' . $diff_days->getDays() . ' ' . lang('Leave.xin_leave_days');
				$remaing_days = $diff_days->getDays();
			}
			$plan_duration = lang('Membership.xin_subscription_yearly');
		} else {
			if ($subs_plan['membership_id'] == 1) {
				$plan_msg = lang('Membership.xin_you_are_using_unlimited_plan');
			} else {
				$plan_msg = lang('Membership.xin_you_are_using_unlimited_plan');
			}
			$time_diff = '';
			$plan_duration = lang('Membership.xin_subscription_unlimit');
			$remaing_days = 300 * 100;
		}
		$plan_data = array('plan_msg' => $plan_msg, 'plan_duration' => $plan_duration, 'diff_days' => $remaing_days);
		return $plan_data;
	}
}
// company membership || In-Active|Active
if (!function_exists('company_membership_activation')) {
	function company_membership_activation()
	{

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		//use CodeIgniter\I18n\Time;
		//new \App\I18n\Time();
		$UsersModel = new \App\Models\UsersModel();
		$MembershipModel = new \App\Models\MembershipModel();
		$CompanymembershipModel = new \App\Models\CompanymembershipModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'company') {
			$company_id = $usession['sup_user_id'];
		} else if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else if ($user_info['user_type'] == 'customer') {
			$company_id = $user_info['company_id'];
		}

		$company_membership = $CompanymembershipModel->where('company_id', $company_id)->first();
		$subs_plan = $MembershipModel->where('membership_id', $company_membership['membership_id'])->first();

		if ($subs_plan['plan_duration'] == 1) {
			$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
			$add_time = $time->addMonths(1);
			$now = Time::now('Asia/Karachi');
			$itime = Time::parse($add_time, 'Asia/Karachi');
			$diff_days = $now->difference($itime);
			$time_diff = $itime->isAfter($now);
			return $time_diff;
		} else if ($subs_plan['plan_duration'] == 2) {
			$time = Time::parse($company_membership['update_at'], 'Asia/Karachi');
			$add_time = $time->addYears(1);
			$now = Time::now('Asia/Karachi');
			$itime = Time::parse($add_time, 'Asia/Karachi');
			$diff_days = $now->difference($itime);
			$time_diff = $itime->isAfter($now);
			return $time_diff;
		} else {
			return 1;
		}
	}
}
if (!function_exists('notification_data')) {
	function notification_data() {}
}
if (!function_exists('erp_company_settings')) {
	function erp_company_settings()
	{
		// Load session service
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!isset($usession['sup_user_id'])) {
			return null; // session user not found
		}

		// Load models
		$UsersModel = new \App\Models\UsersModel();
		$CompanysettingsModel = new \App\Models\CompanysettingsModel();

		// Fetch user info
		$user_info = $UsersModel->find($usession['sup_user_id']);
		if (!$user_info) {
			return null;
		}

		// Determine company_id based on user type
		switch ($user_info['user_type']) {
			case 'company':
				$company_id = $user_info['user_id'];
				break;
			case 'staff':
			case 'customer':
				$company_id = $user_info['company_id'] ?? null;
				break;
			default:
				$company_id = null;
		}

		if (!$company_id) {
			return null;
		}

		// Return company settings
		return $CompanysettingsModel->where('company_id', $company_id)->first();
	}
}


if (!function_exists('generate_timezone_list')) {
	function generate_timezone_list()
	{
		static $regions = array(
			DateTimeZone::AFRICA,
			DateTimeZone::AMERICA,
			DateTimeZone::ANTARCTICA,
			DateTimeZone::ASIA,
			DateTimeZone::ATLANTIC,
			DateTimeZone::AUSTRALIA,
			DateTimeZone::EUROPE,
			DateTimeZone::INDIAN,
			DateTimeZone::PACIFIC,

		);

		$timezones = array();
		foreach ($regions as $region) {
			$timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
		}

		$timezone_offsets = array();
		foreach ($timezones as $timezone) {
			$tz = new DateTimeZone($timezone);
			$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
		}

		// sort timezone by offset
		asort($timezone_offsets);

		$timezone_list = array();
		foreach ($timezone_offsets as $timezone => $offset) {
			$offset_prefix = $offset < 0 ? '-' : '+';
			$offset_formatted = gmdate('H:i', abs($offset));

			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";

			$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
		}

		return $timezone_list;
	}
}
if (!function_exists('timehrm_mail_data')) {

	function timehrm_mail_data($from, $from_name, $to, $subject, $body)
	{

		// get session
		$session = \Config\Services::session();

		$SystemModel = new \App\Models\SystemModel();
		$UsersModel = new \App\Models\UsersModel();
		//$EmailtemplatesModel = new \App\Models\EmailtemplatesModel();

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		

		if ($xin_system['email_type'] == "codeigniter") {

			//default email config
			$email = \Config\Services::email();
			//$email->mailType("html");
			$email->setFrom($from, $from_name);
			$email->setTo($to);
			$email->setSubject($subject);
			$email->setMessage($body);
			$email->send();
		} elseif ($xin_system['email_type'] == "smtp") {
			//default smtp config
			$email = \Config\Services::smtp();
			//$email->mailType("html");
			$email->setFrom($from, $from_name);
			$email->setTo($to);
			$email->setSubject($subject);
			$email->setMessage($body);
			$email->send();
		} elseif ($xin_system['email_type'] == "phpmail") {

			require_once APPPATH . '/ThirdParty/phpmailer/vendor/autoload.php';
			require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/PHPMailer.php';
			require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/Exception.php';
			require_once APPPATH . '/ThirdParty/phpmailer/vendor/phpmailer/src/SMTP.php';
			$mail = new PHPMailer(true);
			try {
				$mail->isSMTP();
				$mail->Host = 'smtp.gmail.com';
				$mail->SMTPAuth = true;
				$mail->Username = 'manish865105@gmail.com';
				$mail->Password = 'nmfbqepjkioluhqp';
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = 587;

				$mail->setFrom($from, $from_name);
				$mail->addAddress($to);

				$mail->isHTML(true);
				$mail->Subject = $subject;
				$mail->Body = $body;

				$mail->send();

				return [
					'success' => true,
					'message' => 'Email sent successfully.',
				];
			} catch (Exception $e) {
				log_message('error', 'Mailer Error: ' . $mail->ErrorInfo);

				return [
					'success' => false,
					'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}",
				];
			}
		}
	}
}
if (!function_exists('account_statement_report')) {

	function account_statement_report($start_date, $end_date, $get_id)
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$AccountsModel = new \App\Models\AccountsModel();
		$TransactionsModel = new \App\Models\TransactionsModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$builder = $db->table('ci_finance_transactions');
		$builder->where('transaction_date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
		$builder->where('account_id', $get_id);
		$builder->where('company_id', $company_id);
		$query = $builder->get();
		$transaction_data = $query->getResult();

		return $transaction_data;
	}
}
if (!function_exists('training_report')) {

	function training_report($start_date, $end_date, $status)
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$TrainingModel = new \App\Models\TrainingModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		if ($status == 'all_status') {
			$training_data = $TrainingModel->where('company_id', $company_id)->where('start_date >=', $start_date)->where('finish_date <=', $end_date)->findAll();
		} else {
			$training_data = $TrainingModel->where('company_id', $company_id)->where('start_date >=', $start_date)->where('finish_date <=', $end_date)->where('training_status', $status)->findAll();
		}

		return $training_data;
	}
}
if (!function_exists('leave_report')) {

	function leave_report($start_date, $end_date, $status)
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$LeaveModel = new \App\Models\LeaveModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		if ($status == 'all_status') {
			$leave_data = $LeaveModel->where('company_id', $company_id)->where('from_date >=', $start_date)->where('to_date <=', $end_date)->findAll();
		} else {
			$leave_data = $LeaveModel->where('company_id', $company_id)->where('from_date >=', $start_date)->where('to_date <=', $end_date)->where('status', $status)->findAll();
		}

		return $leave_data;
	}
}
if (!function_exists('invoice_report')) {

	function invoice_report($start_date, $end_date, $status)
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$AccountsModel = new \App\Models\AccountsModel();
		$TransactionsModel = new \App\Models\TransactionsModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$builder = $db->table('ci_invoices');
		if ($status == 'all_status') {
			$builder->where('invoice_date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
			$builder->where('company_id', $company_id);
		} else {
			$builder->where('invoice_date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
			$builder->where('status', $status);
			$builder->where('company_id', $company_id);
		}

		$query = $builder->get();
		$transaction_data = $query->getResult();

		return $transaction_data;
	}
}
if (!function_exists('currency_converter_values')) {

	function currency_converter_values()
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$url = 'https://api.exchangerate-api.com/v4/latest/' . $xin_system['default_currency'];
		// $to_currency = $toCurrency;
		$url = file_get_contents($url);
		$url = json_decode($url);
		$rates = $url->rates;
		return $rates;
	}
}
if (!function_exists('currency_converted_values')) {

	function currency_converted_values()
	{
		// get db
		$db = \Config\Database::connect();

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$currency_val = unserialize($xin_system['currency_converter']);

		$converted = 0;
		foreach ($currency_val as $_val => $_key) {
			if ($_val == $xin_system['default_currency']) {
				echo $converted = $_key;
			}
		}
	}
}
if (!function_exists('currency_converter')) {

	function currency_converter($amount)
	{

		$SystemModel = new \App\Models\SystemModel();
		$xin_system = erp_company_settings();
		$xin_super_system = $SystemModel->where('setting_id', 1)->first();
		$currency_val = unserialize($xin_super_system['currency_converter']);
		$i = 0;
		foreach ($currency_val as $_val => $_key) {
			if ($_val == $xin_system['default_currency']) {
				$converted = $_key * $amount;
				return $converted;
			}
			$i++;
		}
	}
}
if (!function_exists('staff_projects')) {

	function staff_projects($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$ProjectsModel = new \App\Models\ProjectsModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$company_id = $user_info['company_id'];

		$data = $ProjectsModel->where('company_id', $company_id)->where("assigned_to like '%$id,%' or assigned_to like '%,$id%' or assigned_to = '$id'")->findAll();

		return $data;
	}
}
if (!function_exists('assigned_staff_projects')) {

	function assigned_staff_projects($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$ProjectsModel = new \App\Models\ProjectsModel();


		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];


		$data = $ProjectsModel->where('company_id', $company_id)->where('added_by', $user_info['user_id'])->orWhere('FIND_IN_SET(' . $user_info['user_id'] . ', assigned_to) > 0')->findAll();


		return $data;
	}
}

if (!function_exists('assigned_staff_projects_board')) {

	function assigned_staff_projects_board($id)
	{
		// Get database and session instances
		$db = \Config\Database::connect();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Load models
		$UsersModel = new \App\Models\UsersModel();
		$ProjectsModel = new \App\Models\ProjectsModel();

		// Fetch user info
		$user_info = $UsersModel->find($usession['sup_user_id']);
		$company_id = $user_info['company_id'];
		$user_id = $user_info['user_id'];

		// Initialize cURL for fetching expert user details
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
			die("cURL Error: " . curl_error($curl));
		}

		$expert_user_detail = json_decode($response, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			die("JSON Decoding Error: " . json_last_error_msg());
		}

		curl_close($curl);

		$expert_id = $expert_user_detail['detail']['id'] ?? null;

		$getProjectsByStatus = function ($status) use ($ProjectsModel, $company_id, $user_id, $expert_id) {
			$builder = $ProjectsModel->where('company_id', $company_id)
				->where('status', $status)
				->groupStart()
				->where('added_by', $user_id)
				->orWhere("FIND_IN_SET($user_id, assigned_to) > 0");

			if ($expert_id !== null) {
				$builder->orWhere("FIND_IN_SET($expert_id, expert_to) > 0");
			}

			$builder->groupEnd();

			return $builder->findAll();
		};

		$data = [
			'not_started_projects' => $getProjectsByStatus(0),
			'inprogress_projects' => $getProjectsByStatus(1),
			'completed_projects' => $getProjectsByStatus(2),
			'cancelled_projects' => $getProjectsByStatus(3),
			'hold_projects' => $getProjectsByStatus(4),
		];

		return $data;
	}
}


if (!function_exists('assigned_staff_tasks')) {

	function assigned_staff_tasks($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$TasksModel = new \App\Models\TasksModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];

		// $data = $TasksModel->where('company_id',$company_id)->where("assigned_to like '%$id,%' or assigned_to like '%,$id%' or assigned_to = '$id'")->findAll();
		$data = $TasksModel->where('company_id', $user_info['company_id'])->orWhere('FIND_IN_SET(' . $user_info['user_id'] . ', assigned_to) > 0')->orderBy('task_id', 'ASC')->findAll();

		// var_dump($data);
		// die;

		return $data;
	}
}

if (!function_exists('assigned_staff_tasks_board')) {

	function assigned_staff_tasks_board($id)
	{
		// Get database and session instances
		$db = \Config\Database::connect();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Load models
		$UsersModel = new \App\Models\UsersModel();
		$TasksModel = new \App\Models\TasksModel();

		// Fetch user info
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];
		$user_id = $user_info['user_id'];

		// Initialize cURL for fetching expert user details
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
			curl_close($curl);
			die("cURL Error: " . curl_error($curl));
		}

		$expert_user_detail = json_decode($response, true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			curl_close($curl);
			die("JSON Decoding Error: " . json_last_error_msg());
		}

		curl_close($curl);

		// Extract expert ID if available
		$expert_id = $expert_user_detail['detail']['id'] ?? null;

		// Function to fetch tasks by status
		$getTasksByStatus = function ($status) use ($TasksModel, $company_id, $user_id, $expert_id) {
			$builder = $TasksModel->where('company_id', $company_id)
				->where('task_status', $status)
				->groupStart()
				->where('created_by', $user_id)
				->orWhere("FIND_IN_SET($user_id, assigned_to) > 0");

			if ($expert_id !== null) {
				$builder->orWhere("FIND_IN_SET($expert_id, expert_to) > 0");
			}

			$builder->groupEnd();

			return $builder->findAll();
		};

		// Fetch tasks by different statuses
		$data = [
			'not_started_tasks' => $getTasksByStatus(0),
			'inprogress_tasks' => $getTasksByStatus(1),
			'completed_tasks' => $getTasksByStatus(2),
			'cancelled_tasks' => $getTasksByStatus(3),
			'hold_tasks' => $getTasksByStatus(4),
		];

		// Get all tasks
		$get_data = $TasksModel->where('company_id', $company_id)
			->groupStart()
			->where('created_by', $user_id)
			->orWhere("FIND_IN_SET($user_id, assigned_to) > 0")
			->groupEnd()
			->orderBy('task_id', 'ASC')
			->findAll();

		// Add all tasks to the data array
		$data['data'] = $get_data;

		return $data;
	}
}

if (!function_exists('assigned_staff_training')) {

	function assigned_staff_training($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$TrainingModel = new \App\Models\TrainingModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];

		$data = $TrainingModel->where('company_id', $company_id)->where("employee_id like '%$id,%' or employee_id like '%,$id%' or employee_id = '$id'")->findAll();

		return $data;
	}
}
if (!function_exists('assigned_staff_events')) {

	function assigned_staff_events($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$EventsModel = new \App\Models\EventsModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];

		$data = $EventsModel->where('company_id', $company_id)->where("employee_id like '%$id,%' or employee_id like '%,$id%' or employee_id = '$id'")->findAll();

		return $data;
	}
}
if (!function_exists('assigned_staff_conference')) {

	function assigned_staff_conference($id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$MeetingModel = new \App\Models\MeetingModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$company_id = $user_info['company_id'];

		$data = $MeetingModel->where('company_id', $company_id)->where("employee_id like '%$id,%' or employee_id like '%,$id%' or employee_id = '$id'")->findAll();

		return $data;
	}
}
if (!function_exists('count_leads_followup')) {

	function count_leads_followup($lead_id)
	{
		// get db

		$db = \Config\Database::connect();
		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();
		$LeadsfollowupModel = new \App\Models\LeadsfollowupModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$count_result = $LeadsfollowupModel->where('company_id', $company_id)->where('lead_id', $lead_id)->countAllResults();

		return $count_result;
	}
}
