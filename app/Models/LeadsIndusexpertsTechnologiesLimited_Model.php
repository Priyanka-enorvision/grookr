<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsIndusexpertsTechnologiesLimited_Model extends Model
{
    protected $table = 'leads_indusexperts_technologies_limited';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'name', 'mobile', 'address', 'email', 'description'];
}
