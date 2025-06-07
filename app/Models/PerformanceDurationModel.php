<?php

namespace App\Models;

use CodeIgniter\Model;

class PerformanceDurationModel extends Model
{

    protected $table = 'ci_perform_duration';

    protected $primaryKey = 'id';

    // get all fields of table
    protected $allowedFields = ['company_id', 'duration_type', 'remark', 'created_at', 'updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
