<?php

use CodeIgniter\I18n\Time;
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\TasksModel;
use App\Models\ConstantsModel;
use App\Models\ProjectsModel;
use App\Models\TrackgoalsModel;
use App\Models\ProjectnotesModel;
use App\Models\ProjectfilesModel;
use App\Models\ProjectbugsModel;
use App\Models\ProjectdiscussionModel;
use App\Models\InvoicesModel;
use App\Models\TimelogsModel;


$MilestonesModel = new \App\Models\MilestonesModel();

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$TasksModel = new TasksModel();
$ProjectsModel = new ProjectsModel();
$TrackgoalsModel = new TrackgoalsModel();
$ConstantsModel = new ConstantsModel();
$ProjectnotesModel = new ProjectnotesModel();
$ProjectbugsModel = new ProjectbugsModel();
$ProjectfilesModel = new ProjectfilesModel();
$ProjectdiscussionModel = new ProjectdiscussionModel();
$InvoicesModel = new InvoicesModel();
$TimelogsModel = new TimelogsModel();


$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$locale = service('request')->getLocale();
$request = \Config\Services::request();

$segment_id = $request->getUri()->getSegment(3);
$project_id = udecode($segment_id);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$logged_in_company_id = $user_info['company_id'] ?? null;

$employeelist = $UsersModel->where('user_type', 'staff')
  ->where('company_id !=', $logged_in_company_id)
  ->findAll();

// var_dump($employeelist);
// die;

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

  $applyExpertData = array_filter($rows, function ($row) {
    return $row['status'] === 'A';
  });

  $applyExpertDataId = array_column($applyExpertData, 'expertId');
}

curl_close($curl);

if ($user_info['user_type'] == 'staff') {
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->where('is_active', 1)->findAll();
  $all_clients = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'customer')->findAll();
  $project_data = $ProjectsModel->where('company_id', $user_info['company_id'])->where('project_id', $project_id)->first();

  $track_goals = $TrackgoalsModel->where('company_id', $user_info['company_id'])->orderBy('tracking_id', 'ASC')->findAll();
  $task = $TasksModel->where('company_id', $user_info['company_id'])->where('project_id', $project_id)->groupStart()
    ->where('created_by', $user_id)
    ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')
    ->groupEnd()
    ->findAll();

  $timelogs = $TimelogsModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('project_id', $project_data['project_id'])->findAll();
  $project_files = $ProjectfilesModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('project_id', $project_id)->orderBy('project_file_id', 'ASC')->findAll();

  $project_bug = $ProjectbugsModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('project_id', $project_id)->orderBy('project_bug_id', 'ASC')->findAll();
  $project_notes = $ProjectnotesModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('project_id', $project_id)->orderBy('project_note_id', 'ASC')->first();
  $project_discussion = $ProjectdiscussionModel->where('company_id', $user_info['company_id'])->where('employee_id', $user_id)->where('project_id', $project_id)->orderBy('project_discussion_id', 'ASC')->findAll();

  // $get_invoices = $InvoicesModel->where('company_id', $user_info['company_id'])->where('client_id' == $user_id)->where('project_id', $project_id)->orderBy('invoice_id', 'ASC')->paginate(8);
  $get_invoices = $InvoicesModel->where('company_id', $user_info['company_id'])->where('project_id', $project_id)->where('client_id', $user_id)->orderBy('invoice_id', 'ASC')->paginate(8);

  $count_invoices = $InvoicesModel->where('company_id', $user_info['company_id'])->orderBy('invoice_id', 'ASC')->countAllResults();

  $result = $MilestonesModel->where('company_id', $user_info['company_id'])->where('project_id', $project_data['project_id'])->orderBy('id', 'ASC')->findAll();

  $invoiceList = $InvoicesModel->where('company_id', $user_info['company_id'])->where('project_id', $project_id)->where('client_id', $user_id)->findAll();
} else {
  $company_id = $usession['sup_user_id'];
  $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->where('is_active', 1)->findAll();
  $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
  $project_data = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->first();
  $track_goals = $TrackgoalsModel->where('company_id', $usession['sup_user_id'])->orderBy('tracking_id', 'ASC')->findAll();
  $task = $TasksModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_data['project_id'])->findAll();

  $get_invoices = $InvoicesModel->where('company_id', $usession['sup_user_id'])->orderBy('invoice_id', 'ASC')->paginate(8);
  $count_invoices = $InvoicesModel->where('company_id', $usession['sup_user_id'])->orderBy('invoice_id', 'ASC')->countAllResults();
  $project_bug = $ProjectbugsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->orderBy('project_bug_id', 'ASC')->findAll();
  $project_notes = $ProjectnotesModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->orderBy('project_note_id', 'ASC')->first();
  $project_discussion = $ProjectdiscussionModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->orderBy('project_discussion_id', 'ASC')->findAll();
  $project_files = $ProjectfilesModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->orderBy('project_file_id', 'ASC')->findAll();
  $result = $MilestonesModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_data['project_id'])->orderBy('id', 'ASC')->findAll();
  $timelogs = $TimelogsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_data['project_id'])->findAll();
  $invoiceList = $InvoicesModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_data['project_id'])->findAll();
}

$xin_system = erp_company_settings();

$unpaid = $InvoicesModel->where('company_id', $user_info['company_id'])->where('status', 0)->countAllResults();
$paid = $InvoicesModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();


if ($count_invoices < 1):
  $unpaid = 0;
else:
  $unpaid = $unpaid / $count_invoices * 100;
endif;
$unpaid = number_format((float) $unpaid, 1, '.', '');

if ($count_invoices < 1):
  $paid = 0;
else:
  $paid = $paid / $count_invoices * 100;
endif;
$paid = number_format((float) $paid, 1, '.', '');

$get_type = $request->getVar('type', FILTER_SANITIZE_STRING);
?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">


