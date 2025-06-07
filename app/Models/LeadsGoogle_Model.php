<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsGoogle_Model extends Model
{
    protected $table = 'leads_google';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'nationality___', 'user_name', 'mobile', 'address', 'email'];
}
