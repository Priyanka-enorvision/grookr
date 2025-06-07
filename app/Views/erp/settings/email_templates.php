<?php

use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\SystemModel;
use App\Models\CountryModel;
use App\Models\ConstantsModel;

$LanguageModel = new LanguageModel();
$SystemModel = new SystemModel();
$CountryModel = new CountryModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$router = service('router');

$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$language = $LanguageModel->where('is_active', 1)->orderBy('language_id', 'ASC')->findAll();
$xin_system = $SystemModel->where('setting_id', 1)->first();
?>
<?php
/*
* System Settings - Email Templates View
*/
?>


<hr class="border-light m-0 mb-3">

<div class="card mt-3 user-profile-list">
  <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
        <?= lang('Main.xin_list_all'); ?>
      </strong>
      <?= lang('Main.left_email_templates'); ?>
    </span> </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?= lang('Main.xin_subject'); ?></th>
            <th><?= lang('Main.xin_template_name'); ?></th>
            <th><?= lang('Main.dashboard_xin_status'); ?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>