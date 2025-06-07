<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceitemsModel extends Model
{

    protected $table = 'ci_invoices_items';

    protected $primaryKey = 'invoice_item_id';

    protected $useTimestamps = true;

    protected $allowedFields = [
        'invoice_id',
        'project_id',
        'item_name',
        'item_qty',
        'item_unit_price',
        'item_sub_total',
    ];

    protected $validationRules = [
        'invoice_id' => 'required|integer',
        'project_id' => 'permit_empty|integer',
        'item_name' => 'required|string',
        'item_qty' => 'required|string',
        'item_unit_price' => 'required',
        'item_sub_total' => 'required'
    ];

    protected $validationMessages = [
        // Add custom validation messages here if needed
    ];

    protected $skipValidation = false;
}
