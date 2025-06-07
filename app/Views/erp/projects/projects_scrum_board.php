<?php
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\ConstantsModel;
use App\Models\ProjectsModel;
//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$ProjectsModel = new ProjectsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $usession['sup_user_id'];

$curl = curl_init();
$url = "http://103.104.73.221:3000/api/V1/global/lead?userId=$user_id";

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url,
    CURLOPT_HTTPGET => true,
]);

$response_apply_data = curl_exec($curl);

if (curl_errno($curl)) {
    $applyExpertData = []; 
} else {
    $rows = json_decode($response_apply_data, true)['detail']['rows'] ?? [];

    $applyExpertData = array_filter($rows, function($row) {
      return $row['status'] === 'A';
  });

  $applyExpertDataId = array_column($applyExpertData, 'expertId');
}

curl_close($curl);

if($user_info['user_type'] == 'staff'){
  
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
	$staff_projects_board = assigned_staff_projects_board($usession['sup_user_id']);
	$not_started_projects = $staff_projects_board['not_started_projects'];
  $not_started = empty($not_started_projects) ? 0 : count($not_started_projects);
	$inprogress_projects = $staff_projects_board['inprogress_projects'];
  $inprogress = empty($inprogress_projects) ? 0 : count($inprogress_projects);
	$completed_projects = $staff_projects_board['completed_projects'];
  $completed = empty($completed_projects) ? 0 : count($completed_projects);
	$cancelled_projects = $staff_projects_board['cancelled_projects'];
  $cancelled = empty($cancelled_projects) ? 0 : count($cancelled_projects);
	$hold_projects = $staff_projects_board['hold_projects'];
  $hold = empty($hold_projects) ? 0 : count($hold_projects);
} else {
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
	$not_started_projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->where('status',0)->orderBy('project_id', 'ASC')->findAll();
  $not_started = empty($not_started_projects) ? 0 : count($not_started_projects);
	$inprogress_projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->where('status',1)->orderBy('project_id', 'ASC')->findAll();
  $inprogress = empty($inprogress_projects) ? 0 : count($inprogress_projects);
	$completed_projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->where('status',2)->orderBy('project_id', 'ASC')->findAll();
  $completed = empty($completed_projects) ? 0 : count($completed_projects);
	$cancelled_projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->where('status',3)->orderBy('project_id', 'ASC')->findAll();
  $cancelled = empty($cancelled_projects) ? 0 : count($cancelled_projects);
	$hold_projects = $ProjectsModel->where('company_id',$usession['sup_user_id'])->where('status',4)->orderBy('project_id', 'ASC')->findAll();
  $hold = empty($hold_projects) ? 0 : count($hold_projects);

}
?>


<div class="d-flex justify-content-end mt-2">
  <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    View
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="margin-right: 5rem !important;">
    <?php if(in_array('projects_calendar',staff_role_resource()) || $user_info['user_type'] == 'company') {?>
    <li class="nav-item clickable"> <a href="<?= site_url('erp/projects-calendar');?>" class="mb-3 nav-link"><span class="sw-icon feather icon-calendar"></span>
      <?= lang('Dashboard.xin_acc_calendar');?>
      <div class="text-muted small">
        <?= lang('Projects.xin_projects_calendar');?>
      </div>
      </a> </li>
    <?php } ?>
    <?php if(in_array('projects_sboard',staff_role_resource()) || $user_info['user_type'] == 'company') {?>
    <li class="nav-item clickable"> <a href="<?= site_url('erp/projects-scrum-board');?>" class="mb-3 nav-link"><span class="sw-icon fas fa-tasks"></span>
      <?= lang('Dashboard.xin_projects_scrm_board');?>
      <div class="text-muted small">
        <?= lang('Main.xin_view');?>
        <?= lang('Projects.xin_projects_kanban_board');?>
      </div>
      </a> </li>
    <?php } ?>
    </div>
  </div>
