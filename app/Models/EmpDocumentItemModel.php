<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpDocumentItemModel extends Model
{

    protected $table = 'employe_document_items';

    protected $primaryKey = 'id';

    // get all fields of table
    protected $allowedFields = ['employe_docu_id', 'category_id', 'data', 'created_at', 'updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
