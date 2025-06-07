<?php
namespace App\Models;

use CodeIgniter\Model;
	
class MomdiscussionModel extends Model {
 
    protected $table = 'moms_discussion';

    protected $primaryKey = 'id';
    
	// get all fields of table
    protected $allowedFields = ['id','company_id','mom_id','employee_id','discussion_text','created_at','updated_at'];
	
	protected $validationRules = [];
	protected $validationMessages = [];
	protected $skipValidation = false;
	
}
?>