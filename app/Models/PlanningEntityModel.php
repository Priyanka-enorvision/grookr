<?php
namespace App\Models;

use CodeIgniter\Model;
	
class PlanningEntityModel extends Model {
    protected $table = 'planning_entities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','company_id','entity','description','user_type','type','valid','created_at','updated_at'];
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
?>