</div>
<?php if(in_array('project1',staff_role_resource()) || in_array('projects_calendar',staff_role_resource()) || in_array('projects_sboard',staff_role_resource()) || $user_info['user_type'] == 'company') {?>

<hr class="border-light m-0 mb-3">
<?php } ?>
<div class="row">
  <div class="col-xl-3 col-md-6">
    <div class="card feed-card">
      <div class="card-body p-t-0 p-b-0">
        <div class="row">
          <div class="col-4 bg-success border-feed"> <i class="fas fa-user-tie f-40"></i> </div>
          <div class="col-8">
            <div class="p-t-25 p-b-25">
              <h2 class="f-w-400 m-b-10">
                <?= $completed;?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total');?>
                <span class="text-success f-w-400">
                 <?= lang('Projects.xin_completed');?>
                </span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card feed-card">
      <div class="card-body p-t-0 p-b-0">
        <div class="row">
          <div class="col-4 bg-primary border-feed"> <i class="fas fa-wallet f-40"></i> </div>
          <div class="col-8">
            <div class="p-t-25 p-b-25">
              <h2 class="f-w-400 m-b-10">
                <?= $inprogress;?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total');?>
                <span class="text-primary f-w-400">
                <?= lang('Projects.xin_in_progress');?>
                </span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card feed-card">
      <div class="card-body p-t-0 p-b-0">
        <div class="row">
          <div class="col-4 bg-info border-feed"> <i class="fas fa-sitemap f-40"></i> </div>
          <div class="col-8">
            <div class="p-t-25 p-b-25">
              <h2 class="f-w-400 m-b-10">
                <?= $not_started;?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total');?>
                <span class="text-info f-w-400">
                <?= lang('Projects.xin_not_started');?>
                </span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card feed-card">
      <div class="card-body p-t-0 p-b-0">
        <div class="row">
          <div class="col-4 bg-danger border-feed"> <i class="fas fa-users f-40"></i> </div>
          <div class="col-8">
            <div class="p-t-25 p-b-25">
              <h2 class="f-w-400 m-b-10">
                <?= $hold;?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total');?>
                <span class="text-danger f-w-400">
                <?= lang('Projects.xin_project_hold');?>
                </span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  
  .form-control {
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    color: #333;
    font-size: 1rem;
    transition: all 0.3s ease; 
  }
  .form-control:focus {
    background-color: #e0e0e0; 
    border-color: #007bff; 
    box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
  }
  .form-control option {
    background-color: #fff;
    color: #333; 
  }

</style>

<?php 
  $session = \Config\Services::session();
  $filter_data = $session->get('project_data'); 
  $selected_assigned_to = $filter_data['project_user'] ?? '';
  $selected_status = $filter_data['project_status'] ?? '';
?>

<div class="d-flex justify-content-end mt-2">
  <?php if($user_info['user_type'] == 'staff') {

    $filters = $ProjectsModel->where('company_id', $user_info['company_id'])
        ->groupStart()
        ->where('added_by', $usession['sup_user_id'])
        ->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
        ->groupEnd()
        ->findAll();

    $assigned_user_ids = [];
    foreach ($filters as $project) {
        $assigned_to = explode(',', $project['assigned_to']);
        $assigned_user_ids = array_merge($assigned_user_ids, $assigned_to);
    }
    $assigned_user_ids = array_unique($assigned_user_ids);
  ?>
    <div class="mr-3">
      <select class="form-control staff" name="expert_to" id="expert_to_filter">
            <option value=""><?php echo "Select Experts"; ?></option>
            <?php foreach($applyExpertData as $staff) { ?>
                <option value="<?= $staff['expertId'] ?>" >
                    <?= $staff['expertFullName'] ?>
                </option>
            <?php } ?>
      </select>
    </div>
    <div class="mr-3">
            <select class="form-control staff" name="assigned_to" id="assigned_to_filter">
                <option value=""><?php echo "Select Team"; ?></option>
                <?php foreach($staff_info as $staff) { 
                    if (in_array($staff['user_id'], $assigned_user_ids)) {
                        $selected = ($staff['user_id'] == $selected_assigned_to) ? 'selected' : ''; ?>
                        <option value="<?= $staff['user_id'] ?>" <?= $selected ?>>
                            <?= $staff['first_name'].' '.$staff['last_name'] ?>
                        </option>
                    <?php }
                } ?>
            </select>
    </div>
    <div class="mr-3">
          <select class="form-control" name="status" id="status_filter">
                <option value=""><?php echo "All Status"; ?></option>
                <option value="1" <?= ($selected_status == '1') ? 'selected' : ''; ?>><?= lang('Projects.xin_in_progress'); ?></option>
                <option value="0" <?= ($selected_status == '0') ? 'selected' : ''; ?>><?= lang('Projects.xin_not_started'); ?></option>
                <option value="2" <?= ($selected_status == '2') ? 'selected' : ''; ?>><?= lang('Projects.xin_completed'); ?></option>
                <option value="3" <?= ($selected_status == '3') ? 'selected' : ''; ?>><?= lang('Projects.xin_project_cancelled'); ?></option>
                <option value="4" <?= ($selected_status == '4') ? 'selected' : ''; ?>><?= lang('Projects.xin_project_hold'); ?></option>
          </select>
    </div>
  <?php }else { ?>
    <div class="mr-3">
      <select class="form-control staff" name="expert_to" id="expert_to_filter">
            <option value=""><?php echo "Select Experts"; ?></option>
            <?php foreach($applyExpertData as $staff) { ?>
                <option value="<?= $staff['expertId'] ?>" >
                    <?= $staff['expertFullName'] ?>
                </option>
            <?php } ?>
      </select>
    </div>
    <div class="mr-3">
      <select class="form-control staff" name="assigned_to" id="assigned_to_filter">
            <option value=""><?php echo "Select Team"; ?></option>
            <?php foreach($staff_info as $staff) { 
                $selected = ($staff['user_id'] == $selected_assigned_to) ? 'selected' : ''; ?>
                <option value="<?= $staff['user_id'] ?>" <?= $selected ?>>
                    <?= $staff['first_name'].' '.$staff['last_name'] ?>
                </option>
            <?php } ?>
      </select>
    </div>
    <div class="mr-3">
        <select class="form-control" name="status" id="status_filter">
            <option value=""><?php echo "All Status"; ?></option>
            <option value="1" <?= ($selected_status == '1') ? 'selected' : ''; ?>><?= lang('Projects.xin_in_progress'); ?></option>
            <option value="0" <?= ($selected_status == '0') ? 'selected' : ''; ?>><?= lang('Projects.xin_not_started'); ?></option>
            <option value="2" <?= ($selected_status == '2') ? 'selected' : ''; ?>><?= lang('Projects.xin_completed'); ?></option>
            <option value="3" <?= ($selected_status == '3') ? 'selected' : ''; ?>><?= lang('Projects.xin_project_cancelled'); ?></option>
            <option value="4" <?= ($selected_status == '4') ? 'selected' : ''; ?>><?= lang('Projects.xin_project_hold'); ?></option>
        </select>
    </div>
  <?php } ?>
