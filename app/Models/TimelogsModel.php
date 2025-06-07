<?php

namespace App\Models;

use CodeIgniter\Model;

class TimelogsModel extends Model
{
    protected $table = 'ci_projects_timelogs'; // Your table name
    protected $primaryKey = 'timelogs_id ';

    protected $allowedFields = [
        'company_id',
        'project_id',
        'task_id',
        'employee_id',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
        'total_hours',
        'timelogs_memo',
        'created_at'
    ];

    // Automatically manage timestamps
    protected $useTimestamps = false; // Set this to false if manually setting timestamps
}
