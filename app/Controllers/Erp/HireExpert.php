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

class HireExpert extends BaseController
{


	public function hire_expert_grid()
	{

		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$pager = \Config\Services::pager();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id = $usession['sup_user_id'];
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
		$data['title'] = lang('Dashboard.dashboard_hire_expert') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'hire_expert_grid';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_hire_expert');
		$data['user_id'] = $user_id;
		$data['subview'] = view('erp/hire_expert/hire_expert_grid', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function applied_experts()
	{

		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$pager = \Config\Services::pager();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id = $usession['sup_user_id'];
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
			if (!in_array('staff2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_hire_expert') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'applied-experts';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_hire_expert');
		$data['user_id'] = $user_id;
		$data['subview'] = view('erp/hire_expert/applied_expert', $data);
		return view('erp/layout/layout_main', $data); //page load
	}




	public function expert_details($id)
	{

		$RolesModel = new RolesModel();
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

		$expert_id = $id;
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_hire_expert') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'expert-details';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_hire_expert');
		$data['expert_id'] = $expert_id;
		$data['subview'] = view('erp/hire_expert/hire_expert_details', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function applied_expert_data($id)
	{

		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$pager = \Config\Services::pager();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id =  $usession['sup_user_id'];
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
			if (!in_array('staff2', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$expert_id = $id;
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_hire_expert') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'applied-expert_data';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_hire_expert');
		$data['expert_id'] = $expert_id;
		$data['user_id'] = $user_id;
		$data['subview'] = view('erp/hire_expert/applied_expert_detail', $data);
		return view('erp/layout/layout_main', $data);
	}


	public function expert_apply($id)
	{

		$RolesModel = new RolesModel();
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

		$apply_expert_id = $id;

		$user_id = $usession['sup_user_id'];
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Dashboard.dashboard_hire_expert') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'expert-apply';
		$data['breadcrumbs'] = lang('Dashboard.dashboard_hire_expert');
		$data['apply_expert_id'] = $apply_expert_id;
		$data['user_id'] = $user_id;
		$data['subview'] = view('erp/hire_expert/apply_hire_expert', $data);
		return view('erp/layout/layout_main', $data);
	}
}
