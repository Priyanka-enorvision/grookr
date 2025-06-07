<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadsForTest_Model extends Model
{
    protected $table = 'leads_for_test';
    protected $primaryKey = 'id';
    protected $allowedFields = ['opportunity_id', 'name', 'contact', 'address', 'email', 'description'];
}
