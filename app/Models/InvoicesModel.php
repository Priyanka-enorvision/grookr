<?php
namespace App\Models;

use CodeIgniter\Model;

class InvoicesModel extends Model {
    protected $table = 'ci_invoices';
    protected $primaryKey = 'invoice_id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'invoice_number', 'company_id', 'client_id', 'project_id',
        'invoice_month', 'invoice_date', 'invoice_due_date', 'sub_total_amount',
        'discount_type', 'discount_figure', 'total_discount', 'total_tax',
        'tax_type', 'grand_total', 'invoice_note', 'status', 'payment_method', 'expert_to'
    ];

    protected $validationRules = [
        'invoice_number' => 'required|is_unique[ci_invoices.invoice_number,invoice_id,{invoice_id}]',
        'company_id' => 'required|integer',
        'client_id' => 'permit_empty|integer',
        'project_id' => 'permit_empty|integer',
        'invoice_month' => 'permit_empty|string',
        'invoice_date' => 'required|string',
        'invoice_due_date' => 'required|string',
        'sub_total_amount' => 'required|decimal',
        'discount_type' => 'required|string',
        'discount_figure' => 'required|decimal',
        'total_discount' => 'required|decimal',
        'total_tax' => 'required|decimal',
        'tax_type' => 'permit_empty|string',
        'grand_total' => 'required|decimal',
        'invoice_note' => 'required|string',
        'status' => 'required|integer',
        'payment_method' => 'required|integer',
        'expert_to' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'invoice_number' => [
            'required' => 'Invoice number is required.',
            'is_unique' => 'Invoice number already exists.'
        ],
        // Add more custom messages if needed
    ];

    protected $skipValidation = false;

    /**
     * Update invoice record.
     *
     * @param int $invoice_id
     * @param array $invoice_data
     * @return array
     */
    public function updateInvoice($invoice_id, $invoice_data)
    {
        if (!$this->validate($invoice_data)) {
            $errors = $this->errors();
            return [
                'error' => 'Validation failed. ' . implode(', ', $errors)
            ];
        }

        if (!$this->update($invoice_id, $invoice_data)) {
            $db = \Config\Database::connect();
            $dbError = $db->error();
            return [
                'error' => 'Failed to update invoice. Error: ' . $dbError['message']
            ];
        }

        return [
            'result' => 'Invoice updated successfully.'
        ];
    }
}
?>
