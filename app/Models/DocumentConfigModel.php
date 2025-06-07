<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentConfigModel extends Model
{
    protected $table = 'docu_category_config'; // Table name
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id',
        'category_name',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $validationRules = [
        'category_name' => 'required|min_length[3]'
    ];

    protected $validationMessages = [
        'category_name' => [
            'required' => 'The category name field is required.',
            'min_length' => 'The category name must be at least 3 characters long.'
        ]
    ];

    protected $skipValidation = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
