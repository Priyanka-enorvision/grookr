<?php
namespace App\Models;

use CodeIgniter\Model;

class MonthlyPlanReviewModel extends Model {
    protected $table = 'monthly_review';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id',
        'user_type',
        'monthly_plan_id',
        'status',
        'comment',
        'real_value',
        'expected_value',
        'created_at',
        'updated_at'
    ];    
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
?>