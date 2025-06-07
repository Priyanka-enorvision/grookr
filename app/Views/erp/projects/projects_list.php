<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $usession['sup_user_id'];

// Fetch apply expert data
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
    throw new Exception('cURL Error: ' . curl_error($curl));
  } else {
    $rows = json_decode($response_apply_data, true)['detail']['rows'] ?? [];
    $applyExpertData = array_filter($rows, function ($row) {
      return $row['status'] === 'A';
    });

    $applyExpertDataId = array_column($applyExpertData, 'expertId');
  }

  curl_close($curl);
} catch (Exception $e) {
  // Handle error
  error_log($e->getMessage());
}

// Fetch expert user details for staff
$expert_id = null;




if ($session->has('entityId')) {
  $entityId = $session->get('entityId');
  if ($user_info['user_type'] == 'staff') {
    try {
      $user_id = $user_info['user_id'];
      $company_id = $user_info['company_id'];

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
        throw new Exception('cURL Error: ' . curl_error($curl));
      }

      $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if ($http_status !== 200) {
        throw new Exception("Request failed with status code: $http_status");
      }

      $expert_user_detail = json_decode($response, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON Decoding Error: " . json_last_error_msg());
      }

      if (isset($expert_user_detail['detail']['id'])) {
        $expert_id = $expert_user_detail['detail']['id'];
      } else {
        throw new Exception("Error: 'id' not found in the response detail.");
      }

      curl_close($curl);
    } catch (Exception $e) {
      // Handle error
      error_log($e->getMessage());
    }

    function getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, $status, $expert_id = null)
    {
      $builder = $ProjectsModel
        ->where('company_id', $company_id)
        ->where('status', $status)
        ->groupStart()
        ->where('added_by', $user_id)
        ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
        ->orWhere('employe_ID', $user_id);

      if ($expert_id !== null) {
        $builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) <> 0', null, false);
      }

      return $builder->groupEnd()->countAllResults();
    }

    // Get staff and clients
    $staff_info = $UsersModel
      ->where('company_id', $company_id)
      ->where('user_type', 'staff')
      ->where('is_active', 1)
      ->findAll();

    $all_clients = $UsersModel
      ->where('company_id', $company_id)
      ->where('user_type', 'customer')
      ->where('is_active', 1)
      ->findAll();

    // Get unique company IDs from projects where user is employee
    $companies_ids = array_unique(array_column(
      $ProjectsModel->select('company_id')->where('employe_ID', $user_id)->findAll(),
      'company_id'
    ));

    // Get project list with proper access control
    $project_list = $ProjectsModel
      ->groupStart()
      ->where('company_id', $company_id)
      ->orWhereIn('company_id', !empty($companies_ids) ? $companies_ids : [0])
      ->groupEnd()
      ->groupStart()
      ->where('added_by', $user_id)
      ->orGroupStart()
      ->where('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
      ->orWhere('employe_ID', $user_id)
      ->groupEnd()
      ->groupEnd()
      ->findAll();

    // Count projects
    $total_projects = $ProjectsModel
      ->where('company_id', $company_id)
      ->groupStart()
      ->where('added_by', $user_id)
      ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
      ->orWhere('employe_ID', $user_id)
      ->groupEnd()
      ->countAllResults();

      $not_started =  getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 0, $expert_id);
      $in_progress = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 1, $expert_id);
      $completed = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 2, $expert_id);
      $cancelled = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 3, $expert_id);
      $hold = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 4, $expert_id);
  } else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
    $project_list = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('entities_id', $entityId)->findAll();

    $total_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
    $not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
    $in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
    $hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
  }
} else {
  if ($user_info['user_type'] == 'staff') {

    try {
      $user_id = $user_info['user_id'];
      $company_id = $user_info['company_id'];

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
        throw new Exception('cURL Error: ' . curl_error($curl));
      }

      $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      if ($http_status !== 200) {
        throw new Exception("Request failed with status code: $http_status");
      }

      $expert_user_detail = json_decode($response, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON Decoding Error: " . json_last_error_msg());
      }

      if (isset($expert_user_detail['detail']['id'])) {
        $expert_id = $expert_user_detail['detail']['id'];
      } else {
        throw new Exception("Error: 'id' not found in the response detail.");
      }

      curl_close($curl);
    } catch (Exception $e) {
      // Handle error
      error_log($e->getMessage());
    }

    function getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, $status, $expert_id = null)
    {
      $builder = $ProjectsModel
        ->where('company_id', $company_id)
        ->where('status', $status)
        ->groupStart()
        ->where('added_by', $user_id)
        ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
        ->orWhere('employe_ID', $user_id);

      if ($expert_id !== null) {
        $builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) <> 0', null, false);
      }

      return $builder->groupEnd()->countAllResults();
    }

    // Get staff and clients
    $staff_info = $UsersModel
      ->where('company_id', $company_id)
      ->where('user_type', 'staff')
      ->where('is_active', 1)
      ->findAll();

    $all_clients = $UsersModel
      ->where('company_id', $company_id)
      ->where('user_type', 'customer')
      ->where('is_active', 1)
      ->findAll();

    // Get unique company IDs from projects where user is employee
    $companies_ids = array_unique(array_column(
      $ProjectsModel->select('company_id')->where('employe_ID', $user_id)->findAll(),
      'company_id'
    ));

    // Get project list with proper access control
    $project_list = $ProjectsModel
      ->groupStart()
      ->where('company_id', $company_id)
      ->orWhereIn('company_id', !empty($companies_ids) ? $companies_ids : [0])
      ->groupEnd()
      ->groupStart()
      ->where('added_by', $user_id)
      ->orGroupStart()
      ->where('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
      ->orWhere('employe_ID', $user_id)
      ->groupEnd()
      ->groupEnd()
      ->findAll();

    // Count projects
    $total_projects = $ProjectsModel
      ->where('company_id', $company_id)
      ->groupStart()
      ->where('added_by', $user_id)
      ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) <> 0', null, false)
      ->orWhere('employe_ID', $user_id)
      ->groupEnd()
      ->countAllResults();

    // Count projects by status
    $not_started =  getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 0, $expert_id);
    $in_progress = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 1, $expert_id);
    $completed = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 2, $expert_id);
    $cancelled = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 3, $expert_id);
    $hold = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 4, $expert_id);
  } else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
    $project_list = $ProjectsModel->where('company_id', $usession['sup_user_id'])->findAll();

    $total_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
    $not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
    $in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
    $hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
  }
}

