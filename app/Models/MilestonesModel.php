<?php

namespace App\Models;

use CodeIgniter\Model;

class MilestonesModel extends Model
{
    protected $table = 'ci_milestones'; // Your table name
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'company_id',
        'project_id',
        'name',
        'due_date',
        'description',
        'orders',
        'status',
        'created_at'
    ];

    // Automatically manage timestamps
    protected $useTimestamps = false; // Set this to false if manually setting timestamps
}
