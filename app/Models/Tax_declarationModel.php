<?php

namespace App\Models;

use CodeIgniter\Model;

class Tax_declarationModel extends Model
{

    protected $table = 'tax_declaration';

    protected $primaryKey = 'id';

    // get all fields of assets table
    protected $allowedFields = ['company_id', 'employee_id', 'invest_name', 'section', 'declared_amount', 'status', 'proof', 'created_at', 'updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
