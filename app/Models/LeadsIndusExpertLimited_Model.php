<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsIndusExpertLimited_Model extends Model
{
    protected $table = 'leads_indus_expert_limited';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'employee', 'user_name', 'mobile', 'address', 'email'];
}