?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<style>
  /* Adjust the modal width */
  #copyProjectModal .modal-dialog {
    max-width: 600px;
    /* Change this value to your desired width */
  }

  /* Change modal heading colors */
  #copyProjectLabel {
    color: #fff;
    /* Dark blue */
  }

  #copyProjectModal .modal-header {
    background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);

    border-radius: 6px;
    color: #fff;
    padding: 18px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    border-color: transparent;
  }

  #copyProjectModal .modal-title {
    font-weight: bold;
    /* Optional: make the title bold */
  }

  .project-summary {
    display: flex;
    justify-content: space-around;
    border-bottom: 1px solid #e0e0e0;
    padding-bottom: 10px;
    font-family: Arial, sans-serif;
  }

  .project-column {
    border-right: 1px solid #e0e0e0;
  }

  .project-column:last-child {
    border-right: none;
  }

  .project-count {
    font-size: 24px;
    font-weight: 600;
    color: #007bff;
    /* Blue color for numbers */
    margin: 0;
  }

  .project-status {
    font-size: 14px;
    color: #666;
    margin: 0;
  }

  .project-column h3,
  .project-column p {
    margin: 0;
    padding: 5px 0;
  }


  #projects-table {
    padding-top: 20px;
    width: 100%;
  }


  @media (max-width: 768px) {
    .project-column {
      border-right: none;
    }

    .project-summary {
      flex-direction: column;
    }

  }

  .border-light {
    border-color: #dcdee0 !important;
  }

  .table-projects {
    width: 100%;
    border-collapse: collapse;
    background-color: #f9f9f9;
  }

  .table-projects th,
  .table-projects td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }

  .table-projects th {
    background-color: #007bff;
    color: #fff;
    font-weight: 600;
  }

  /* Hover effect */
  .table-projects tbody tr:hover {
    background-color: #f1f1f1;
  }

  /* Styling for links */
  .table-projects a {
    color: #007bff;
    text-decoration: none;
  }

  .table-projects a:hover {
    text-decoration: underline;
  }

  /* Image and status label */
  .staff-profile-image-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    vertical-align: middle;
  }


  .project-status-2 {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    color: #03a9f4;
    border: 1px solid #03a9f4;
  }

  .icon-btn {
    background-color: #ffffff;
    /* White background for contrast */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    /* Stronger shadow */
    border-radius: 8px;
    /* More rounded corners */
    transition: all 0.3s ease;
    border: none;
    color: #495057;
    /* Darker icon color */
  }

  .icon-btn:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    /* Deeper shadow on hover */
    background: linear-gradient(145deg, #e0e0e0, #f0f0f0);
    /* Light gradient effect */
    transform: translateY(-3px);
    /* Slight "lift" effect */
    color: #000000;
    /* Darker color on hover */
    opacity: 0.95;
  }

  .btn-light-primary {
    color: #007bff;
    /* Primary color for icon */
  }

  .btn-light-secondary {
    color: #6c757d;
    /* Secondary color */
  }

  .btn-light-info {
    color: #17a2b8;
    /* Info color */
  }

  .btn-light-danger {
    color: #dc3545;
    /* Danger color */
  }
