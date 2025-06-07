<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\TrackgoalsModel;
use App\Models\TasksModel;
use App\Models\TravelModel;
use App\Models\TrainingModel;
use App\Models\AwardsModel;
use App\Models\ProjectsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$TasksModel = new TasksModel();
$TravelModel = new TravelModel();
$TrainingModel = new TrainingModel();
$AwardsModel = new AwardsModel();
$ProjectsModel = new ProjectsModel();
$ConstantsModel = new ConstantsModel();
$TrackgoalsModel = new TrackgoalsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$request = \Config\Services::request();
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'] ?? 0)->first();
$locale = service('request')->getLocale();

$segment_id = $request->getUri()->getSegment(3);
$ifield_id = udecode($segment_id);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'] ?? 0)->first();
if ($user_info['user_type'] == 'staff') {
  $company_id = $user_info['company_id'] ?? 0;
  $user_id = $user_info['user_id'] ?? 0;

  $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->where('is_active', 1)->findAll();
  $training = assigned_staff_training($usession['sup_user_id'] ?? 0);

  $projects = $ProjectsModel->where('company_id', $user_info['company_id'] ?? 0)->groupStart()
    ->where('added_by', $user_id)
    ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')
    ->groupEnd()->findAll();

  $projectstatus = $ProjectsModel->where('company_id', $user_info['company_id'] ?? 0)->where('status', '2')->findAll();
  $completed_projects = count($projectstatus);
  $total_projects = count($projects);
  $progress_percentage = ($total_projects > 0) ? round(($completed_projects / $total_projects) * 100, 2) : 0;

  $tasks = $TasksModel->where('company_id', $user_info['company_id'] ?? 0)->groupStart()
    ->where('created_by', $user_id)
    ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')
    ->groupEnd()->orderBy('task_id', 'ASC')->findAll();

  $awards = $AwardsModel->where('employee_id', $user_info['user_id'] ?? 0)->orderBy('award_id', 'ASC')->findAll();
  $travel = $TravelModel->where('employee_id', $user_info['user_id'] ?? 0)->orderBy('travel_id', 'ASC')->findAll();
  $all_tracking_types = $ConstantsModel->where('company_id', $user_info['company_id'] ?? 0)->where('type', 'goal_type')->orderBy('constants_id', 'ASC')->findAll();
  $track_data = $TrackgoalsModel->where('company_id', $user_info['company_id'] ?? 0)->where('tracking_id', $ifield_id)->first() ?? [];
  $type_data = $ConstantsModel->where('company_id', $user_info['company_id'] ?? 0)
    ->where('constants_id', $track_data['tracking_type_id'] ?? 0)
    ->where('type', 'goal_type')
    ->first() ?? [];
} else {
  $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'] ?? 0)->where('user_type', 'staff')->where('is_active', 1)->findAll();
  $tasks = $TasksModel->where('company_id', $usession['sup_user_id'] ?? 0)->orderBy('task_id', 'ASC')->findAll();
  $projects = $ProjectsModel->where('company_id', $usession['sup_user_id'] ?? 0)->findAll();
  $projectstatus = $ProjectsModel->where('company_id', $usession['sup_user_id'] ?? 0)->where('status', '2')->findAll();
  $completed_projects = count($projectstatus);
  $total_projects = count($projects);
  $progress_percentage = ($total_projects > 0) ? round(($completed_projects / $total_projects) * 100, 2) : 0;

  $awards = $AwardsModel->where('company_id', $usession['sup_user_id'] ?? 0)->orderBy('award_id', 'ASC')->findAll();
  $travel = $TravelModel->where('company_id', $usession['sup_user_id'] ?? 0)->orderBy('travel_id', 'ASC')->findAll();
  $training = $TrainingModel->where('company_id', $usession['sup_user_id'] ?? 0)->orderBy('training_id', 'ASC')->findAll();
  $all_tracking_types = $ConstantsModel->where('company_id', $usession['sup_user_id'] ?? 0)->where('type', 'goal_type')->orderBy('constants_id', 'ASC')->findAll();
  $track_data = $TrackgoalsModel->where('company_id', $usession['sup_user_id'] ?? 0)->where('tracking_id', $ifield_id)->first() ?? [];

  if (!empty($track_data)) {
    $type_data = $ConstantsModel->where('company_id', $usession['sup_user_id'] ?? 0)
      ->where('constants_id', $track_data['tracking_type_id'] ?? 0)
      ->where('type', 'goal_type')
      ->first() ?? [];
    $goal_work = isset($track_data['goal_work']) ? unserialize($track_data['goal_work']) : [];
  } else {
    $type_data = [];
    $goal_work = [];
  }
}