</div>


<?php if(in_array('project1',staff_role_resource()) || in_array('projects_calendar',staff_role_resource()) || in_array('projects_sboard',staff_role_resource()) || $user_info['user_type'] == 'company') {?>

<hr class="border-light m-0 mb-3">
<?php } ?>
<div class="form-row">
  <!-- Not Started -->
  <div class="col-md">
    <div class="card mb-3">
      <h6 class="card-header">
        <i class="ion ion-md-football text-info"></i> &nbsp;
        <?= lang('Projects.xin_not_started');?>
      </h6>
      <div class="kanban-box first-notstarted px-2 pt-2" id="first-notstarted" data-status="0">
        <?php foreach($not_started_projects as $ntprojects): ?>
          <?php if($ntprojects['status'] == 0): ?>
            <div class="ui-bordered notstarted_<?php echo $ntprojects['project_id'];?> p-2 mb-2"
                data-id="<?php echo $ntprojects['project_id'];?>"
                data-status="0"
                data-assigned-to="<?php echo htmlspecialchars($ntprojects['assigned_to']);?>"
                data-experted-to="<?php echo htmlspecialchars($ntprojects['expert_to']);?>">
              <a target="_blank" href="<?php echo site_url('erp/project-detail').'/'.uencode($ntprojects['project_id']);?>">
                <?php echo htmlspecialchars($ntprojects['title']);?>
              </a>
              <div><small class="text-muted">
                <?= lang('Projects.xin_completed');?>
                <?php echo $ntprojects['project_progress'];?>%
              </small></div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo get_progress_class($ntprojects['project_progress']);?>" style="width: <?php echo $ntprojects['project_progress'];?>%"></div>
              </div>
              <div class="text-muted small mb-1 mt-2">
                <?= lang('Projects.xin_project_users');?>
              </div>
              <div class="d-flex flex-wrap mt-1">
                <?= multi_user_profile_photo(explode(',', $ntprojects['assigned_to']));?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if(in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-center py-2">
        <!-- projects-grid -->
          <a href="<?= site_url('erp/projects-list');?>" class="edit-data add-task">
            <?= lang('Projects.xin_add_project');?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- In progess  -->

  <div class="col-md">
    <div class="card mb-3">
      <h6 class="card-header">
        <i class="ion ion-md-football text-info"></i> &nbsp;
        <?= lang('Projects.xin_in_progress');?>
      </h6>
      <div class="kanban-box first-inprogress px-2 pt-2" id="first-inprogress" data-status="1">
        <?php foreach($inprogress_projects as $ntprojects): ?>
          <?php if($ntprojects['status'] == 1): ?>
            <?php
              $cc = explode(',', $ntprojects['assigned_to']);
              $progress_class = get_progress_class($ntprojects['project_progress']);
            ?>
            <div class="ui-bordered in-progress_<?php echo $ntprojects['project_id'];?> p-2 mb-2"
                data-id="<?php echo $ntprojects['project_id'];?>"
                data-status="1"
                data-assigned-to="<?php echo htmlspecialchars(implode(',', $cc));?>"
                data-experted-to="<?php echo htmlspecialchars($ntprojects['expert_to']);?>">
              <a target="_blank" href="<?php echo site_url('erp/project-detail').'/'.uencode($ntprojects['project_id']);?>">
                <?php echo htmlspecialchars($ntprojects['title']);?>
              </a>
              <div><small class="text-muted">
                <?= lang('Projects.xin_completed');?>
                <?php echo $ntprojects['project_progress'];?>%
              </small></div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class;?>" style="width: <?php echo $ntprojects['project_progress'];?>%"></div>
              </div>
              <div class="text-muted small mb-1 mt-2">
                <?= lang('Projects.xin_project_users');?>
              </div>
              <div class="d-flex flex-wrap mt-1">
                <?= multi_user_profile_photo($cc);?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if(in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-center py-2">
          <a href="<?= site_url('erp/projects-list');?>" class="edit-data add-task">
            <?= lang('Projects.xin_add_project');?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <!-- Completed Projects -->
  <div class="col-md">
    <div class="card mb-3">
      <h6 class="card-header">
        <i class="ion ion-md-football text-info"></i> &nbsp;
        <?= lang('Projects.xin_completed');?>
      </h6>
      <div class="kanban-box first-completed px-2 pt-2" id="first-completed" data-status="2">
        <?php foreach($completed_projects as $ntprojects): ?>
          <?php if($ntprojects['status'] == 2): ?>
            <?php
              $cc = explode(',', $ntprojects['assigned_to']);
              $progress_class = get_progress_class($ntprojects['project_progress']);
            ?>
            <div class="ui-bordered complete_<?php echo $ntprojects['project_id'];?> p-2 mb-2"
                data-id="<?php echo $ntprojects['project_id'];?>"
                data-status="2"
                data-assigned-to="<?php echo htmlspecialchars(implode(',', $cc));?>"
                data-experted-to="<?php echo htmlspecialchars($ntprojects['expert_to']);?>">
              <a target="_blank" href="<?php echo site_url('erp/project-detail').'/'.uencode($ntprojects['project_id']);?>">
                <?php echo htmlspecialchars($ntprojects['title']);?>
              </a>
              <div><small class="text-muted">
                <?= lang('Projects.xin_completed');?>
                <?php echo $ntprojects['project_progress'];?>%
              </small></div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class;?>" style="width: <?php echo $ntprojects['project_progress'];?>%"></div>
              </div>
              <div class="text-muted small mb-1 mt-2">
                <?= lang('Projects.xin_project_users');?>
              </div>
              <div class="d-flex flex-wrap mt-1">
                <?= multi_user_profile_photo($cc);?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if(in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-center py-2">
          <a href="<?= site_url('erp/projects-list');?>" class="edit-data add-task">
            <?= lang('Projects.xin_add_project');?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <!-- Cancelled Projects -->
  <div class="col-md">
    <div class="card mb-3">
      <h6 class="card-header">
        <i class="ion ion-md-football text-info"></i> &nbsp;
        <?= lang('Projects.xin_project_cancelled');?>
      </h6>
      <div class="kanban-box first-cancelled px-2 pt-2" id="first-cancelled" data-status="3">
        <?php foreach($cancelled_projects as $ntprojects): ?>
          <?php if($ntprojects['status'] == 3): ?>
            <?php
              $cc = explode(',', $ntprojects['assigned_to']);
              $progress_class = get_progress_class($ntprojects['project_progress']);
            ?>
            <div class="ui-bordered cancelled_<?php echo $ntprojects['project_id'];?> p-2 mb-2"
                data-id="<?php echo $ntprojects['project_id'];?>"
                data-status="3"
                data-assigned-to="<?php echo htmlspecialchars(implode(',', $cc));?>"
                data-experted-to="<?php echo htmlspecialchars($ntprojects['expert_to']);?>">
              <a target="_blank" href="<?php echo site_url('erp/project-detail').'/'.uencode($ntprojects['project_id']);?>">
                <?php echo htmlspecialchars($ntprojects['title']);?>
              </a>
              <div><small class="text-muted">
                <?= lang('Projects.xin_completed');?>
                <?php echo $ntprojects['project_progress'];?>%
              </small></div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class;?>" style="width: <?php echo $ntprojects['project_progress'];?>%"></div>
              </div>
              <div class="text-muted small mb-1 mt-2">
                <?= lang('Projects.xin_project_users');?>
              </div>
              <div class="d-flex flex-wrap mt-1">
                <?= multi_user_profile_photo($cc);?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if(in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-center py-2">
          <a href="<?= site_url('erp/projects-list');?>" class="edit-data add-task">
            <?= lang('Projects.xin_add_project');?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>


  <!-- On Hold Projects -->
  <div class="col-md">
    <div class="card mb-3">
      <h6 class="card-header">
        <i class="ion ion-md-football text-info"></i> &nbsp;
        <?= lang('Projects.xin_project_hold');?>
      </h6>
      <div class="kanban-box first-hold px-2 pt-2" id="first-hold" data-status="4">
        <?php foreach($hold_projects as $ntprojects): ?>
          <?php if($ntprojects['status'] == 4): ?>
            <?php
              $cc = explode(',', $ntprojects['assigned_to']);
              $progress_class = get_progress_class($ntprojects['project_progress']);
            ?>
            <div class="ui-bordered hold_<?php echo $ntprojects['project_id'];?> p-2 mb-2" 
                data-id="<?php echo $ntprojects['project_id'];?>" 
                data-status="4" 
                data-assigned-to="<?php echo htmlspecialchars(implode(',', $cc));?>"
                data-experted-to="<?php echo htmlspecialchars($ntprojects['expert_to']);?>" 
                id="hold_<?php echo $ntprojects['project_id'];?>">
              <a target="_blank" href="<?php echo site_url('erp/project-detail').'/'.uencode($ntprojects['project_id']);?>">
                <?php echo htmlspecialchars($ntprojects['title']);?>
              </a>
              <div><small class="text-muted">
                <?= lang('Projects.xin_completed');?>
                <?php echo $ntprojects['project_progress'];?>%
              </small></div>
              <div class="progress" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class;?>" style="width: <?php echo $ntprojects['project_progress'];?>%"></div>
              </div>
              <div class="text-muted small mb-1 mt-2">
                <?= lang('Projects.xin_project_users');?>
              </div>
              <div class="d-flex flex-wrap mt-1">
                <?= multi_user_profile_photo($cc);?>
              </div>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <?php if(in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-center py-2">
          <a href="<?= site_url('erp/projects-list');?>" class="edit-data add-task">
            <?= lang('Projects.xin_add_project');?>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>


</div>
  <?php
    function get_progress_class($progress) {
        if($progress <= 20) {
            return 'bg-danger';
        } elseif($progress > 20 && $progress <= 50) {
            return 'bg-warning';
        } elseif($progress > 50 && $progress <= 75) {
            return 'bg-info';
        } else {
            return 'bg-success';
        }
    }
  ?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const assignedToFilter = document.getElementById("assigned_to_filter");
    const statusFilter = document.getElementById("status_filter");
    const expertFilter = document.getElementById("expert_to_filter");

    function filterTasks() {
        const assignedTo = assignedToFilter.value;
        const status = statusFilter.value;
        const expertTo = expertFilter.value;

        const allTasks = document.querySelectorAll(".kanban-box > div.ui-bordered");

        allTasks.forEach(task => {
            const taskAssignedTo = task.getAttribute("data-assigned-to");
            const taskStatus = task.getAttribute("data-status");
            const taskExpert = task.getAttribute("data-experted-to");

            let isVisible = true;

            if (assignedTo && !taskAssignedTo.split(',').includes(assignedTo)) {
                isVisible = false;
            }
            if (status && taskStatus !== status) {
                isVisible = false;
            }
            if (expertTo && !taskExpert.split(',').includes(expertTo)) {
                isVisible = false;
            }

            task.style.display = isVisible ? "block" : "none";
        });
    }

    assignedToFilter.addEventListener("change", filterTasks);
    statusFilter.addEventListener("change", filterTasks);
    expertFilter.addEventListener("change", filterTasks);

    filterTasks();
});
</script>