</style>


<?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
  <div class="d-flex justify-content-between mb-4 mt-2">
    <h4>All Project List</h4>
    <a class="btn btn-info" href="<?= base_url('erp/create-project/'); ?>">Add Project</a>
  </div>

<?php } ?>


<?php if (in_array('project1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
  <hr class="border-light m-0 mb-3">
<?php } ?>

<div class="row text-center project-summary">
  <div class="col-md-12">
    <h4 class="no-margin" style="text-align: left; font-size: 20px">Projects Summary</h4>
  </div>
  <!-- Not Started -->
  <div class="col-md-2 mb-3 p-0 project-column">
    <h3 class="project-count"><?= $completed; ?></h3>
    <p class="text-muted m-0">
      <?= lang('Main.xin_total'); ?>
      <span class="text-success f-w-400">
        <?= lang('Projects.xin_completed'); ?>
      </span>
    </p>
  </div>

  <!-- In Progress -->
  <div class="col-md-2 mb-3 p-0 project-column">
    <h3 class="project-count"> <?= $in_progress; ?></h3>
    <p class="text-muted m-0">
      <?= lang('Main.xin_total'); ?>
      <span class="text-primary f-w-400">
        <?= lang('Projects.xin_in_progress'); ?>
      </span>
    </p>
  </div>

  <!-- On Hold -->
  <div class="col-md-2 mb-3 p-0 project-column">
    <h3 class="project-count"><?= $not_started; ?></h3>
    <p class="text-muted m-0">
      <?= lang('Main.xin_total'); ?>
      <span class="text-info f-w-400">
        <?= lang('Projects.xin_not_started'); ?>
      </span>
    </p>
  </div>

  <!-- Cancelled -->
  <div class="col-md-2 mb-3 p-0 project-column">
    <h3 class="project-count"> <?= $hold; ?></h3>
    <p class="text-muted m-0">
      <?= lang('Main.xin_total'); ?>
      <span class="text-danger f-w-400">
        <?= lang('Projects.xin_project_hold'); ?>
      </span>
    </p>
  </div>
</div>


<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper ">


  <table data-last-order-identifier="projects" data-default-order=""
    class="table table-projects dataTable no-footer dtr-inline" id="projects-table" role="grid"
    aria-describedby="DataTables_Table_0_info">
    <thead>
      <tr>
        <th>#</th>
        <th><?php echo lang('Dashboard.left_projects'); ?></th>
        <th><?php echo lang('Projects.xin_client'); ?></th>
        <th><?php echo lang('Projects.xin_start_date'); ?></th>
        <th><?php echo lang('Projects.xin_end_date'); ?></th>
        <th><?php echo lang('Projects.xin_project_users'); ?></th>
        <th><?php echo lang('Projects.xin_p_priority'); ?></th>
        <th><?php echo lang('Projects.dashboard_xin_progress'); ?></th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1;
      foreach ($project_list as $list) { ?>
        <tr class="has-row-options odd" role="row">
          <td><?= $i++; ?></td>
          <td><a href="<?= site_url('erp/project-detail/' . uencode($list['project_id'])) ?>"><?= $list['title']; ?></a>
          </td>
          <td>
            <?php
            $client_info = $UsersModel->where('user_id', $list['client_id'])->where('user_type', 'customer')->first();
            $iclient = isset($client_info['first_name']) && isset($client_info['last_name']) ?
              $client_info['first_name'] . ' ' . $client_info['last_name'] : 'Unknown Client';
            ?>
            <?= $iclient; ?>
          </td>

          <td><?= set_date_format($list['start_date']); ?></td>
          <td><?= set_date_format($list['end_date']); ?></td>
          <td>
            <?php
            $assigned_to = explode(',', $list['assigned_to']);
            $multi_users = multi_user_profile_photo($assigned_to);
            ?>
            <?= $multi_users; ?>
          </td>
          <td>
            <?php if ($list['priority'] == 1) { ?>
              <span class="badge badge-light-danger"><?= lang('Projects.xin_highest'); ?></span>
            <?php } elseif ($list['priority'] == 2) { ?>
              <span class="badge badge-light-danger"><?= lang('Projects.xin_high'); ?></span>
            <?php } elseif ($list['priority'] == 3) { ?>
              <span class="badge badge-light-primary"><?= lang('Projects.xin_normal'); ?></span>
            <?php } elseif ($list['priority'] == 4) { ?>
              <span class="badge badge-light-success"><?= lang('Projects.xin_low'); ?></span>
            <?php } ?>
          </td>
          <td>
            <?php
            $progress_class = $list['project_progress'] <= 20 ? 'bg-danger' : ($list['project_progress'] <= 50 ? 'bg-warning' : ($list['project_progress'] <= 75 ? 'bg-info' : 'bg-success'));
            ?>

            <div class="progress" style="height: 10px;">
              <div class="progress-bar <?= $progress_class; ?> progress-bar-striped" role="progressbar"
                style="width: <?= $list['project_progress']; ?>%;" aria-valuenow="<?= $list['project_progress']; ?>"
                aria-valuemin="0" aria-valuemax="100">
                <?= $list['project_progress']; ?>%
              </div>
            </div>
          </td>
          <td>
            <?php if ($list['status'] == 1) { ?>
              <span class="project-status-2">In Progress</span>
            <?php } elseif ($list['status'] == 2) { ?>
              <span class="project-status-2">Completed</span>
            <?php } elseif ($list['status'] == 3) { ?>
              <span class="project-status-2">Cancelled</span>
            <?php } elseif ($list['status'] == 4) { ?>
              <span class="project-status-2">Hold</span>
            <?php } elseif ($list['status'] == 0) { ?>
              <span class="project-status-2">Not Started</span>
            <?php } ?>
          </td>
          <?php $created_by = $UsersModel->where('user_id', $list['added_by'])->first(); ?>

          <td>
            <span data-toggle="tooltip" title="View Project">
              <a href="<?= site_url('erp/project-detail') . '/' . uencode($list['project_id']); ?>">
                <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light">
                  <i class="feather icon-eye"></i>
                </button>
              </a>
            </span>
            <?php if (in_array('project14', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
              <span data-toggle="tooltip" title="Copy Project">
                <button type="button" class="btn icon-btn btn-sm btn-light-secondary waves-effect waves-light copy-project"
                  data-record-id="<?= $list['project_id']; ?>" data-project-id="<?= $list['project_id']; ?>">
                  <i class="feather icon-copy"></i>
                </button>
              </span>
            <?php } ?>
            <?php if (in_array('project3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
              <span data-toggle="tooltip" title="Edit Project">
                <a href="<?= site_url('erp/edit-project/') . uencode($list['project_id']); ?>">
                  <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                    <i class="feather icon-edit"></i>
                  </button>
                </a>
              </span>
            <?php } ?>
            <?php if (in_array('project4', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
              <span data-toggle="tooltip" title="<?= lang('Main.xin_delete'); ?>">
                <button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                  data-toggle="modal" data-target=".delete-modal" data-record-id="<?= uencode($list['project_id']); ?>">
                  <i class="feather icon-trash-2"></i>
                </button>
              </span>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>

  </table>
</div>

<!-- Copy Project Modal -->
<div class="modal fade" id="copyProjectModal" tabindex="-1" aria-labelledby="copyProjectLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="copyProjectLabel">Copy Project</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= base_url('erp/copy-project'); ?>" method="post" id="copyProjectForm">
        <?= csrf_field() ?>
        <div class="modal-body">
          <input type="hidden" name="project_id" value=""> <!-- Hidden input for project_id -->

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="copyTasks" name="task" checked>
            <label class="form-check-label" for="copyTasks">Tasks</label>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="copyMilestones" name="milestones" checked>
            <label class="form-check-label" for="copyMilestones">Milestones</label>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="copyMembers" name="members" checked>
            <label class="form-check-label" for="copyMembers">Members</label>
          </div>

          <h6 class="mt-3">Tasks Status</h6>
          <div class="form-check">
            <input type="radio" name="taskStatus" class="form-check-input" value="0" checked>
            <label class="form-check-label" for="notStarted">Not Started</label><br>
            <input type="radio" name="taskStatus" class="form-check-input" value="1">
            <label class="form-check-label" for="inProgress">In Progress</label><br>
            <input type="radio" name="taskStatus" class="form-check-input" value="3">
            <label class="form-check-label" for="testing">Cancelled</label><br>
            <input type="radio" name="taskStatus" class="form-check-input" value="4">
            <label class="form-check-label" for="awaitingFeedback">Hold</label><br>
            <input type="radio" name="taskStatus" class="form-check-input" value="2">
            <label class="form-check-label" for="complete">Complete</label>
          </div>

          <div class="form-group mt-3">
            <label for="customer">Client</label>
            <select name="client_id" id="client_id" class="form-control" data-plugin="select_hrm"
              data-placeholder="<?php echo lang('Projects.xin_client'); ?>">
              <option value=""></option>
              <?php foreach ($all_clients as $client) { ?>
                <option value="<?= $client['user_id'] ?>">
                  <?= $client['first_name'] . ' ' . $client['last_name'] ?>
                </option>
              <?php } ?>
            </select>
          </div>

          <div class="form-group">
            <label for="startDate">Start Date</label>
            <input type="date" class="form-control" id="startDate" name="startDate">
          </div>

          <div class="form-group">
            <label for="deadline">Deadline</label>
            <input type="date" class="form-control" id="deadline" name="deadline">
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"
            style="background-color: #f1f5f7;">Close</button>
          <button type="submit" class="btn btn-info" id="copyProjectButton">Copy Project</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script>
  $(document).ready(function() {
    <?php if (session()->getFlashdata('error')): ?>
      <?php
      $errors = $session->getFlashdata('error');
      if (is_array($errors)) {
        foreach ($errors as $err) {
          echo 'toastr.error("' . esc($err) . '");';
        }
      } else {
        echo 'toastr.error("' . esc($errors) . '");';
      }
      ?>
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
      toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
        timeOut: 5000
      });
    <?php endif; ?>
  });
</script>
<script>
  $(document).on('click', '.copy-project', function() {
    const projectId = $(this).data('project-id');
    $('#copyProjectForm input[name="project_id"]').val(projectId);

    $('#copyProjectModal').modal('show'); // Show the modal
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0];
    document.getElementById("startDate").setAttribute('min', today);
    document.getElementById("deadline").setAttribute('min', today);
  });
</script>


<script>
  $(document).ready(function() {
    $('#projects-table').DataTable({
      paging: true, // Enable pagination
      searching: true, // Enable search
      ordering: true, // Enable ordering
      lengthMenu: [10, 25, 50, 100], // Options for number of records to show
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search records",
      }
    });
  });
</script>

<?php
$session = \Config\Services::session();
$session->remove('entityId');
?>