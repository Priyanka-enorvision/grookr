<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;


use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\DocumentsModel;
use App\Models\DepartmentModel;
use App\Models\OfficialdocumentsModel;

class Documents extends BaseController
{

	public function upload_files()
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
			if (!in_array('file1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Employees.xin_general_documents') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'documents';
		$data['breadcrumbs'] = lang('Employees.xin_general_documents');

		$data['subview'] = view('erp/system_documents/upload_files', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	public function official_documents()
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
			return redirect()->to(site_url('erp/login'));
		}
		if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff') {
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}
		if ($user_info['user_type'] != 'company') {
			if (!in_array('officialfile1', staff_role_resource())) {
				$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
				return redirect()->to(site_url('erp/desk'));
			}
		}
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = lang('Employees.xin_official_documents') . ' | ' . $xin_system['application_name'];
		$data['path_url'] = 'official_documents';
		$data['breadcrumbs'] = lang('Employees.xin_official_documents');

		$data['subview'] = view('erp/system_documents/official_documents', $data);
		return view('erp/layout/layout_main', $data); //page load
	}
	// record list
	public function system_documents_list($department_id)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('/'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$id = $this->request->getUri()->getSegment(5);

		$DocumentsModel = new DocumentsModel();
		$DepartmentModel = new DepartmentModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		if ($department_id == '0') {
			$get_data = $DocumentsModel->where('company_id', $company_id)->orderBy('document_id', 'ASC')->findAll();
		} else {
			$get_data = $DocumentsModel->where('company_id', $company_id)->where('department_id', $department_id)->orderBy('document_id', 'ASC')->findAll();
		}
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('file3', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".view-modal-data" data-field_type="document" data-field_id="' . uencode($r['document_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('file4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="document" data-record-id="' . uencode($r['document_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;
			$download_link = '<a href="' . site_url() . 'download?type=system_documents&filename=' . urlencode($r['document_file']) . '">' . lang('Main.xin_download') . '</a>';
			$department = $DepartmentModel->where('department_id', $r['department_id'])->first();
			if (in_array('file3', staff_role_resource()) || in_array('file4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$department_name = '
					' . $department['department_name'] . '
					<div class="overlay-edit">
						' . $combhr . '
					</div>';
			} else {
				$department_name = $department['department_name'];
			}
			$data[] = array(
				$department_name,
				$r['document_name'],
				$r['document_type'],
				$download_link
			);
		}
		$output = array(
			//"draw" => $draw,
			"data" => $data
		);
		echo json_encode($output);
		exit();
	}
	// public function system_documents_list($department_id = null)
	// {

	// 	$session = \Config\Services::session();
	// 	if (!$session->has('sup_username')) {
	// 		return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
	// 	}

	// 	// Initialize models
	// 	$UsersModel = new UsersModel();
	// 	$DocumentsModel = new DocumentsModel();
	// 	$DepartmentModel = new DepartmentModel();

	// 	// Get user info
	// 	$usession = $session->get('sup_username');
	// 	$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

	// 	if (!$user_info) {
	// 		return $this->response->setJSON(['error' => 'User not found'])->setStatusCode(404);
	// 	}

	// 	$company_id = ($user_info['user_type'] == 'staff') ? $user_info['company_id'] : $usession['sup_user_id'];

	// 	// Build query
	// 	$builder = $DocumentsModel->where('company_id', $company_id);

	// 	if ($department_id && $department_id != '0') {
	// 		$builder->where('department_id', $department_id);
	// 	}

	// 	$documents = $builder->orderBy('document_id', 'ASC')->findAll();

	// 	// Prepare data - ensure we always return an array
	// 	$data = [];
	// 	if ($documents) {
	// 		foreach ($documents as $doc) {
	// 			$department = $DepartmentModel->find($doc['department_id']);
	// 			$department_name = $department ? $department['department_name'] : 'N/A';

	// 			// Check permissions
	// 			$edit = $delete = '';
	// 			if (in_array('file3', staff_role_resource()) || $user_info['user_type'] == 'company') {
	// 				$edit = '<span data-toggle="tooltip" title="Edit"><button class="btn btn-sm btn-light-primary" data-target=".view-modal-data" data-field_id="' . uencode($doc['document_id']) . '"><i class="feather icon-edit"></i></button></span>';
	// 			}
	// 			if (in_array('file4', staff_role_resource()) || $user_info['user_type'] == 'company') {
	// 				$delete = '<span data-toggle="tooltip" title="Delete"><button class="btn btn-sm btn-light-danger delete" data-record-id="' . uencode($doc['document_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
	// 			}

	// 			$data[] = [
	// 				$department_name . '<div class="overlay-edit">' . $edit . $delete . '</div>',
	// 				$doc['document_name'] ?? '',
	// 				$doc['document_type'] ?? '',
	// 				$doc['document_file'] ? '<a href="' . site_url('download?type=system_documents&filename=' . uencode($doc['document_file'])) . '">' . lang('Main.xin_download') . '</a>' : 'N/A'
	// 			];
	// 		}
	// 		var_dump($data);
	// 		die;
	// 	}

	// 	return $this->response->setJSON($data);
	// }
	// record list
	public function official_documents_list()
	{

		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		$RolesModel = new RolesModel();
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$request = \Config\Services::request();
		$OfficialdocumentsModel = new OfficialdocumentsModel();
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if ($user_info['user_type'] == 'staff') {
			$company_id = $user_info['company_id'];
		} else {
			$company_id = $usession['sup_user_id'];
		}
		$get_data = $OfficialdocumentsModel->where('company_id', $company_id)->orderBy('document_id', 'ASC')->findAll();
		$data = array();

		foreach ($get_data as $r) {

			if (in_array('officialfile3', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$edit = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_edit') . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" data-toggle="modal" data-target=".edit-modal-data" data-field_type="document" data-field_id="' . uencode($r['document_id']) . '"><i class="feather icon-edit"></i></button></span>';
			} else {
				$edit = '';
			}
			if (in_array('officialfile4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-field_type="document" data-record-id="' . uencode($r['document_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
			} else {
				$delete = '';
			}

			$combhr = $edit . $delete;
			$download_link = '<a href="' . site_url() . 'download?type=official_documents&filename=' . uencode($r['document_file']) . '">' . lang('Main.xin_download') . '</a>';
			if (in_array('officialfile3', staff_role_resource()) || in_array('officialfile4', staff_role_resource()) || $user_info['user_type'] == 'company') {
				$document_type = '
					' . $r['document_type'] . '
					<div class="overlay-edit">
						' . $combhr . '
					</div>';
			} else {
				$document_type = $r['document_type'];
			}
			$data[] = array(
				$document_type,
				$r['license_name'],
				$r['license_no'],
				$r['expiry_date'],
				$download_link
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
	public function add_document()
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
				'department_id' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Employees.xin_employee_error_department')
					]
				],
				'document_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_type' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_file' => [
					'rules'  => 'uploaded[document_file]|mime_in[document_file,image/jpg,image/jpeg,image/gif,image/png]|max_size[document_file,5120]',
					'errors' => [
						'uploaded' => lang('Main.xin_error_field_text'),
						'mime_in' => 'wrong size'
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"department_id" => $validation->getError('department_id'),
					"document_name" => $validation->getError('document_name'),
					"document_type" => $validation->getError('document_type'),
					"document_file" => $validation->getError('document_file')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				// upload file
				$document_file = $this->request->getFile('document_file');
				$file_name = $document_file->getName();
				$document_file->move('public/uploads/system_documents/');

				$department_id = $this->request->getPost('department_id', FILTER_SANITIZE_STRING);
				$document_name = $this->request->getPost('document_name', FILTER_SANITIZE_STRING);
				$document_type = $this->request->getPost('document_type', FILTER_SANITIZE_STRING);


				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'company_id' => $company_id,
					'department_id' => $department_id,
					'document_name'  => $document_name,
					'document_type'  => $document_type,
					'document_file'  => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$DocumentsModel = new DocumentsModel();
				$result = $DocumentsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_general_document_added_msg');
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
	public function add_official_document()
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
				'license_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_type' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'expiry_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'license_number' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_file' => [
					'rules'  => 'uploaded[document_file]|mime_in[document_file,image/jpg,image/jpeg,image/gif,image/png]|max_size[document_file,5120]',
					'errors' => [
						'uploaded' => lang('Main.xin_error_field_text'),
						'mime_in' => 'wrong size'
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"license_name" => $validation->getError('license_name'),
					"document_type" => $validation->getError('document_type'),
					"expiry_date" => $validation->getError('expiry_date'),
					"license_number" => $validation->getError('license_number'),
					"document_file" => $validation->getError('document_file')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				// upload file
				$document_file = $this->request->getFile('document_file');
				$file_name = $document_file->getName();
				$document_file->move('public/uploads/official_documents/');

				$license_name = $this->request->getPost('license_name', FILTER_SANITIZE_STRING);
				$document_type = $this->request->getPost('document_type', FILTER_SANITIZE_STRING);
				$expiry_date = $this->request->getPost('expiry_date', FILTER_SANITIZE_STRING);
				$license_number = $this->request->getPost('license_number', FILTER_SANITIZE_STRING);


				$UsersModel = new UsersModel();
				$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
				if ($user_info['user_type'] == 'staff') {
					$company_id = $user_info['company_id'];
				} else {
					$company_id = $usession['sup_user_id'];
				}
				$data = [
					'company_id' => $company_id,
					'license_name' => $license_name,
					'document_type'  => $document_type,
					'expiry_date'  => $expiry_date,
					'license_no'  => $license_number,
					'document_file'  => $file_name,
					'created_at' => date('d-m-Y h:i:s')
				];
				$OfficialdocumentsModel = new OfficialdocumentsModel();
				$result = $OfficialdocumentsModel->insert($data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_official_document_added_msg');
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
	// |||edit record|||
	public function update_official_document()
	{

		$validation =  \Config\Services::validation();
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
			$rules = [
				'license_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_type' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'expiry_date' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'license_number' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"license_name" => $validation->getError('license_name'),
					"document_type" => $validation->getError('document_type'),
					"expiry_date" => $validation->getError('expiry_date'),
					"license_number" => $validation->getError('license_number')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				// upload file
				$validated = $this->validate([
					'document_file' => [
						'rules'  => 'uploaded[document_file]|mime_in[document_file,image/jpg,image/jpeg,image/gif,image/png]|max_size[document_file,5120]',
						'errors' => [
							'uploaded' => lang('Main.xin_error_field_text'),
							'mime_in' => 'wrong size'
						]
					]
				]);
				if ($validated) {
					$document_file = $this->request->getFile('document_file');
					$file_name = $document_file->getName();
					$document_file->move('public/uploads/official_documents/');
				}

				$license_name = $this->request->getPost('license_name', FILTER_SANITIZE_STRING);
				$document_type = $this->request->getPost('document_type', FILTER_SANITIZE_STRING);
				$expiry_date = $this->request->getPost('expiry_date', FILTER_SANITIZE_STRING);
				$license_number = $this->request->getPost('license_number', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				if ($validated) {
					$data = [
						'license_name'  => $license_name,
						'document_type'  => $document_type,
						'expiry_date'  => $expiry_date,
						'license_no'  => $license_number,
						'document_file'  => $file_name,
					];
				} else {
					$data = [
						'license_name'  => $license_name,
						'document_type'  => $document_type,
						'expiry_date'  => $expiry_date,
						'license_no'  => $license_number,
					];
				}
				$OfficialdocumentsModel = new OfficialdocumentsModel();
				$result = $OfficialdocumentsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_official_document_updated_msg');
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
	public function update_document()
	{

		$validation =  \Config\Services::validation();
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if (!$session->has('sup_username')) {
			return redirect()->to(site_url('erp/login'));
		}
		if ($this->request->getPost('type') === 'edit_record') {
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');
			$Return['csrf_hash'] = csrf_hash();
			// set rules
			$rules = [
				'document_name' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				],
				'document_type' => [
					'rules'  => 'required',
					'errors' => [
						'required' => lang('Main.xin_error_field_text')
					]
				]
			];
			if (!$this->validate($rules)) {
				$ruleErrors = [
					"document_name" => $validation->getError('document_name'),
					"document_type" => $validation->getError('document_type')
				];
				foreach ($ruleErrors as $err) {
					$Return['error'] = $err;
					if ($Return['error'] != '') {
						$this->output($Return);
					}
				}
			} else {
				$document_name = $this->request->getPost('document_name', FILTER_SANITIZE_STRING);
				$document_type = $this->request->getPost('document_type', FILTER_SANITIZE_STRING);
				$id = udecode($this->request->getPost('token', FILTER_SANITIZE_STRING));

				$data = [
					'document_name'  => $document_name,
					'document_type'  => $document_type
				];
				$DocumentsModel = new DocumentsModel();
				$result = $DocumentsModel->update($id, $data);
				$Return['csrf_hash'] = csrf_hash();
				if ($result == TRUE) {
					$Return['result'] = lang('Success.ci_general_document_updated_msg');
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
	public function read_document()
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
			return view('erp/system_documents/dialog_document', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// read record
	public function read_official_document()
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
			return view('erp/system_documents/dialog_official_documents', $data);
		} else {
			return redirect()->to(site_url('/'));
		}
	}
	// delete record
	public function delete_document()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$DocumentsModel = new DocumentsModel();
			$result = $DocumentsModel->where('document_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_general_document_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
			exit;
		}
	}
	// delete record
	public function delete_official_document()
	{

		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		if ($this->request->getPost('_method') == 'DELETE') {
			/* Define return | here result is used to return user data and error for error message */
			$Return = array('result' => '', 'error' => '', 'csrf_hash' => '');

			$id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));
			$Return['csrf_hash'] = csrf_hash();
			$OfficialdocumentsModel = new OfficialdocumentsModel();
			$result = $OfficialdocumentsModel->where('document_id', $id)->delete($id);
			if ($result == TRUE) {
				$Return['result'] = lang('Success.ci_official_document_deleted_msg');
			} else {
				$Return['error'] = lang('Main.xin_error_msg');
			}
			return $this->response->setJSON($Return);
			exit;
		}
	}

	public function upload_document()
	{
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();
		$UsersModel = new UsersModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();
		// $kpi_performance = $KpiModel->where('performance_indicator_id', $performances_id)->first();

		$data['title'] = ' Documents';
		$data['breadcrumbs'] = ' Documents';
		$data['path_url'] = ' ';

		$data['subview'] = view('erp/document/document_list', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function save_document()
	{
		$UsersModel = new \App\Models\UsersModel();
		$documentModel = new \App\Models\VerifyEmployeDocModel();
		$itemDocument = new \App\Models\EmpDocumentItemModel();
		$session = \Config\Services::session();
		$validation = \Config\Services::validation();

		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		$validation->setRules([
			'employee_id' => 'required',
			'category_id' => 'required',
			'docu_name' => 'required',
			'docu_image' => 'uploaded[docu_image]|ext_in[docu_image,jpg,jpeg,png,pdf,docx,doc]',
		]);

		if ($this->request->getMethod()) {
			$employee_id = $this->request->getPost('employee_id');
			$category_id = $this->request->getPost('category_id');
			$docu_names = $this->request->getPost('docu_name');
			$docu_images = $this->request->getFileMultiple('docu_image');

			$documents = [];
			$success = true;
			$existingDocument = $documentModel->where('user_id', $employee_id)->first();

			if ($existingDocument) {
				$insert_id = $existingDocument['id'];
			} else {
				$data = [
					'company_id' => $user_info['company_id'],
					'user_id' => $employee_id,
					'created_at' => date('Y-m-d H:i:s'),
				];
				$documentModel->insert($data);
				$insert_id = $documentModel->insertID();
			}

			foreach ($docu_names as $index => $docu_name) {
				if (isset($docu_images[$index])) {
					$docu_image = $docu_images[$index];
					if ($docu_image->isValid() && !$docu_image->hasMoved()) {
						$newName = $docu_image->getRandomName();
						$docu_image->move('uploads/employe_document/', $newName);

						// Prepare document data for JSON
						$documents[] = [
							'docu_name' => $docu_name,
							'docu_image' => $newName,
						];
					} else {
						$success = false;
						$session->setFlashdata('error', 'Invalid file upload for ' . $docu_name);
						break;
					}
				} else {
					$success = false;
					$session->setFlashdata('error', 'File missing for ' . $docu_name);
					break;
				}
			}

			if ($success) {
				$document_json = json_encode($documents);
				$data1 = [
					'employe_docu_id' => $insert_id,
					'category_id' => $category_id,
					'data' => $document_json,
					'created_at' => date('Y-m-d H:i:s'),
				];

				if ($itemDocument->insert($data1)) {
					$session->setFlashdata('message', 'Documents successfully uploaded.');
				} else {
					$session->setFlashdata('error', 'Failed to save documents.');
				}
			}
		} else {
			$session->setFlashdata('error', implode(", ", $validation->getErrors()));
		}

		return redirect()->back()->withInput();
	}

	public function view_document($document_id)
	{

		// $document_id = base64_decode($enc_id);
		$session = \Config\Services::session();
		$SystemModel = new SystemModel();

		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$xin_system = $SystemModel->where('setting_id', 1)->first();

		$data['title'] = 'View Documents';
		$data['breadcrumbs'] = 'View Documents';
		$data['path_url'] = '';
		$data['document_id'] = $document_id;


		$data['subview'] = view('erp/document/view_document', $data);
		return view('erp/layout/layout_main', $data); //page load
	}

	public function documentfiles_updates()
	{
		$updated_id = $this->request->getPost('document_id'); // The ID of the document to update
		$category_id = $this->request->getPost('category_id'); // The category ID

		$UsersModel = new \App\Models\UsersModel();
		$itemDocument = new \App\Models\EmpDocumentItemModel();
		$session = \Config\Services::session();
		$validation = \Config\Services::validation();

		$validation->setRules([
			'docu_name' => 'required',
		]);

		if ($validation->withRequest($this->request)->run()) {
			$docu_names = $this->request->getPost('docu_name');
			$docu_image_hidden = $this->request->getPost('docu_image_hidden');
			$docu_images = $this->request->getFileMultiple('docu_image');

			$existingRecord = $itemDocument->where('employe_docu_id', $updated_id)
				->where('category_id', $category_id)
				->first();

			$documents = [];
			foreach ($docu_names as $index => $docu_name) {
				$new_image_name = $docu_image_hidden[$index];
				if (isset($docu_images[$index]) && $docu_images[$index]->isValid() && !$docu_images[$index]->hasMoved()) {
					$docu_image = $docu_images[$index];
					$new_image_name = $docu_image->getRandomName();
					$docu_image->move('uploads/employe_document/', $new_image_name);
				}

				$documents[] = [
					'docu_name' => $docu_name,
					'docu_image' => $new_image_name,
				];
			}

			$document_json = json_encode($documents);
			$dataToSave = [
				'employe_docu_id' => $updated_id,
				'category_id' => $category_id,
				'data' => $document_json,
				'updated_at' => date('Y-m-d H:i:s'),
			];

			if ($existingRecord) {
				if ($itemDocument->update($existingRecord['id'], $dataToSave)) {
					$session->setFlashdata('message', 'Documents successfully updated.');
				} else {
					$session->setFlashdata('error', 'Failed to update documents.');
				}
			} else {
				$dataToSave['created_at'] = date('Y-m-d H:i:s');
				if ($itemDocument->insert($dataToSave)) {
					$session->setFlashdata('message', 'Documents successfully updated.');
				} else {
					$session->setFlashdata('error', 'Failed to insert documents.');
				}
			}
		} else {
			$session->setFlashdata('error', implode(", ", $validation->getErrors()));
		}

		return redirect()->back()->withInput();
	}
}
