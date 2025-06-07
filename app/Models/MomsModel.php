<?php
namespace App\Models;

use CodeIgniter\Model;

class MomsModel extends Model {
 
    protected $table = 'moms';

    protected $primaryKey = 'id';
    
	// get all fields of table
    protected $allowedFields = ['id','title','summary','description','meeting_date','status','company_id','created_at','updated_at','project_id','assigned_to'];
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>