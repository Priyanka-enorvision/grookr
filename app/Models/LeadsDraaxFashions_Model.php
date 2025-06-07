<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsDraaxFashions_Model extends Model
{
    protected $table = 'leads_draax_fashions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'name', 'contact', 'address', 'email', 'description'];
}
