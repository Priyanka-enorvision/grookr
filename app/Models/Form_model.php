<?php

namespace App\Models;

use CodeIgniter\Model;

class Form_model extends Model
{
    protected $table = 'form_data';
    protected $primaryKey = 'id';

    protected $allowedFields = ['name', 'contact', 'email', 'description','status','created_at','updated_at','company','industry','source','address','city','zip_code','category','participate','subject','request','opportunity_id','lead_status'];
}
?>
