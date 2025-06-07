<?php
//Process String
if (!function_exists('super_user_role_resource')) {
	function super_user_role_resource()
	{
		// Initialize session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Validate session data
		if (!$usession || !isset($usession['sup_user_id'])) {
			return []; // Return empty array if session data is missing
		}

		// Initialize database connection
		$db = \Config\Database::connect();
		$UsersModel = new \App\Models\UsersModel();
		$SuperroleModel = new \App\Models\SuperroleModel();

		// Fetch user info
		$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if (!$user || !isset($user['user_role_id'])) {
			return []; // Return empty array if user not found or role ID missing
		}

		// Fetch role info
		$role_user = $SuperroleModel->where('role_id', $user['user_role_id'])->first();

		if (!$role_user || !isset($role_user['role_resources'])) {
			return []; // Return empty array if role not found or resources missing
		}

		// Convert role resources to array
		$role_resources_ids = explode(',', $role_user['role_resources']);

		return $role_resources_ids;
	}
}
if (!function_exists('set_date_format')) {
	//set currency sign
	function set_date_format($date)
	{

		// get session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new \App\Models\UsersModel();
		$SystemModel = new \App\Models\SystemModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($user_info['user_type'] == 'super_user') {

			$xin_system = $SystemModel->where('setting_id', 1)->first();
			// date format
			if ($xin_system['date_format_xi'] == 'Y-m-d') {
				$d_format = date("Y-m-d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y-d-m') {
				$d_format = date("Y-d-m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd-m-Y') {
				$d_format = date("d-m-Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm-d-Y') {
				$d_format = date("m-d-Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y/m/d') {
				$d_format = date("Y/m/d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y/d/m') {
				$d_format = date("Y/d/m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd/m/Y') {
				$d_format = date("d/m/Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm/d/Y') {
				$d_format = date("m/d/Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y.m.d') {
				$d_format = date("Y.m.d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y.d.m') {
				$d_format = date("Y.d.m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd.m.Y') {
				$d_format = date("d.m.Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm.d.Y') {
				$d_format = date("m.d.Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'F j, Y') {
				$d_format = date("F j, Y", strtotime($date));
			} else {
				$d_format = date('Y-m-d');
			}
		} else {
			$xin_system = erp_company_settings();
			// date format
			if ($xin_system['date_format_xi'] == 'Y-m-d') {
				$d_format = date("Y-m-d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y-d-m') {
				$d_format = date("Y-d-m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd-m-Y') {
				$d_format = date("d-m-Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm-d-Y') {
				$d_format = date("m-d-Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y/m/d') {
				$d_format = date("Y/m/d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y/d/m') {
				$d_format = date("Y/d/m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd/m/Y') {
				$d_format = date("d/m/Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm/d/Y') {
				$d_format = date("m/d/Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y.m.d') {
				$d_format = date("Y.m.d", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'Y.d.m') {
				$d_format = date("Y.d.m", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'd.m.Y') {
				$d_format = date("d.m.Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'm.d.Y') {
				$d_format = date("m.d.Y", strtotime($date));
			} else if ($xin_system['date_format_xi'] == 'F j, Y') {
				$d_format = date("F j, Y", strtotime($date));
			} else {
				$d_format = date('Y-m-d');
			}
		}


		return $d_format;
	}
}
if (!function_exists('leave_halfday_cal')) {
	function leave_halfday_cal($employee_id, $leave_type_id)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Check session more securely
		if (empty($usession['sup_user_id'])) {
			return 0;
		}

		// Use dependency injection instead of creating new instances
		$leaveModel = model('App\Models\LeaveModel');

		// Fetch leave records with more precise query
		$leave_halfday_cal = $leaveModel->where([
			'employee_id' => $employee_id,
			'leave_type_id' => $leave_type_id,
			'is_half_day' => 1,
			'status' => 2
		])->findAll();

		// Calculate count more efficiently
		return count($leave_halfday_cal) * 0.5;
	}
}
if (! function_exists('count_employee_leave')) {
	function count_employee_leave($employee_id, $leave_type_id): float
	{
		$totalDays = 0.0;
		$leaveModel = new \App\Models\LeaveModel();

		$approvedLeaves = $leaveModel->where([
			'employee_id' => $employee_id,
			'leave_type_id' => $leave_type_id,
			'status' => 2
		])->findAll();

		foreach ($approvedLeaves as $leave) {
			$days = erp_date_difference($leave['from_date'], $leave['to_date']);
			$totalDays += ($days < 2) ? 1 : $days;
		}

		return $totalDays;
	}
}

// generate employee id
if (!function_exists('generate_random_employeeid')) {
	function generate_random_employeeid($length = 6)
	{
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
// generate subscription id
if (!function_exists('generate_subscription_id')) {
	function generate_subscription_id($length = 10)
	{
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
// generate code
if (!function_exists('generate_random_code')) {
	function generate_random_code($length = 6)
	{
		$characters = '01Ikro23JKW2ElOK32IKlqwe902LOK789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}
// generate employee id
if (!function_exists('erp_date_difference')) {
	function erp_date_difference($datetime1, $datetime2)
	{

		$idatetime1 = date_create($datetime1);
		$idatetime2 = date_create($datetime2);
		$interval = date_diff($idatetime1, $idatetime2);
		$no_of_days = $interval->format('%a') + 1;

		return $no_of_days;
	}
}
if (!function_exists('staff_role_resource')) {
	function staff_role_resource()
	{
		// Initialize session
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		// Validate session data
		if (!$usession || !isset($usession['sup_user_id'])) {
			return []; // Return empty array if session data is missing
		}

		// Initialize database connection
		$db = \Config\Database::connect();
		$UsersModel = new \App\Models\UsersModel();
		$RolesModel = new \App\Models\RolesModel();

		// Fetch user info
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if (!$user_info || !isset($user_info['user_role_id'])) {
			return []; // Return empty array if user not found or role ID missing
		}

		// Fetch role info
		$role_user = $RolesModel->where('role_id', $user_info['user_role_id'])->first();

		if (!$role_user || !isset($role_user['role_resources'])) {
			return []; // Return empty array if role not found or resources missing
		}

		// Convert role resources to array
		$role_resources_ids = explode(',', $role_user['role_resources']);

		return $role_resources_ids;
	}
}
// get selected module
if (!function_exists('select_module_class')) {
	function select_module_class($mClass, $mMethod)
	{
		$arr = array();
		// dashboard
		if ($mClass == '\App\Controllers\Erp\Dashboard') {
			$arr['desk_active'] = 'active';
			$arr['super_open'] = '';
			return $arr;
		} else if ($mMethod == 'constants' || $mMethod == 'email_templates' || $mClass == '\App\Controllers\Erp\Languages') {
			$arr['constants_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Companies' && $mMethod == 'company_details') {
			$arr['companies_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Membership' && $mMethod == 'membership_details') {
			$arr['membership_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Users' && $mMethod == 'user_details') {
			$arr['users_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Membershipinvoices' && $mMethod == 'billing_details') {
			$arr['billing_details_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Projects' && $mMethod == 'client_project_details') {
			$arr['client_project_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Tasks' && $mMethod == 'client_task_details') {
			$arr['client_task_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Invoices' && $mMethod == 'invoice_details') {
			$arr['invoice_details_active'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Department' && $mMethod == 'index') {
			$arr['department_active'] = 'active';
			$arr['core_style_ul'] = 'style="display: block;"';
			$arr['corehr_open'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Designation' && $mMethod == 'index') {
			$arr['designation_active'] = 'active';
			$arr['core_style_ul'] = 'style="display: block;"';
			$arr['corehr_open'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Announcements' && $mMethod == 'index') {
			$arr['announcements_active'] = 'active';
			$arr['core_style_ul'] = 'style="display: block;"';
			$arr['corehr_open'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Policies' && $mMethod == 'index') {
			$arr['policies_active'] = 'active';
			$arr['core_style_ul'] = 'style="display: block;"';
			$arr['corehr_open'] = 'active';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Timesheet' && $mMethod == 'attendance') {
			$arr['attnd_active'] = 'active';
			$arr['attendance_open'] = 'active';
			$arr['attendance_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Timesheet' && $mMethod == 'update_attendance') {
			$arr['upd_attnd_active'] = 'active';
			$arr['attendance_open'] = 'active';
			$arr['attendance_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Timesheet' && $mMethod == 'monthly_timesheet') {
			$arr['timesheet_active'] = 'active';
			$arr['attendance_open'] = 'active';
			$arr['attendance_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Timesheet' && $mMethod == 'overtime_request') {
			$arr['overtime_request_act'] = 'active';
			$arr['attendance_open'] = 'active';
			$arr['attendance_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Talent' && $mMethod == 'performance_indicator') {
			$arr['indicator_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Talent' && $mMethod == 'performance_appraisal') {
			$arr['appraisal_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Trackgoals' && $mMethod == 'index') {
			$arr['goal_track_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Trackgoals' && $mMethod == 'goals_calendar') {
			$arr['goals_calendar_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Types' && $mMethod == 'competencies') {
			$arr['competencies_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		} else if ($mClass == '\App\Controllers\Erp\Types' && $mMethod == 'goal_type') {
			$arr['tracking_type_active'] = 'active';
			$arr['talent_open'] = 'active';
			$arr['talent_style_ul'] = 'style="display: block;"';
			return $arr;
		}
	}
}
// get timezone
if (!function_exists('all_timezones')) {
	function all_timezones()
	{
		$timezones = array(
			'Pacific/Midway'       => "(GMT-11:00) Midway Island",
			'US/Samoa'             => "(GMT-11:00) Samoa",
			'US/Hawaii'            => "(GMT-10:00) Hawaii",
			'US/Alaska'            => "(GMT-09:00) Alaska",
			'US/Pacific'           => "(GMT-08:00) Pacific Time (US &amp; Canada)",
			'America/Tijuana'      => "(GMT-08:00) Tijuana",
			'US/Arizona'           => "(GMT-07:00) Arizona",
			'US/Mountain'          => "(GMT-07:00) Mountain Time (US &amp; Canada)",
			'America/Chihuahua'    => "(GMT-07:00) Chihuahua",
			'America/Mazatlan'     => "(GMT-07:00) Mazatlan",
			'America/Mexico_City'  => "(GMT-06:00) Mexico City",
			'America/Monterrey'    => "(GMT-06:00) Monterrey",
			'Canada/Saskatchewan'  => "(GMT-06:00) Saskatchewan",
			'US/Central'           => "(GMT-06:00) Central Time (US &amp; Canada)",
			'US/Eastern'           => "(GMT-05:00) Eastern Time (US &amp; Canada)",
			'US/East-Indiana'      => "(GMT-05:00) Indiana (East)",
			'America/Bogota'       => "(GMT-05:00) Bogota",
			'America/Lima'         => "(GMT-05:00) Lima",
			'America/Caracas'      => "(GMT-04:30) Caracas",
			'Canada/Atlantic'      => "(GMT-04:00) Atlantic Time (Canada)",
			'America/La_Paz'       => "(GMT-04:00) La Paz",
			'America/Santiago'     => "(GMT-04:00) Santiago",
			'Canada/Newfoundland'  => "(GMT-03:30) Newfoundland",
			'America/Buenos_Aires' => "(GMT-03:00) Buenos Aires",
			'Greenland'            => "(GMT-03:00) Greenland",
			'Atlantic/Stanley'     => "(GMT-02:00) Stanley",
			'Atlantic/Azores'      => "(GMT-01:00) Azores",
			'Atlantic/Cape_Verde'  => "(GMT-01:00) Cape Verde Is.",
			'Africa/Casablanca'    => "(GMT) Casablanca",
			'Europe/Dublin'        => "(GMT) Dublin",
			'Europe/Lisbon'        => "(GMT) Lisbon",
			'Europe/London'        => "(GMT) London",
			'Africa/Monrovia'      => "(GMT) Monrovia",
			'Europe/Amsterdam'     => "(GMT+01:00) Amsterdam",
			'Europe/Belgrade'      => "(GMT+01:00) Belgrade",
			'Europe/Berlin'        => "(GMT+01:00) Berlin",
			'Europe/Bratislava'    => "(GMT+01:00) Bratislava",
			'Europe/Brussels'      => "(GMT+01:00) Brussels",
			'Europe/Budapest'      => "(GMT+01:00) Budapest",
			'Europe/Copenhagen'    => "(GMT+01:00) Copenhagen",
			'Europe/Ljubljana'     => "(GMT+01:00) Ljubljana",
			'Europe/Madrid'        => "(GMT+01:00) Madrid",
			'Europe/Paris'         => "(GMT+01:00) Paris",
			'Europe/Prague'        => "(GMT+01:00) Prague",
			'Europe/Rome'          => "(GMT+01:00) Rome",
			'Europe/Sarajevo'      => "(GMT+01:00) Sarajevo",
			'Europe/Skopje'        => "(GMT+01:00) Skopje",
			'Europe/Stockholm'     => "(GMT+01:00) Stockholm",
			'Europe/Vienna'        => "(GMT+01:00) Vienna",
			'Europe/Warsaw'        => "(GMT+01:00) Warsaw",
			'Europe/Zagreb'        => "(GMT+01:00) Zagreb",
			'Europe/Athens'        => "(GMT+02:00) Athens",
			'Europe/Bucharest'     => "(GMT+02:00) Bucharest",
			'Africa/Cairo'         => "(GMT+02:00) Cairo",
			'Africa/Harare'        => "(GMT+02:00) Harare",
			'Europe/Helsinki'      => "(GMT+02:00) Helsinki",
			'Europe/Istanbul'      => "(GMT+02:00) Istanbul",
			'Asia/Jerusalem'       => "(GMT+02:00) Jerusalem",
			'Europe/Kiev'          => "(GMT+02:00) Kyiv",
			'Europe/Minsk'         => "(GMT+02:00) Minsk",
			'Europe/Riga'          => "(GMT+02:00) Riga",
			'Europe/Sofia'         => "(GMT+02:00) Sofia",
			'Europe/Tallinn'       => "(GMT+02:00) Tallinn",
			'Europe/Vilnius'       => "(GMT+02:00) Vilnius",
			'Asia/Baghdad'         => "(GMT+03:00) Baghdad",
			'Asia/Kuwait'          => "(GMT+03:00) Kuwait",
			'Africa/Nairobi'       => "(GMT+03:00) Nairobi",
			'Asia/Riyadh'          => "(GMT+03:00) Riyadh",
			'Europe/Moscow'        => "(GMT+03:00) Moscow",
			'Asia/Tehran'          => "(GMT+03:30) Tehran",
			'Asia/Baku'            => "(GMT+04:00) Baku",
			'Europe/Volgograd'     => "(GMT+04:00) Volgograd",
			'Asia/Muscat'          => "(GMT+04:00) Muscat",
			'Asia/Tbilisi'         => "(GMT+04:00) Tbilisi",
			'Asia/Yerevan'         => "(GMT+04:00) Yerevan",
			'Asia/Kabul'           => "(GMT+04:30) Kabul",
			'Asia/Karachi'         => "(GMT+05:00) Karachi",
			'Asia/Tashkent'        => "(GMT+05:00) Tashkent",
			'Asia/Kolkata'         => "(GMT+05:30) Kolkata",
			'Asia/Kathmandu'       => "(GMT+05:45) Kathmandu",
			'Asia/Yekaterinburg'   => "(GMT+06:00) Ekaterinburg",
			'Asia/Almaty'          => "(GMT+06:00) Almaty",
			'Asia/Dhaka'           => "(GMT+06:00) Dhaka",
			'Asia/Novosibirsk'     => "(GMT+07:00) Novosibirsk",
			'Asia/Bangkok'         => "(GMT+07:00) Bangkok",
			'Asia/Jakarta'         => "(GMT+07:00) Jakarta",
			'Asia/Krasnoyarsk'     => "(GMT+08:00) Krasnoyarsk",
			'Asia/Chongqing'       => "(GMT+08:00) Chongqing",
			'Asia/Hong_Kong'       => "(GMT+08:00) Hong Kong",
			'Asia/Kuala_Lumpur'    => "(GMT+08:00) Kuala Lumpur",
			'Australia/Perth'      => "(GMT+08:00) Perth",
			'Asia/Singapore'       => "(GMT+08:00) Singapore",
			'Asia/Taipei'          => "(GMT+08:00) Taipei",
			'Asia/Ulaanbaatar'     => "(GMT+08:00) Ulaan Bataar",
			'Asia/Urumqi'          => "(GMT+08:00) Urumqi",
			'Asia/Irkutsk'         => "(GMT+09:00) Irkutsk",
			'Asia/Seoul'           => "(GMT+09:00) Seoul",
			'Asia/Tokyo'           => "(GMT+09:00) Tokyo",
			'Australia/Adelaide'   => "(GMT+09:30) Adelaide",
			'Australia/Darwin'     => "(GMT+09:30) Darwin",
			'Asia/Yakutsk'         => "(GMT+10:00) Yakutsk",
			'Australia/Brisbane'   => "(GMT+10:00) Brisbane",
			'Australia/Canberra'   => "(GMT+10:00) Canberra",
			'Pacific/Guam'         => "(GMT+10:00) Guam",
			'Australia/Hobart'     => "(GMT+10:00) Hobart",
			'Australia/Melbourne'  => "(GMT+10:00) Melbourne",
			'Pacific/Port_Moresby' => "(GMT+10:00) Port Moresby",
			'Australia/Sydney'     => "(GMT+10:00) Sydney",
			'Asia/Vladivostok'     => "(GMT+11:00) Vladivostok",
			'Asia/Magadan'         => "(GMT+12:00) Magadan",
			'Pacific/Auckland'     => "(GMT+12:00) Auckland",
			'Pacific/Fiji'         => "(GMT+12:00) Fiji",
		);
		return $timezones;
	}
	if (!function_exists('secret_key')) {
		function secret_key($string = '')
		{
			$data = 'J87JUHYTG5623GHrhej789kjhyrRe34k';
			$data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
			return $data;
		}
	}
	if (!function_exists('safe_b64encode')) {
		function safe_b64encode($string = '')
		{
			$data = base64_encode($string);
			$data = str_replace(['+', '/', '='], ['-', '_', ''], $data);
			return $data;
		}
	}
	if (!function_exists('safe_b64decode')) {
		function safe_b64decode($string = '')
		{
			$data = str_replace(['-', '_'], ['+', '/'], $string);
			$mod4 = strlen($data) % 4;
			if ($mod4) {
				$data .= substr('====', $mod4);
			}
			return base64_decode($data);
		}
	}
	if (!function_exists('uencode')) {
		function uencode($value = false)
		{
			if (!$value) return false;
			$iv_size = openssl_cipher_iv_length('aes-256-cbc');
			$iv = openssl_random_pseudo_bytes($iv_size);
			$crypttext = openssl_encrypt($value, 'aes-256-cbc', secret_key(), OPENSSL_RAW_DATA, $iv);
			return safe_b64encode($iv . $crypttext);
		}
	}
	if (!function_exists('udecode')) {
		function udecode($value = false)
		{
			if (!$value) return false;
			$crypttext = safe_b64decode($value);
			$iv_size = openssl_cipher_iv_length('aes-256-cbc');
			$iv = substr($crypttext, 0, $iv_size);
			$crypttext = substr($crypttext, $iv_size);
			if (!$crypttext) return false;
			$decrypttext = openssl_decrypt($crypttext, 'aes-256-cbc', secret_key(), OPENSSL_RAW_DATA, $iv);
			return rtrim($decrypttext);
		}
	}

	//// file helper
	if (!function_exists('filesrc')) {
		function filesrc($fileName, $type = 'full')
		{

			$path = './public/uploads/users/';
			if ($type != 'full')
				$path .= $type . '/';
			return $path . $fileName;
		}
	}
	if (!function_exists('filecsrc')) {
		function filecsrc($fileName, $type = 'full')
		{

			$path = './public/uploads/clients/';
			if ($type != 'full')
				$path .= $type . '/';
			return $path . $fileName;
		}
	}
	if (!function_exists('langfilesrc')) {
		function langfilesrc($fileName, $type = 'full')
		{

			$path = './public/uploads/languages_flag/temp/';
			if ($type != 'full')
				$path .= $type . '/';
			return $path . $fileName;
		}
	}
	if (!function_exists('convertNumberToWord')) {
		function convertNumberToWord($num = false)
		{
			$num = str_replace(array(',', ' '), '', trim($num));
			if (! $num) {
				return false;
			}
			$num = (int) $num;
			$words = array();
			$list1 = array(
				'',
				'one',
				'two',
				'three',
				'four',
				'five',
				'six',
				'seven',
				'eight',
				'nine',
				'ten',
				'eleven',
				'twelve',
				'thirteen',
				'fourteen',
				'fifteen',
				'sixteen',
				'seventeen',
				'eighteen',
				'nineteen'
			);
			$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
			$list3 = array(
				'',
				'thousand',
				'million',
				'billion',
				'trillion',
				'quadrillion',
				'quintillion',
				'sextillion',
				'septillion',
				'octillion',
				'nonillion',
				'decillion',
				'undecillion',
				'duodecillion',
				'tredecillion',
				'quattuordecillion',
				'quindecillion',
				'sexdecillion',
				'septendecillion',
				'octodecillion',
				'novemdecillion',
				'vigintillion'
			);
			$num_length = strlen($num);
			$levels = (int) (($num_length + 2) / 3);
			$max_length = $levels * 3;
			$num = substr('00' . $num, -$max_length);
			$num_levels = str_split($num, 3);
			for ($i = 0; $i < count($num_levels); $i++) {
				$levels--;
				$hundreds = (int) ($num_levels[$i] / 100);
				$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
				$tens = (int) ($num_levels[$i] % 100);
				$singles = '';
				if ($tens < 20) {
					$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
				} else {
					$tens = (int)($tens / 10);
					$tens = ' ' . $list2[$tens] . ' ';
					$singles = (int) ($num_levels[$i] % 10);
					$singles = ' ' . $list1[$singles] . ' ';
				}
				$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
			} //end for loop
			$commas = count($words);
			if ($commas > 1) {
				$commas = $commas - 1;
			}
			return implode(' ', $words);
		}
	}
	if (!function_exists('erp_paid_invoices')) {
		function erp_paid_invoices(string $invoice_month): float
		{
			// Get services
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session
			if (!isset($usession['sup_user_id'])) {
				return 0.0;
			}

			// Initialize models
			$usersModel = new \App\Models\UsersModel();
			$invoicesModel = new \App\Models\InvoicesModel();

			// Get user info
			$user = $usersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$companyId = ($user['user_type'] === 'staff')
				? $user['company_id']
				: $usession['sup_user_id'];

			// Get paid invoices (status = 1)
			$paidInvoices = $invoicesModel
				->where('company_id', $companyId)
				->where('invoice_month', $invoice_month)
				->where('status', 1)
				->findAll();

			// Calculate total
			$totalPaid = array_reduce($paidInvoices, function ($carry, $invoice) {
				return $carry + (float)($invoice['grand_total'] ?? 0);
			}, 0.0);

			return $totalPaid;
		}
	}
	if (!function_exists('company_paid_invoices')) {
		function company_paid_invoices(string $invoice_month): float
		{
			// Get session service
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session and required data
			if (!isset($usession['sup_user_id'])) {
				log_message('warning', 'Unauthorized access attempt to company_paid_invoices');
				return 0.0;
			}

			// Initialize models with dependency injection
			$usersModel = new \App\Models\UsersModel();
			$invoicePaymentsModel = new \App\Models\InvoicepaymentsModel();

			// Get user info (simplified query)
			$user = $usersModel->select('user_type, company_id')
				->where('user_id', $usession['sup_user_id'])
				->first();

			// Get paid invoices for the month
			$paidInvoices = $invoicePaymentsModel
				->select('membership_price')
				->where('invoice_month', $invoice_month)
				->findAll();

			// Calculate total using array_reduce
			$totalAmount = array_reduce($paidInvoices, function ($total, $invoice) {
				return $total + (float)($invoice['membership_price'] ?? 0);
			}, 0.0);

			return round($totalAmount, 2); // Round to 2 decimal places for currency
		}
	}
	if (!function_exists('client_paid_invoices')) {
		function client_paid_invoices(string $invoice_month): float
		{
			// Initialize total amount
			$totalAmount = 0.0;

			// Get session service
			$session = \Config\Services::session();

			// Validate session
			if (!$session->has('sup_username')) {
				log_message('error', 'Session not found in client_paid_invoices');
				return $totalAmount;
			}

			$usession = $session->get('sup_username');

			// Validate user session data
			if (!isset($usession['sup_user_id'])) {
				log_message('error', 'User ID not found in session');
				return $totalAmount;
			}

			// Initialize models with proper dependency injection
			$invoicesModel = new \App\Models\InvoicesModel();

			try {
				// Get paid invoices with single optimized query
				$paidInvoices = $invoicesModel
					->select('grand_total')
					->where('client_id', $usession['sup_user_id'])
					->where('invoice_month', $invoice_month)
					->where('status', 1) // Assuming 1 means 'paid'
					->findAll();

				// Calculate total with type safety
				foreach ($paidInvoices as $invoice) {
					$totalAmount += (float)($invoice['grand_total'] ?? 0);
				}
			} catch (\Exception $e) {
				log_message('error', 'Error in client_paid_invoices: ' . $e->getMessage());
			}

			return round($totalAmount, 2); // Proper rounding for currency
		}
	}

	if (!function_exists('erp_unpaid_invoices')) {
		function erp_unpaid_invoices(string $invoice_month): float
		{
			// Initialize total amount
			$totalUnpaid = 0.0;

			// Get session service
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session and user data
			if (!isset($usession['sup_user_id'])) {
				log_message('error', 'Invalid session in erp_unpaid_invoices');
				return $totalUnpaid;
			}

			// Initialize models
			$usersModel = new \App\Models\UsersModel();
			$invoicesModel = new \App\Models\InvoicesModel();

			try {
				// Get user info (only needed columns)
				$user = $usersModel->select('user_type, company_id')
					->where('user_id', $usession['sup_user_id'])
					->first();

				if (!$user) {
					log_message('error', 'User not found');
					return $totalUnpaid;
				}

				// Determine company ID
				$companyId = ($user['user_type'] === 'staff')
					? $user['company_id']
					: $usession['sup_user_id'];

				// Get unpaid invoices (status = 0)
				$unpaidInvoices = $invoicesModel
					->select('grand_total')
					->where('company_id', $companyId)
					->where('invoice_month', $invoice_month)
					->where('status', 0) // 0 means unpaid
					->findAll();

				// Calculate total with type safety
				foreach ($unpaidInvoices as $invoice) {
					$totalUnpaid += (float)($invoice['grand_total'] ?? 0);
				}
			} catch (\Exception $e) {
				log_message('error', 'Error in erp_unpaid_invoices: ' . $e->getMessage());
			}

			return round($totalUnpaid, 2); // Proper rounding for currency
		}
	}
	if (!function_exists('client_unpaid_invoices')) {
		function client_unpaid_invoices(string $invoice_month): float
		{
			// Initialize total amount
			$totalUnpaid = 0.0;

			// Get session service
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session data
			if (!isset($usession['sup_user_id'])) {
				log_message('error', 'Invalid session in client_unpaid_invoices');
				return $totalUnpaid;
			}

			// Initialize models with dependency injection
			$invoicesModel = new \App\Models\InvoicesModel();

			try {
				// Get unpaid invoices with optimized query
				$unpaidInvoices = $invoicesModel
					->select('grand_total')
					->where('client_id', $usession['sup_user_id'])
					->where('invoice_month', $invoice_month)
					->where('status', 0) // 0 means unpaid
					->findAll();

				// Calculate total with type safety
				foreach ($unpaidInvoices as $invoice) {
					$totalUnpaid += (float)($invoice['grand_total'] ?? 0);
				}
			} catch (\Exception $e) {
				log_message('error', 'Error in client_unpaid_invoices: ' . $e->getMessage());
			}

			return round($totalUnpaid, 2); // Proper rounding for currency
		}
	}

	if (!function_exists('erp_payroll')) {
		function erp_payroll(string $salary_month): float
		{
			// Initialize total amount
			$totalSalary = 0.0;

			// Get session service
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session data
			if (!isset($usession['sup_user_id'])) {
				log_message('error', 'Invalid session in erp_payroll');
				return $totalSalary;
			}

			// Initialize models
			$usersModel = new \App\Models\UsersModel();
			$payrollModel = new \App\Models\PayrollModel();

			try {
				// Get user info (only needed columns)
				$user = $usersModel->select('user_type, company_id')
					->where('user_id', $usession['sup_user_id'])
					->first();

				if (!$user) {
					log_message('error', 'User not found');
					return $totalSalary;
				}

				// Determine company ID
				$companyId = ($user['user_type'] === 'staff')
					? $user['company_id']
					: $usession['sup_user_id'];

				// Get payroll data with optimized query
				$payrollData = $payrollModel
					->select('net_salary')
					->where('company_id', $companyId)
					->where('salary_month', $salary_month)
					->findAll();

				// Calculate total with type safety
				foreach ($payrollData as $payroll) {
					$totalSalary += (float)($payroll['net_salary'] ?? 0);
				}
			} catch (\Exception $e) {
				log_message('error', 'Error in erp_payroll: ' . $e->getMessage());
			}

			return round($totalSalary, 2); // Proper rounding for currency
		}
	}

	if (!function_exists('staff_payroll')) {
		function staff_payroll($salary_month, $staff_id)
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$PayrollModel = new \App\Models\PayrollModel();

			// Default company_id
			$company_id = null;

			// Get user info
			if (isset($usession['sup_user_id'])) {
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if ($user_info && $user_info['user_type'] === 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
			}

			// Initialize amount
			$total_salary = 0;

			if ($company_id !== null) {
				$paid_records = $PayrollModel
					->where('company_id', $company_id)
					->where('salary_month', $salary_month)
					->where('staff_id', $staff_id)
					->findAll();

				foreach ($paid_records as $record) {
					$total_salary += floatval($record['net_salary']);
				}
			}

			return $total_salary;
		}
	}

	if (!function_exists('total_expense')) {

		if (!function_exists('total_expense')) {
			function total_expense()
			{
				$session = \Config\Services::session();
				$usession = $session->get('sup_username');

				if (!$usession || !isset($usession['sup_user_id'])) {
					return 0; // Default value if session data is missing
				}

				// Initialize database connection
				$db = \Config\Database::connect();
				$UsersModel = new \App\Models\UsersModel();
				$TransactionsModel = new \App\Models\TransactionsModel();

				// Fetch user info
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if (!$user_info) {
					return 0; // Return default value if user not found
				}

				// Determine company ID
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				// Fetch expenses
				$expense = $TransactionsModel->where('company_id', $company_id)->where('transaction_type', 'expense')->findAll();

				// Calculate total expense
				$total_expense = array_sum(array_column($expense, 'amount'));

				return $total_expense;
			}
		}
	}
	if (!function_exists('total_deposit')) {
		function total_deposit()
		{
			// Initialize session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session data
			if (!$usession || !isset($usession['sup_user_id'])) {
				return 0; // Default value if session data is missing
			}

			// Initialize database connection
			$db = \Config\Database::connect();
			$UsersModel = new \App\Models\UsersModel();
			$TransactionsModel = new \App\Models\TransactionsModel();

			// Fetch user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if (!$user_info) {
				return 0; // Return default value if user not found
			}

			// Determine company ID
			$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

			// Fetch transactions
			$expense = $TransactionsModel->where('company_id', $company_id)->where('transaction_type', 'income')->findAll();

			// Calculate total deposit
			$exp_amn = array_sum(array_column($expense, 'amount'));

			return $exp_amn;
		}
	}
	if (!function_exists('total_payroll')) {

		if (!function_exists('total_payroll')) {
			function total_payroll()
			{
				// Initialize session
				$session = \Config\Services::session();
				$usession = $session->get('sup_username');

				// Validate session data
				if (!$usession || !isset($usession['sup_user_id'])) {
					return 0; // Default value if session data is missing
				}

				// Initialize database connection
				$db = \Config\Database::connect();
				$UsersModel = new \App\Models\UsersModel();
				$PayrollModel = new \App\Models\PayrollModel();

				// Fetch user info
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if (!$user_info) {
					return 0; // Return default value if user not found
				}

				// Determine company ID
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				// Fetch payroll data
				$paid_amount = $PayrollModel->where('company_id', $company_id)->findAll();

				// Calculate total payroll
				$total_payroll = array_sum(array_column($paid_amount, 'net_salary'));

				return $total_payroll;
			}
		}
	}
	if (!function_exists('payroll_this_month')) {
		if (!function_exists('payroll_this_month')) {
			function payroll_this_month()
			{
				// Initialize session
				$session = \Config\Services::session();
				$usession = $session->get('sup_username');

				// Validate session data
				if (!$usession || !isset($usession['sup_user_id'])) {
					return 0; // Default value if session data is missing
				}

				// Initialize database connection
				$db = \Config\Database::connect();
				$UsersModel = new \App\Models\UsersModel();
				$PayrollModel = new \App\Models\PayrollModel();

				// Fetch user info
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if (!$user_info) {
					return 0; // Return default value if user not found
				}

				// Determine company ID
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				// Fetch payroll data for the current month
				$paid_amount = $PayrollModel->where('company_id', $company_id)->where('salary_month', date('Y-m'))->findAll();

				// Calculate total payroll for the month
				$total_payroll = array_sum(array_column($paid_amount, 'net_salary'));

				return $total_payroll;
			}
		}
	}
	if (!function_exists('staff_total_payroll')) {
		function staff_total_payroll()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check if session is valid
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$PayrollModel = new \App\Models\PayrollModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if (!$user_info) {
				return 0;
			}

			// Determine company_id
			if ($user_info['user_type'] === 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}

			// Get payroll records
			$payrolls = $PayrollModel
				->where('company_id', $company_id)
				->where('staff_id', $usession['sup_user_id'])
				->findAll();

			// Calculate total salary
			$total_salary = 0;
			foreach ($payrolls as $record) {
				$total_salary += floatval($record['net_salary']);
			}

			return $total_salary;
		}
	}

	if (!function_exists('staff_payroll_this_month')) {
		function staff_payroll_this_month()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check if session is valid
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$PayrollModel = new \App\Models\PayrollModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if (!$user_info) {
				return 0;
			}

			// Determine company_id
			$company_id = ($user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Get payroll records for current month
			$salary_month = date('Y-m');
			$payrolls = $PayrollModel
				->where('company_id', $company_id)
				->where('salary_month', $salary_month)
				->where('staff_id', $usession['sup_user_id'])
				->findAll();

			// Calculate total for current month
			$total_salary = 0;
			foreach ($payrolls as $record) {
				$total_salary += floatval($record['net_salary']);
			}

			return $total_salary;
		}
	}

	if (!function_exists('erp_total_paid_invoices')) {

		if (!function_exists('erp_total_paid_invoices')) {
			function erp_total_paid_invoices()
			{
				// Initialize session
				$session = \Config\Services::session();
				$usession = $session->get('sup_username');

				// Validate session data
				if (!$usession || !isset($usession['sup_user_id'])) {
					return 0; // Default value if session data is missing
				}

				// Initialize database connection
				$db = \Config\Database::connect();
				$UsersModel = new \App\Models\UsersModel();
				$InvoicesModel = new \App\Models\InvoicesModel();

				// Fetch user info
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if (!$user_info) {
					return 0; // Return default value if user not found
				}

				// Determine company ID
				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				// Fetch paid invoices
				$paid_invoice = $InvoicesModel->where('company_id', $company_id)->where('status', 1)->findAll();

				// Calculate total paid invoices
				$total_paid = array_sum(array_column($paid_invoice, 'grand_total'));

				return $total_paid;
			}
		}
	}
	if (!function_exists('erp_total_unpaid_invoices')) {

		if (!function_exists('erp_total_unpaid_invoices')) {
			function erp_total_unpaid_invoices()
			{
				$session = \Config\Services::session();
				$usession = $session->get('sup_username');

				if (!$usession || !isset($usession['sup_user_id'])) {
					return 0; // Default value if session data is missing
				}

				$db = \Config\Database::connect();
				$UsersModel = new \App\Models\UsersModel();
				$InvoicesModel = new \App\Models\InvoicesModel();

				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

				if (!$user_info) {
					return 0;
				}

				$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

				$unpaid_invoice = $InvoicesModel->where('company_id', $company_id)->where('status', 0)->findAll();

				$total_unpaid = array_sum(array_column($unpaid_invoice, 'grand_total'));

				return $total_unpaid;
			}
		}
	}
	if (!function_exists('client_total_unpaid_invoices')) {
		function client_total_unpaid_invoices()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check if session exists
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$InvoicesModel = new \App\Models\InvoicesModel();

			// Get unpaid invoices
			$unpaid_invoices = $InvoicesModel
				->where('client_id', $usession['sup_user_id'])
				->where('status', 0)
				->findAll();

			// Calculate total unpaid amount
			$total_unpaid = 0;
			foreach ($unpaid_invoices as $invoice) {
				$total_unpaid += floatval($invoice['grand_total']);
			}

			return $total_unpaid;
		}
	}

	if (!function_exists('client_total_paid_invoices')) {
		function client_total_paid_invoices()
		{
			// Load session service
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check if session is valid
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load model
			$InvoicesModel = new \App\Models\InvoicesModel();

			// Fetch paid invoices for this client
			$paid_invoices = $InvoicesModel
				->where('client_id', $usession['sup_user_id'])
				->where('status', 1)
				->findAll();

			// Calculate total paid amount
			$total_paid = 0;
			foreach ($paid_invoices as $invoice) {
				$total_paid += floatval($invoice['grand_total']);
			}

			return $total_paid;
		}
	}

	if (!function_exists('total_membership_payments')) {
		function total_membership_payments()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Optional: Check if session is valid
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load model
			$InvoicepaymentsModel = new \App\Models\InvoicepaymentsModel();

			// Fetch all membership payments
			$membership_payments = $InvoicepaymentsModel
				->orderBy('membership_invoice_id', 'ASC')
				->findAll();

			// Calculate total
			$total = 0;
			foreach ($membership_payments as $payment) {
				$total += floatval($payment['membership_price']);
			}

			return $total;
		}
	}

	if (!function_exists('staff_total_expense')) {
		function staff_total_expense()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check for valid session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$TransactionsModel = new \App\Models\TransactionsModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Get staff expense transactions
			$expenses = $TransactionsModel
				->where('company_id', $company_id)
				->where('transaction_type', 'expense')
				->where('staff_id', $usession['sup_user_id'])
				->findAll();

			// Calculate total expense amount
			$total_expense = 0;
			foreach ($expenses as $expense) {
				$total_expense += floatval($expense['amount']);
			}

			return $total_expense;
		}
	}

	if (!function_exists('staff_leave')) {
		function staff_leave()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$LeaveModel = new \App\Models\LeaveModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Count leaves
			return $LeaveModel
				->where('company_id', $company_id)
				->where('employee_id', $usession['sup_user_id'])
				->countAllResults();
		}
	}

	if (!function_exists('staff_overtime_request')) {
		function staff_overtime_request()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$OvertimerequestModel = new \App\Models\OvertimerequestModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Count overtime requests
			return $OvertimerequestModel
				->where('company_id', $company_id)
				->where('staff_id', $usession['sup_user_id'])
				->countAllResults();
		}
	}

	if (!function_exists('staff_travel_request')) {
		function staff_travel_request()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$TravelModel = new \App\Models\TravelModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Count travel requests
			return $TravelModel
				->where('company_id', $company_id)
				->where('employee_id', $usession['sup_user_id'])
				->countAllResults();
		}
	}

	if (!function_exists('staff_awards')) {
		function staff_awards()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Validate session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$AwardsModel = new \App\Models\AwardsModel();

			// Get user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company ID
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Count awards for this staff
			return $AwardsModel
				->where('company_id', $company_id)
				->where('employee_id', $usession['sup_user_id'])
				->countAllResults();
		}
	}

	if (!function_exists('staff_assets')) {
		function staff_assets()
		{
			// Load session
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');

			// Check for valid session
			if (!isset($usession['sup_user_id'])) {
				return 0;
			}

			// Load models
			$UsersModel = new \App\Models\UsersModel();
			$AssetsModel = new \App\Models\AssetsModel();

			// Fetch user info
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			// Determine company_id
			$company_id = ($user_info && $user_info['user_type'] === 'staff')
				? $user_info['company_id']
				: $usession['sup_user_id'];

			// Count assigned assets
			return $AssetsModel
				->where('company_id', $company_id)
				->where('employee_id', $usession['sup_user_id'])
				->countAllResults();
		}
	}

	if (!function_exists('time_ago')) {
		function time_ago($time_ago)
		{
			$time_ago = strtotime($time_ago);
			$cur_time   = time();
			$time_elapsed   = $cur_time - $time_ago;
			$seconds    = $time_elapsed;
			$minutes    = round($time_elapsed / 60);
			$hours      = round($time_elapsed / 3600);
			$days       = round($time_elapsed / 86400);
			$weeks      = round($time_elapsed / 604800);
			$months     = round($time_elapsed / 2600640);
			$years      = round($time_elapsed / 31207680);
			// Seconds
			if ($seconds <= 60) {
				return lang('Main.xin_just_now');
			}
			//Minutes
			else if ($minutes <= 60) {
				if ($minutes == 1) {
					return lang('Main.xin_one_minute_ago');
				} else {
					return "$minutes " . lang('Main.xin_minutes_ago');
				}
			}
			//Hours
			else if ($hours <= 24) {
				if ($hours == 1) {
					return lang('Main.xin_an_hour_ago');
				} else {
					return "$hours " . lang('Main.xin_hours_ago');
				}
			}
			//Days
			else if ($days <= 7) {
				if ($days == 1) {
					return lang('Main.xin_yesterday');
				} else {
					return "$days " . lang('Main.xin_days_ago');
				}
			}
			//Weeks
			else if ($weeks <= 4.3) {
				if ($weeks == 1) {
					return lang('Main.xin_a_week_ago');
				} else {
					return "$weeks " . lang('Main.xin_weeks_ago');
				}
			}
			//Months
			else if ($months <= 12) {
				if ($months == 1) {
					return lang('Main.xin_a_month_ago');
				} else {
					return "$months " . lang('Main.xin_months_ago');
				}
			}
			//Years
			else {
				if ($years == 1) {
					return lang('Main.xin_one_year_ago');
				} else {
					return "$years " . lang('Main.xin_years_go');
				}
			}
		}
	}

	if (!function_exists('getOpportunityName')) {
		function getOpportunityName($opp_id)
		{
			$opportunityModel = new App\Models\OpportunityModel();
			$opportunity = $opportunityModel->where('id', $opp_id)->first();

			return $opportunity ? $opportunity['opportunity_name'] : 'Not Found';
		}
	}

	if (!function_exists('getClientname')) {
		function getClientname($client_id)
		{
			$UsersModel = new \App\Models\UsersModel();
			$client_name = $UsersModel->where('user_id', $client_id)->first();

			return $client_name ? $client_name['first_name'] . ' ' . $client_name['last_name'] : 'Not Found';
		}
	}

	if (!function_exists('getProjectName')) {
		function getProjectName($pro_id)
		{
			$projectModel = new \App\Models\ProjectsModel();
			$project_name = $projectModel->where('project_id', $pro_id)->first();

			return $project_name ? $project_name['title']  : 'Not Found';
		}
	}

	if (!function_exists('getTaskName')) {
		function getTaskName($task_id)
		{
			$model = new \App\Models\TasksModel();
			$task = $model->where('task_id', $task_id)->first();

			return $task ? $task['task_name'] : 'Not Found';
		}
	}
	if (!function_exists('formatTotalHours')) {
		function formatTotalHours($hours)
		{
			// Get the integer value for hours
			$h = floor($hours);

			// Get the decimal part, multiply by 60 to get minutes
			$m = round(($hours - $h) * 60);

			// Return formatted time as H:i
			return sprintf("%d:%02d", $h, $m);
		}
	}
	if (!function_exists('getpayamount')) {
		function getpayamount($pro_id)
		{
			$InvoicesModel = new App\Models\InvoicesModel();
			$tasks = $InvoicesModel->where('project_id', $pro_id)->findAll();

			if ($tasks) {
				$total = 0;
				foreach ($tasks as $task) {
					$total += (float) $task['grand_total'];
				}
				return $total;
			} else {
				return '0';
			}
		}
	}

	if (!function_exists('getEmployeeId')) {
		function getEmployeeId($user_id)
		{
			$staffDetailsModel = new \App\Models\StaffdetailsModel();
			$user_details = $staffDetailsModel->where('user_id', $user_id)->first();
			return $user_details ? $user_details['employee_id'] : 'NA0000';
		}
	}
	if (!function_exists('designation')) {
		function designation($user_id)
		{
			$staffDetailsModel = new \App\Models\StaffdetailsModel();
			$DesignationModel = new \App\Models\DesignationModel();

			$user_details = $staffDetailsModel->where('user_id', $user_id)->first();

			if (!$user_details || !isset($user_details['designation_id'])) {
				return 'Not Found';
			}

			$Designationdata = $DesignationModel->where('designation_id', $user_details['designation_id'])->first();

			return $Designationdata['designation_name'] ?? 'Not Found';
		}
	}

	if (!function_exists('getcategoryName')) {
		function getcategoryName($cat_id)
		{
			$model = new \App\Models\DocumentConfigModel();
			$task = $model->where('id', $cat_id)->first();

			return $task ? $task['category_name'] : 'Not Found';
		}
	}
	if (!function_exists('department')) {
		function department($user_id)
		{

			$staffDetailsModel = new \App\Models\StaffdetailsModel();
			$DepartmentModel = new \App\Models\DepartmentModel();
			$user_details = $staffDetailsModel->where('user_id', $user_id)->first();

			$DepartmentData = $DepartmentModel->where('department_id', $user_details['department_id'])->first();


			return $user_details ? $DepartmentData['department_name'] : 'Not Found';
		}
	}
	if (!function_exists('assestCategoryname')) {
		function assestCategoryname($cat_id)
		{
			$model = new \App\Models\ConstantsModel();
			$task = $model->where(['constants_id' => $cat_id, 'type' => 'assets_category'])->first();

			return $task ? $task['category_name'] : 'Not Found';
		}
	}
	if (!function_exists('getBrandname')) {
		function getBrandname($brand_id)
		{
			$model = new \App\Models\ConstantsModel();
			$task = $model->where(['constants_id' => $brand_id, 'type' => 'assets_brand'])->first();

			return $task ? $task['category_name'] : 'Not Found';
		}
	}

	if (!function_exists('getdeclarationAmount')) {
		function getdeclarationAmount($user_id)
		{
			$Taxdeclarationlist = new \App\Models\Tax_declarationModel();
			$result = $Taxdeclarationlist->where('employee_id', $user_id)->findAll();

			$totalDeclaredAmount = 0;
			foreach ($result as $item) {
				$totalDeclaredAmount += $item['declared_amount'];
			}

			return ['taxList' => $result, 'totalDeclaredAmount' => $totalDeclaredAmount];
		}
	}


	if (!function_exists('getfilescount')) {
		function getfilescount($decl_id)
		{
			$model = new \App\Models\TaxProofModel();
			$result = $model->where(['declaration_id' => $decl_id])->findAll();

			return count($result); // Return the count of rows
		}
	}
	if (!function_exists('getSalaryTax')) {
		function getSalaryTax($annualSalary, $deductions)
		{
			// Ensure valid numeric values for salary and deductions
			$annualSalary = is_numeric($annualSalary) ? $annualSalary : 0;
			$deductions = is_numeric($deductions) ? $deductions : 0;

			// Calculate the net taxable income
			$netTaxableIncome = $annualSalary - $deductions;

			// Ensure net taxable income is non-negative
			$netTaxableIncome = max($netTaxableIncome, 0);

			$tax = 0;

			// Old tax regime slabs
			if ($netTaxableIncome <= 250000) {
				$tax = 0; // No tax for income up to Rs. 2,50,000
			} elseif ($netTaxableIncome <= 500000) {
				$tax = ($netTaxableIncome - 250000) * 0.05; // 5% tax for income between Rs. 2,50,001 and Rs. 5,00,000
			} elseif ($netTaxableIncome <= 1000000) {
				$tax = (250000 * 0.05) + (($netTaxableIncome - 500000) * 0.20); // 20% tax for income between Rs. 5,00,001 and Rs. 10,00,000
			} else {
				$tax = (250000 * 0.05) + (500000 * 0.20) + (($netTaxableIncome - 1000000) * 0.30); // 30% tax for income above Rs. 10,00,000
			}

			// Add 4% cess to the tax
			$cess = $tax * 0.04;
			$totalTax = $tax + $cess;

			// Return the total tax formatted to 2 decimal places
			return number_format($totalTax, 2, '.', '');
		}
	}

	if (!function_exists('generateCertificateNumber')) {
		function generateCertificateNumber($length = 7)
		{
			$randomBytes = random_bytes(ceil($length / 2));
			$randomString = strtoupper(bin2hex($randomBytes));
			return substr($randomString, 0, $length);
		}
	}
}