<style>
  .nav-pills .nav-link {
    border-radius: 0.25rem;
    color: #6c757d;
    background-color: #f8f9fa;
    margin-right: 5px;
    transition: background-color 0.3s, color 0.3s;
  }

  .nav-pills .nav-link.active {
    color: #007bff;
    position: relative;
    background-color: transparent;
  }

  .nav-pills .nav-link.active::after,
  .nav-pills .nav-link:hover::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -3px;
    height: 2px;
    background-color: #007bff;
    width: 120%;
    transform: translateX(-10%);
  }

  .nav-pills .nav-link:hover {
    color: #007bff;
    /* text-decoration: underline; */
    /* text-underline-offset: 10px; */
    position: relative;
  }

  .card {
    border: none;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .card-body {
    padding-left: 15px !important;
    padding-right: 10px !important;
    padding-top: 5px !important;
    padding-bottom: 5px !important;
  }

  .note-editor {
    margin-bottom: 8px !important;
    height: 185px !important;
  }

  .dropzone {
    min-height: initial !important;
    border: 2px;
    background: none !important;
    padding: 10px;
  }

  .panel_s .panel-body {
    background: #fff;
    border: 1px solid #dce1ef;
    border-radius: 4px;
    padding: 20px;
    position: relative;
  }

  .form-style {
    box-shadow: 0px 8px 16px rgba(0, 0, 255, 0.5);
    /* Dark blue shadow */
    border-radius: 10px;
    /* Rounded corners */
    background-color: #ffffff;
    /* White background */
    padding: 20px;
    /* Space inside the form */
  }

  /* Optional: Add hover effect for a more interactive design */
  .form-style:hover {
    box-shadow: 0px 10px 20px rgba(0, 0, 255, 0.7);
    /* Even darker shadow on hover */
    transition: box-shadow 0.3s ease;
    /* Smooth transition effect */
  }


  .row {
    margin-right: -15px;
    margin-left: -15px;
  }

  .project-overview-left {
    margin-top: -20px;
    padding-top: 20px;
  }

  .border-right {
    border-right: 1px solid #f0f0f0;
  }

  .staff-profile-image-small {
    height: 32px;
    width: 32px;
    border-radius: 50%;
  }

  .media-object {
    display: block;
  }

  @media (min-width: 992px) {
    .col-md-12 {
      width: 100%;
    }
  }


  h5 {
    font-size: 13px;
    margin-bottom: 18px;
  }

  #timesheetsChart {
    display: block !important;
    opacity: 1 !important;
  }

  #DataTables_Table_0_wrapper {
    padding-top: 10px;
    width: 100%;
  }

  .modal-header {
    background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
  }

  /* Dropdown Menu Styles */
  .custom-dropdown-menu {
    background-color: #ffffff;
    padding: 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    min-width: 160px;
  }

  .custom-dropdown-menu li a {
    display: block;
    padding: 10px 20px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    transition: background-color 0.3s ease;
    border-bottom: 1px solid #eee;
  }

  .custom-dropdown-menu li a:last-child {
    border-bottom: none;
  }

  .custom-dropdown-menu li a:hover {
    /* background-color: #f0f0f0; */
    color: #333;
    cursor: pointer;
    border-radius: 8px;
  }

  .custom-dropdown-menu li a.active {
    background-color: #007bff;
    color: #ffffff;
    font-weight: bold;
  }

  tbody {
    font-size: 13px;
  }

  tbody td {
    padding: 8px 12px;
    color: #333;
  }

  tbody td.bold {
    font-weight: bold;
  }

  tbody a {
    color: #007bff;
    text-decoration: none;
  }

  tbody a:hover {
    text-decoration: underline;
  }

  .nav {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    padding-left: 0;
    margin-bottom: 0;
    list-style: none;
    gap: 30px;
  }

  .nav-pills-custom .nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #000;
    font-weight: bold;
    transition: color 0.2s;
  }

  .nav-pills-custom .nav-link i {
    border: 2px solid #e0e0e0;
    border-radius: 50%;
    padding: 8px;
    font-size: 20px;
    color: #6c757d;
    margin-right: 8px;
    transition: background-color 0.2s, color 0.2s;
  }

  .nav-pills-custom .nav-link.active i {
    border-color: #007bff;
    background-color: #e0e0ff;
    color: #6c5ce7;
  }

  .nav-pills-custom .nav-link.active {
    color: #6c5ce7;
  }

  .nav-pills-custom .nav-link div {
    font-weight: normal;
    color: #6c757d;
    font-size: 12px;
  }

  .status-label {
    font-size: 12px;
    font-weight: normal;
    text-transform: uppercase;
    border: 1px solid #dc3545;
    padding: 5px;
    border-radius: 4px;
  }



  .status-paid {
    color: #007bff;
    /* Blue color for Paid */
    text-decoration-color: #007bff;
    /* Blue underline */
  }

  .status-unpaid {
    color: #dc3545;
    /* Red color for Unpaid */
    text-decoration-color: #dc3545;
    /* Red underline */
  }

  .custom-btn {
    border-radius: 4px;
    border: 1px solid #ccc;
    background-color: white;
    color: #333;
    padding: 5px 10px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease-in-out;
  }

  .custom-btn:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
  }

  .custom-btn:active {
    background-color: #e2e6ea;
    transform: translateY(0);
  }

  /* Tooltip styling */
  [data-toggle="tooltip"] {
    position: relative;
    cursor: pointer;
  }
</style>

