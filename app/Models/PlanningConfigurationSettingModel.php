<?php
namespace App\Models;

use CodeIgniter\Model;
	
class PlanningConfigurationSettingModel extends Model {
    protected $table = 'planning_configuration_setting';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','year','month','percentage','company_id','user_type','created_at','updated_at'];
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
?>