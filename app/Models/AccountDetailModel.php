<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountDetailModel extends Model
{
    protected $table = 'ci_account_details';
    protected $primaryKey = 'account_id'; // Change 'id' to your actual primary key if different
    protected $allowedFields = ['lead_id', 'company_id', 'account_name', 'office_address', 'pincode', 'gst_no', 'pan_card_no', 'created_at', 'updated_at'];
    protected $useTimestamps = true; // Automatically manage created_at and updated_at
}
