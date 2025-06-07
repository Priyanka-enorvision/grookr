<?php
namespace App\Models;

use CodeIgniter\Model;

class SubscriptionModel extends Model
{
    protected $table = 'subscriptions'; 
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'company_id',
        'email',
        'plan',
        'start_date',
        'end_date',
        'payment_status',
        'notes',
        'price'
    ];
    protected $useTimestamps = true;
}
