<?php

namespace App\Models;

use CodeIgniter\Model;

class KpiModel extends Model
{

	protected $table = 'ci_performance_indicator';

	protected $primaryKey = 'performance_indicator_id';

	// get all fields of table
	protected $allowedFields = ['company_id', 'title', 'designation_id', 'added_by', 'created_at', 'user_id', 'review_period', 'year', 'emp_total_rating', 'mang_total_rating', 'manager_overallRemark', 'updated_by'];

	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
