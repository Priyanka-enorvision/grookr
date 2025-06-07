<?php

namespace App\Models;

use CodeIgniter\Model;

class VerifyEmployeDocModel extends Model
{

	protected $table = 'employe_document_verify';

	protected $primaryKey = 'id';

	// get all fields of table
	protected $allowedFields = ['company_id', 'user_id', 'created_at'];

	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
