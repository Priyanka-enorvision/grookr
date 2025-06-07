<?php
namespace App\Models;

use CodeIgniter\Model;

class MonthlyAchivedModel extends Model {
    protected $table = 'monthly_achive_submit';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'entities_id', 'entity_value','company_id','month','year', 'user_type','created_at', 'updated_at'];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
?>