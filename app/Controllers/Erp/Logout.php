<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use App\Models\UsersModel;

class Logout extends BaseController
{
	public function index()
	{
		$UsersModel = new \App\Models\UsersModel();
		$session = \Config\Services::session();

		$usession = $session->get('sup_username');

		if ($usession && isset($usession['sup_user_id'])) {
			$last_data = [
				'is_logged_in' => '0',
				'last_logout_date' => date('d-m-Y H:i:s')
			];

			$UsersModel->update($usession['sup_user_id'], $last_data);
		}

		$session->destroy();

		$session->setFlashdata('message', 'Successfully logged out.');

		return redirect()->to(site_url('/'));
	}
}