<div class="row">
  <div class="col-lg-12">
    <div class="bg-light card mb-2" style="font-size: 13px;">
      <div class="card-body">
        <ul class="nav nav-pills" id="pills-tab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pills-overview-tab" data-toggle="pill" href="#pills-overview" role="tab"
              aria-controls="pills-overview" aria-selected="true">
              <i class="fas fa-tasks" style="margin-right: 4px;"></i><?= lang('Main.xin_overview'); ?>
            </a>
          </li>
          <?php if (in_array('project12', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-milestones-tab" data-toggle="pill" href="#pills-milestones" role="tab"
                aria-controls="pills-milestones" aria-selected="false">
                <i class="fas fa-flag"></i> Milestones
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project8', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-tasks-tab" data-toggle="pill" href="#pills-tasks" role="tab"
                aria-controls="pills-tasks" aria-selected="false">
                <i class="fa fa-check-circle"></i> <?= lang('Dashboard.left_tasks'); ?>
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project11', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-timelogs-tab" data-toggle="pill" href="#pills-timelogs" role="tab"
                aria-controls="pills-timelogs" aria-selected="false">
                <i class="fas fa-clock"></i> <?= lang('Dashboard.xin_project_timelogs'); ?>
              </a>
            </li>
          <?php } ?>

          <?php if (in_array('project9', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-files-tab" data-toggle="pill" href="#pills-files" role="tab"
                aria-controls="pills-files" aria-selected="false">
                <i class="fas fa-file"></i> Files
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project6', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-discussion-tab" data-toggle="pill" href="#pills-discussion" role="tab"
                aria-controls="pills-discussion" aria-selected="false">
                <i class="fas fa-comments"></i> <?= lang('Projects.xin_discussion'); ?>
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project7', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-bugs-tab" data-toggle="pill" href="#pills-bugs" role="tab"
                aria-controls="pills-bugs" aria-selected="false">
                <i class="fas fa-bug"></i> <?= lang('Projects.xin_bugs'); ?>
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project10', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-notes-tab" data-toggle="pill" href="#pills-notes" role="tab"
                aria-controls="pills-notes" aria-selected="false">
                <i class="fas fa-sticky-note"></i> <?= lang('Projects.xin_note'); ?>
              </a>
            </li>
          <?php } ?>
          <?php if (in_array('project13', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="nav-item">
              <a class="nav-link" id="pills-invoice-tab" data-toggle="pill" href="#pills-invoice" role="tab"
                aria-controls="pills-notes" aria-selected="false">
                <i class="fas fa-file-invoice"></i> Invoice
              </a>
            </li>
          <?php } ?>
        </ul>
        <br>

        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade active show" id="pills-overview" role="tabpanel"
            aria-labelledby="pills-overview-tab">
            <div class="panel_s">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-6 border-right project-overview-left">
                    <div class="row">
                      <div class="col-md-12">
                        <p class="project-info bold font-size-14">
                          Overview </p>
                      </div>
                      <div class="col-md-7">
                        <table class="table no-margin project-overview-table">
                          <tbody>
                            <tr class="project-overview-total-logged-hours">
                              <td class="bold">Project</td>

                              <td><?= $project_data['title']; ?></td>
                            </tr>
                            <tr class="project-overview-customer">
                              <td class="bold">Customer</td>
                              <td><a href=""> <?= getClientname($project_data['client_id']); ?></a>
                              </td>

                            </tr>
                            <tr class="project-overview-billing">
                              <td class="bold">Billing Type</td>
                              <td>
                                <?php if ($project_data['billing_type'] == 'fixed_rate') { ?>
                                  Fixed Rate
                                <?php } elseif ($project_data['billing_type'] == 'project') { ?>
                                  Project Hours
                                <?php } elseif ($project_data['billing_type'] == 'task') { ?>
                                  Task Hours
                                <?php } ?>
                              </td>

                            </tr>
                            <tr>
                              <td class="bold">Total Rate</td>
                              <td>
                                <?= number_to_currency($project_data['revenue'] ?? 0, $xin_system['default_currency'], 'en_US', 2); ?>
                              </td>
                            </tr>
                            <tr> </tr>
                            <tr class="project-overview-status">
                              <td class="bold">Status</td>
                              <td>
                                <?php if ($project_data['status'] == 0) { ?>
                                  <?= lang('Projects.xin_not_started'); ?>
                                <?php } elseif ($project_data['status'] == 1) { ?>
                                  <?= lang('Projects.xin_in_progress'); ?>
                                <?php } elseif ($project_data['status'] == 2) { ?>
                                  <?= lang('Projects.xin_completed'); ?>
                                <?php } elseif ($project_data['status'] == 3) { ?>
                                  <?= lang('Projects.xin_project_cancelled'); ?>
                                <?php } elseif ($project_data['status'] == 4) { ?>
                                  <?= lang('Projects.xin_project_hold'); ?>
                                <?php } ?>
                              </td>

                            </tr>
                            <tr class="project-overview-date-created">
                              <td class="bold">Date Created</td>
                              <td><?= $project_data['created_at']; ?></td>
                            </tr>
                            <tr class="project-overview-start-date">
                              <td class="bold">Start Date</td>
                              <td><?= $project_data['start_date']; ?></td>
                            </tr>
                            <tr class="project-overview-deadline">
                              <td class="bold">Deadline</td>
                              <td><?= $project_data['end_date']; ?></td>
                            </tr>

                          </tbody>
                        </table>
                      </div>
                      <div class="col-md-5 text-center project-percent-col mtop10">
                        <p class="bold">Project Progress</p>
                        <div id="project-progress-chart-1" class="mtop15"></div>
                      </div>
                    </div>
                    <div class="tc-content project-overview-description" style="font-size: 13px;">
                      <hr class="hr-panel-heading project-area-separation">
                      <p class="bold font-size-14 project-info">Description</p>
                      <?= $project_data['description']; ?>
                    </div>
                    <div class="team-members project-overview-team-members">
                      <hr class="hr-panel-heading project-area-separation">
                      <p>Members</p>

                      <?php
                      $assigned_to = explode(',', $project_data['assigned_to']);
                      $UsersModel = new \App\Models\UsersModel();
                      $team_members = [];

                      foreach ($assigned_to as $user_id) {
                        $user = $UsersModel->where('user_id', trim($user_id))->first();
                        if ($user) {
                          $team_members[] = $user;
                        }
                      }
                      ?>

                      <?php foreach ($team_members as $member): ?>
                        <div class="media" style="display: flex; align-items: center;">
                          <div class="media-left">
                            <?php
                            $default_img = base_url('uploads/users/default/' . ($member['gender'] == 1 ? 'default_male.png' : 'default_female.png'));
                            $profile_img_path = 'uploads/users/' . $member['profile_photo'];
                            $profile_img = file_exists($profile_img_path) && !empty($member['profile_photo']) ? base_url($profile_img_path) : $default_img;
                            ?>
                            <img src="<?= $profile_img; ?>" class="staff-profile-image-small media-object"
                              style="width: 40px; height: 40px; border-radius: 50%;">
                          </div>
                          <div class="media-body"
                            style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                            <h5 class="media-heading mtop5" style="margin-top: 5px; margin-left: 13px;">
                              <a href="#">
                                <?= $member['first_name'] . ' ' . $member['last_name']; ?>
                              </a>
                            </h5>
                            <a href="#"
                              class="text-danger _delete" style="font-size: 13px; display: flex; align-items: center;">
                              <i class="fa fa-times"></i>
                            </a>
                          </div>
                        </div>
                      <?php endforeach; ?>

                    </div>
                  </div>
                  <div class="col-md-6 project-overview-right">


                    <div class="row project-overview-expenses-finance" style="font-size: 13px;">
                      <div class="col-md-3">
                        <p class="text-uppercase text-muted">Total Expenses</p>
                        <p class="bold font-medium">₹ <?= number_format($project_data['revenue'], 0) ?></p>
                      </div>
                      <div class="col-md-3">
                        <p class="text-uppercase text-info">Billable Expenses</p>
                        <?php
                        $created_at = $project_data['created_at']; // The creation timestamp
                        $current_time = new DateTime(); // Current date and time
                        $created_time = new DateTime($created_at); // Convert creation time to DateTime object
                        $interval = $created_time->diff($current_time); // Calculate the difference
                        ?>
                        <div style="display: flex; align-items: center;">
                          <div style="margin-right: 10px;">
                            <i class="fa fa-clock text-info" style="font-size: 24px;"></i>
                          </div>
                          <div>
                            <p class="bold font-medium" style="font-size: 16px; margin: 0;">
                              <?= $interval->days ?> <span style="font-size: 12px; color: gray;">days</span>
                            </p>
                            <p class="bold font-medium" style="font-size: 16px; margin: 0;">
                              <?= $interval->h ?> <span style="font-size: 12px; color: gray;">hours</span>
                            </p>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-3">
                        <p class="text-uppercase text-success">Billed Expenses</p>
                        <p class="bold font-medium">₹ <?= getpayamount($project_data['project_id']) ?></p>
                      </div>

                      <div class="col-md-3">
                        <p class="text-uppercase text-danger">Unbilled Expenses</p>
                        <p class="bold font-medium">₹
                          <?= number_format($project_data['revenue'] - getpayamount($project_data['project_id']), 0) ?>
                        </p>
                      </div>

                    </div>


                    <div class="project-overview-timesheets-chart">
                      <hr class="hr-panel-heading">

                      <!-- Align dropdown to the right -->
                      <div class="dropdown pull-right text-right" style="position: relative;">
                        <a href="#" class="dropdown-toggle custom-dropdown-toggle" type="button"
                          id="dropdownMenuProjectLoggedTime" data-toggle="dropdown" aria-haspopup="true"
                          aria-expanded="true">
                          <span id="timePeriod">This Week</span> <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdownMenuProjectLoggedTime">
                          <li><a href="#" onclick="updateChart('This Week')">This Week</a></li>
                          <li><a href="#" onclick="updateChart('Last Week')">Last Week</a></li>
                          <li><a href="#" onclick="updateChart('This Month')">This Month</a></li>
                          <li><a href="#" onclick="updateChart('Last Month')">Last Month</a></li>
                        </ul>
                      </div>

                      <div class="clearfix"></div>
                      <canvas id="timesheetsChart"
                        style="max-height: 300px; display: block; height: 300px; width: 100%;"></canvas>
                    </div>

                  </div>
                </div>


              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-milestones" role="tabpanel" aria-labelledby="pills-milestones-tab">
            <div class="panel_s">
              <div class="panel-body">
                <div class="d-flex justify-content-between mb-4 mt-2">
                  <h5>All Milestone List</h5>
                  <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#milestone_modal">New Milestone</a>
                </div>
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper ">
                  <table class="table" id="milstone_table" role="grid">
                    <thead>
                      <tr role="row">
                        <th>#</th>
                        <th>Name</th>
                        <th>Due Date</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $i = 1;
                      foreach ($result as $list) {
                        if (is_array($list) && isset($list['id'], $list['name'], $list['due_date'], $list['description'])) {
                      ?>
                          <tr class="odd" role="row">
                            <td><?= $i++; ?></td>
                            <td><?= htmlspecialchars($list['name']); ?></td>
                            <td><?= htmlspecialchars($list['due_date']); ?></td>
                            <td><?= htmlspecialchars($list['description']); ?></td>
                            <td>
                              <span data-toggle="tooltip" title="Edit Project">
                                <a onclick="openModal(<?= $list['id'] ?>);">
                                  <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                    <i class="feather icon-edit"></i>
                                  </button>
                                </a>
                              </span>
                              <span data-toggle="tooltip" title="Delete Milestone">
                                <button type="button"
                                  class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                  data-toggle="modal" data-target="#deleteModal"
                                  data-url="<?= base_url('erp/milestones-delete') ?>"
                                  data-record-id="<?= htmlspecialchars($list['id']); ?>"
                                  data-project-id="<?= $project_data['project_id']; ?>">
                                  <i class="feather icon-trash-2"></i>
                                </button>
                              </span>
                            </td>
                          </tr>
                      <?php }
                      } ?>
                    </tbody>


                  </table>
                </div>

              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-tasks" role="tabpanel" aria-labelledby="pills-tasks-tab">
            <div class="panel_s">
              <div class="panel-body">

                <div class="d-flex justify-content-between mb-4 mt-2">
                  <h5>All Task List</h5>
                  <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#task_modal">Add Task</a>
                </div>


                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                  <table data-last-order-identifier="projects" data-default-order=""
                    class="table table-projects dataTable no-footer dtr-inline" id="DataTables_Table_0" role="grid"
                    aria-describedby="DataTables_Table_0_info">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th><?php echo lang('Dashboard.xin_title'); ?></th>
                        <th><?php echo lang('Projects.xin_project_users'); ?></th>
                        <th><?php echo lang('Projects.xin_start_date'); ?></th>
                        <th><?php echo lang('Projects.xin_end_date'); ?></th>
                        <th><?php echo lang('Projects.xin_status'); ?></th>
                        <th><?php echo lang('Projects.dashboard_xin_progress'); ?></th>
                        <th>Project Name</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 1;
                      foreach ($task as $list): ?>
                        <tr class="has-row-options odd" role="row">
                          <td><?= $i++; ?></td>
                          <td><?= $list['task_name']; ?></td>
                          <td>
                            <?php
                            $assigned_to = explode(',', $list['assigned_to']);
                            $multi_users = multi_user_profile_photo($assigned_to);
                            echo $multi_users;
                            ?>
                          </td>
                          <td><?= set_date_format($list['start_date']); ?></td>
                          <td><?= set_date_format($list['end_date']); ?></td>
                          <td>
                            <?php
                            switch ($list['task_status']) {
                              case 0:
                                echo '<span class="badge badge-light-warning">' . lang('Projects.xin_not_started') . '</span>';
                                break;
                              case 1:
                                echo '<span class="badge badge-light-info">' . lang('Projects.xin_in_progress') . '</span>';
                                break;
                              case 2:
                                echo '<span class="badge badge-light-success">' . lang('Projects.xin_completed') . '</span>';
                                break;
                              case 3:
                                echo '<span class="badge badge-light-danger">' . 'cancelled' . '</span>';
                                break;
                              case 4:
                                echo '<span class="badge badge-light-secondary">' . lang('Projects.xin_on_hold') . '</span>';
                                break;
                            }
                            ?>
                          </td>
                          <td>
                            <?php
                            $progress = $list['task_progress'];
                            $progress_class = '';

                            if ($progress <= 20) {
                              $progress_class = 'bg-danger';
                            } elseif ($progress > 20 && $progress <= 50) {
                              $progress_class = 'bg-warning';
                            } elseif ($progress > 50 && $progress <= 75) {
                              $progress_class = 'bg-info';
                            } else {
                              $progress_class = 'bg-success';
                            }

                            echo '<div class="progress" style="height: 10px;">
                              <div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" 
                              style="width: ' . $progress . '%;" aria-valuenow="' . $progress . '" 
                              aria-valuemin="0" aria-valuemax="100">' . $progress . '%</div>
                            </div>';
                            ?>
                          </td>
                          <td><?= getProjectName($list['project_id']); ?></td>
                          <td>
                            <span data-toggle="tooltip" title="Edit Project">
                              <a href="<?= site_url('erp/edit-task/') . uencode($list['task_id']); ?>">
                                <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                  <i class="feather icon-edit"></i>
                                </button>
                              </a>
                            </span>


                            <span data-toggle="tooltip" data-placement="top" data-state="danger"
                              title="<?= lang('Main.xin_delete'); ?>">
                              <button type="button"
                                class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                data-toggle="modal" data-target=".delete-modal"
                                data-record-id="<?= uencode($list['task_id']); ?>">
                                <i class="feather icon-trash-2"></i>
                              </button>
                            </span>

                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-timelogs" role="tabpanel" aria-labelledby="pills-timelogs-tab">
            <div class="panel_s">
              <div class="panel-body">
                <div class="d-flex justify-content-between mb-4 mt-2">
                  <h5>All TimeLogs List</h5>
                  <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#timelog_modal">Add
                    TimeLogs</a>
                </div>

                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper ">
                  <table data-last-order-identifier="projects" data-default-order=""
                    class="table table-projects dataTable no-footer dtr-inline" id="timelogs_Table_0" role="grid"
                    aria-describedby="timelogs_Table_0_info">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Member</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Hours</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i = 1;
                      foreach ($timelogs as $list): ?>
                        <tr class="has-row-options odd" role="row">
                          <td><?= $i++; ?></td>
                          <td> <?= getTaskName($list['task_id']); ?></td>
                          <td><?= getClientname($list['employee_id']); ?></td>
                          <td><?= $list['start_date']; ?></td>
                          <td><?= $list['end_date']; ?></td>
                          <td> <?= formatTotalHours($list['total_hours']) . ' hours'; ?></td>

                          <td>
                            <span data-toggle="tooltip" title="Edit Project">
                              <a href="<?= site_url('erp/edit-timelogs/') . uencode($list['timelogs_id']); ?>">
                                <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                  <i class="feather icon-edit"></i>
                                </button>
                              </a>
                            </span>

                            <span data-toggle="tooltip" title="Delete Timelog">
                              <button type="button"
                                class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                data-toggle="modal" data-target="#deleteModal"
                                data-url="<?= base_url('erp/timelogs-delete') ?>"
                                data-record-id="<?= htmlspecialchars($list['timelogs_id']); ?>"
                                data-project-id="<?= $project_data['project_id']; ?>">
                                <i class="feather icon-trash-2"></i>
                              </button>
                            </span>

                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>


          </div>

          <div class="tab-pane fade " id="pills-files" role="tabpanel" aria-labelledby="pills-files-tab">
            <div class="panel_s">
              <div class="panel-body">
                <?php
                $attributes = array('name' => 'add_attachment', 'id' => 'add_attachment', 'class' => 'dropzone', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data');
                $hidden = array('token' => $segment_id);
                echo form_open('erp/projects-add-attachment', $attributes, $hidden);
                ?>
                <div class="bg-white">
                  <div class="row mt-4">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="file_name"><?= lang('Dashboard.xin_title'); ?> <span
                            class="text-danger">*</span></label>
                        <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="file_name"
                          type="text" value="">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div id="fileDropzone" class="dropzone p-4"
                        style="border: 2px dashed #d3d3d3; border-radius: 5px;">
                        <div class="dz-message text-center">
                          <span class="text-muted">
                            Drag files here to upload or <strong class="text-primary"
                              style="cursor: pointer;">browse</strong>
                          </span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <?php echo form_close(); ?>
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper">
                  <table data-last-order-identifier="projects" data-default-order=""
                    class="table table-projects dataTable no-footer dtr-inline" id="files_Table_0" role="grid"
                    aria-describedby="files_Table_0_info">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>file</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($project_files as $index => $_files): ?>
                        <?php $file_user = $UsersModel->where('user_id', $_files['employee_id'])->first(); ?>
                        <tr>
                          <td><?= $index + 1; ?></td>
                          <td><img src="<?= base_url('uploads/project_files/' . $_files['attachment_file']) ?>"
                              height="50px" width="50px"> </td>
                          <td><?= $_files['file_title']; ?></td>
                          <td>
                            <?= lang('Main.xin_by'); ?>:
                            <?= $file_user['first_name'] . ' ' . $file_user['last_name']; ?>
                            <br>
                            <small class="text-muted"><?= time_ago($_files['created_at']); ?></small>
                          </td>
                          <td>
                            <a href="<?= site_url('download') ?>?type=project_files&filename=<?= urlencode($_files['attachment_file']) ?>"
                              data-toggle="tooltip" title="Download File" class="btn btn-sm btn-secondary">
                              <i class="fas fa-download"></i>
                            </a>
                            <a href="#!" data-field="<?= $_files['project_file_id']; ?>"
                              class="btn btn-sm btn-danger delete_file" data-toggle="tooltip" title="Delete File">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-discussion" role="tabpanel" aria-labelledby="pills-discussion-tab">
            <div class="panel_s">
              <div class="panel-body">
                <div class="d-flex justify-content-between mb-4 mt-2">
                  <h5>All Discussion List</h5>
                  <button id="showFormBtn" class="btn btn-info btn-sm">Create Discussion</button>
                  <button id="hideFormBtn" class="btn btn-info btn-sm d-none">Hide</button>
                </div>

                <div id="discussionForm" class="mb-4 d-none form-style">
                  <?php $attributes = array('name' => 'add_discussion', 'id' => 'add_discussion', 'autocomplete' => 'off'); ?>
                  <?php $hidden = array('token' => $segment_id); ?>
                  <?= form_open('erp/add-discussion', $attributes, $hidden); ?>
                  <input type="hidden" name="discussion_id" id="discussion_id">
                  <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter subject"
                      required>
                  </div>
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"
                      placeholder="Enter description" required></textarea>
                  </div>
                  <button type="submit" id="saveDiscussionBtn" class="btn btn-primary"
                    style="background-color: #007bff !important; border-color: #007bff !important;">Save
                    Discussion</button>
                  <?= form_close(); ?>
                </div>

                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper">
                  <table class="table table-projects dataTable no-footer dtr-inline" id="discussion_Table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($project_discussion)) { ?>
                        <?php foreach ($project_discussion as $index => $log) { ?>
                          <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($log['subject']); ?></td>
                            <td><?= htmlspecialchars($log['discussion_text']); ?></td>
                            <td>
                              <a href="<?= base_url('timelog/edit/' . $log['project_discussion_id']); ?>"
                                class="btn btn-sm btn-primary delete_edit" data-toggle="tooltip" title="Edit"
                                style="background-color: #007bff !important; border-color: #007bff !important;">
                                <i class="fas fa-edit"></i>
                              </a>

                              <a href="#!" data-field="<?= $log['project_discussion_id'] ?? '' ?>"
                                class="btn btn-sm btn-danger delete_discussion" data-toggle="tooltip"
                                title="Delete Discussion">
                                <i class="fas fa-trash-alt"></i>
                              </a>
                            </td>

                          </tr>
                        <?php } ?>
                      <?php } else { ?>
                        <tr>
                          <td colspan="4" class="text-center">No data available</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>


                <script>
                  document.getElementById('showFormBtn').addEventListener('click', function() {
                    document.getElementById('discussionForm').classList.remove('d-none');
                    document.getElementById('hideFormBtn').classList.remove('d-none');
                    document.getElementById('showFormBtn').classList.add('d-none');

                    document.getElementById('discussion_id').value = '';
                    document.getElementById('subject').value = '';
                    document.getElementById('description').value = '';
                    document.getElementById('saveDiscussionBtn').textContent = 'Save Discussion';
                  });

                  document.getElementById('hideFormBtn').addEventListener('click', function() {
                    // Hide the form
                    document.getElementById('discussionForm').classList.add('d-none');
                    document.getElementById('showFormBtn').classList.remove('d-none');
                    this.classList.add('d-none');
                  });

                  document.querySelectorAll('.delete_edit').forEach(function(editBtn) {
                    editBtn.addEventListener('click', function(event) {
                      event.preventDefault();
                      const row = this.closest('tr');
                      const subject = row.querySelector('td:nth-child(2)').textContent.trim();
                      const description = row.querySelector('td:nth-child(3)').textContent.trim();
                      const discussionId = this.getAttribute('href').split('/').pop();
                      document.getElementById('discussion_id').value = discussionId;
                      document.getElementById('subject').value = subject;
                      document.getElementById('description').value = description;

                      document.getElementById('discussionForm').classList.remove('d-none');
                      document.getElementById('hideFormBtn').classList.remove('d-none');
                      document.getElementById('showFormBtn').classList.add('d-none');

                      document.getElementById('saveDiscussionBtn').textContent = 'Update Discussion';
                    });
                  });
                </script>


              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-bugs" role="tabpanel" aria-labelledby="pills-bugs-tab">
            <div class="panel_s">
              <div class="panel-body">
                <h5>All Bugs List</h5>
                <hr>
                <?php
                $attributes = array('name' => 'add_bug', 'id' => 'add_bug', 'autocomplete' => 'off');
                $hidden = array('token' => $segment_id);
                echo form_open('erp/add-bug', $attributes, $hidden);
                ?>
                <textarea
                  name="bug_description"
                  id="bug_description"
                  class="form-control bug-description"
                  rows="5"
                  cols="50"
                  minlength="20"
                  maxlength="2000"
                  placeholder="Please describe the bug in detail, including steps to reproduce..."
                  required
                  aria-label="Bug description"
                  aria-describedby="bug_description_help"
                  spellcheck="true"
                  data-validate="true"></textarea>
                <button type="submit" class="btn btn-primary"
                  style="background-color: #007bff !important; border-color: #007bff !important; margin-top:10px">Save Bugs</button>
                <?= form_close(); ?>
                <hr>
                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper">
                  <table data-last-order-identifier="bugs" class="table table-bugs dataTable no-footer dtr-inline"
                    id="bugs_Table_1" role="grid">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Reported By</th>
                        <th>Bug</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($project_bug as $index => $_bug): ?>
                        <?php $time = Time::parse($_bug['created_at']); ?>
                        <?php $bug_user = $UsersModel->where('user_id', $_bug['employee_id'])->first(); ?>
                        <tr id="bug_option_id_<?= $_bug['project_bug_id']; ?>">
                          <td><?= $index + 1; ?></td>
                          <td>
                            <div class="media">
                              <img class="img-fluid media-object img-radius comment-img mr-2"
                                src="<?= staff_profile_photo($_bug['employee_id']); ?>" width="40px;" alt="">
                              <div class="media-body">
                                <strong><?= $bug_user['first_name'] . ' ' . $bug_user['last_name']; ?></strong>
                                <br>
                                <small class="text-muted"><?= time_ago($_bug['created_at']); ?></small>
                              </div>
                            </div>
                          </td>
                          <td><?= html_entity_decode($_bug['bug_note']); ?></td>
                          <td>
                            <a href="#!" data-field="<?= $_bug['project_bug_id']; ?>"
                              class="btn btn-sm btn-danger delete_bug" data-toggle="tooltip" title="Delete Bug">
                              <i class="fas fa-trash-alt"></i>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-notes" role="tabpanel" aria-labelledby="pills-notes-tab">
            <div class="panel_s">
              <div class="panel-body">
                <h5>Private Notes</h5>
                <hr>
                <?php $attributes = array('name' => 'add_note', 'id' => 'add_note', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('token' => $segment_id); ?>
                <?= form_open('erp/add-note', $attributes, $hidden); ?>
                <textarea
                  name="description"
                  id="description"
                  class="form-control description"
                  rows="5"
                  cols="50"
                  minlength="20"
                  maxlength="2000"
                  placeholder="write Note"
                  required
                  aria-label="description"
                  aria-describedby="description_help"
                  spellcheck="true"
                  data-validate="true"><?= isset($project_notes['project_note'])?$project_notes['project_note']:''; ?></textarea>
                <button type="submit" class="btn btn-primary"
                  style="background-color: #007bff !important; border-color: #007bff !important; margin-top:10px;">Save Note</button>

                <?= form_close(); ?>

              </div>
            </div>
          </div>

          <div class="tab-pane fade" id="pills-invoice" role="tabpanel" aria-labelledby="pills-invoice-tab">
            <div class="panel_s">
              <div class="panel-body">
                <div class="d-flex justify-content-between mb-4 mt-2">
                  <h5>Invoice List</h5>
                  <a href="<?= base_url('erp/project-invoice/' . $project_data['project_id']) ?>"
                    class="btn btn-info btn-sm">Create Invoice</a>
                </div>


                <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper ">
                  <table data-last-order-identifier="projects" data-default-order=""
                    class="table table-projects dataTable no-footer dtr-inline" id="invoiceTable_0" role="grid"
                    aria-describedby="invoiceTable_0_info">
                    <thead>
                      <tr>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Total Tax</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>project</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      <?php foreach ($invoiceList as $list): ?>
                        <tr>
                          <td><?= $list['invoice_number']; ?></td>
                          <td><?= "₹ " . $list['grand_total']; ?></td>
                          <td><?= "₹ " . $list['total_tax']; ?></td>
                          <td><?= $list['invoice_date']; ?></td>
                          <td><?= getClientname($list['client_id']); ?></td>
                          <td><?= getProjectName($list['project_id']); ?></td>
                          <td><?= $list['invoice_due_date']; ?></td>
                          <td>
                            <?php if ($list['status'] == 1): ?>
                              <span class="status-label status-paid">Paid</span>
                            <?php else: ?>
                              <span class="status-label status-unpaid">Unpaid</span>
                            <?php endif; ?>
                          </td>
                          <td> <!-- Download Button -->
                            <!-- <a href="<?= site_url() . 'erp/print-invoice/' . uencode($list['invoice_id']); ?>" data-toggle="tooltip" title="Download File" class="btn btn-sm custom-btn"> <i class="fas fa-download"></i> </a> -->

                            <a href="<?= site_url() . 'erp/invoice-detail/' . uencode($list['invoice_id']); ?>"
                              data-toggle="tooltip" title="View Invoice" class="btn btn-sm custom-btn"> <i
                                class="fas fa-eye"></i> </a>
                            <!-- Edit Button -->
                            <a href="<?= site_url() . 'erp/edit-projectInvoice/' . uencode($list['invoice_id']); ?>"
                              data-toggle="tooltip" title="Edit Invoice" class="btn btn-sm custom-btn">
                              <i class="fas fa-edit"></i> </a>

                          </td>

                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>




        </div>
      </div>
    </div>


    <!-- delete popup -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this record?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
          </div>
        </div>
      </div>
    </div>



    <!-- Milestone MOdal -->
    <div class="modal fade" id="milestone_modal" tabindex="-1" role="dialog" aria-labelledby="milestone_modalLabel"
      aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content" style="width: 110%;">
          <div class="modal-header">
            <h5 class="modal-title" id="groupsModalLabel" style="color: #fff;">New Milestone </h5> <button type="button"
              class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          </div>
          <form action="<?= base_url('erp/milestones-save'); ?>" method="post">
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">

                  <div id="additional_milestone"></div>
                  <div class="form-group" app-field-wrapper="name">
                    <label for="name" class="control-label"><small class="req text-danger">* </small>Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                    <input type="hidden" id="name" name="project_id" value="<?= $project_data['project_id'] ?>">
                  </div>
                  <div class="form-group" app-field-wrapper="due_date">
                    <label for="due_date" class="control-label"><small class="req text-danger">* </small>Due
                      date</label>
                    <div class="input-group date">
                      <input type="date" id="due_date" name="due_date" class="form-control datepicker" required
                        autocomplete="off">

                    </div>
                  </div>
                  <div class="form-group" app-field-wrapper="description">
                    <label for="description" class="control-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                  </div>

                  <div class="form-group" app-field-wrapper="milestone_order">
                    <label for="milestone_order" class="control-label">Order</label>
                    <input type="number" id="milestone_order" name="milestone_order" class="form-control" value="1">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer " style="padding:10px;">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-info">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--  -->
    <!-- view Milestone -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-body" id="modalBody" style="padding: 0px;">

          </div>
        </div>
      </div>
    </div>
    <!--  -->

    <!-- Task MOdal -->
    <div class="modal fade" id="task_modal" tabindex="-1" role="dialog" aria-labelledby="task_modalLabel"
      aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content" style="width: 110%;">
          <div class="modal-header">
            <h5 class="modal-title" id="groupsModalLabel" style="color: #fff;">New Task </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                aria-hidden="true">&times;</span> </button>
          </div>
          <?php $attributes = array('name' => 'add_task', 'autocomplete' => 'off'); ?>
          <?php $hidden = array('user_id' => '0'); ?>
          <?php echo form_open('erp/add-projectTask', $attributes, $hidden); ?>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">

                <div class="form-group" app-field-wrapper="name">
                  <label for="name" class="control-label mb-0">Title <small class="req text-danger">* </small></label>
                  <input type="text" id="name" name="task_name" class="form-control" required
                    placeholder="Enter Task Title">
                  <input type="hidden" id="name" name="project_id" value="<?= $project_data['project_id'] ?>" required>
                </div>

                <div class="form-group" app-field-wrapper="name">
                  <label for="name" class="control-label mb-0">Milestones</label>
                  <select name="milestone_id" class="form-control">
                    <option value=""> Select One</option>
                    <?php foreach ($result as $list) { ?>
                      <option value="<?= $list['id']; ?>"> <?= $list['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="row">
                  <div class="form-group col-6" app-field-wrapper="start_date">
                    <label for="start_date" class="control-label mb-0">Start Date <small class="req text-danger">*
                      </small></label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required
                      autocomplete="off">
                  </div>

                  <div class="form-group col-6" app-field-wrapper="due_date">
                    <label for="due_date" class="control-label mb-0">Due Date <small class="req text-danger">*
                      </small></label>
                    <input type="date" id="due_date" name="end_date" class="form-control" required autocomplete="off">
                  </div>
                </div>

                <div class="form-group" app-field-wrapper="milestone_order">
                  <label for="milestone_order" class="control-label">Estimated Hour</label>
                  <input type="number" id="task_hour" name="task_hour" class="form-control"
                    placeholder="Enter Estimate Time">
                </div>

                <div class="form-group" app-field-wrapper="milestone_order">
                  <label for="summary">Summary <span class="text-danger">*</span></label>
                  <textarea class="form-control" placeholder="Summary" name="summary" cols="30" rows="1" id="summary"
                    required></textarea>
                </div>
                <div class="form-group" app-field-wrapper="name">
                  <label for="name" class="control-label mb-0">Employee</label>
                  <select name="employee_id" class="form-control">
                    <option value="">Select employee</option>
                    <?php foreach ($employeelist as $list) { ?>
                      <option
                        value="<?= $list['user_id']; ?>"
                        <?= ($project_data['employe_ID'] == $list['user_id']) ? 'selected' : ''; ?>>
                        <?= $list['first_name'] . " " . $list['last_name']; ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="expert_to">
                        <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                      </label>
                      <input type="hidden" value="0" name="expert_to[]" />
                      <select multiple name="expert_to[]" class="form-control" data-plugin="select_hrm">
                        <option value="">Select Expert</option>
                        <?php foreach ($applyExpertData as $staff) { ?>
                          <option value="<?= $staff['expertId'] ?>">
                            <?= $staff['expertFullName'] ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="employee"><?php echo lang('Projects.xin_project_users'); ?></label>
                      <input type="hidden" value="0" name="assigned_to[]" />
                      <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm">
                        <option value="">Select Member</option>
                        <?php foreach ($staff_info as $staff) { ?>
                          <option value="<?= $staff['user_id'] ?>">
                            <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>



                <div class="form-group" app-field-wrapper="description">
                  <label for="description" class="control-label">Description</label>
                  <textarea id="description" name="description" class="form-control" placeholder="Enter Description"
                    rows="3" required></textarea>
                </div>


              </div>
            </div>
          </div>
          <div class="modal-footer " style="padding:10px;">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-info">Save</button>
          </div>
          <?= form_close(); ?>
        </div>
      </div>
    </div>
    <!--  -->

    <!-- time Log MOdal -->
    <div class="modal fade" id="timelog_modal" tabindex="-1" role="dialog" aria-labelledby="timelog_modalLabel"
      aria-hidden="true">
      <div class="modal-dialog " role="document">
        <div class="modal-content" style="width: 110%;">
          <div class="modal-header">
            <h5 class="modal-title" id="groupsModalLabel" style="color: #fff;">New Timelogs </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                aria-hidden="true">&times;</span> </button>
          </div>
          <form action="<?= base_url('erp/timelogs-save'); ?>" method="POST">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <input type="hidden" id="name" name="project_id" value="<?= $project_data['project_id'] ?>"
                      required>

                    <div class="form-group col-6" app-field-wrapper="start_date">
                      <label for="start_date" class="control-label">Start Date <small
                          class="req text-danger">*</small></label>
                      <input type="date" id="start_date" name="start_date" class="form-control" required
                        autocomplete="off">
                    </div>
                    <div class="form-group col-6" app-field-wrapper="start_time">
                      <label for="start_time" class="control-label">Start Time <small
                          class="req text-danger">*</small></label>
                      <input type="time" id="start_time" name="start_time" class="form-control" required
                        autocomplete="off">
                    </div>

                    <div class="form-group col-6" app-field-wrapper="due_date">
                      <label for="due_date" class="control-label">Due Date <small
                          class="req text-danger">*</small></label>
                      <input type="date" id="due_date" name="due_date" class="form-control" required autocomplete="off">
                    </div>
                    <div class="form-group col-6" app-field-wrapper="due_time">
                      <label for="due_time" class="control-label">Due Time <small
                          class="req text-danger">*</small></label>
                      <input type="time" id="due_time" name="due_time" class="form-control" required autocomplete="off">
                    </div>

                    <div class="form-group col-12" app-field-wrapper="name">
                      <label for="name" class="control-label">Task <small class="req text-danger">*</small></label>
                      <select name="task_id" class="form-control" required>
                        <option value="">Select One</option>
                        <?php foreach ($task as $list) { ?>
                          <option value="<?= $list['task_id']; ?>"><?= $list['task_name']; ?></option>
                        <?php } ?>
                      </select>
                    </div>

                    <div class="form-group col-12" app-field-wrapper="name">
                      <label for="employee">Member</label>
                      <select multiple name="member[]" class="form-control" data-plugin="select_hrm">
                          <option value="">Select Member</option>
                          <?php foreach ($staff_info as $staff) { ?>
                              <option value="<?= $staff['user_id'] ?>">
                                  <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                              </option>
                          <?php } ?>
                      </select>
                    </div>

                    <div class="form-group col-12" app-field-wrapper="description">
                      <label for="description" class="control-label">Note</label>
                      <textarea id="description" name="note" class="form-control" placeholder="Enter Note"
                        rows="3"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer" style="padding:10px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-info">Save</button>
              </div>
          </form>

        </div>
      </div>
    </div>
    <!--  -->

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
      $(document).ready(function() {
        $('a[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
          if ($(e.target).attr('id') === 'pills-invoice-tab') {
            const url = "<?= base_url('erp/invoices-list'); ?>";
            $('#invoice-content').html('<p>Loading...</p>'); // Optional: Add a loading indicator
            $.ajax({
              url: url,
              type: 'GET',
              success: function(response) {
                $('#invoice-content').html(response);
              },
              error: function() {
                $('#invoice-content').html('<p>Error loading content.</p>');
              }
            });
          }
        });
      });
    </script>
    <script>
      Dropzone.options.addAttachment = {
        paramName: "attachment_file", // Field name for the uploaded file
        maxFilesize: 5, // Maximum file size in MB
        addRemoveLinks: true, // Show remove link
        dictDefaultMessage: "Drag files here to upload or click to browse", // Default dropzone message
        acceptedFiles: ".pdf,.docx,.doc,.xlsx,.jpg,.jpeg,.gif,.png", // Allowed file types
        clickable: "#fileDropzone", // Restricts clicks to the Dropzone area
        init: function() {
          this.on("success", function(file, response) {
            let res = JSON.parse(response);
            if (res.error) {
              toastr.error(res.error);

            } else {
              toastr.success(res.result);
            }

            setTimeout(() => {
              location.reload();
            }, 2000);
          });

          this.on("error", function(file, errorMessage) {
            toastr.error("Error: " + errorMessage);
          });
        }
      };
    </script>
    <script>
      $('#summernote').summernote({
        placeholder: 'Write your content here...',
        tabsize: 2,
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'italic', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });
    </script>

    <script>
      $('#summernote1').summernote({
        placeholder: 'Write your content here...',
        tabsize: 2,
        height: 300,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'italic', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });
    </script>

    <script>
      <?php if (session()->getFlashdata('error')): ?>
        toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
          timeOut: 5000
        });
      <?php endif; ?>

      <?php if (session()->getFlashdata('message')): ?>
        toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
          timeOut: 5000
        });
      <?php endif; ?>
    </script>
    <script>
      $(document).ready(function() {
        $('#milstone_table').DataTable({
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
    <script>
      $(document).ready(function() {
        $('#DataTables_Table_0').DataTable({
          paging: true,
          searching: true,
          ordering: true,
          lengthMenu: [10, 25, 50, 100],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records"
          },
          responsive: true
        });
      });
    </script>
    <script>
      $(document).ready(function() {
        $('#timelogs_Table_0').DataTable({
          paging: true,
          searching: true,
          ordering: true,
          lengthMenu: [10, 25, 50, 100],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records"
          },
          responsive: true
        });
      });
    </script>
    <script>
      $(document).ready(function() {
        $('#files_Table_0').DataTable({
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
    <script>
      $(document).ready(function() {
        $('#bugs_Table_1').DataTable({
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
    <script>
      $(document).ready(function() {
        $('#discussion_Table').DataTable({
          paging: true,
          searching: true,
          ordering: true,
          lengthMenu: [10, 25, 50, 100],
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records"
          },
          responsive: true
        });
      });
    </script>

    <script>
      $(document).ready(function() {
        $('#invoiceTable_0').DataTable({
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


    <script>
      $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
          var button = $(event.relatedTarget); // Button that triggered the modal
          var recordId = button.data('record-id'); // Get the record ID
          var projectId = button.data('project-id'); // Get the project ID (if exists)
          var url = button.data('url'); // Get the URL from the button's data-url attribute
          var modal = $(this);

          // Store these values in the confirm delete button
          modal.find('#confirmDeleteBtn')
            .data('record-id', recordId)
            .data('project-id', projectId)
            .data('url', url);
        });

        $('#confirmDeleteBtn').on('click', function() {
          var recordId = $(this).data('record-id'); // Record ID
          var projectId = $(this).data('project-id'); // Project ID
          var url = $(this).data('url'); // Endpoint URL

          // Disable the button to prevent multiple clicks
          $('#confirmDeleteBtn').prop('disabled', true);

          // Perform the AJAX request
          $.ajax({
            url: url + '/' + recordId, // Use the dynamic URL
            type: 'DELETE',
            dataType: "json",
            data: {
              <?= csrf_token() ?>: '<?= csrf_hash() ?>',
              project_id: projectId // Include project ID if available
            },
            success: function(response) {
              $('#confirmDeleteBtn').prop('disabled', false); // Re-enable the button
              if (response.result) {
                toastr.success(response.result);
                $('#deleteModal').modal('hide');
                setTimeout(function() {
                  if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                  } else {
                    location.reload();
                  }
                }, 1000);
              } else if (response.error) {
                toastr.error(response.error);
              }
              // Update CSRF token
              $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
            },
            error: function(xhr, status, error) {
              $('#confirmDeleteBtn').prop('disabled', false); // Re-enable the button
              toastr.error('An error occurred while deleting the record.');
              console.error("Error deleting record: ", error);
            }
          });
        });
      });
    </script>


    <script>
      // Set project progress value from PHP
      const projectProgressValue = <?= isset($project_data['project_progress']) ? $project_data['project_progress'] : 0 ?>;

      var options = {
        series: [projectProgressValue], // Percentage of project progress
        chart: {
          height: 150,
          type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            hollow: {
              size: '70%', // Adjusts the thickness
            },
            dataLabels: {
              name: {
                show: false,
              },
              value: {
                fontSize: '24px',
                color: '#ff4560', // Set to red color theme as requested
                fontWeight: 'bold',
                formatter: function(val) {
                  return val + "%";
                }
              }
            }
          }
        },
        colors: ['#ff4560'], // Red color theme
      };

      // Render the chart
      var chart = new ApexCharts(document.querySelector("#project-progress-chart-1"), options);

      chart.render();
    </script>

    <?php
    $created_at = $project_data['created_at']; // The creation timestamp
    $current_time = new DateTime(); // Current date and time
    $created_time = new DateTime($created_at); // Convert creation time to DateTime object
    $interval = $created_time->diff($current_time); // Calculate the difference
    $days = $interval->days; // Total days
    $hours = $interval->h; // Hours
    $total_hours = ($days * 24) + $hours; // Total hours
    ?>
    <script>
      // Extract data dynamically from PHP variables
      var totalExpenses = <?= isset($project_data['revenue']) ? $project_data['revenue'] : 0 ?>;
      var billedExpenses = <?= getpayamount($project_data['project_id']) ?>;
      var unbilledExpenses = totalExpenses - billedExpenses;
      var totalHours = <?= $total_hours ?>;

      // Ensure values are not undefined or null
      totalExpenses = totalExpenses || 0;
      billedExpenses = billedExpenses || 0;
      unbilledExpenses = unbilledExpenses || 0;
      totalHours = totalHours || 0;

      // Initialize chart with dynamic data
      var ctx = document.getElementById('timesheetsChart').getContext('2d');
      var timesheetsChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Total Expenses', 'Billed Expenses', 'Unbilled Expenses', 'Time Spent (hrs)'],
          datasets: [{
            label: 'Project Finances',
            data: [totalExpenses, billedExpenses, unbilledExpenses, totalHours],
            backgroundColor: [
              'rgba(54, 162, 235, 0.2)', // Blue for Total Expenses
              'rgba(75, 192, 192, 0.2)', // Green for Billed Expenses
              'rgba(255, 99, 132, 0.2)', // Red for Unbilled Expenses
              'rgba(255, 206, 86, 0.2)' // Yellow for Time Spent
            ],
            borderColor: [
              'rgba(54, 162, 235, 1)',
              'rgba(75, 192, 192, 1)',
              'rgba(255, 99, 132, 1)',
              'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 500
              }
            }
          }
        }
      });
    </script>

    <script>
      function updateChart(period) {
        document.getElementById('timePeriod').textContent = period;

        // Sample data for each period
        const dataSets = {
          'This Week': [2, 4, 3, 5, 1, 0, 6],
          'Last Week': [1, 3, 2, 4, 5, 6, 3],
          'This Month': [8, 12, 10, 6, 15, 9, 10],
          'Last Month': [5, 9, 11, 7, 8, 6, 4]
        };

        // Update chart data
        timesheetsChart.data.datasets[0].data = dataSets[period];
        timesheetsChart.update();
      }
    </script>
    <script>
      var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

      function openModal(id) {
        fetch(base_url + 'erp/milestones-getdata/' + id) // Add base_url to the request
          .then(response => response.text())
          .then(data => {
            document.getElementById('modalBody').innerHTML = data;
            let modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
            modal.show();
          })
          .catch(error => {
            console.error('Error loading data:', error);
            alert('Failed to load data. Please try again.');
          });
      }
    </script>





    <script>
      $(document).ready(function() {
        $.ajax({
          url: main_url + 'invoices/invoice_status_chart',
          contentType: "application/json; charset=utf-8",
          dataType: "json",
          success: function(response) {
            var options = {
              chart: {
                height: 130,
                type: 'pie',
              },
              series: [response.paid_count, response.unpaid_count],
              labels: [response.paid, response.unpaid],
              legend: {
                show: true,
                offsetY: 10,
              },
              dataLabels: {
                enabled: true,
                dropShadow: {
                  enabled: false,
                }
              },
              theme: {
                mode: 'light',
                palette: 'palette4',
                monochrome: {
                  enabled: true,
                  color: '#64d999',
                  shadeTo: 'light',
                  shadeIntensity: 0.65
                }
              },
              responsive: [{
                breakpoint: 768,
                options: {
                  chart: {
                    height: 320,

                  },
                  legend: {
                    position: 'bottom',
                    offsetY: 0,
                  }
                }
              }]
            }
            //alert(response.iseries);
            var chart = new ApexCharts(document.querySelector("#invoice-status-chart"), options);
            chart.render();
          },
          error: function(data) {
            console.log(data);
          }
        });

      });
    </script>