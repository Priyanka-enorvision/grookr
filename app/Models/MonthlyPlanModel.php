<?php
namespace App\Models;
use CodeIgniter\Model;

class MonthlyPlanModel extends Model {
    protected $table = 'monthly_planning';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','company_id','annual_id','month','revenue','employee','clients','distributers','valid','created','updated'];
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
?>