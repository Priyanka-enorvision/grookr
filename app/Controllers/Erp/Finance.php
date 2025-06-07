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
use App\Models\PayeesModel;
use App\Models\AccountsModel;
use App\Models\ConstantsModel;
use App\Models\TransactionsModel;
use App\Models\InvestmentTypeModel;
use App\Models\Tax_declarationModel;
use Dompdf\Options;
use Dompdf\Dompdf;
use App\Models\ContractModel;
use App\Models\PayrollModel;

class Finance extends BaseController
{

	public function bank_cash()
	{
		$RolesModel = new RolesModel();
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
			if (!in_array('accounts1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Finance.xin_accounts') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_accounts';
		$data['breadcrumbs'] = lang('Finance.xin_accounts');

		$data['subview'] = view('erp/finance/finance_accounts', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function deposit()
	{
		$RolesModel = new RolesModel();
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
			if (!in_array('deposit1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_deposit') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_deposit';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_deposit');

		$data['subview'] = view('erp/finance/finance_deposit', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function account_ledger()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');
		$AccountsModel = new AccountsModel();
		$request = \Config\Services::request();
		$ifield_id = udecode($request->getUri()->getSegment(3));
		$isegment_val = $AccountsModel->where('account_id', $ifield_id)->first();
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
			if (!in_array('deposit1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Main.xin_account_ledger') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_deposit';
		$data['breadcrumbs'] = lang('Main.xin_account_ledger');

		$data['subview'] = view('erp/finance/finance_account_ledger', $data);
		return view('erp/layout/pre_layout_main', $data); //page load
	}
	public function transaction_details()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$TransactionsModel = new TransactionsModel();
		$ifield_id = udecode($request->getUri()->getSegment(3));
		$isegment_val = $TransactionsModel->where('transaction_id', $ifield_id)->first();
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
			if (!in_array('deposit1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Main.xin_transaction_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_deposit';
		$data['breadcrumbs'] = lang('Main.xin_transaction_details');

		$data['subview'] = view('erp/finance/finance_transaction_details', $data);
		return view('erp/layout/pre_layout_main', $data); //page load
	}
	public function expense()
	{
		$RolesModel = new RolesModel();
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
			if (!in_array('expense1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_expense') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_expense';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_expense');

		$data['subview'] = view('erp/finance/finance_expense', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function transfer()
	{
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
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
		$data['title'] = lang('Employees.xin_employee_details') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'employee_details';
		$data['breadcrumbs'] = lang('Employees.xin_employee_details');

		$data['subview'] = view('erp/finance/finance_transfer', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function transactions()
	{
		$RolesModel = new RolesModel();
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
			if (!in_array('transaction1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.xin_acc_transactions') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'finance_transactions';
		$data['breadcrumbs'] = lang('Dashboard.xin_acc_transactions');

		$data['subview'] = view('erp/finance/finance_transactions', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	// record list
	public function accounts_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$AccountsModel = new AccountsModel();
		$xin_system = erp_company_settings();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $AccountsModel->where('company_id', $user_info['company_id'])->orderBy('account_id', 'ASC')->findAll();
		} else {
			$get_data = $AccountsModel->where('company_id', $usession['sup_user_id'])->orderBy('account_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('accounts3', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . uencode($r['account_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('accounts4', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['account_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}
			// account ledger
			if (in_array('accounts3', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$ledger = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/account-ledger') . '/' . uencode($r['account_id']) . '" target="_blank"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			} else {
				$ledger = $r['account_name'];
			}
			$account_balance = number_to_currency($r['account_balance'], $xin_system['default_currency'], null, 2);
			$created_at = set_date_format($r['created_at']);
			//$account_name = $ledger;
			$combhr = $ledger . $edit . $delete;
			if (in_array('accounts3', staff_role_resource()) || in_array('accounts4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$iaccount_name = '
				' . $r['account_name'] . '
				<div class="overlay-edit">
					' . $combhr . '
				</div>';
			} else {
				$iaccount_name = $r['account_name'];
			}

			$data[] = array(
				$iaccount_name,
				$r['account_number'],
				$account_balance,
				$r['bank_branch']
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
	public function deposit_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TransactionsModel = new TransactionsModel();
		$AccountsModel = new AccountsModel();
		$PayeesModel = new PayeesModel();
		$ConstantsModel = new ConstantsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$xin_system = erp_company_settings();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $TransactionsModel->where('company_id', $user_info['company_id'])->where('transaction_type', 'income')->orderBy('transaction_id', 'ASC')->findAll();
		} else {
			$get_data = $TransactionsModel->where('company_id', $usession['sup_user_id'])->where('transaction_type', 'income')->orderBy('transaction_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('deposit3', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . uencode($r['transaction_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('deposit4', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['transaction_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$iaccounts = $AccountsModel->where('account_id', $r['account_id'])->first();
			//$f_entity = $PayeesModel->where('entity_id', $r['entity_id'])->where('type', 'payer')->first();
			// user info
			$f_entity = $UsersModel->where('user_id', $r['entity_id'])->where('user_type', 'staff')->first();
			$payer_name = $f_entity['first_name'] . ' ' . $f_entity['last_name'];
			$amount = number_to_currency($r['amount'], $xin_system['default_currency'], null, 2);
			$category_info = $ConstantsModel->where('constants_id', $r['entity_category_id'])->where('type', 'income_type')->first();
			$payment_method = $ConstantsModel->where('constants_id', $r['payment_method_id'])->where('type', 'payment_method')->first();

			$transaction_date = set_date_format($r['transaction_date']);
			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/transaction-details') . '/' . uencode($r['transaction_id']) . '" target="_blank"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			$combhr = $view . $edit . $delete;
			$iaccount_name = '
			' . $iaccounts['account_name'] . '
			<div class="overlay-edit">
				' . $combhr . '
			</div>';
			$data[] = array(
				$iaccount_name,
				$payer_name,
				$amount,
				$category_info['category_name'],
				$r['reference'],
				$payment_method['category_name'],
				$transaction_date
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
	public function expense_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TransactionsModel = new TransactionsModel();
		$AccountsModel = new AccountsModel();
		$PayeesModel = new PayeesModel();
		$ConstantsModel = new ConstantsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$xin_system = erp_company_settings();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $TransactionsModel->where('entity_id', $usession['sup_user_id'])->where('transaction_type', 'expense')->orderBy('transaction_id', 'ASC')->findAll();
		} else {
			$get_data = $TransactionsModel->where('company_id', $usession['sup_user_id'])->where('transaction_type', 'expense')->orderBy('transaction_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('expense3', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_id="' . uencode($r['transaction_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('expense4', staff_role_resource()) || $user_info['user_type'] == 'company') { //edit
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['transaction_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$iaccounts = $AccountsModel->where('account_id', $r['account_id'])->first();
			//$f_entity = $PayeesModel->where('entity_id', $r['entity_id'])->where('type', 'payee')->first();
			$f_entity = $UsersModel->where('user_id', $r['entity_id'])->where('user_type', 'staff')->first();
			$payer_name = $f_entity['first_name'] . ' ' . $f_entity['last_name'];
			$amount = number_to_currency($r['amount'], $xin_system['default_currency'], null, 2);
			$category_info = $ConstantsModel->where('constants_id', $r['entity_category_id'])->where('type', 'expense_type')->first();
			$payment_method = $ConstantsModel->where('constants_id', $r['payment_method_id'])->where('type', 'payment_method')->first();

			$transaction_date = set_date_format($r['transaction_date']);
			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/transaction-details') . '/' . uencode($r['transaction_id']) . '" target="_blank"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			$combhr = $view . $edit . $delete;
			$iaccount_name = '
			' . $iaccounts['account_name'] . '
			<div class="overlay-edit">
				' . $combhr . '
			</div>';

			$data[] = array(
				$iaccount_name,
				$payer_name,
				$amount,
				$category_info['category_name'],
				$r['reference'],
				$payment_method['category_name'],
				$transaction_date
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
	public function transaction_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$TransactionsModel = new TransactionsModel();
		$AccountsModel = new AccountsModel();
		$PayeesModel = new PayeesModel();
		$ConstantsModel = new ConstantsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$xin_system = erp_company_settings();
		if ($user_info['user_type'] == 'staff') {
			$get_data = $TransactionsModel->where('company_id', $user_info['company_id'])->orderBy('transaction_id', 'ASC')->findAll();
		} else {
			$get_data = $TransactionsModel->where('company_id', $usession['sup_user_id'])->orderBy('transaction_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			$iaccounts = $AccountsModel->where('account_id', $r['account_id'])->first();
			$f_entity = $PayeesModel->where('entity_id', $r['entity_id'])->first();
			$amount = number_to_currency($r['amount'], $xin_system['default_currency'], null, 2);
			$category_info = $ConstantsModel->where('constants_id', $r['entity_category_id'])->first();
			$payment_method = $ConstantsModel->where('constants_id', $r['payment_method_id'])->where('type', 'payment_method')->first();
			$transaction_date = set_date_format($r['transaction_date']);
			// credit
			$cr_dr = $r['dr_cr'] == "cr" ? lang('Finance.xin_credit') : lang('Finance.xin_debit');
			$view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/transaction-details') . '/' . uencode($r['transaction_id']) . '" target="_blank"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-arrow-right"></i></button></a></span>';
			$combhr = $view;
			$iaccount_name = '
			' . $iaccounts['account_name'] . '
			<div class="overlay-edit">
				' . $combhr . '
			</div>';
			$data[] = array(
				$iaccount_name,
				$transaction_date,
				$cr_dr,
				$payment_method['category_name'],
				$amount,
				$r['reference']
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
	public function add_account()
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
				'account_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'account_balance' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'account_number' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_name" => $validation->getError('account_name'),
					"account_balance" => $validation->getError('account_balance'),
					"account_number" => $validation->getError('account_number')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$account_name = $this->request->getPost('account_name', FILTER_SANITIZE_STRING);
				$account_balance = $this->request->getPost('account_balance', FILTER_SANITIZE_STRING);
				$account_number = $this->request->getPost('account_number', FILTER_SANITIZE_STRING);
				$branch_code = $this->request->getPost('branch_code', FILTER_SANITIZE_STRING);
				$bank_branch = $this->request->getPost('bank_branch', FILTER_SANITIZE_STRING);
				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'company_id'  => $company_id,
					'account_name' => $account_name,
					'account_balance'  => $account_balance,
					'account_opening_balance' => $account_balance,
					'account_number'  => $account_number,
					'branch_code' => $branch_code,
					'bank_branch'  => $bank_branch,
					'created_at' => date('d-m-Y h:i:s')
				];
				$AccountsModel = new AccountsModel();
				$result = $AccountsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_account_added_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				return $this->response->setJSON($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
			exit;
		}
	}
	// |||add record|||
	public function add_deposit()
	{

		$validation =  \Config\Services::validation();
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
			$rules = [
				'account_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_account_field_error')
					]
				],
				'amount' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'deposit_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'category_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_category_field_error')
					]
				],
				'payer_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_payer_field_error')
					]
				],
				'payment_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'attachment' => [
					'rules'  => 'uploaded[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/gif,image/png]|max_size[attachment,3072]',
					'errors' => [
						'uploaded' => lang('Main.xin_error_field_text'),
						'mime_in' => 'wrong size'
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_id" => $validation->getError('account_id'),
					"amount" => $validation->getError('amount'),
					"deposit_date" => $validation->getError('deposit_date'),
					"category_id" => $validation->getError('category_id'),
					"payer_id" => $validation->getError('payer_id'),
					"payment_method" => $validation->getError('payment_method'),
					"attachment" => $validation->getError('attachment')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				// upload file
				$attachment = $this->request->getFile('attachment');
				$file_name = $attachment->getName();
				$attachment->move('public/uploads/transactions/');

				$account_id = $this->request->getPost('account_id', FILTER_SANITIZE_STRING);
				$amount = $this->request->getPost('amount', FILTER_SANITIZE_STRING);
				$deposit_date = $this->request->getPost('deposit_date', FILTER_SANITIZE_STRING);
				$category_id = $this->request->getPost('category_id', FILTER_SANITIZE_STRING);
				$payer_id = $this->request->getPost('payer_id', FILTER_SANITIZE_STRING);
				$payment_method = $this->request->getPost('payment_method', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$reference = $this->request->getPost('reference', FILTER_SANITIZE_STRING);

				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}

				$data = [
					'company_id' => $company_id,
					'staff_id' => $usession['sup_user_id'],
					'account_id'  => $account_id,
					'transaction_date'  => $deposit_date,
					'transaction_type'  => 'income',
					'entity_id'  => $payer_id,
					'entity_type'  => 'payer',
					'entity_category_id'  => $category_id,
					'description'  => $description,
					'amount'  => $amount,
					'dr_cr'  => 'cr',
					'payment_method_id' => $payment_method,
					'reference' => $reference,
					'attachment_file' => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$TransactionsModel = new TransactionsModel();
				$result = $TransactionsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_deposit_added_msg');
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
	public function add_expense()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'account_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_account_field_error')
					]
				],
				'amount' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'deposit_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_date_field_error')
					]
				],
				'category_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_category_field_error')
					]
				],
				'payer_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_payee_field_error')
					]
				],
				'payment_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'attachment' => [
					'rules'  => 'uploaded[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/gif,image/png]|max_size[attachment,3072]',
					'errors' => [
						'uploaded' => lang('Main.xin_error_field_text'),
						'mime_in' => 'wrong size'
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_id" => $validation->getError('account_id'),
					"amount" => $validation->getError('amount'),
					"deposit_date" => $validation->getError('deposit_date'),
					"category_id" => $validation->getError('category_id'),
					"payer_id" => $validation->getError('payer_id'),
					"payment_method" => $validation->getError('payment_method'),
					"attachment" => $validation->getError('attachment')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				// upload file
				$attachment = $this->request->getFile('attachment');
				$file_name = $attachment->getName();
				$attachment->move('uploads/transactions/');

				$account_id = $this->request->getPost('account_id', FILTER_SANITIZE_STRING);
				$amount = $this->request->getPost('amount', FILTER_SANITIZE_STRING);
				$deposit_date = $this->request->getPost('deposit_date', FILTER_SANITIZE_STRING);
				$category_id = $this->request->getPost('category_id', FILTER_SANITIZE_STRING);
				$payer_id = $this->request->getPost('payer_id', FILTER_SANITIZE_STRING);
				$payment_method = $this->request->getPost('payment_method', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$reference = $this->request->getPost('reference', FILTER_SANITIZE_STRING);

				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}

				$data = [
					'company_id' => $company_id,
					'staff_id' => $usession['sup_user_id'],
					'account_id'  => $account_id,
					'transaction_date'  => $deposit_date,
					'transaction_type'  => 'expense',
					'entity_id'  => $payer_id,
					'entity_type'  => 'payee',
					'entity_category_id'  => $category_id,
					'description'  => $description,
					'amount'  => $amount,
					'dr_cr'  => 'dr',
					'payment_method_id' => $payment_method,
					'reference' => $reference,
					'attachment_file' => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$TransactionsModel = new TransactionsModel();
				$result = $TransactionsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_expense_added_msg');
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
	public function update_deposit()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'account_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_account_field_error')
					]
				],
				'amount' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'deposit_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'category_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_category_field_error')
					]
				],
				'payer_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_payer_field_error')
					]
				],
				'payment_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_id" => $validation->getError('account_id'),
					"amount" => $validation->getError('amount'),
					"deposit_date" => $validation->getError('deposit_date'),
					"category_id" => $validation->getError('category_id'),
					"payer_id" => $validation->getError('payer_id'),
					"payment_method" => $validation->getError('payment_method')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->response->setJSON($Return);
					}
				}
			} else {
				// upload file
				$validated = $this->validate([
					'attachment' => [
						'rules'  => 'uploaded[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/gif,image/png]|max_size[attachment,3072]',
						'errors' => [
							'uploaded' => lang('Main.xin_error_field_text'),
							'mime_in' => 'wrong size'
						]
					]
				]);
				if ($validated) {
					$attachment = $this->request->getFile('attachment');
					$file_name = $attachment->getName();
					$attachment->move('uploads/transactions/');
				}

				$account_id = $this->request->getPost('account_id', FILTER_SANITIZE_STRING);
				$amount = $this->request->getPost('amount', FILTER_SANITIZE_STRING);
				$deposit_date = $this->request->getPost('deposit_date', FILTER_SANITIZE_STRING);
				$category_id = $this->request->getPost('category_id', FILTER_SANITIZE_STRING);
				$payer_id = $this->request->getPost('payer_id', FILTER_SANITIZE_STRING);
				$payment_method = $this->request->getPost('payment_method', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$reference = $this->request->getPost('reference', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));
				var_dump($id);die;

				if ($validated) {
					$data = [
						'account_id'  => $account_id,
						'transaction_date'  => $deposit_date,
						'entity_id'  => $payer_id,
						'entity_category_id'  => $category_id,
						'description'  => $description,
						'amount'  => $amount,
						'payment_method_id' => $payment_method,
						'reference' => $reference,
						'attachment_file' => $file_name
					];
				} else {
					$data = [
						'account_id'  => $account_id,
						'transaction_date'  => $deposit_date,
						'entity_id'  => $payer_id,
						'entity_category_id'  => $category_id,
						'description'  => $description,
						'amount'  => $amount,
						'payment_method_id' => $payment_method,
						'reference' => $reference
					];
				}

				$TransactionsModel = new TransactionsModel();
				$result = $TransactionsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_deposit_updated_msg');
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
	public function update_expense()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'account_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_account_field_error')
					]
				],
				'amount' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'deposit_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_date_field_error')
					]
				],
				'category_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_category_field_error')
					]
				],
				'payer_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Success.xin_payee_field_error')
					]
				],
				'payment_method' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_id" => $validation->getError('account_id'),
					"amount" => $validation->getError('amount'),
					"deposit_date" => $validation->getError('deposit_date'),
					"category_id" => $validation->getError('category_id'),
					"payer_id" => $validation->getError('payer_id'),
					"payment_method" => $validation->getError('payment_method')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						return $this->response->setJSON($Return);
					}
				}
			} else {
				// upload file
				$validated = $this->validate([
					'attachment' => [
						'rules'  => 'uploaded[attachment]|mime_in[attachment,image/jpg,image/jpeg,image/gif,image/png]|max_size[attachment,3072]',
						'errors' => [
							'uploaded' => lang('Main.xin_error_field_text'),
							'mime_in' => 'wrong size'
						]
					]
				]);
				if ($validated) {
					$attachment = $this->request->getFile('attachment');
					$file_name = $attachment->getName();
					$attachment->move('public/uploads/transactions/');
				}

				$account_id = $this->request->getPost('account_id', FILTER_SANITIZE_STRING);
				$amount = $this->request->getPost('amount', FILTER_SANITIZE_STRING);
				$deposit_date = $this->request->getPost('deposit_date', FILTER_SANITIZE_STRING);
				$category_id = $this->request->getPost('category_id', FILTER_SANITIZE_STRING);
				$payer_id = $this->request->getPost('payer_id', FILTER_SANITIZE_STRING);
				$payment_method = $this->request->getPost('payment_method', FILTER_SANITIZE_STRING);
				$description = $this->request->getPost('description', FILTER_SANITIZE_STRING);
				$reference = $this->request->getPost('reference', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				if ($validated) {
					$data = [
						'account_id'  => $account_id,
						'transaction_date'  => $deposit_date,
						'entity_id'  => $payer_id,
						'entity_category_id'  => $category_id,
						'description'  => $description,
						'amount'  => $amount,
						'payment_method_id' => $payment_method,
						'reference' => $reference,
						'attachment_file' => $file_name
					];
				} else {
					$data = [
						'account_id'  => $account_id,
						'transaction_date'  => $deposit_date,
						'entity_id'  => $payer_id,
						'entity_category_id'  => $category_id,
						'description'  => $description,
						'amount'  => $amount,
						'payment_method_id' => $payment_method,
						'reference' => $reference
					];
				}

				$TransactionsModel = new TransactionsModel();
				$result = $TransactionsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_expense_updated_msg');
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
	public function update_account()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost()) {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'account_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'account_balance' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'account_number' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"account_name" => $validation->getError('account_name'),
					"account_balance" => $validation->getError('account_balance'),
					"account_number" => $validation->getError('account_number')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$account_name = $this->request->getPost('account_name', FILTER_SANITIZE_STRING);
				$account_balance = $this->request->getPost('account_balance', FILTER_SANITIZE_STRING);
				$account_number = $this->request->getPost('account_number', FILTER_SANITIZE_STRING);
				$branch_code = $this->request->getPost('branch_code', FILTER_SANITIZE_STRING);
				$bank_branch = $this->request->getPost('bank_branch', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				$data = [
					'account_name' => $account_name,
					'account_balance'  => $account_balance,
					'account_opening_balance' => $account_balance,
					'account_number'  => $account_number,
					'branch_code' => $branch_code,
					'bank_branch'  => $bank_branch
				];
				$AccountsModel = new AccountsModel();
				$result = $AccountsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_finance_account_updated_msg');
				} else {
					$Return['error'] = lang('Main.xin_error_msg');
				}
				return $this->response->setJSON($Return);
				exit;
			}
		} else {
			$Return['error'] = lang('Main.xin_error_msg');
			return $this->response->setJSON($Return);
			exit;
		}
	}
	// read record
	public function read_accounts()
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
			return view('erp/finance/dialog_accounts', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// read record
	public function read_transactions()
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
			return view('erp/finance/dialog_transactions', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_account()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$AccountsModel = new AccountsModel();
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}
			$result = $AccountsModel->where('account_id', $id)->where('company_id', $company_id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_finance_account_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}
	// delete record
	public function delete_transaction()
	{

		if ($this->request->getPost('type') == 'delete_record') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$TransactionsModel = new TransactionsModel();
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
			if ($user_info['user_type'] == 'staff') {
				$company_id = $user_info['company_id'];
			} else {
				$company_id = $usession['sup_user_id'];
			}
			$result = $TransactionsModel->where('transaction_id', $id)->where('company_id', $company_id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_finance_data_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
		}
	}

	public function tax_verification()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = 'Tax Verification';
		$data['breadcrumbs'] = 'Tax Verification';
		$data['path_url'] = '';

		$data['subview'] = view('erp/finance/verify_tax', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function view_mypay()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = 'My pay';
		$data['breadcrumbs'] = 'My pay';

		$data['subview'] = view('erp/finance/my_pay', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function tax_declaration($enc_id)
	{
		$employe_id = base64_decode($enc_id);
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = 'Declaration';
		$data['breadcrumbs'] = 'Declaration';
		$data['path_url'] = '';
		$data['employe_id'] = $employe_id;

		$data['subview'] = view('erp/finance/declaration', $data);
		return view('erp/layout/layout_main', $data); //page load
	}



	public function save_declaration()
	{
		$UsersModel = new \App\Models\UsersModel();
		$DeclarationModel = new \App\Models\Tax_declarationModel();
		$ProofModel = new \App\Models\TaxProofModel();
		$investmentModel = new \App\Models\InvestmentTypeModel();
		$session = \Config\Services::session();
		$validation = \Config\Services::validation();
		$usession = $session->get('sup_username');

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$validation->setRules([
			'section' => 'required',
			'name' => 'required',
			'newDeclaration' => 'required',
		]);

		if ($this->request->getMethod() && $validation->withRequest($this->request)->run()) {
			$section = $this->request->getPost('section');
			$data = [
				'company_id' => $user_info['company_id'],
				'employee_id' => $usession['sup_user_id'],
				'section' => $section,
				'invest_name' => $this->request->getPost('name'),
				'declared_amount' => $this->request->getPost('newDeclaration'),
				'proof' => 'No proof',
				'status' => $this->request->getPost('status'),
				'created_at' => date('Y-m-d H:i:s'),
			];

			$investment = $investmentModel->where('section', $section)->first();
			$declaration = $DeclarationModel->where(['employee_id' => $usession['sup_user_id'], 'section' => $section])->findall();

			$limitAmount = $investment['limit_amount'];
			$declaredAmount = 0;

			foreach ($declaration as $declared) {
				$declaredAmount += $declared['declared_amount'];
			}
			if ($declaredAmount + $data['declared_amount'] <= $limitAmount) {
				$result = $DeclarationModel->insert($data);
				$insertedID = $DeclarationModel->insertID();
				$files = $this->request->getFiles();
				$proofFiles = $files['proof'] ?? [];

				if (!empty($proofFiles)) {
					$proofCount = 0;
					foreach ($proofFiles as $file) {
						if ($file->isValid() && !$file->hasMoved()) {
							$uploadPath = FCPATH . 'public/uploads/tax_proof/';
							if (!is_dir($uploadPath)) {
								mkdir($uploadPath, 0777, true);
							}

							$newName = $file->getRandomName();
							if ($file->move($uploadPath, $newName)) {
								// Insert file into the Proof table
								$ProofModel->insert([
									'declaration_id' => $insertedID,
									'file_name' => $newName,
								]);
								$proofCount++;
							} else {
								// Handle file move failure
								return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to upload proof file.']);
							}
						}
					}

					if ($proofCount > 0) {
						$DeclarationModel->update($insertedID, ['proof' => 'Proof uploaded']);
					}
				}
				if ($result) {
					$Result['result'] = 'Declaration successfully added.'; // Changed from 'success' to 'result'
				} else {
					$Result['error'] = 'Failed to add declaration.';
				}
				return $this->response->setJSON($Result);
			} else {
				$Result['error'] = 'Your limit is only ' . $limitAmount . '. You have declared ' . ($declaredAmount + $data['declared_amount']) . '.';
				return $this->response->setJSON($Result);
			}
		} else {
			$Result['error'] = implode(", ", $validation->getErrors());
			return $this->response->setJSON($Result);
		}
	}







	public function investment_type()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = 'Investment Type';
		$data['breadcrumbs'] = 'Investment Type';
		$data['path_url'] = '';

		$data['subview'] = view('erp/finance/investment_type', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function insert_investment()
	{
		$UsersModel = new \App\Models\UsersModel();
		$validation = \Config\Services::validation();
		$InvestmentModel = new \App\Models\InvestmentTypeModel(); // Added full namespace
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession || !isset($usession['sup_user_id'])) {
			return redirect()->to('/')->with('error', 'Please login first.');
		}
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if (!$user_info) {
			return redirect()->back()->with('error', 'User not found.');
		}
		$validation->setRules([
			'investment_name' => 'required|min_length[3]',
			'section' => 'required',
			'maximum_limit' => 'required',
		]);
		if ($this->request->getMethod()) {

			$maximum_limit = str_replace(',', '', $this->request->getPost('maximum_limit'));

			$data = [
				'company_id' => $user_info['company_id'],
				'investment_name' => $this->request->getPost('investment_name'),
				'section' => $this->request->getPost('section'),
				'limit_amount' => $maximum_limit,
				'status' => 1,
				'created_at' => date('Y-m-d H:i:s'),
			];

			try {
				if ($InvestmentModel->insert($data)) {
					return redirect()->back()->with('message', 'Investment Type successfully added.');
				} else {
					return redirect()->back()->with('error', 'Failed to add investment.');
				}
			} catch (\Exception $e) {
				return redirect()->back()->with('error', 'Database error: ' . $e->getMessage());
			}
		} else {
			$errors = $validation->getErrors();
			return redirect()->back()->withInput()->with('error', implode(", ", $errors));
		}
	}

	public function updateStatus($invest_id, $status)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$usession || !isset($usession['sup_user_id'])) {
			return redirect()->to('/')->with('error', 'Please login first.');
		}


		if (!in_array($status, [0, 1])) {
			return redirect()->back()->with('error', 'Invalid status value');
		}
		$investmentType = new \App\Models\InvestmentTypeModel();

		try {
			$investment = $investmentType->find($invest_id);
			if (!$investment) {
				return redirect()->back()->with('error', 'Investment not found');
			}
			$data = ['status' => $status];
			$result = $investmentType->update($invest_id, $data);

			if ($result) {
				$statusText = $status ? 'activated' : 'deactivated';
				return redirect()->back()->with('message', "Investment {$statusText} successfully");
			} else {
				return redirect()->back()->with('error', 'No changes made to investment status');
			}
		} catch (\Exception $e) {
			log_message('error', 'Failed to update investment status: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Failed to update status. Please try again');
		}
	}

	public function delete_investment($id)
	{
		$Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if (!$usession) {
			$Return['error'] = 'Session expired or user not logged in.';
			return $this->response->setJSON($Return)->setStatusCode(401);
		}

		// Validate ID is numeric
		if (!is_numeric($id)) {
			$Return['error'] = 'Invalid investment ID';
			return $this->response->setJSON($Return)->setStatusCode(400);
		}

		$Model = new InvestmentTypeModel();

		// Check if record exists before deleting
		$investment = $Model->find($id);
		if (!$investment) {
			$Return['error'] = 'Investment not found';
			return $this->response->setJSON($Return)->setStatusCode(404);
		}

		$result = $Model->delete($id);

		if ($result) {
			$Return['result'] = 'Deleted Successfully';
			$Return['redirect_url'] = base_url('erp/investment-type');
			return $this->response->setJSON($Return)->setStatusCode(200);
		}

		$Return['error'] = 'Failed to delete the investment.';
		$Return['redirect_url'] = base_url('erp/investment-type');
		return $this->response->setJSON($Return)->setStatusCode(500);
	}

	public function getData($id)
	{
		$session = \Config\Services::session();
		$UsersModel = new UsersModel();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$Model = new InvestmentTypeModel();
		$result = $Model->where('investment_id', $id)->first();

		if ($result) {
			return view('erp/finance/edit_investment', ['result' => $result]);
		} else {
			return redirect()->back()->with('error', 'No data found for the given ID');
		}
	}

	public function update_investment($investment_id)
	{
		// Validate investment_id
		if (!is_numeric($investment_id)) {
			return redirect()->to(site_url('erp/investment-type'))->with('error', 'Invalid investment ID');
		}

		$session = \Config\Services::session();
		$model = new InvestmentTypeModel();
		$validation = \Config\Services::validation();

		// Define validation rules
		$validation->setRules([
			'investment_name' => [
				'label' => 'Investment Name',
				'rules' => 'required|min_length[3]|max_length[255]'
			],
			'section' => [
				'label' => 'Section',
				'rules' => 'required|in_list[80C,80CCC,80CCD,80D,80DD,80E,80G,80GG,80GGA,80GGC,80U,24D,other]'
			],
			'maximum_limit' => [
				'label' => 'Maximum Limit',
				'rules' => 'required|numeric'
			]
		]);

		// Run validation
		if (!$validation->withRequest($this->request)->run()) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Check if investment exists
		$investment = $model->find($investment_id);
		if (!$investment) {
			return redirect()->to(site_url('erp/investment-type'))->with('error', 'Investment not found');
		}

		// Prepare data
		$maximum_limit = str_replace(',', '', $this->request->getPost('maximum_limit'));

		$data = [
			'investment_name' => esc($this->request->getPost('investment_name')),
			'section' => esc($this->request->getPost('section')),
			'limit_amount' => $maximum_limit,
			'updated_at' => date('Y-m-d H:i:s'),
		];

		// Update record
		try {
			$result = $model->update($investment_id, $data);

			if ($result) {
				return redirect()->to(site_url('erp/investment-type'))->with('message', 'Investment Type successfully updated.');
			} else {
				return redirect()->to(site_url('erp/investment-type'))->with('error', 'Failed to update investment. No changes made.');
			}
		} catch (\Exception $e) {
			log_message('error', 'Error updating investment: ' . $e->getMessage());
			return redirect()->to(site_url('erp/investment-type'))->with('error', 'An error occurred while updating the investment.');
		}
	}

	public function getInvestmentname()
	{
		$section = $this->request->getGet('section');

		if (!$section) {
			return $this->response->setJSON([]);
		}

		$investmentModel = new InvestmentTypeModel();
		$investments = $investmentModel->where('section', $section)->findAll();

		return $this->response->setJSON($investments);
	}
	public function getLimitedAmount()
	{
		$investment_name = $this->request->getPost('name');
		$investmentModel = new \App\Models\InvestmentTypeModel();
		$Taxdeclarationlist = new Tax_declarationModel();

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$investment = $investmentModel->where('investment_name', $investment_name)->first();
		$taxdeclaration = $Taxdeclarationlist->where(['section' => $investment['section'], 'employee_id' => $usession['sup_user_id']])->findAll();

		if ($taxdeclaration && count($taxdeclaration) > 0) {
			$max_amount = $taxdeclaration[0]['max_amount'];
			$declared_amount = 0;
			foreach ($taxdeclaration as $declaration) {
				$declared_amount += $declaration['declared_amount'];
			}
			$remaining_amount = $max_amount - $declared_amount;

			$returnData = [
				'limit_amount' => $remaining_amount,
				'status' => 'success'
			];
		} elseif ($investment) {
			$returnData = [
				'limit_amount' => $investment['limit_amount'],
				'status' => 'success'
			];
		} else {
			$returnData = [
				'status' => 'error',
				'message' => 'Investment not found.'
			];
		}

		return $this->response->setJSON($returnData);
	}



	public function tax_statusUpdate()
	{
		$status = $this->request->getPost('status');
		$id = $this->request->getPost('id');
		$TaxModel = new \App\Models\Tax_declarationModel();  // Ensure correct namespace
		$result = $TaxModel->update($id, ['status' => $status]);
		if ($result) {
			return $this->response->setJSON(['success' => true]);
		} else {
			return $this->response->setJSON(['success' => false]);
		}
	}

	public function delete_alltaxProof($id)
	{
		$Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$usession) {
			$Return['error'] = 'Session expired or user not logged in.';
			return $this->response->setJSON($Return);
		}

		$Model = new Tax_declarationModel();
		$ProofModel = new \App\Models\TaxProofModel();

		$result = $Model->where('id', $id)->delete();
		if (!$result) {
			$Return['error'] = 'Failed to delete the tax declaration.';
			return $this->response->setJSON($Return);
		}
		$proof = $ProofModel->where('declaration_id', $id)->delete();
		if (!$proof) {
			$Return['error'] = 'Failed to delete the associated tax proof.';
			return $this->response->setJSON($Return);
		}

		// If both deletions were successful
		$Return['result'] = 'Deleted Successfully';
		$Return['redirect_url'] = base_url('erp/tax-declaration/' . base64_encode($usession['sup_user_id']));

		return $this->response->setJSON($Return);
	}


	public function tax_updateItem()
	{
		$UsersModel = new \App\Models\UsersModel();
		$DeclarationModel = new \App\Models\Tax_declarationModel();
		$ProofModel = new \App\Models\TaxProofModel();
		$validation = \Config\Services::validation();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$id = $this->request->getPost('id');


		if ($this->request->getMethod()) {
			$data = [
				'section' => $this->request->getPost('section'),
				'invest_name' => $this->request->getPost('invest_name'),
				'max_amount' => $this->request->getPost('max_amount'),
				'declared_amount' => $this->request->getPost('declared_amount'),
				'updated_at' => date('Y-m-d H:i:s'),
			];

			$result = $DeclarationModel->update($id, $data);

			// Handle proof files if provided
			$files = $this->request->getFileMultiple('proof');
			if ($files) {
				$uploadPath = FCPATH . 'uploads/tax_proof/';
				if (!is_dir($uploadPath)) {
					mkdir($uploadPath, 0777, true);
				}

				$existingProofs = $ProofModel->where('declaration_id', $id)->findAll();
				foreach ($existingProofs as $existingProof) {
					if (file_exists($uploadPath . $existingProof['file_name'])) {
						unlink($uploadPath . $existingProof['file_name']);
					}
					$ProofModel->delete($existingProof['id']);
				}

				foreach ($files as $file) {
					if ($file->isValid() && !$file->hasMoved()) {
						$newName = $file->getRandomName();
						if ($file->move($uploadPath, $newName)) {
							$proofdata = [
								'declaration_id' => $id,
								'file_name' => $newName,
							];
							$ProofModel->insert($proofdata);
						} else {
							$Result['error'] =  'Failed to upload proof file.';
							return $this->response->setJSON($Result);
						}
					}
				}

				$DeclarationModel->update($id, ['proof' => 'Proof uploaded']);
			}

			if ($result) {
				$Result['result'] = 'Declaration successfully updated.'; // Changed from 'success' to 'result'
			} else {
				$Result['error'] = 'Failed to update declaration.';
			}
			return $this->response->setJSON($Result);
		} else {
			$Result['error'] =  'Invalid request method.';
			return $this->response->setJSON($Result);
		}
	}


	public function checktax_apply()
	{
		return	view('tax/applyTax');
	}
	public function generatePdf($id)
	{
		require_once APPPATH . 'ThirdParty/dompdf/vendor/autoload.php';
		$options = new \Dompdf\Options();
		$options->set('isRemoteEnabled', true);

		$dompdf = new \Dompdf\Dompdf($options);
		$data = $this->getFormDetails($id);
		$html = view('erp/finance/form16', $data);

		$dompdf->loadHtml($html);
		// $dompdf->setPaper('A4', 'landscape');
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream("sample_partA.pdf", array("Attachment" => 1));
	}

	public function form16_partB($id)
	{
		require_once APPPATH . 'ThirdParty/dompdf/vendor/autoload.php';
		$options = new \Dompdf\Options();
		$options->set('isRemoteEnabled', true);

		$dompdf = new \Dompdf\Dompdf($options);
		$data = $this->getFormDetails($id);
		$html = view('erp/finance/form16_part_b', $data);

		$dompdf->loadHtml($html);
		// $dompdf->setPaper('A4', 'landscape');
		$dompdf->setPaper('A4', 'portrait');
		$dompdf->render();
		$dompdf->stream("sample_partB.pdf", array("Attachment" => 1));
	}
	public function getFormDetails($id = null)
	{
		$UsersModel = new UsersModel();
		$Taxdeclarationlist = new Tax_declarationModel();
		$ContractModel = new ContractModel();
		$session = \Config\Services::session();
		$db = \Config\Database::connect();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		$employee_id = $id ?? $user_info['user_id'];

		$tax_list = $Taxdeclarationlist->where('company_id', $user_info['company_id'])->where('employee_id', $employee_id)->orderBy('id', 'desc')->findAll();

		$getsalary = $db->table('ci_erp_users_details');
		$getsalary->where('user_id', $employee_id);
		$query = $getsalary->get();
		$getgrossSalary = $query->getRowArray();

		$approved_tax_list = $Taxdeclarationlist->where('company_id', $user_info['company_id'])
			->where('employee_id', $employee_id)
			->whereIn('status', ['Approved', 'Pending'])
			->orderBy('id', 'desc')
			->findAll();

		$total_declared_amount = 0;
		if (!empty($approved_tax_list)) {
			foreach ($approved_tax_list as $tax) {
				$total_declared_amount += isset($tax['declared_amount']) ? $tax['declared_amount'] : 0;
			}
		}
		$HRA_Exemption = $ContractModel->where(['user_id' => $employee_id, 'option_title' => 'HRA'])->first();
		$contract_amount = isset($HRA_Exemption['contract_amount']) ? $HRA_Exemption['contract_amount'] : 0;

		$annualSalary  = $getgrossSalary['basic_salary'] * 12  - ($total_declared_amount + $contract_amount);
		$total_salary = $getgrossSalary['basic_salary'] * 12;

		$totalDeductions = $total_declared_amount + $contract_amount;
		$result = array(
			'tax_list' => $tax_list,
			'Taxable_Amount' => $annualSalary,
			'Tax_amount' => getSalaryTax($total_salary, $totalDeductions),
			'declared' => $totalDeductions,
			'hra_exemption' => $contract_amount,
			'certificateNumber' => generateCertificateNumber(),
			'user_info' => $user_info,
		);
		return $result;
	}
}
