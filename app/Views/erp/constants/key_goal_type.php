<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();
$i = 1;
?>

<?php if (in_array('indicator1', staff_role_resource()) || in_array('appraisal1', staff_role_resource()) || in_array('competency1', staff_role_resource()) || in_array('tracking1', staff_role_resource()) || in_array('track_type1', staff_role_resource()) || in_array('track_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

  <hr class="border-light m-0 mb-3">
<?php } ?>
<div class="row m-b-1 animated fadeInRight">
  <?php if (in_array('track_type2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <div class="col-md-4">
      <div class="card">
        <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
              <?= lang('Main.xin_add_new'); ?>
            </strong>
            <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>
          </span> </div>
        <div class="card-body">
          <?php $attributes = array('name' => 'add_goal_type', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
          <?php $hidden = array('user_id' => '0'); ?>
          <?= form_open('erp/add-goal-type', $attributes, $hidden); ?>
          <div class="form-group">
            <label for="name">
              <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>
              <span class="text-danger">*</span> </label>
            <input type="text" class="form-control" name="name" placeholder="<?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>">
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-primary">
            <?= lang('Main.xin_save'); ?>
          </button>
        </div>
        <?= form_close(); ?>
      </div>
    </div>
    <?php $colmdval = 'col-md-8'; ?>
  <?php } else { ?>
    <?php $colmdval = 'col-md-12'; ?>
  <?php } ?>
  <div class="<?= $colmdval; ?>">
    <div class="card user-profile-list">
      <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
            <?= lang('Main.xin_list_all'); ?>
          </strong>
          <?= lang('Performance.xin_hr_goal_tracking_types'); ?>
        </span> </div>
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th><i class="fas fa-braille"></i>
                  <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?></th>
                <th> <?= lang('Main.xin_created_at'); ?></th>
                <?php if (in_array('track_type3', staff_role_resource()) || in_array('track_type4', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                  <th> Action </th>
                <?php } ?>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>