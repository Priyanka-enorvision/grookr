<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\AssetsModel;
use App\Models\AssetscategoryModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$UsersModel = new UsersModel();
$RolesModel = new RolesModel();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
/*
* Job Promotion - View Page
*/
?>

<?php if (in_array('ats2', staff_role_resource()) || in_array('candidate', staff_role_resource()) || in_array('interview', staff_role_resource()) || in_array('promotion', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

  <hr class="border-light m-0 mb-3">
<?php } ?>
<div class="card user-profile-list">
  <div class="card-header">
    <h5>
      <?= lang('Main.xin_list_all'); ?>
      <?= lang('Recruitment.xin_promoted_employees'); ?>
    </h5>
  </div>
  <div class="card-body">
    <div class="card-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?php echo lang('Dashboard.dashboard_employee'); ?></th>
            <th><?php echo lang('Recruitment.xin_promotion_title'); ?></th>
            <th><?php echo lang('Recruitment.xin_interviewer'); ?></th>
            <th><?php echo lang('Recruitment.xin_promotion_date'); ?></th>
            <th><?php echo lang('Recruitment.xin_remarks'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>