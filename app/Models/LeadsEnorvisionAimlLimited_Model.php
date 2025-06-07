<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsEnorvisionAimlLimited_Model extends Model
{
    protected $table = 'leads_enorvision_aiml_limited';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'lead_owner', 're_assigned_date', 'name', 'contact', 'lead_type', 'address', 'profile_image', 'email', 'description', 'test'];
}
