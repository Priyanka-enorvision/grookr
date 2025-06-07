<?php

namespace App\Models;

use CodeIgniter\Model;

class OpportunityModel extends Model
{
    protected $table = 'ci_opportunity';
    protected $primaryKey = 'id';

    // Allowed fields for mass assignment
    protected $allowedFields = [
        'company_id',
        'user_id',
        'opportunity_name',
        'opportunity_stage',
        'expected_closing_date',
        'value',
        'probability',
        'comments',
        'status',
        'valid',
        'created_at',
        'updated_at',
    ];

    // Enable timestamps for created_at and updated_at fields
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Disable validation skipping
    protected $skipValidation = false;
}
