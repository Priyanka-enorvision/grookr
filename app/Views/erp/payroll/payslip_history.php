<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\DepartmentModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$DepartmentModel = new DepartmentModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if ($user['user_type'] == 'staff') {
  $main_department = $DepartmentModel->where('company_id', $user['company_id'])->findAll();
  $staff_info = $UsersModel->where('company_id', $user['company_id'])->where('user_type', 'staff')->findAll();
} else {
  $main_department = $DepartmentModel->where('company_id', $usession['sup_user_id'])->findAll();
  $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
}
$locale = service('request')->getLocale();
?>


<div class="card user-profile-list">
  <div class="box-header with-border">
    <div id="accordion">
      <div class="card-header">
        <h5>
          <?= lang('Dashboard.xin_payslip_history'); ?>
        </h5>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?= lang('Dashboard.dashboard_employee'); ?></th>
            <th><?= lang('Payroll.xin_net_payable'); ?></th>
            <th><?= lang('Payroll.xin_salary_month'); ?></th>
            <th><?= lang('Payroll.xin_pay_date'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<style type="text/css">
  .hide-calendar .ui-datepicker-calendar {
    display: none !important;
  }

  .hide-calendar .ui-priority-secondary {
    display: none !important;
  }
</style>