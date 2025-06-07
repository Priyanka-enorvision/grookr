<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsHbfDirectLimited_Model extends Model
{
    protected $table = 'leads_hbf_direct_limited';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'name', 'contact', 'address', 'email', 'description'];
}
