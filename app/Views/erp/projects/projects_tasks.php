<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\TasksModel;
use App\Models\ProjectsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();
$TasksModel = new TasksModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$user_id = $usession['sup_user_id'];

$applyExpertData = [];
try {
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
    error_log("cURL Error: " . curl_error($curl));
  } else {
    $rows = json_decode($response_apply_data, true)['detail']['rows'] ?? [];
    $applyExpertData = array_filter($rows, function ($row) {
      return $row['status'] === 'A';
    });
    $applyExpertDataId = array_column($applyExpertData, 'expertId');
  }

  curl_close($curl);
} catch (Exception $e) {
  error_log("Exception in cURL (applyExpertData): " . $e->getMessage());
}

$expert_id = null;

if ($user_info['user_type'] == 'staff') {
  try {
    $user_id = $user_info['user_id'];
    $company_id = $user_info['company_id'];

    // Fetch expert user details via cURL
    $curl = curl_init();
    $url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id";


    curl_setopt_array($curl, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL => $url,
      CURLOPT_HTTPGET => true,
      CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
      $error_msg = curl_error($curl);
      curl_close($curl);
      error_log("cURL Error (expert-user): " . $error_msg);
      throw new Exception("cURL failed for expert-user");
    }

    $expert_user_detail = json_decode($response, true);


    if (json_last_error() !== JSON_ERROR_NONE) {
      curl_close($curl);
      error_log("JSON Decoding Error: " . json_last_error_msg());
      throw new Exception("JSON Decoding Error: " . json_last_error_msg());
    }

    curl_close($curl);
  } catch (Exception $e) {
    error_log("Exception in cURL (expert-user): " . $e->getMessage());
  }

  $expert_id = $expert_user_detail['detail']['id'] ?? null;

  // Function to build the query for task counts
  function buildTaskQuery($TasksModel, $company_id, $user_id, $status, $expert_id = null)
  {
    $builder = $TasksModel->where('company_id', $company_id)
      ->where('task_status', $status)
      ->groupStart()
      ->where('created_by', $user_id)
      ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0');

    if ($expert_id !== null) {
      $builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
    }

    $builder->groupEnd();

    return $builder->findAll();
  }

  // Retrieve staff and project information
  $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $company_id)->findAll();

  // Total tasks count
  $total_tasks = buildTaskQuery($TasksModel, $company_id, $user_id, null, $expert_id);

  // Task counts by status
  $not_started = count(buildTaskQuery($TasksModel, $company_id, $user_id, 0, $expert_id));
  $in_progress = count(buildTaskQuery($TasksModel, $company_id, $user_id, 1, $expert_id));
  $completed = count(buildTaskQuery($TasksModel, $company_id, $user_id, 2, $expert_id));
  $cancelled = count(buildTaskQuery($TasksModel, $company_id, $user_id, 3, $expert_id));
  $hold = count(buildTaskQuery($TasksModel, $company_id, $user_id, 4, $expert_id));
} else {
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->findAll();
  $total_tasks = $TasksModel->where('company_id', $usession['sup_user_id'])->orderBy('task_id', 'ASC')->countAllResults();
  $not_started = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 0)->countAllResults();
  $in_progress = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 1)->countAllResults();
  $completed = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 2)->countAllResults();
  $cancelled = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 3)->countAllResults();
  $hold = $TasksModel->where('company_id', $usession['sup_user_id'])->where('task_status', 4)->countAllResults();
}
?>

