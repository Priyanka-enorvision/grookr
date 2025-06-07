<?php
namespace App\Models;

use CodeIgniter\Model;
	
class AnnualPlanningModel extends Model {
    protected $table = 'annual_planning';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','company_id','revenue','employees','clients','distributers','created','updated','flag','year'];
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
}
?>