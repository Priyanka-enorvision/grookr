<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\ConstantsModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\InvoicesModel;
use App\Models\InvoiceitemsModel;
use App\Models\YearPlanningModel;
use App\Models\PlanningEntityModel;
use App\Models\MonthlyPlanningModel;
use App\Models\MonthlyAchivedModel;
use App\Models\AnnualPlanningModel;
use App\Models\MonthlyPlanModel;
use App\Models\PlanningConfigurationSettingModel;
use Config\Services;

use Exception;

class Invoices extends BaseController
{


	public function project_invoices()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
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
			if (!in_array('invoice2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Invoices.xin_billing_invoices') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices';
		$data['breadcrumbs'] = lang('Invoices.xin_billing_invoices');

		$data['subview'] = view('erp/invoices/invoice_project_list', $data);
		return view('erp/layout/layout_main', $data);
	}
	public function project_invoice_payment()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
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
			if (!in_array('invoice_payments', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_acc_invoice_payments') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoice_project_payments';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_invoice_payments');

		$data['subview'] = view('erp/invoices/project_invoice_payment_list', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function client_invoice_payment()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_invoice_payments') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoice_client_payments';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_invoice_payments');

		$data['subview'] = view('erp/invoices/client_invoice_payment_list', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function invoices_client()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Invoices.xin_billing_invoices') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices_client';
		$data['breadcrumbs'] = lang('Invoices.xin_billing_invoices');

		$data['subview'] = view('erp/invoices/client_invoice_project_list', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function invoice_dashboard()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Invoices.xin_billing_invoices') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices';
		$data['breadcrumbs'] = lang('Invoices.xin_billing_invoices');

		$data['subview'] = view('erp/invoices/invoice_dashboard', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function invoice_calendar()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
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
			if (!in_array('invoice_calendar', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Dashboard.xin_invoice_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices';
		$data['breadcrumbs'] = lang('Dashboard.xin_invoice_calendar');

		$data['subview'] = view('erp/invoices/calendar_invoices', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function client_invoice_calendar()
	{
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_invoice_calendar') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoices';
		$data['breadcrumbs'] = lang('Dashboard.xin_invoice_calendar');

		$data['subview'] = view('erp/invoices/calendar_client_invoices', $data);
		return view('erp/layout/layout_main', $data); //page load

	}
	public function create_invoice()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
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
			if (!in_array('invoice3', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Invoices.xin_create_new_invoices') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'create_invoice';
		$data['breadcrumbs'] = lang('Invoices.xin_create_new_invoices');

		$data['subview'] = view('erp/invoices/create_invoice', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function edit_invoice($ifield_id)
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$request = \Config\Services::request();
		// $ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $InvoicesModel->where('invoice_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
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
			if (!in_array('invoice4', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Invoices.xin_edit_invoice') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'create_invoice';
		$data['breadcrumbs'] = lang('Invoices.xin_edit_invoice');
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/invoices/edit_invoice', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function invoice_details($ifield_id)
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$request = \Config\Services::request();
		// $ifield_id = udecode($request->getUri()->getSegment(3));
		$isegment_val = $InvoicesModel->where('invoice_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if (!$session->has('sup_username')) {
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('/'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff' && $user_info['user_type'] != 'customer') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'customer') {
			if (!in_array('invoice2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$data['title'] = lang('Invoices.xin_view_invoice') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoice_details';
		$data['breadcrumbs'] = lang('Invoices.xin_view_invoice');
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/invoices/project_billing_details', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function view_project_invoice($ifield_id)
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		//$SuperroleModel = new SuperroleModel();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$request = \Config\Services::request();
		// $ifield_id = udecode($request->uri->getSegment(3));
		$isegment_val = $InvoicesModel->where('invoice_id', $ifield_id)->first();
		if (!$isegment_val) {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Invoices.xin_view_invoice') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'invoice_details';
		$data['breadcrumbs'] = lang('Invoices.xin_view_invoice');
		$data['ifield_id'] = $ifield_id;

		$data['subview'] = view('erp/invoices/view_project_invoice', $data);
		return view('erp/layout/pre_layout_main', $data); //page load
	}
	// list
	public function invoices_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $InvoicesModel->where('company_id', $user_info['company_id'])->orderBy('invoice_id', 'ASC')->findAll();
		} else {
			$get_data = $InvoicesModel->where('company_id', $usession['sup_user_id'])->orderBy('invoice_id', 'ASC')->findAll();
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data = array();

		foreach ($get_data as $r) {


			$project = $ProjectsModel->where('company_id', $r['company_id'])->where('project_id', $r['project_id'])->first();
			$invoice_total = number_to_currency($r['grand_total'], $xin_system['default_currency']);

			$invoice_date = set_date_format($r['invoice_date']);
			$invoice_due_date = set_date_format($r['invoice_due_date']);
			$invoice_id = '<a href="' . site_url('erp/invoice-detail') . '/' . uencode($r['invoice_id']) . '"><span>#' . $r['invoice_number'] . '</span></a>';
			if ($r['status'] == 0) {
				$status = '<span class="badge badge-light-danger">' . lang('Invoices.xin_unpaid') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Invoices.xin_paid') . '</span>';
			} else {
				$status = '<span class="badge badge-light-info">' . lang('Projects.xin_project_cancelled') . '</span>';
			}
			$links = '
				' . $invoice_id . '
				<div class="overlay-edit">
					<a href="' . site_url('erp/invoice-detail/') . uencode($r['invoice_id']) . '"><button type="button" class="btn btn-sm btn-icon btn-light-primary"><i class="feather icon-download"></i></button></a>
				</div>
			';
			$data[] = array(
				$links,
				$project['title'],
				$invoice_total,
				$invoice_date,
				$invoice_due_date,
				$status,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// list
	public function client_invoices_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $InvoicesModel->where('company_id', $user_info['company_id'])->where('client_id', $usession['sup_user_id'])->orderBy('invoice_id', 'ASC')->findAll();
		$xin_system = erp_company_settings();

		$data = array();

		foreach ($get_data as $r) {


			$project = $ProjectsModel->where('company_id', $r['company_id'])->where('project_id', $r['project_id'])->first();
			$invoice_total = number_to_currency($r['grand_total'], $xin_system['default_currency'], null, 2);

			$invoice_date = set_date_format($r['invoice_date']);
			$invoice_due_date = set_date_format($r['invoice_due_date']);
			$invoice_id = '<a href="' . site_url('erp/invoice-detail') . '/' . $r['invoice_id'] . '"><span>#' . $r['invoice_number'] . '</span></a>';
			if ($r['status'] == 0) {
				$status = '<span class="badge badge-light-danger">' . lang('Invoices.xin_unpaid') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Invoices.xin_paid') . '</span>';
			} else {
				$status = '<span class="badge badge-light-info">' . lang('Projects.xin_project_cancelled') . '</span>';
			}
			$view = '<a href="' . site_url('erp/invoice-detail/') . $r['invoice_id'] . '"><button type="button" class="btn btn-sm btn-icon btn-light-success"><i class="feather icon-eye"></i></button></a>';
			if ($r['status'] == 1) {
				$download = '<a href="' . site_url('erp/print-invoice/') . $r['invoice_id'] . '"><button type="button" class="btn btn-sm btn-icon btn-light-primary"><i class="feather icon-download"></i></button></a>';
			} else {
				$download = '';
			}
			$links = '
				' . $invoice_id . '
				<div class="overlay-edit">
					 ' . $view . $download . '
				</div>
			';
			$data[] = array(
				$links,
				$project['title'],
				$invoice_total,
				$invoice_date,
				$invoice_due_date,
				$status,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// list
	public function client_profile_invoices_list($client_id)
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$request = \Config\Services::request();
		$InvoicesModel = new InvoicesModel();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ProjectsModel = new ProjectsModel();
		// $client_id = udecode($this->request->getVar('client_id', FILTER_SANITIZE_STRING));
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		$get_data = $InvoicesModel->where('company_id', $company_id)->where('client_id', $client_id)->orderBy('invoice_id', 'ASC')->findAll();
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data = array();

		foreach ($get_data as $r) {


			$project = $ProjectsModel->where('company_id', $r['company_id'])->where('project_id', $r['project_id'])->first();
			$invoice_total = number_to_currency($r['grand_total'], $xin_system['default_currency'], null, 2);

			$invoice_date = set_date_format($r['invoice_date']);
			$invoice_due_date = set_date_format($r['invoice_due_date']);
			$invoice_id = '<a href="' . site_url('erp/invoice-detail') . '/' . uencode($r['invoice_id']) . '"><span>#' . $r['invoice_number'] . '</span></a>';
			if ($r['status'] == 0) {
				$status = '<span class="badge badge-light-danger">' . lang('Invoices.xin_unpaid') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Invoices.xin_paid') . '</span>';
			} else {
				$status = '<span class="badge badge-light-info">' . lang('Projects.xin_project_cancelled') . '</span>';
			}
			$links = '
				' . $invoice_id . '
				<div class="overlay-edit">
					<a href="' . site_url('erp/invoice-detail/') . uencode($r['invoice_id']) . '"><button type="button" class="btn btn-sm btn-icon btn-light-primary"><i class="feather icon-download"></i></button></a>
				</div>
			';
			$data[] = array(
				$links,
				isset($project['title'])?$project['title']:'---',
				$invoice_date,
				$invoice_total,
				$status,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// list
	public function project_billing_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ProjectsModel = new ProjectsModel();
		$ConstantsModel = new ConstantsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $InvoicesModel->where('company_id', $user_info['company_id'])->where('status', 1)->orderBy('invoice_id', 'ASC')->findAll();
		} else {
			$get_data = $InvoicesModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->orderBy('invoice_id', 'ASC')->findAll();
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data = array();

		foreach ($get_data as $r) {


			$project = $ProjectsModel->where('company_id', $r['company_id'])->where('project_id', $r['project_id'])->first();
			$invoice_total = number_to_currency($r['grand_total'], $xin_system['default_currency'], null, 2);

			$invoice_date = set_date_format($r['invoice_date']);
			$invoice_due_date = set_date_format($r['invoice_due_date']);
			$invoice_id = '<a href="' . site_url('erp/invoice-detail') . '/' . $r['invoice_id']. '"><span>' . $r['invoice_number'] . '</span></a>';
			if ($r['status'] == 0) {
				$status = '<span class="badge badge-light-danger">' . lang('Invoices.xin_unpaid') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Invoices.xin_paid') . '</span>';
			} else {
				$status = '<span class="badge badge-light-info">' . lang('Projects.xin_project_cancelled') . '</span>';
			}
			$_payment_method = $ConstantsModel->where('type', 'payment_method')->where('constants_id', $r['payment_method'])->first();
			$links = '
				' . $invoice_id . '
				<div class="overlay-edit">
					<a href="' . site_url('erp/invoice-detail/') . $r['invoice_id'] . '"><button type="button" class="btn btn-sm btn-icon btn-light-success"><i class="feather icon-eye"></i></button></a> <a href="' . site_url('erp/print-invoice/') . $r['invoice_id'] . '"><button type="button" class="btn btn-sm btn-icon btn-light-primary"><i class="feather icon-download"></i></button></a>
				</div>
			';
			$data[] = array(
				$links,
				isset($project['title'])?$project['title']:'',
				$invoice_date,
				$invoice_total,
				$_payment_method['category_name'],
				$status,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// list
	public function client_project_billing_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$InvoicesModel = new InvoicesModel();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();
		$ConstantsModel = new ConstantsModel();
		$ProjectsModel = new ProjectsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$get_data = $InvoicesModel->where('company_id', $user_info['company_id'])->where('client_id', $usession['sup_user_id'])->where('status', 1)->orderBy('invoice_id', 'ASC')->findAll();
		$xin_system = erp_company_settings();

		$data = array();

		foreach ($get_data as $r) {

			$project = $ProjectsModel->where('company_id', $r['company_id'])->where('project_id', $r['project_id'])->first();
			$invoice_total = number_to_currency($r['grand_total'], $xin_system['default_currency'], null, 2);

			$invoice_date = set_date_format($r['invoice_date']);
			$invoice_due_date = set_date_format($r['invoice_due_date']);
			$invoice_id = '<a href="' . site_url('erp/invoice-detail') . '/' . uencode($r['invoice_id']) . '"><span>#' . $r['invoice_number'] . '</span></a>';
			if ($r['status'] == 0) {
				$status = '<span class="badge badge-light-danger">' . lang('Invoices.xin_unpaid') . '</span>';
			} else if ($r['status'] == 1) {
				$status = '<span class="badge badge-light-success">' . lang('Invoices.xin_paid') . '</span>';
			} else {
				$status = '<span class="badge badge-light-info">' . lang('Projects.xin_project_cancelled') . '</span>';
			}
			$_payment_method = $ConstantsModel->where('type', 'payment_method')->where('constants_id', $r['payment_method'])->first();
			$links = '
				' . $invoice_id . '
				<div class="overlay-edit">
					<a href="' . site_url('erp/invoice-detail/') . uencode($r['invoice_id']) . '"><button type="button" class="btn btn-sm btn-icon btn-light-primary"><i class="feather icon-download"></i></button></a>
				</div>
			';
			$data[] = array(
				$links,
				$project['title'],
				$invoice_date,
				$invoice_total,
				$_payment_method['category_name'],
				$status,
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}



	public function getProjectsByClient()
	{

		$client_id = $this->request->getPost('client_id');

		if ($client_id) {
			$db = \Config\Database::connect();

			$builder = $db->table('ci_projects');
			$builder->select('project_id, title');
			$builder->where('client_id', $client_id);
			$query = $builder->get();
			$projects = $query->getResultArray();

			return $this->response->setJSON($projects);
		}

		return $this->response->setJSON([]);
	}
	public function getProjectsByExpert()
	{

		$expert_id = $this->request->getPost('expert_id');

		if ($expert_id) {
			$db = \Config\Database::connect();

			$builder = $db->table('ci_projects');
			$builder->select('project_id, title');
			$builder->where('expert_to', $expert_id);
			$query = $builder->get();
			$projects = $query->getResultArray();

			return $this->response->setJSON($projects);
		}

		return $this->response->setJSON([]);
	}

	public function getTasksByProject()
	{
		$project_id = $this->request->getPost('project_id');
		if ($project_id) {

			$db = \Config\Database::connect();

			$builder = $db->table('ci_tasks');
			$builder->select('task_id, task_name, task_status');
			$builder->where('project_id', $project_id);
			$query = $builder->get();
			$tasks = $query->getResultArray();
			return $this->response->setJSON($tasks);
		}
		return $this->response->setJSON([]);
	}

	public function create_new_invoice()
	{
		
		$session = Services::session();
		$request = Services::request();
		$usession = $session->get('sup_username');
		$db = \Config\Database::connect();

		log_message('info', 'Request Data: ' . print_r($request->getPost(), true));

		if ($request->getPost()) {

			$return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			$rules = [
				'invoice_number' => 'required|string',
				'invoice_date' => 'required|valid_date[Y-m-d]',
				'invoice_due_date' => 'required|valid_date[Y-m-d]',
				'item_name.*' => 'required|string|min_length[1]',
				'qty_hrs.*' => 'required|numeric|greater_than[0]',
				'unit_price.*' => 'required|numeric|greater_than[0]',
				'sub_total_item.*' => 'required|numeric|greater_than[0]',
				'items_sub_total' => 'required|numeric|greater_than[0]',
				'discount_type' => 'required|string',
				'discount_figure' => 'required|numeric',
				'tax_type' => 'required|string',
				'tax_rate' => 'required|numeric',
				'fgrand_total' => 'required|numeric|greater_than[0]'
			];

			if (!$this->validate($rules)) {
				$errors = $this->validator->getErrors();

				$itemErrors = array_filter($errors, function ($key) {
					return in_array($key, ['item_name', 'qty_hrs', 'unit_price', 'sub_total_item']);
				}, ARRAY_FILTER_USE_KEY);

				if (empty(array_filter($this->request->getPost('item_name')))) {
					$itemErrors['item_name'] = 'At least one valid item is required.';
				}

				foreach ($errors + $itemErrors as $error) {
					$session->setFlashdata('error', $error);
				}

				return redirect()->back()->withInput();
			}

			$invoice_number = filter_var($request->getPost('invoice_number'), FILTER_SANITIZE_STRING);
			$client_id = filter_var($request->getPost('client'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
			$project_id = filter_var($request->getPost('project'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
			$invoice_date = filter_var($request->getPost('invoice_date'), FILTER_SANITIZE_STRING);
			$invoice_due_date = filter_var($request->getPost('invoice_due_date'), FILTER_SANITIZE_STRING);
			$invoice_note = filter_var($request->getPost('invoice_note'), FILTER_SANITIZE_STRING);
			$expert_id = filter_var($request->getPost('expert_to'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

			$inv_mnth = date('Y-m', strtotime($invoice_date));

			$item_names = $request->getPost('item_name');
			$qtys = $request->getPost('qty_hrs');
			$unit_prices = $request->getPost('unit_price');
			$sub_total_items = $request->getPost('sub_total_item');

			if (empty($item_names) || count($item_names) !== count($qtys) || count($qtys) !== count($unit_prices) || count($unit_prices) !== count($sub_total_items)) {
				$return['error'] = 'Item details are incomplete or mismatched.';
				return $this->response->setJSON($return);
			}

			$items_sub_total = filter_var($request->getPost('items_sub_total'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$discount_type = filter_var($request->getPost('discount_type'), FILTER_SANITIZE_STRING);
			$discount_figure = filter_var($request->getPost('discount_figure'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$tax_type = filter_var($request->getPost('tax_type'), FILTER_SANITIZE_STRING);
			$tax_rate = filter_var($request->getPost('tax_rate'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fgrand_total = filter_var($request->getPost('fgrand_total'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

			$total_discount = ($discount_type === 'percentage') ? ($items_sub_total * $discount_figure / 100) : $discount_figure;
			$total_tax = ($tax_type === 'percentage') ? ($items_sub_total * $tax_rate / 100) : $tax_rate;

			$UsersModel = model('UsersModel');
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			$company_id = $user_info['company_id'];

			$InvoicesModel = model('InvoicesModel');
			$invoice_data = [
				'invoice_number' => $invoice_number,
				'company_id' => $company_id,
				'client_id' => $client_id,
				'project_id' => $project_id,
				'invoice_month' => $inv_mnth,
				'invoice_date' => $invoice_date,
				'invoice_due_date' => $invoice_due_date,
				'sub_total_amount' => $items_sub_total,
				'discount_type' => $discount_type,
				'discount_figure' => $discount_figure,
				'total_discount' => $total_discount,
				'tax_type' => $tax_type,
				'total_tax' => $total_tax,
				'grand_total' => $fgrand_total,
				'invoice_note' => $invoice_note,
				'expert_to' => $expert_id,
				'status' => 0,
				'payment_method' => 0,
				'created_at' => date('Y-m-d H:i:s')
			];

			log_message('info', 'Invoice data being inserted: ' . print_r($invoice_data, true));
			$db = \Config\Database::connect();
			$builder = $db->table('ci_invoices');

			$invoiceInsertResult = $builder->insert($invoice_data);

			// $invoiceInsertResult = $InvoicesModel->insert($invoice_data);

			if ($invoiceInsertResult === false) {
				$dbError = $db->error();
				log_message('error', 'Database error: ' . $dbError['message']);
				return $this->response->setJSON($return);
			}

			$invoice_id = $InvoicesModel->insertID();

			$InvoiceitemsModel = model('InvoiceitemsModel');
			$items_data = [];

			foreach ($item_names as $key => $item_name) {
				$items_data[] = [
					'invoice_id' => $invoice_id,
					'project_id' => $project_id,
					'item_name' => $item_name,
					'item_qty' => $qtys[$key],
					'item_unit_price' => $unit_prices[$key],
					'item_sub_total' => $sub_total_items[$key],
					'created_at' => date('Y-m-d H:i:s')
				];
			}


			if (!$InvoiceitemsModel->insertBatch($items_data)) {
				$return['error'] = 'Failed to add invoice items. Please try again later.';
				log_message('error', 'Failed to insert invoice items.');
				return $this->response->setJSON($return);
			}
			$current_url = $this->request->getServer('HTTP_REFERER') ?? base_url('erp/invoices-list');

			$session->setFlashdata('message', 'Invoice created successfully !! Invoice Number: ' . $invoice_number);
			$return['result'] = 'Invoice created successfully. Invoice Number: ' . $invoice_number;

			return redirect()->to($current_url);
		}
	}

	// |||update record|||

	public function update_invoice()
	{
		$validation = Services::validation();
		$session = Services::session();
		$request = Services::request();
		$usession = $session->get('sup_username');
		$db = \Config\Database::connect();

		log_message('info', 'Request Data: ' . print_r($request->getPost(), true));

		if ($request->getPost()) {
			$return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

			$rules = [
				// 'invoice_number' => 'required|string',
				'invoice_date' => 'required|valid_date[Y-m-d]',
				'invoice_due_date' => 'required|valid_date[Y-m-d]',
				'eitem_name.*' => 'required|string',
				'eqty_hrs.*' => 'required|numeric',
				'eunit_price.*' => 'required|numeric',
				'esub_total_item.*' => 'required|numeric',
				'items_sub_total' => 'required|numeric',
				'discount_type' => 'required|string',
				'discount_figure' => 'required|numeric',
				'tax_type' => 'required|string',
				'tax_rate' => 'required|numeric',
				'fgrand_total' => 'required|numeric'
			];


			if (!$this->validate($rules)) {
				$return['error'] = 'Validation failed: ' . implode(', ', $validation->getErrors());
				log_message('error', 'Validation errors: ' . $return['error']);
				return $this->output($return);
			}

			// Decode invoice ID
			$invoice_id = udecode($request->getPost('token'), FILTER_SANITIZE_STRING);
			if (!$invoice_id) {
				$return['error'] = 'Invalid invoice ID.';
				return $this->output($return);
			}

			// Sanitize the input
			$invoice_number = filter_var($request->getPost('invoice_number'), FILTER_SANITIZE_STRING);
			$client_id = filter_var($request->getPost('client'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
			$project_id = filter_var($request->getPost('project'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
			$invoice_date = filter_var($request->getPost('invoice_date'), FILTER_SANITIZE_STRING);
			$invoice_due_date = filter_var($request->getPost('invoice_due_date'), FILTER_SANITIZE_STRING);
			$invoice_note = filter_var($request->getPost('invoice_note'), FILTER_SANITIZE_STRING);
			$expert_id = filter_var($request->getPost('expert_to'), FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

			$inv_mnth = date('Y-m', strtotime($invoice_date));

			$item_names = $request->getPost('eitem_name');
			$qtys = $request->getPost('eqty_hrs');
			$unit_prices = $request->getPost('eunit_price');
			$sub_total_items = $request->getPost('esub_total_item');

			if (empty($item_names) || count($item_names) !== count($qtys) || count($qtys) !== count($unit_prices) || count($unit_prices) !== count($sub_total_items)) {
				$return['error'] = 'Item details are incomplete or mismatched.';
				return $this->output($return);
			}

			$items_sub_total = filter_var($request->getPost('items_sub_total'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$discount_type = filter_var($request->getPost('discount_type'), FILTER_SANITIZE_STRING);
			$discount_figure = filter_var($request->getPost('discount_figure'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$tax_type = filter_var($request->getPost('tax_type'), FILTER_SANITIZE_STRING);
			$tax_rate = filter_var($request->getPost('tax_rate'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$fgrand_total = filter_var($request->getPost('fgrand_total'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

			$total_discount = ($discount_type === 'percentage') ? ($items_sub_total * $discount_figure / 100) : $discount_figure;
			$total_tax = ($tax_type === 'percentage') ? ($items_sub_total * $tax_rate / 100) : $tax_rate;

			// Get company info from the user session
			$UsersModel = model('UsersModel');
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			$company_id = $user_info['company_id'];

			$InvoicesModel = model('InvoicesModel');
			$invoice_data = [
				'company_id' => $company_id,
				'client_id' => $client_id,
				'project_id' => $project_id,
				'invoice_month' => $inv_mnth,
				'invoice_date' => $invoice_date,
				'invoice_due_date' => $invoice_due_date,
				'sub_total_amount' => $items_sub_total,
				'discount_type' => $discount_type,
				'discount_figure' => $discount_figure,
				'total_discount' => $total_discount,
				'tax_type' => $tax_type,
				'total_tax' => $total_tax,
				'grand_total' => $fgrand_total,
				'invoice_note' => $invoice_note,
				'expert_to' => $expert_id,
				'status' => 0,
				'payment_method' => 0,
			];

			log_message('info', 'Invoice data being updated: ' . print_r($invoice_data, true));
			try {
				// $invoiceUpdateResult = $InvoicesModel->update($invoice_id, $invoice_data);
				$db = \Config\Database::connect();
				$builder = $db->table('ci_invoices');

				$builder->where('invoice_id', $invoice_id);
				$invoiceUpdateResult = $builder->update($invoice_data);

				if ($invoiceUpdateResult === false) {
					$dbError = $db->error();
					$return['error'] = 'Failed to update invoice: ' . $dbError['message'];
					log_message('error', 'Database error: ' . $dbError['message']);
					return $this->output($return);
				}

				$InvoiceitemsModel = model('InvoiceitemsModel');
				$InvoiceitemsModel->where('invoice_id', $invoice_id)->delete();

				$items_data = [];
				foreach ($item_names as $key => $item_name) {
					$items_data[] = [
						'invoice_id' => $invoice_id,
						'project_id' => $project_id,
						'item_name' => $item_name,
						'item_qty' => $qtys[$key],
						'item_unit_price' => $unit_prices[$key],
						'item_sub_total' => $sub_total_items[$key],
						'created_at' => date('Y-m-d H:i:s')
					];
				}

				if (!$InvoiceitemsModel->insertBatch($items_data)) {
					$return['error'] = 'Failed to update invoice items. Please try again later.';
					log_message('error', 'Failed to update invoice items.');
					return $this->output($return);
				}

				$session->setFlashdata('message', 'Invoice updated successfully !! Invoice Number: ' . $invoice_number);
				$return['result'] = 'Invoice updated successfully. Invoice Number: ' . $invoice_number;
			} catch (\Exception $e) {
				$session->setFlashdata('error', 'Failed to update invoice !!');
				$return['error'] = 'Failed to update invoice. Error: ' . $e->getMessage();
				log_message('error', 'Exception occurred: ' . $e->getMessage());
			}
			$current_url = $this->request->getServer('HTTP_REFERER') ?? base_url('erp/invoices-list');
			return redirect()->to($current_url);
		}
	}





	// delete record
	public function delete_invoice_items()
	{

		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		
		$record_id = $this->request->getVar('record_id');
		$record_id = udecode($record_id);
		
		if ($record_id) {

			$InvoiceitemsModel = new InvoiceitemsModel();
			$result = $InvoiceitemsModel->where('invoice_item_id', $record_id)->delete();
			
			if ($result) {
				$Return['result'] = lang('Success.ci_invoice_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
		} else {
			$Return['error'] = 'Invalid record ID';
		}
		
		return $this->response->setJSON($Return);
	}
	public function invoice_status_chart()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$InvoicesModel = new InvoicesModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$unpaid_count = $InvoicesModel->where('company_id', $user_info['company_id'])->where('status', 0)->countAllResults();
			$paid_count = $InvoicesModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
		} else {
			$unpaid_count = $InvoicesModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
			$paid_count = $InvoicesModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
		}
		/* Define return | here result is used to return user data and error for error message */
		$Return = array('paid' => '', 'paid_count' => '', 'unpaid' => '', 'unpaid_count' => '');

		// unpaid
		$Return['unpaid'] = lang('Invoices.xin_unpaid');
		$Return['unpaid_count'] = $unpaid_count;
		// paid
		$Return['paid'] = lang('Invoices.xin_paid');
		$Return['paid_count'] = $paid_count;
		return $this->response->setJSON($Return);
	}

	public function invoice_amount_chart()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		$Return = array('invoice_amount' => '', 'paid_invoice' => '', 'unpaid_invoice' => '', 'paid_inv_label' => '', 'unpaid_inv_label' => '');
		$invoice_month = array();
		$paid_invoice = array();
		$unpaid_invoice = array();

		for ($i = 0; $i <= 5; $i++) {
			$months = date("Y-m", strtotime(date('Y-m-01') . " -$i months"));
			$paid_amount = erp_paid_invoices($months);
			$paid_amount = number_format($paid_amount, 2, '.', '');
			$unpaid_amount = erp_unpaid_invoices($months);
			$unpaid_amount = number_format($unpaid_amount, 2, '.', '');
			$paid_invoice[] = $paid_amount;
			$unpaid_invoice[] = $unpaid_amount;
			$invoice_month[] = $months;
		}

		$Return['invoice_month'] = $invoice_month;
		$Return['paid_inv_label'] = lang('Invoices.xin_paid_invoices');
		$Return['unpaid_inv_label'] = lang('Invoices.xin_unpaid_invoices');
		$Return['paid_invoice'] = $paid_invoice;
		$Return['unpaid_invoice'] = $unpaid_invoice;
		return $this->response->setJSON($Return);
	}


	public function planing_monthly_chart()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$UsersModel = new UsersModel();

		$annualPlanModel = new AnnualPlanningModel();
		$monthlyPlanModel = new MonthlyPlanModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$annual_plan = $annualPlanModel->where('company_id', $user_info['company_id'])->first();
		$annual_plan_id = $annual_plan['id'];

		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}


		$total_revenue = $annual_plan['revenue'];

		$Return = array('paid_invoice' => '', 'unpaid_invoice' => '', 'paid_inv_label' => '', 'unpaid_inv_label' => '');
		$invoice_month = array();
		$paid_invoice = array();
		$someArray = array();
		$monthly_earning_percentage = array(4, 5, 6, 6, 7, 7, 10, 10, 10, 10, 12, 13);
		// $monthly_earning = array(380000,400000,540000,680000,0,0,0,0,0,0,0,0);
		// $month_list = array('2024-01-01','2024-02-01','2024-03-01','2024-04-01','2024-05-01','2024-06-01','2024-07-01','2024-08-01','2024-09-01','2024-10-01','2024-11-01','2024-12-01');

		$currentYear = date('Y');
		$month_list = array();
		for ($month = 1; $month <= 12; $month++) {
			$date = sprintf('%4d-%02d-01', $currentYear, $month);
			$month_list[] = $date;
		}

		if ($annual_plan_id != null) {
			// $annualRecord = $annualPlanModel->where('year', $currentYear)->first();
			$annualYear = $annual_plan['year'];
			if ($annualYear == $currentYear) {

				$existingRecords = $monthlyPlanModel->where('annual_id', $annual_plan_id)->findAll();
				// var_dump($existingRecords);
				// die;
				$monthly_earning = array();

				foreach ($existingRecords as $record) {
					$data = $record['revenue'];
					$monthly_earning[] = $data;
				}
			} else {
				return $this->response->setJSON(['message' => 'Annual plan not found for the company.']);
			}
		} else {
			$monthly_earning[] = 0;
		}


		$j = 0;
		for ($i = 0; $i < 12; $i++) {
			//$months = date("Y-m", strtotime( date( 'Y-m-01' )." +$i months"));
			$months = date("Y-m", strtotime(date($month_list[$i])));
			$paid_amount = ($total_revenue / 100) * $monthly_earning_percentage[$i];
			$paid_amount = number_format($paid_amount, 2, '.', '');
			$unpaid_amount = $monthly_earning[$i];
			$unpaid_amount = number_format($unpaid_amount, 2, '.', '');
			$paid_invoice[] = $paid_amount;
			$unpaid_invoice[] = $unpaid_amount;
			$invoice_month[] = $months;
		}

		$Return['invoice_month'] = $invoice_month;
		$Return['paid_inv_label'] = lang('Invoices.xin_planned_revenue');
		$Return['unpaid_inv_label'] = lang('Invoices.xin_achieved_revenue');
		$Return['paid_invoice'] = $paid_invoice;
		$Return['unpaid_invoice'] = $unpaid_invoice;
		return $this->response->setJSON($Return);
	}

	public function month_plan_chart()
	{

		$session = \Config\Services::session();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$YearPlanningModel = new YearPlanningModel();
		$MonthlyPlanningModel = new MonthlyPlanningModel();
		$MonthlyAchivedModel = new MonthlyAchivedModel();
		$PlanningEntityModel = new PlanningEntityModel();
		$PlanningConfigurationSettingModel = new PlanningConfigurationSettingModel();

		$selectedFinancialYear = $this->request->getVar('year') ?? date('Y') . '-' . substr(date('Y') + 1, 2);

		$startYear = (int)substr($selectedFinancialYear, 0, 4);
		$endYear = $startYear + 1;

		$user_info = $UsersModel->find($session->get('sup_username')['sup_user_id']);

		$company_id = ($user_info['user_type'] === 'staff') ? $user_info['company_id'] : $session->get('sup_username')['sup_user_id'];
		$user_type = $user_info['user_type'];

		$monthly_earning_percentage = array_fill(0, 12, 0);
		$achieved_revenue = array_fill_keys(array_map('strtolower', [
			'april',
			'may',
			'june',
			'july',
			'august',
			'september',
			'october',
			'november',
			'december',
			'january',
			'february',
			'march'
		]), 0);
		$planned_revenue = $achieved_revenue;

		try {
			$monthly_config = $PlanningConfigurationSettingModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($monthly_config as $config) {
				$month = strtolower(explode('-', $config['month'])[0]);
				$month_index = array_search($month, array_keys($achieved_revenue));
				if ($month_index !== false) {
					$monthly_earning_percentage[$month_index] = (float)$config['percentage'];
				}
			}

			// Get achieved revenue data (entity_id = 1 is assumed to be revenue)
			$monthly_achieved = $MonthlyAchivedModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear,
					'entities_id' => 1 // Assuming 1 is the revenue entity
				])
				->findAll();

			foreach ($monthly_achieved as $record) {
				$month = strtolower(explode('-', $record['month'])[0]);
				if (isset($achieved_revenue[$month])) {
					$achieved_revenue[$month] += (float)$record['entity_value'];
				}
			}

			// Get planned revenue data
			$monthly_planned = $MonthlyPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear,
					'entities_id' => 1 // Assuming 1 is the revenue entity
				])
				->findAll();

			foreach ($monthly_planned as $record) {
				$month = strtolower(explode('-', $record['month'])[0]);
				if (isset($planned_revenue[$month])) {
					$planned_revenue[$month] += (float)$record['entity_value'];
				}
			}

			// Prepare chart data in financial year order (April-March)
			$invoice_month = [];
			$paid_invoice = [];
			$unpaid_invoice = [];

			// Financial year months in order (April to March)
			$financialYearMonths = [
				['name' => 'April', 'year' => $startYear],
				['name' => 'May', 'year' => $startYear],
				['name' => 'June', 'year' => $startYear],
				['name' => 'July', 'year' => $startYear],
				['name' => 'August', 'year' => $startYear],
				['name' => 'September', 'year' => $startYear],
				['name' => 'October', 'year' => $startYear],
				['name' => 'November', 'year' => $startYear],
				['name' => 'December', 'year' => $startYear],
				['name' => 'January', 'year' => $endYear],
				['name' => 'February', 'year' => $endYear],
				['name' => 'March', 'year' => $endYear]
			];

			foreach ($financialYearMonths as $month) {
				$month_lower = strtolower($month['name']);
				$invoice_month[] = substr($month['name'], 0, 3) . ' ' . $month['year'];
				$paid_invoice[] = (float)($planned_revenue[$month_lower] ?? 0);
				$unpaid_invoice[] = (float)($achieved_revenue[$month_lower] ?? 0);
			}

			// Get year planning entities
			$year_planning_entities = [];
			$year_entities = $YearPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($year_entities as $entity) {
				$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
				if ($planning_entity) {
					$year_planning_entities[] = [
						'id' => $entity['entities_id'],
						'entity_name' => $planning_entity['entity'],
						'entity_value' => $entity['entity_value'],
					];
				}
			}

			// Get monthly planning entities
			$monthly_planning_entities = [];
			$monthly_entities = $MonthlyPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($monthly_entities as $entity) {
				$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
				if ($planning_entity) {
					$month = strtolower(explode('-', $entity['month'])[0]);
					if (!isset($monthly_planning_entities[$month])) {
						$monthly_planning_entities[$month] = [];
					}
					$monthly_planning_entities[$month][] = [
						'id' => $entity['entities_id'],
						'entity_name' => $planning_entity['entity'],
						'entity_value' => $entity['entity_value'],
						'month' => $month
					];
				}
			}

			// Prepare final response
			$response = [
				'status' => 'success',
				'invoice_month' => $invoice_month, // Format: ["Apr 2025", "May 2025", ..., "Mar 2026"]
				'paid_invoice' => $paid_invoice,   // Planned revenue data
				'unpaid_invoice' => $unpaid_invoice, // Achieved revenue data
				'paid_inv_label' => lang('Invoices.xin_planned_revenue'),
				'unpaid_inv_label' => lang('Invoices.xin_achieved_revenue'),
				'total_revenue' => array_sum($planned_revenue),
				'achieved' => array_sum($achieved_revenue),
				'year_planning_entities' => $year_planning_entities,
				'monthly_planning_entities' => $monthly_planning_entities,
				'monthly_percentages' => $monthly_earning_percentage
			];

			return $this->response->setJSON($response);
		} catch (\Exception $e) {
			log_message('error', 'Error in month_plan_chart: ' . $e->getMessage());

			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred while processing the data'
			])->setStatusCode(500);
		}
	}


	// public function company_month_plan_chart()
	// {
	// 	$session = \Config\Services::session();
	// 	if (!$session->has('sup_username')) {
	// 		return redirect()->to(site_url('/'));
	// 	}

	// 	$UsersModel = new UsersModel();
	// 	$YearPlanningModel = new YearPlanningModel();
	// 	$MonthlyPlanningModel = new MonthlyPlanningModel();
	// 	$MonthlyAchivedModel = new MonthlyAchivedModel();
	// 	$PlanningEntityModel = new PlanningEntityModel();
	// 	$PlanningConfigurationSettingModel = new PlanningConfigurationSettingModel();

	// 	$selectedFinancialYear = $this->request->getVar('year') ?? date('Y') . '-' . substr(date('Y') + 1, 2);
	// 	$company_id = $this->request->getVar('companyId');

	// 	$startYear = (int)substr($selectedFinancialYear, 0, 4);
	// 	$endYear = $startYear + 1;

	// 	$user_info = $UsersModel->find($company_id);
	// 	$user_type = $user_info['user_type'] ?? 'company';

	// 	$monthly_earning_percentage = array_fill(0, 12, 0);
	// 	$achieved_revenue = array_fill_keys(array_map('strtolower', [
	// 		'april',
	// 		'may',
	// 		'june',
	// 		'july',
	// 		'august',
	// 		'september',
	// 		'october',
	// 		'november',
	// 		'december',
	// 		'january',
	// 		'february',
	// 		'march'
	// 	]), 0);
	// 	$planned_revenue = $achieved_revenue;

	// 	try {
	// 		// Fetch Monthly Configuration
	// 		$monthly_config = $PlanningConfigurationSettingModel
	// 			->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $selectedFinancialYear])
	// 			->findAll();

	// 		foreach ($monthly_config as $config) {
	// 			$month = strtolower(explode('-', $config['month'])[0]);
	// 			$month_index = array_search($month, array_keys($achieved_revenue));
	// 			if ($month_index !== false) {
	// 				$monthly_earning_percentage[$month_index] = (float)$config['percentage'];
	// 			}
	// 		}

	// 		$monthly_achieved = $MonthlyAchivedModel
	// 			->where([
	// 				'company_id' => $company_id,
	// 				'user_type' => $user_type,
	// 				'year' => $selectedFinancialYear,
	// 				'entities_id' => 1
	// 			])
	// 			->findAll();

	// 		foreach ($monthly_achieved as $record) {
	// 			$month = strtolower(explode('-', $record['month'])[0]);
	// 			if (isset($achieved_revenue[$month])) {
	// 				$achieved_revenue[$month] += (float)$record['entity_value'];
	// 			}
	// 		}

	// 		$monthly_planned = $MonthlyPlanningModel
	// 			->where([
	// 				'company_id' => $company_id,
	// 				'user_type' => $user_type,
	// 				'year' => $selectedFinancialYear,
	// 				'entities_id' => 1
	// 			])
	// 			->findAll();

	// 		foreach ($monthly_planned as $record) {
	// 			$month = strtolower(explode('-', $record['month'])[0]);
	// 			if (isset($planned_revenue[$month])) {
	// 				$planned_revenue[$month] += (float)$record['entity_value'];
	// 			}
	// 		}

	// 		// Prepare Chart Data
	// 		$invoice_month = [];
	// 		$paid_invoice = [];
	// 		$unpaid_invoice = [];
	// 		$financialYearMonths = [
	// 			['name' => 'April', 'year' => $startYear],
	// 			['name' => 'May', 'year' => $startYear],
	// 			['name' => 'June', 'year' => $startYear],
	// 			['name' => 'July', 'year' => $startYear],
	// 			['name' => 'August', 'year' => $startYear],
	// 			['name' => 'September', 'year' => $startYear],
	// 			['name' => 'October', 'year' => $startYear],
	// 			['name' => 'November', 'year' => $startYear],
	// 			['name' => 'December', 'year' => $startYear],
	// 			['name' => 'January', 'year' => $endYear],
	// 			['name' => 'February', 'year' => $endYear],
	// 			['name' => 'March', 'year' => $endYear]
	// 		];

	// 		foreach ($financialYearMonths as $month) {
	// 			$month_lower = strtolower($month['name']);
	// 			$invoice_month[] = substr($month['name'], 0, 3) . ' ' . $month['year'];
	// 			$paid_invoice[] = (float)($planned_revenue[$month_lower] ?? 0);
	// 			$unpaid_invoice[] = (float)($achieved_revenue[$month_lower] ?? 0);
	// 		}

	// 		// Fetch Year Planning Entities
	// 		$year_planning_entities = [];
	// 		$year_entities = $YearPlanningModel
	// 			->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $selectedFinancialYear])
	// 			->findAll();

	// 		foreach ($year_entities as $entity) {
	// 			$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
	// 			if ($planning_entity) {
	// 				$year_planning_entities[] = [
	// 					'id' => $entity['entities_id'],
	// 					'entity_name' => $planning_entity['entity'],
	// 					'entity_value' => $entity['entity_value'],
	// 				];
	// 			}
	// 		}

	// 		// Fetch Monthly Planning Entities
	// 		$monthly_planning_entities = [];
	// 		$monthly_entities = $MonthlyPlanningModel
	// 			->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $selectedFinancialYear])
	// 			->findAll();

	// 		foreach ($monthly_entities as $entity) {
	// 			$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
	// 			if ($planning_entity) {
	// 				$month = strtolower(explode('-', $entity['month'])[0]);
	// 				if (!isset($monthly_planning_entities[$month])) {
	// 					$monthly_planning_entities[$month] = [];
	// 				}
	// 				$monthly_planning_entities[$month][] = [
	// 					'id' => $entity['entities_id'],
	// 					'entity_name' => $planning_entity['entity'],
	// 					'entity_value' => $entity['entity_value'],
	// 					'month' => $month
	// 				];
	// 			}
	// 		}

	// 		// Prepare Final Response
	// 		$response = [
	// 			'status' => 'success',
	// 			'invoice_month' => $invoice_month,
	// 			'paid_invoice' => $paid_invoice,
	// 			'unpaid_invoice' => $unpaid_invoice,
	// 			'paid_inv_label' => lang('Invoices.xin_planned_revenue'),
	// 			'unpaid_inv_label' => lang('Invoices.xin_achieved_revenue'),
	// 			'total_revenue' => array_sum($planned_revenue),
	// 			'achieved' => array_sum($achieved_revenue),
	// 			'year_planning_entities' => $year_planning_entities,
	// 			'monthly_planning_entities' => $monthly_planning_entities,
	// 			'monthly_percentages' => $monthly_earning_percentage
	// 		];

	// 		return $this->response->setJSON($response);
	// 	} catch (\Exception $e) {
	// 		log_message('error', 'Error in company_month_plan_chart: ' . $e->getMessage());

	// 		return $this->response->setJSON([
	// 			'status' => 'error',
	// 			'message' => 'An error occurred while processing the data'
	// 		])->setStatusCode(500);
	// 	}
	// }
	public function company_month_plan_chart()
	{

		$session = \Config\Services::session();
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}

		$UsersModel = new UsersModel();
		$YearPlanningModel = new YearPlanningModel();
		$MonthlyPlanningModel = new MonthlyPlanningModel();
		$MonthlyAchivedModel = new MonthlyAchivedModel();
		$PlanningEntityModel = new PlanningEntityModel();
		$PlanningConfigurationSettingModel = new PlanningConfigurationSettingModel();

		$selectedFinancialYear = $this->request->getVar('year') ?? date('Y') . '-' . substr(date('Y') + 1, 2);

		$startYear = (int)substr($selectedFinancialYear, 0, 4);
		$endYear = $startYear + 1;

		$company_id = $this->request->getVar('company_id');

		$user_info = $UsersModel->where('company_id',$company_id)->first();
		$user_type = $user_info['user_type'];

		$monthly_earning_percentage = array_fill(0, 12, 0);
		$achieved_revenue = array_fill_keys(array_map('strtolower', [
			'april',
			'may',
			'june',
			'july',
			'august',
			'september',
			'october',
			'november',
			'december',
			'january',
			'february',
			'march'
		]), 0);
		$planned_revenue = $achieved_revenue;

		try {
			$monthly_config = $PlanningConfigurationSettingModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($monthly_config as $config) {
				$month = strtolower(explode('-', $config['month'])[0]);
				$month_index = array_search($month, array_keys($achieved_revenue));
				if ($month_index !== false) {
					$monthly_earning_percentage[$month_index] = (float)$config['percentage'];
				}
			}

			// Get achieved revenue data (entity_id = 1 is assumed to be revenue)
			$monthly_achieved = $MonthlyAchivedModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear,
					'entities_id' => 1 // Assuming 1 is the revenue entity
				])
				->findAll();

			foreach ($monthly_achieved as $record) {
				$month = strtolower(explode('-', $record['month'])[0]);
				if (isset($achieved_revenue[$month])) {
					$achieved_revenue[$month] += (float)$record['entity_value'];
				}
			}

			// Get planned revenue data
			$monthly_planned = $MonthlyPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear,
					'entities_id' => 1 // Assuming 1 is the revenue entity
				])
				->findAll();

			foreach ($monthly_planned as $record) {
				$month = strtolower(explode('-', $record['month'])[0]);
				if (isset($planned_revenue[$month])) {
					$planned_revenue[$month] += (float)$record['entity_value'];
				}
			}

			// Prepare chart data in financial year order (April-March)
			$invoice_month = [];
			$paid_invoice = [];
			$unpaid_invoice = [];

			// Financial year months in order (April to March)
			$financialYearMonths = [
				['name' => 'April', 'year' => $startYear],
				['name' => 'May', 'year' => $startYear],
				['name' => 'June', 'year' => $startYear],
				['name' => 'July', 'year' => $startYear],
				['name' => 'August', 'year' => $startYear],
				['name' => 'September', 'year' => $startYear],
				['name' => 'October', 'year' => $startYear],
				['name' => 'November', 'year' => $startYear],
				['name' => 'December', 'year' => $startYear],
				['name' => 'January', 'year' => $endYear],
				['name' => 'February', 'year' => $endYear],
				['name' => 'March', 'year' => $endYear]
			];

			foreach ($financialYearMonths as $month) {
				$month_lower = strtolower($month['name']);
				$invoice_month[] = substr($month['name'], 0, 3) . ' ' . $month['year'];
				$paid_invoice[] = (float)($planned_revenue[$month_lower] ?? 0);
				$unpaid_invoice[] = (float)($achieved_revenue[$month_lower] ?? 0);
			}

			// Get year planning entities
			$year_planning_entities = [];
			$year_entities = $YearPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($year_entities as $entity) {
				$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
				if ($planning_entity) {
					$year_planning_entities[] = [
						'id' => $entity['entities_id'],
						'entity_name' => $planning_entity['entity'],
						'entity_value' => $entity['entity_value'],
					];
				}
			}

			// Get monthly planning entities
			$monthly_planning_entities = [];
			$monthly_entities = $MonthlyPlanningModel
				->where([
					'company_id' => $company_id,
					'user_type' => $user_type,
					'year' => $selectedFinancialYear
				])
				->findAll();

			foreach ($monthly_entities as $entity) {
				$planning_entity = $PlanningEntityModel->find($entity['entities_id']);
				if ($planning_entity) {
					$month = strtolower(explode('-', $entity['month'])[0]);
					if (!isset($monthly_planning_entities[$month])) {
						$monthly_planning_entities[$month] = [];
					}
					$monthly_planning_entities[$month][] = [
						'id' => $entity['entities_id'],
						'entity_name' => $planning_entity['entity'],
						'entity_value' => $entity['entity_value'],
						'month' => $month
					];
				}
			}

			// Prepare final response
			$response = [
				'status' => 'success',
				'invoice_month' => $invoice_month, // Format: ["Apr 2025", "May 2025", ..., "Mar 2026"]
				'paid_invoice' => $paid_invoice,   // Planned revenue data
				'unpaid_invoice' => $unpaid_invoice, // Achieved revenue data
				'paid_inv_label' => lang('Invoices.xin_planned_revenue'),
				'unpaid_inv_label' => lang('Invoices.xin_achieved_revenue'),
				'total_revenue' => array_sum($planned_revenue),
				'achieved' => array_sum($achieved_revenue),
				'year_planning_entities' => $year_planning_entities,
				'monthly_planning_entities' => $monthly_planning_entities,
				'monthly_percentages' => $monthly_earning_percentage
			];

			return $this->response->setJSON($response);
		} catch (\Exception $e) {
			log_message('error', 'Error in month_plan_chart: ' . $e->getMessage());

			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'An error occurred while processing the data'
			])->setStatusCode(500);
		}
	}



	public function client_invoice_amount_chart()
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
		$InvoicesModel = new InvoicesModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}

		/* Define return | here result is used to return user data and error for error message */ //
		$Return = array('invoice_amount' => '', 'paid_invoice' => '', 'unpaid_invoice' => '', 'paid_inv_label' => '', 'unpaid_inv_label' => '');
		$invoice_month = array();
		$paid_invoice = array();
		$someArray = array();
		$j = 0;
		for ($i = 0; $i <= 5; $i++) {
			$months = date("Y-m", strtotime(date('Y-m-01') . " -$i months"));
			$paid_amount = client_paid_invoices($months);
			$unpaid_amount = client_unpaid_invoices($months);
			$paid_invoice[] = $paid_amount;
			$unpaid_invoice[] = $unpaid_amount;
			$invoice_month[] = $months;
		}

		$Return['invoice_month'] = $invoice_month;
		$Return['paid_inv_label'] = lang('Invoices.xin_paid_invoices');
		$Return['unpaid_inv_label'] = lang('Invoices.xin_unpaid_invoices');
		$Return['paid_invoice'] = $paid_invoice;
		$Return['unpaid_invoice'] = $unpaid_invoice;

		return $this->response->setJSON($Return);
		
	}
	// read record
	public function read_invoice_data()
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
			return view('erp/invoices/pay_invoice', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// |||update record|||
	public function pay_invoice_record()
	{
		$validation = \Config\Services::validation();
		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

		// Check if the request type is 'edit_record'
		if ($this->request->getPost('type') !== 'edit_record') {
			$Return['error'] = 'Invalid request type.';
			return $this->output($Return);
		}

		// Set validation rules
		$rules = [
			'payment_method' => 'required|integer',
			'status' => 'required|integer',
		];

		// Validate the input data
		if (!$this->validate($rules)) {
			$Return['error'] = reset($validation->getErrors());
			return $this->response->setJSON($Return);
		}

		// Sanitize and process input data
		$payment_method = (int) $this->request->getPost('payment_method');
		$status = (int) $this->request->getPost('status');
		$id = $this->request->getPost('token');

		log_message('info', 'Decoded ID: ' . $id);

		// Load the InvoicesModel
		$InvoicesModel = new InvoicesModel();

		// Check if the invoice exists
		$invoice = $InvoicesModel->find($id);
		if (!$invoice) {
			$Return['error'] = 'Invoice not found.';
			return $this->response->setJSON($Return);
		}

		// Prepare data for update
		$data = [
			'payment_method' => $payment_method,
			'status' => $status,
		];

		log_message('info', 'Data for Update: ' . print_r($data, true));

		try {
			$result = $InvoicesModel->update($id, $data);

			if ($result) {
				$Return['result'] = 'Invoice updated successfully.';
			} else {
				$db = \Config\Database::connect();
				$error = $db->error();
				log_message('error', 'Database update failed. Error: ' . $error['message']);
				$Return['error'] = 'Database update failed. ' . $error['message'];
			}
		} catch (\Exception $e) {
			log_message('error', 'Database exception: ' . $e->getMessage());
			$Return['error'] = 'Database error: ' . $e->getMessage();
		}

		return $this->response->setJSON($Return);
	}

	// delete record
	public function delete_invoice($id)
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		// $id = $this->request->getVar('_token', FILTER_SANITIZE_STRING);
		$Return['csrf_hash'] = csrf_hash();
		$InvoicesModel = new InvoicesModel();
		$UsersModel = new UsersModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		$result = $InvoicesModel->where('invoice_id', $id)->where('company_id', $company_id)->delete($id);
		if ($result == TRUE) {
			$Return['result'] = lang('Success.ci_invoice_deleted_msg');
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
		}
		return $this->response->setJSON($Return);
	}
}