<hr class="border-light m-0 mb-3">
<div class="row">
  <div class="col-xl-3 col-md-6">
    <div class="card feed-card">
      <div class="card-body p-t-0 p-b-0">
        <div class="row">
          <div class="col-4 bg-success border-feed"> <i class="fas fa-user-tie f-40"></i> </div>
          <div class="col-8">
            <div class="p-t-25 p-b-25">
              <h2 class="f-w-400 m-b-10">
                <?= $completed; ?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total'); ?>
                <span class="text-success f-w-400">
                  <?= lang('Projects.xin_completed'); ?>
                </span>
              </p>
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
                <?= $in_progress; ?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total'); ?>
                <span class="text-primary f-w-400">
                  <?= lang('Projects.xin_in_progress'); ?>
                </span>
              </p>
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
                <?= $not_started; ?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total'); ?>
                <span class="text-info f-w-400">
                  <?= lang('Projects.xin_not_started'); ?>
                </span>
              </p>
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
                <?= $hold; ?>
              </h2>
              <p class="text-muted m-0">
                <?= lang('Main.xin_total'); ?>
                <span class="text-danger f-w-400">
                  <?= lang('Projects.xin_project_hold'); ?>
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row m-b-1 animated fadeInRight">
  <div class="col-md-12">
    <?php if (in_array('task2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
      <div id="add_form" class="collapse add-form " data-parent="#accordion" style="">
        <div class="card mb-2">
          <div id="accordion">
            <div class="card-header">
              <h5>
                <?= lang('Main.xin_add_new'); ?>
                <?= lang('Projects.xin_task'); ?>
              </h5>
              <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                  <?= lang('Main.xin_hide'); ?>
                </a> </div>
            </div>
            <?php $attributes = array('name' => 'add_tasks', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('user_id' => '0'); ?>
            <?php echo form_open('erp/add-tasks', $attributes, $hidden); ?>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="task_name"><?php echo lang('Dashboard.xin_title'); ?> <span class="text-danger">*</span></label>
                    <input class="form-control" placeholder="<?php echo lang('Dashboard.xin_title'); ?>" name="task_name" type="text" value="">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input class="form-control date" placeholder="<?php echo lang('Projects.xin_start_date'); ?>" name="start_date" type="text" value="">
                      <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <input class="form-control date" placeholder="<?php echo lang('Projects.xin_end_date'); ?>" name="end_date" type="text" value="">
                      <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="task_hour" class="control-label"><?php echo lang('Projects.xin_estimated_hour'); ?></label>
                    <div class="input-group">
                      <input class="form-control" placeholder="<?php echo lang('Projects.xin_estimated_hour'); ?>" name="task_hour" type="text" value="">
                      <div class="input-group-append"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" id="project_ajax">
                    <label for="project_ajax" class="control-label"><?php echo lang('Projects.xin_project'); ?> <span class="text-danger">*</span></label>
                    <select class="form-control" name="project_id" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project'); ?>">
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
                    <label for="summary"><?php echo lang('Main.xin_summary'); ?> <span class="text-danger">*</span></label>
                    <textarea class="form-control" placeholder="<?php echo lang('Main.xin_summary'); ?>" name="summary" cols="30" rows="1" id="summary"></textarea>
                  </div>
                </div>
                <input type="hidden" value="0" name="expert_to[]" />
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="expert_to">
                      <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                    </label>
                    <select multiple name="expert_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo "Select Experts"; ?>">
                      <option value=""></option>
                      <?php foreach ($applyExpertData as $staff) { ?>
                        <option value="<?= $staff['expertId'] ?>">
                          <?= $staff['expertFullName'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <input type="hidden" value="0" name="assigned_to[]" />
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="employee"><?php echo lang('Projects.xin_project_users'); ?></label>
                    <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project_users'); ?>">
                      <option value=""></option>
                      <?php foreach ($staff_info as $staff) { ?>
                        <option value="<?= $staff['user_id'] ?>">
                          <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="description"><?php echo lang('Projects.xin_description'); ?></label>
                    <textarea class="form-control editor" placeholder="<?php echo lang('Projects.xin_description'); ?>" name="description" id="description"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse" aria-expanded="false">
                <?= lang('Main.xin_reset'); ?>
              </button>
              &nbsp;
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
      </div>
    <?php } ?>
    <style>
      .card-header-right {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
      }

      .card-header-right .form-control {
        min-width: 125px;
      }

      .card-header-right .staff {
        max-width: 150px;
      }

      .card-header-right .project {
        max-width: 150px;
      }

      .card-header-right .btn {
        margin-left: 10px;
      }

      .card-header-right .mr-2,
      .card-header-right .mr-3 {
        margin-top: -10px;
        margin-right: 15px !important;
      }

      .select2-container .select2-selection--single .select2-selection__rendered {
        display: block;
        padding-left: 10px;
        padding-right: 85px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }
    </style>


    <?php
    $session = \Config\Services::session();
    $filter_data = $session->get('task_data');
    $selected_assigned_to = $filter_data['task_user'] ?? '';
    $selected_status = $filter_data['task_status'] ?? '';
    $selected_project = $filter_data['project'] ?? '';
    $selected_expert_to = $filter_data['task_expert'] ?? '';
    ?>
    <div class="card user-profile-list">
      <div class="card-header">
        <h5>
          <?= lang('Main.xin_list_all'); ?>
          <?= lang('Projects.xin_tasks'); ?>
        </h5>
        <div class="card-header-right">

          <?php if ($user_info['user_type'] == 'staff') {
            $filters = $TasksModel->where('company_id', $user_info['company_id'])
              ->groupStart()
              ->where('created_by', $usession['sup_user_id'])
              ->orWhere('FIND_IN_SET(' . $usession['sup_user_id'] . ', assigned_to) > 0')
              ->groupEnd()
              ->findAll();

            $assigned_user_ids = [];
            $project_ids = [];
            foreach ($filters as $task) {
              $assigned_to = explode(',', $task['assigned_to']);
              $assigned_user_ids = array_merge($assigned_user_ids, $assigned_to);
              $project_ids[] = $task['project_id'];
            }
            $assigned_user_ids = array_unique($assigned_user_ids);
            $project_ids = array_unique($project_ids);
          ?>
            <div class="mr-3">
              <select class="form-control staff" name="expert_to" id="expert_to_filter">
                <option value=""><?php echo "Select Experts"; ?></option>
                <?php foreach ($applyExpertData as $staff) { ?>
                  <option value="<?= $staff['expertId'] ?>">
                    <?= $staff['expertFullName'] ?>
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="mr-3">
              <select class="form-control project" name="project" id="project_filter">
                <option value=""><?php echo "Select Project"; ?></option>
                <?php foreach ($projects as $iprojects) {
                  if (in_array($iprojects['project_id'], $project_ids)) {
                    $selected = ($iprojects['project_id'] == $selected_project) ? 'selected' : ''; ?>
                    <option value="<?= $iprojects['project_id'] ?>" <?= $selected ?>>
                      <?= $iprojects['title'] ?>
                    </option>
                <?php }
                } ?>
              </select>
            </div>
            <div class="mr-3">
              <select class="form-control staff" name="assigned_to" id="assigned_to_filter">
                <option value=""><?php echo "Select Team"; ?></option>
                <?php foreach ($staff_info as $staff) {
                  if (in_array($staff['user_id'], $assigned_user_ids)) {
                    $selected = ($staff['user_id'] == $selected_assigned_to) ? 'selected' : ''; ?>
                    <option value="<?= $staff['user_id'] ?>" <?= $selected ?>>
                      <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
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
          <?php } else { ?>
            <div class="mr-3">
              <select class="form-control staff" name="expert_to" id="expert_to_filter">
                <option value=""><?php echo "Select Experts"; ?></option>
                <?php foreach ($applyExpertData as $staff) { ?>
                  <option value="<?= $staff['expertId'] ?>">
                    <?= $staff['expertFullName'] ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="mr-3">
              <select class="form-control project" name="project" id="project_filter">
                <option value=""><?php echo "Select Project"; ?></option>
                <?php foreach ($projects as $iprojects) {
                  $selected = ($iprojects['project_id'] == $selected_project) ? 'selected' : ''; ?>
                  <option value="<?= $iprojects['project_id'] ?>" <?= $selected ?>>
                    <?= $iprojects['title'] ?>
                  </option>
                <?php } ?>
              </select>
            </div>
            <div class="mr-3">
              <select class="form-control staff" name="assigned_to" id="assigned_to_filter">
                <option value=""><?php echo "Select Team"; ?></option>
                <?php foreach ($staff_info as $staff) {
                  $selected = ($staff['user_id'] == $selected_assigned_to) ? 'selected' : ''; ?>
                  <option value="<?= $staff['user_id'] ?>" <?= $selected ?>>
                    <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
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
          <div class="mr-2">
            <!-- tasks-grid -->
            <!-- <a href="<?= site_url() . 'erp/tasks-scrum-board'; ?>" class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="<?= lang('Dashboard.xin_projects_scrm_board'); ?>"> <i class="fas fa-th-large"></i>
            </a> -->
          </div>
          <div class="mr-2">
            <?php if (in_array('task2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
              <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
                <?= lang('Main.xin_add_new'); ?>
              </a>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th><?php echo lang('Dashboard.xin_title'); ?></th>
                <th><i class="fa fa-user"></i> <?php echo lang('Projects.xin_project_users'); ?></th>
                <th><i class="fa fa-calendar"></i> <?php echo lang('Projects.xin_start_date'); ?></th>
                <th><i class="fa fa-calendar"></i> <?php echo lang('Projects.xin_end_date'); ?></th>
                <th><?php echo lang('Projects.xin_status'); ?></th>
                <th><?php echo lang('Projects.dashboard_xin_progress'); ?></th>
                <th>Created By</th>
                <th>Project Name</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    $(document).ready(function() {
      var xin_table = $('#xin_table').DataTable({
        "bDestroy": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
          url: main_url + "tasks-data-Lists",
          type: 'GET',
          data: function(d) {
            d.project = $('#project_filter').val();
            d.status = $('#status_filter').val();
            d.assigned_to = $('#assigned_to_filter').val();
            d.expert_to = $('#expert_to_filter').val();
          }
        },
        "language": {
          "lengthMenu": dt_lengthMenu,
          "zeroRecords": dt_zeroRecords,
          "info": dt_info,
          "infoEmpty": dt_infoEmpty,
          "infoFiltered": dt_infoFiltered,
          "search": dt_search,
          "paginate": {
            "first": dt_first,
            "previous": dt_previous,
            "next": dt_next,
            "last": dt_last
          }
        },
        "fnDrawCallback": function(settings) {
          $('[data-toggle="tooltip"]').tooltip();
        }
      });

      $('#project_filter, #status_filter, #assigned_to_filter, #expert_to_filter').on('change', function() {
        xin_table.ajax.reload();
      });
    });
  });
</script>