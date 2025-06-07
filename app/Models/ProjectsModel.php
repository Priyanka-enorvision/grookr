<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjectsModel extends Model
{
	protected $table = 'ci_projects';
	protected $primaryKey = 'project_id';

	// Allowed fields for insert and update
	protected $allowedFields = [
		'company_id',
		'client_id',
		'title',
		'start_date',
		'end_date',
		'assigned_to',
		'associated_goals',
		'priority',
		'project_no',
		'budget_hours',
		'summary',
		'description',
		'project_progress',
		'project_note',
		'status',
		'added_by',
		'updated_at',
		'created_at',
		'revenue',
		'expert_to',
		'send_email',
		'billing_type',
		'tags',
		'entities_id',
		'planning_configuration_id',
		'companies_ID',
		'employe_ID'

	];


	// Validation rules (add as needed)
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
