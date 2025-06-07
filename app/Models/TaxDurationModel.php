<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxDurationModel extends Model
{

    protected $table = 'tax_duration';

    protected $primaryKey = 'id';

    // get all fields of assets table
    protected $allowedFields = ['company_id', 'from_date', 'to_date'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
