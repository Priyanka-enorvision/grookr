<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectdiscussionModel extends Model
{

	protected $table = 'ci_projects_discussion';

	protected $primaryKey = 'project_discussion_id';

	// get all fields of table
	protected $allowedFields = ['company_id', 'project_id', 'employee_id', 'subject', 'discussion_text', 'created_at'];

	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
