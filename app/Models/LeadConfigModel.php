<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadConfigModel extends Model
{
    protected $table = 'ci_lead_config'; // Make sure this table exists in the database
    protected $primaryKey = 'id'; // Ensure your table has 'id' as the primary key

    // Fields allowed for mass assignment
    protected $allowedFields = [
        'company_id',
        'column_name',
        'type',
        'is_required',
        'status',
        'created_at',
        'updated_at',
    ];

    // Automatically manage timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // Ensure your table has this column

    // Validation rules for data input
    protected $validationRules = [
        'column_name' => 'required|min_length[3]|max_length[100]',
        'type' => 'required',
        'is_required' => 'required|in_list[0,1]', // Accepts 0 or 1 for boolean type
    ];

    // Custom validation messages
    protected $validationMessages = [
        'column_name' => [
            'required' => 'Field Label is required',
            'min_length' => 'Field Label must be at least 3 characters long',
            'max_length' => 'Field Label cannot exceed 100 characters'
        ],
        'type' => [
            'required' => 'Data Type is required'
        ],
        'is_required' => [
            'required' => 'Please specify if the field is required',
            'in_list' => 'Is Required should be either 0 or 1'
        ]
    ];

    // Disable skipping validation
    protected $skipValidation = false;
}
