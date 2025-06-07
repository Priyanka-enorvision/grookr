<?php

namespace App\Models;

use CodeIgniter\Model;

class TaxProofModel extends Model
{

    protected $table = 'tax_proof';

    protected $primaryKey = 'id';

    // get all fields of assets table
    protected $allowedFields = ['declaration_id', 'file_name', 'created_at', 'updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
