<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestmentTypeModel extends Model
{
    protected $table = 'investment_type';
    protected $primaryKey = 'investment_id';

    protected $allowedFields = [
        'company_id',
        'investment_name',
        'section',
        'limit_amount',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'investment_name' => 'required|min_length[3]',
        'section' => 'required',
        'limit_amount' => 'required|decimal',
    ];

    protected $validationMessages = [
        'investment_name' => [
            'required' => 'Investment name is required',
            'min_length' => 'Investment name must be at least 3 characters long',
        ],
        'section' => [
            'required' => 'Section is required',
        ],
        'limit_amount' => [
            'required' => 'Maximum limit is required',
            'decimal' => 'Maximum limit must be a decimal number',
        ],
    ];

    protected $skipValidation = false;
}