$get_type = '';
?>

<div class="row">
  <!-- [ trackgoal-detail-right ] start -->
  <div class="col-lg-12">
    <div class="bg-light card mb-2">
      <div class="card-body">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
          <?php if (in_array('tracking6', staff_role_resource()) || ($user_info['user_type'] ?? '') == 'company') : ?>
            <li class="nav-item">
              <a class="nav-link active" id="pills-overview-tab" data-toggle="pill" href="#pills-overview" role="tab" aria-controls="pills-overview" aria-selected="true">
                <?= lang('Main.xin_overview'); ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if (in_array('tracking3', staff_role_resource()) || ($user_info['user_type'] ?? '') == 'company') : ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-edit-tab" data-toggle="pill" href="#pills-edit" role="tab" aria-controls="pills-edit" aria-selected="false">
                <?= lang('Main.xin_edit'); ?>
              </a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" id="pills-work-tab" data-toggle="pill" href="#pills-work" role="tab" aria-controls="pills-work" aria-selected="false">
              <?= lang('Main.xin_add_work'); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5><i class="feather icon-lock mr-1"></i><?= lang('Performance.xin_goal_details'); ?>:
          <?= htmlspecialchars($track_data['title'] ?? ''); ?>
        </h5>
      </div>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade <?= ($get_type == '') ? 'show active' : ''; ?>" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless">
                <tbody class="text-muted">
                  <tr>
                    <td><i class="fas fa-adjust m-r-5"></i> <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>:</td>
                    <td><span><?= htmlspecialchars($type_data['category_name'] ?? ''); ?></span></td>
                  </tr>
                  <tr>
                    <td><i class="far fa-calendar-alt m-r-5"></i> <?= lang('Main.xin_subject'); ?>:</td>
                    <td><?= htmlspecialchars($track_data['subject'] ?? ''); ?></td>
                  </tr>
                  <tr>
                    <td><i class="far fa-credit-card m-r-5"></i><?= lang('Performance.xin_target'); ?>:</td>
                    <td><?= htmlspecialchars($track_data['target_achiement'] ?? ''); ?></td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-chart-line m-r-5"></i> <?= lang('Projects.dashboard_xin_progress'); ?>:</td>
                    <td>
                      <div class="btn-group"><?= lang('Projects.xin_completed'); ?>
                        <?= $progress_percentage; ?>%
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-sync-alt m-r-5"></i> <?= lang('Projects.xin_start_date'); ?>:</td>
                    <td><?= htmlspecialchars($track_data['start_date'] ?? ''); ?></td>
                  </tr>
                  <tr>
                    <td><i class="fas fa-sync-alt m-r-5"></i> <?= lang('Projects.xin_end_date'); ?>:</td>
                    <td><?= htmlspecialchars($track_data['end_date'] ?? ''); ?></td>
                  </tr>
                  <tr>
                    <?php if (!empty($track_data)) : ?>
                      <?php if (($track_data['for'] ?? '') == 'all') : ?>
                        <td><i class="fas fa-users m-r-5"></i> <?= "For"; ?>:</td>
                        <td><?= htmlspecialchars($track_data['for'] ?? ''); ?></td>
                      <?php else : ?>
                        <td><i class="fas fa-user m-r-5"></i> <?= "For"; ?>:</td>
                        <td><?= htmlspecialchars($track_data['for'] ?? ''); ?></td>
                      <?php endif; ?>
                    <?php endif; ?>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="m-b-20 m-t-20">
              <h6><?= lang('Main.xin_related_work') . (!empty($track_data['tracking_type_id']) ? htmlspecialchars($track_data['tracking_type_id']) : ''); ?></h6>
              <hr>
              <div class="table-responsive">
                <table class="table table-borderless">
                  <tbody class="text-muted">
                    <tr>
                      <td><i class="fas fa-layer-group m-r-5"></i> <?= lang('Projects.xin_project'); ?>:</td>
                      <td>
                        <?php foreach ($projects as $iprojects) : ?>
                          <?php if (($goal_work['project'] ?? '') == $iprojects['project_id']) : ?>
                            <?= htmlspecialchars($iprojects['title']); ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fas fa-edit m-r-5"></i> <?= lang('Projects.xin_task'); ?>:</td>
                      <td>
                        <?php foreach ($tasks as $_task) : ?>
                          <?php if (($goal_work['task'] ?? '') == $_task['task_id']) : ?>
                            <?= htmlspecialchars($_task['task_name']); ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fas fa-award m-r-5"></i> <?= lang('Dashboard.left_award'); ?>:</td>
                      <td>
                        <?php foreach ($awards as $_award) : ?>
                          <?php $award_cat = $ConstantsModel->where('constants_id', $_award['award_type_id'] ?? 0)
                            ->where('type', 'award_type')
                            ->first() ?? []; ?>
                          <?php if (($goal_work['award'] ?? '') == ($award_cat['constants_id'] ?? '')) : ?>
                            <?= htmlspecialchars($award_cat['category_name'] ?? ''); ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fas fa-star-of-life m-r-5"></i> <?= lang('Dashboard.left_training'); ?>:</td>
                      <td>
                        <?php foreach ($training as $_training) : ?>
                          <?php $training_cat = $ConstantsModel->where('constants_id', $_training['training_type_id'] ?? 0)
                            ->where('type', 'training_type')
                            ->first() ?? []; ?>
                          <?php if (($goal_work['training'] ?? '') == ($training_cat['constants_id'] ?? '')) : ?>
                            <?= htmlspecialchars($training_cat['category_name'] ?? ''); ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </td>
                    </tr>
                    <tr>
                      <td><i class="fas fa-globe m-r-5"></i> <?= lang('Dashboard.left_travel'); ?>:</td>
                      <td>
                        <?php foreach ($travel as $_travel) : ?>
                          <?php $travel_type = $ConstantsModel->where('constants_id', $_travel['arrangement_type'] ?? 0)
                            ->where('type', 'travel_type')
                            ->first() ?? []; ?>
                          <?php if (($goal_work['travel'] ?? '') == ($travel_type['constants_id'] ?? '')) : ?>
                            <?= htmlspecialchars($travel_type['category_name'] ?? ''); ?>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="m-b-20 m-t-20">
              <h6><?= lang('Main.xin_description'); ?></h6>
              <hr>
              <?= !empty($track_data['description']) ? html_entity_decode($track_data['description']) : ''; ?>
            </div>
          </div>
        </div>

        <!-- Edit Tab Content -->
        <div class="tab-pane fade <?php if ($get_type == 'edit'): ?>show active<?php endif; ?>" id="pills-edit"
          role="tabpanel" aria-labelledby="pills-overview-tab">
          <?php $attributes = array('name' => 'update_goal_tracking', 'id' => 'update_goal_tracking', 'autocomplete' => 'off', 'class' => 'form-hrm'); ?>
          <?php $hidden = array('token' => isset($segment_id) ? $segment_id : ''); ?>
          <?php echo form_open('erp/trackgoals/update_goal_tracking', $attributes, $hidden); ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Dashboard.xin_hr_goal_tracking_type'); ?> <span
                      class="text-danger">*</span></label>
                  <select class="form-control" name="tracking_type" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Dashboard.xin_hr_goal_tracking_type'); ?>">
                    <option value=""></option>
                    <?php foreach (isset($all_tracking_types) ? $all_tracking_types : array() as $tracking_type) : ?>
                      <option value="<?= $tracking_type['constants_id']; ?>" <?= ((isset($track_data['tracking_type_id']) && $track_data['tracking_type_id'] == $tracking_type['constants_id']) ? 'selected="selected"' : ''); ?>>
                        <?= htmlspecialchars($tracking_type['category_name']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="xin_subject"><?php echo lang('Main.xin_subject'); ?> <span
                      class="text-danger">*</span></label>
                  <input class="form-control" placeholder="<?php echo lang('Main.xin_subject'); ?>" name="subject"
                    type="text" value="<?= isset($track_data['subject']) ? htmlspecialchars($track_data['subject']) : ''; ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="target_achiement"><?php echo lang('Performance.xin_hr_target_achiement'); ?> <span
                      class="text-danger">*</span></label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i
                          class="fas fa-vector-square"></i></span></div>
                    <input class="form-control" placeholder="<?php echo lang('Performance.xin_hr_target_achiement'); ?>"
                      name="target_achiement" type="text" value="<?= isset($track_data['target_achiement']) ? htmlspecialchars($track_data['target_achiement']) : ''; ?>">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <input type="hidden" id="completed_projects" value="<?= isset($projectstatus) ? count($projectstatus) : 0; ?>">
                  <input type="hidden" id="total_projects" value="<?= isset($projects) ? count($projects) : 0; ?>">
                  <input type="hidden" id="progres_val" name="progres_val"
                    value="0">
                  <label for="progress">
                    <?= lang('Projects.dashboard_xin_progress'); ?>
                  </label>
                  <div class="progress">
                    <div id="progress-bar" class="progress-bar bg-blue" role="progressbar" aria-valuenow="0"
                      aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                      0%
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="for_id">For <span class="text-danger">*</span></label>
                  <select class="form-control" id="for_id" name="for" data-plugin="select_hrm" data-placeholder="For"
                    onchange="toggleEmployeeField()">
                    <option value="">-- Select --</option>
                    <option value="self" <?= (isset($track_data['for']) && $track_data['for'] === 'self' ? 'selected="selected"' : '') ?>>Self</option>
                    <option value="all" <?= (isset($track_data['for']) && $track_data['for'] === 'all' ? 'selected="selected"' : '') ?>>Employee</option>
                  </select>
                </div>
              </div>
              <?php $employee_id = isset($track_data['employee_id']) ? explode(',', $track_data['employee_id']) : array(); ?>
              <div class="col-md-4" id="employee_ajax"
                style="display: <?= (isset($track_data['for']) && $track_data['for'] === 'all') ? 'block' : 'none'; ?>;">
                <div class="form-group">
                  <label for="employee_id">Employee List <span class="text-danger">*</span></label>
                  <select class="form-control" id="employee_id" name="employee_id[]" multiple="multiple"
                    data-plugin="select_hrm" data-placeholder="Employee List">
                    <option value=""></option>
                    <?php foreach (isset($staff_info) ? $staff_info : array() as $staff): ?>
                      <option value="<?= htmlspecialchars($staff['user_id']) ?>" <?= in_array($staff['user_id'], $employee_id) ? 'selected="selected"' : '' ?>>
                        <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="goal_rating">
                    <?= lang('Performance.xin_goal_rating'); ?>
                  </label>
                  <select class="star-rating " name="goal_rating" autocomplete="off">
                    <option value=""></option>
                    <option value="1" <?= (isset($track_data['goal_rating']) && $track_data['goal_rating'] == 1) ? 'selected="selected"' : '' ?>>1</option>
                    <option value="2" <?= (isset($track_data['goal_rating']) && $track_data['goal_rating'] == 2) ? 'selected="selected"' : '' ?>>2</option>
                    <option value="3" <?= (isset($track_data['goal_rating']) && $track_data['goal_rating'] == 3) ? 'selected="selected"' : '' ?>>3</option>
                    <option value="4" <?= (isset($track_data['goal_rating']) && $track_data['goal_rating'] == 4) ? 'selected="selected"' : '' ?>>4</option>
                    <option value="5" <?= (isset($track_data['goal_rating']) && $track_data['goal_rating'] == 5) ? 'selected="selected"' : '' ?>>5</option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="status">
                    <?= lang('Main.dashboard_xin_status'); ?>
                  </label>
                  <select name="status" class="form-control" data-plugin="select_hrm"
                    data-placeholder="<?= lang('Main.dashboard_xin_status'); ?>...">
                    <option value="0" <?= (isset($track_data['goal_status']) && $track_data['goal_status'] == '0' ? 'selected' : '') ?>>
                      <?= lang('Projects.xin_not_started'); ?>
                    </option>
                    <option value="1" <?= (isset($track_data['goal_status']) && $track_data['goal_status'] == '1' ? 'selected' : '') ?>>
                      <?= lang('Projects.xin_in_progress'); ?>
                    </option>
                    <option value="2" <?= (isset($track_data['goal_status']) && $track_data['goal_status'] == '2' ? 'selected' : '') ?>>
                      <?= lang('Projects.xin_completed'); ?>
                    </option>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span
                      class="text-danger">*</span></label>
                  <div class="input-group">
                    <input class="form-control date" placeholder="<?php echo lang('Projects.xin_start_date'); ?>"
                      name="start_date" type="text" value="<?= isset($track_data['start_date']) ? $track_data['start_date'] : ''; ?>">
                    <div class="input-group-append"><span class="input-group-text"><i
                          class="fas fa-calendar-alt"></i></span></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span
                      class="text-danger">*</span></label>
                  <div class="input-group">
                    <input class="form-control date" placeholder="<?php echo lang('Projects.xin_end_date'); ?>"
                      name="end_date" type="text" value="<?= isset($track_data['end_date']) ? $track_data['end_date'] : ''; ?>">
                    <div class="input-group-append"><span class="input-group-text"><i
                          class="fas fa-calendar-alt"></i></span></div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="description"><?php echo lang('Main.xin_description'); ?></label>
                  <textarea class="form-control editor" placeholder="<?php echo lang('Main.xin_description'); ?>"
                    name="description" rows="4"><?= isset($track_data['description']) ? $track_data['description'] : ''; ?></textarea>
                </div>
              </div>
            </div>
          </div>
          <?php if ((isset($user_info['user_type']) && $user_info['user_type'] == 'company') || (isset($staff_role_resource) && in_array('tracking5', $staff_role_resource))) { ?>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Performance.xin_update_goal'); ?>
              </button>
            </div>
          <?php } ?>

          <?= form_close(); ?>
        </div>

        <!-- Work Tab Content -->
        <div class="tab-pane fade" id="pills-work" role="tabpanel" aria-labelledby="pills-work-tab">
          <?php $attributes = array('name' => 'add_work', 'id' => 'add_work', 'autocomplete' => 'off', 'class' => 'form-hrm'); ?>
          <?php $hidden = array('token' => $segment_id); ?>
          <?php echo form_open('erp/trackgoals/add_work', $attributes, $hidden); ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Projects.xin_project'); ?> <a
                      href="<?= site_url('erp/projects-grid'); ?>"><i class="fas fa-plus"></i></a> </label>
                  <select class="form-control" name="goal_work[project]" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Projects.xin_project'); ?>">
                    <option value=""></option>
                    <?php foreach ($projects as $iprojects) { ?>
                      <option value="<?= $iprojects['project_id'] ?>">
                        <?= $iprojects['title'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Projects.xin_task'); ?> <a
                      href="<?= site_url('erp/tasks-grid'); ?>"><i class="fas fa-plus"></i></a></label>
                  <select class="form-control" name="goal_work[task]" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Projects.xin_task'); ?>">
                    <option value=""></option>
                    <?php foreach ($tasks as $_task) { ?>
                      <option value="<?= $_task['task_id'] ?>">
                        <?= $_task['task_name'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Dashboard.left_award'); ?> <a
                      href="<?= site_url('erp/awards-list'); ?>"><i class="fas fa-plus"></i></a></label>
                  <select class="form-control" name="goal_work[award]" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Dashboard.left_award'); ?>">
                    <option value=""></option>
                    <?php foreach ($awards as $_award) { ?>
                      <?php $award_cat = $ConstantsModel->where('constants_id', $_award['award_type_id'])->where('type', 'award_type')->first(); ?>
                      <option value="<?= $award_cat['constants_id'] ?>">
                        <?= $award_cat['category_name'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Dashboard.left_training'); ?> <a
                      href="<?= site_url('erp/training-sessions'); ?>"><i class="fas fa-plus"></i></a></label>
                  <select class="form-control" name="goal_work[training]" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Dashboard.left_training'); ?>">
                    <option value=""></option>
                    <?php foreach ($training as $_training) { ?>
                      <?php $training_cat = $ConstantsModel->where('constants_id', $_training['training_type_id'])->where('type', 'training_type')->first(); ?>
                      <option value="<?= $training_cat['constants_id'] ?>">
                        <?= $training_cat['category_name'] ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="tracking_type"><?php echo lang('Dashboard.left_travel'); ?> <a
                      href="<?= site_url('erp/business-travel'); ?>"><i class="fas fa-plus"></i></a></label>
                  <select class="form-control" name="goal_work[travel]" data-plugin="select_hrm"
                    data-placeholder="<?php echo lang('Dashboard.left_travel'); ?>">
                    <option value=""></option>
                    <?php foreach ($travel as $_travel) { ?>
                      <?php $travel_type = $ConstantsModel->where('constants_id', $_travel['arrangement_type'])->where('type', 'travel_type')->first(); ?>
                      <option value="<?= $travel_type['constants_id'] ?? '' ?>">
                        <?= $travel_type['category_name'] ?? 'Unknown' ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="alert alert-primary" role="alert">
              <?= lang('Main.xin_add_work_details'); ?>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
              <?= lang('Main.xin_add_work'); ?>
            </button>
          </div>
          <?= form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleEmployeeField() {
    const forSelect = document.getElementById('for_id');
    const employeeField = document.getElementById('employee_ajax');
    if (forSelect && employeeField) {
      employeeField.style.display = (forSelect.value === 'all') ? 'block' : 'none';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    // Initialize progress bar
    const completedProjects = parseInt(document.getElementById('completed_projects')?.value || 0);
    const totalProjects = parseInt(document.getElementById('total_projects')?.value || 0);
    const progressPercentage = (totalProjects > 0) ? Math.round((completedProjects / totalProjects) * 100) : 0;

    const progressBar = document.getElementById('progress-bar');
    if (progressBar) {
      progressBar.style.width = progressPercentage + '%';
      progressBar.setAttribute('aria-valuenow', progressPercentage);
      progressBar.textContent = progressPercentage + '%';
    }

    // Initialize date pickers
    $('.date').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true
    });
  });
</script>