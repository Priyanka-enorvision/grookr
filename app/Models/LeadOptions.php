<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadOptions extends Model
{
    protected $table = 'ci_options';
    protected $primaryKey = 'id';
    protected $allowedFields = ['lead_config_id', 'options', 'valid', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $useSoftDeletes = false;
}
