<?php
namespace App\Models;

use CodeIgniter\Model;

class YearPlanningModel extends Model {
    protected $table = 'year_planning_submit';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'entities_id', 'entity_value','company_id','year','user_type','created_at', 'updated_at'];
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
?>