<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsBharatesa_Model extends Model
{
    protected $table = 'leads_bharatesa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'user_name', 'mobile', 'address', 'email', 'rakesh_kumar'];
}
