<?php 
use App\Models\RolesModel;
use App\Models\UsersModel;


$session = \Config\Services::session();
$usession = $session->get('sup_username');

$UsersModel = new UsersModel();
$RolesModel = new RolesModel();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

?>


<div class="card user-profile-list">
  <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><?php echo lang('Main.xin_list_all');?></strong> <?php echo lang('Dashboard.left_job_candidates');?></span> </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?php echo lang('Recruitment.xin_job_title');?></th>
            <th><?php echo lang('Recruitment.xin_candidate_name');?></th>
            <th><?php echo lang('Main.xin_email');?></th>
            <th><?php echo lang('Main.dashboard_xin_status');?></th>
            <th><?php echo lang('Recruitment.xin_cover_letter');?></th>
            <th><?php echo lang('Recruitment.xin_apply_date');?></th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
