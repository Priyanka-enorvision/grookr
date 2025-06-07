<?php

use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>
<?php if (in_array('attendance', staff_role_resource()) || in_array('upattendance1', staff_role_resource()) || in_array('monthly_time', staff_role_resource()) || in_array('overtime_req1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>


  <hr class="border-light m-0 mb-3">
<?php } ?>
<div class="row m-b-1">
  <div class="col-md-12">
    <div class="card mb-4 user-profile-list">
      <div class="card-header">
        <h5>
          <?= lang('Dashboard.xin_overtime_request'); ?>
        </h5>
        <?php if (in_array('overtime_req2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <div class="card-header-right">
            <button type="button" class="btn btn-sm btn-primary" id="add_attendance_btn" data-toggle="modal" data-target=".view-modal-data"><i data-feather="plus"></i>
              <?= lang('Main.xin_add_new'); ?>
            </button>
          </div>
        <?php } ?>
      </div>
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th><?= lang('Dashboard.dashboard_employee'); ?></th>
                <th><?= lang('Main.xin_e_details_date'); ?></th>
                <th><?= lang('Employees.xin_shift_in_time'); ?></th>
                <th><?= lang('Employees.xin_shift_out_time'); ?></th>
                <th><?= lang('Attendance.xin_overtime_thours'); ?></th>
                <th><?= lang('Main.dashboard_xin_status'); ?></th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>