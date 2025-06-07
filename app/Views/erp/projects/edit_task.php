<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ProjectsModel;
use App\Models\TrackgoalsModel;
use App\Models\ConstantsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ProjectsModel = new ProjectsModel();
$TrackgoalsModel = new TrackgoalsModel();
$ConstantsModel = new ConstantsModel();
$MilestonesModel = new \App\Models\MilestonesModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$request = \Config\Services::request();

$segment_id = $request->getUri()->getSegment(3);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$logged_in_company_id = $user_info['company_id'] ?? null;

$employeelist = $UsersModel->where('user_type', 'staff')
    ->where('company_id !=', $logged_in_company_id)
    ->findAll();
$user_id = $usession['sup_user_id'];
$track_goals = $TrackgoalsModel->where('company_id', $usession['sup_user_id'])->orderBy('tracking_id', 'ASC')->findAll();

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
        curl_close($curl);
        die("cURL Error: " . curl_error($curl));
    }

    $expert_user_detail = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        curl_close($curl);
        die("JSON Decoding Error: " . json_last_error_msg());
    }

    curl_close($curl);

    $expert_id = $expert_user_detail['detail']['id'] ?? null;

    // Function to count projects by status
    function getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, $status, $expert_id = null)
    {
        $builder = $ProjectsModel->where('company_id', $company_id)
            ->where('status', $status)
            ->groupStart()
            ->where('added_by', $user_id)
            ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0');

        if ($expert_id !== null) {
            $builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
        }

        $builder->groupEnd();

        return $builder->countAllResults();
    }

    // Retrieve staff and client information
    $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $company_id)->where('user_type', 'customer')->findAll();

    // Count total projects
    $total_projects = $ProjectsModel->where('company_id', $company_id)
        ->groupStart()
        ->where('added_by', $user_id)
        ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')
        ->groupEnd()
        ->countAllResults();

    // Count projects by status
    $not_started = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 0, $expert_id);
    $in_progress = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 1, $expert_id);
    $completed = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 2, $expert_id);
    $cancelled = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 3, $expert_id);
    $hold = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 4, $expert_id);
} else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
    $total_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
    $not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
    $in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
    $hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
}

$result  = $MilestonesModel->where('project_id', $task_data['project_id'])->orderBy('id', 'ASC')->findAll();
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
    .custom-checkbox .form-check-input {
        width: 20px;
        height: 20px;
    }

    .custom-checkbox .form-check-label {
        font-size: 16px;
        margin-left: 8px;
    }

    .form-range {
        width: 100%;
        height: 8px;
        background: #ff0000;
        border-radius: 5px;
        outline: none;
    }

    .form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: #ff0000;
        border-radius: 50%;
        cursor: pointer;
    }

    .form-range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: #ff0000;
        border-radius: 50%;
        cursor: pointer;
    }

    .form-range::-moz-range-track {
        background: #ff0000;
        height: 8px;
        border-radius: 5px;
    }

    .progress-bar {
        background-color: #ff0000 !important;
    }
</style>
<div class="row m-b-1 animated fadeInRight">
    <div class="col-md-12">
        <?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <div id="add_form" class="add-form " data-parent="#accordion" style="">

                <form action="<?= base_url('erp/update-projectTask/' . $task_data['task_id']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="card mb-2">
                        <div id="accordion">
                            <div class="card-header">
                                <h5>
                                    Edit Task
                                </h5>

                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="title"><?php echo lang('Dashboard.xin_title'); ?> <span class="text-danger">*</span></label>
                                            <input class="form-control" placeholder="<?php echo lang('Dashboard.xin_title'); ?>" name="task_name" type="text"
                                                value="<?= $task_data['task_name']; ?>" required>

                                            <input type="hidden" name="project_id" value="<?= $task_data['project_id']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group" app-field-wrapper="name">
                                            <label for="name" class="control-label">Milestones</label>
                                            <select name="milestone_id" class="form-control">
                                                <option value="">Select One</option>
                                                <?php foreach ($result as $list): ?>
                                                    <option value="<?= $list['id']; ?>"
                                                        <?= isset($task_data['milestones_id']) && $task_data['milestones_id'] == $list['id'] ? 'selected' : ''; ?>>
                                                        <?= $list['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control date" placeholder="<?php echo lang('Projects.xin_start_date'); ?>" name="start_date" type="text"
                                                    value="<?= $task_data['start_date']; ?>">
                                                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input class="form-control date" placeholder="<?php echo lang('Projects.xin_end_date'); ?>" name="end_date" type="text"
                                                    value="<?= $task_data['end_date']; ?>">
                                                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="budget_hours"><?php echo lang('Projects.xin_estimated_hour'); ?></label>
                                            <div class="input-group">
                                                <input class="form-control" placeholder="<?php echo lang('Projects.xin_estimated_hour'); ?>" name="task_hour" type="text"
                                                    value="<?= $task_data['task_hour']; ?>">
                                                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="summary"><?php echo lang('Main.xin_summary'); ?> <span class="text-danger">*</span></label>
                                            <textarea class="form-control" placeholder="<?php echo lang('Main.xin_summary'); ?>" name="summary" cols="30" rows="1" id="summary"><?= $task_data['summary']; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="progress">
                                                <?= lang('Projects.dashboard_xin_progress'); ?>
                                            </label>
                                            <input type="hidden" id="progres_val" name="progres_val" value="<?= $task_data['task_progress']; ?>">
                                            <input type="range" id="range_grid" class="form-range" min="0" max="100" value="<?= $task_data['task_progress']; ?>" step="1">
                                            <div class="progress">
                                                <div id="progress_bar" class="progress-bar" role="progressbar"
                                                    style="width: <?= $task_data['task_progress']; ?>%;"
                                                    aria-valuenow="<?= $task_data['task_progress']; ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                    <?= $task_data['task_progress']; ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="form-group project-status">
                                            <label for="status">
                                                <?= lang('Main.dashboard_xin_status'); ?> <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-control demo-movie" name="status">
                                                <option value="0" <?php if ($task_data['task_status'] == '0'): ?> selected <?php endif; ?>>
                                                    <?= lang('Projects.xin_not_started'); ?>
                                                </option>
                                                <option value="1" <?php if ($task_data['task_status'] == '1'): ?> selected <?php endif; ?>>
                                                    <?= lang('Projects.xin_in_progress'); ?>
                                                </option>
                                                <option value="3" <?php if ($task_data['task_status'] == '3'): ?> selected <?php endif; ?>>
                                                    <?= lang('Projects.xin_project_cancelled'); ?>
                                                </option>
                                                <option value="4" <?php if ($task_data['task_status'] == '4'): ?> selected <?php endif; ?>>
                                                    <?= lang('Projects.xin_project_hold'); ?>
                                                </option>
                                                <option value="2" <?php if ($task_data['task_status'] == '2'): ?> selected <?php endif; ?>>
                                                    <?= lang('Projects.xin_completed'); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="control-label mb-0">Employee</label>
                                            <select name="employee_id" class="form-control" data-plugin="select_hrm">
                                                <option value="">Select employee</option>
                                                <?php foreach ($employeelist as $list) { ?>
                                                    <option
                                                        value="<?= $list['user_id']; ?>"
                                                        <?= ($task_data['employe_ID'] == $list['user_id']) ? 'selected' : ''; ?>>
                                                        <?= $list['first_name'] . " " . $list['last_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expert_to">
                                                <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                                            </label>
                                            <input type="hidden" value="0" name="expert_to[]" />
                                            <select multiple name="expert_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo "Select Experts"; ?>">
                                                <option value=""></option>
                                                <?php foreach ($applyExpertData as $staff) { ?>
                                                    <option value="<?= $staff['expertId'] ?>"
                                                        <?php if (isset($task_data['expert_to']) && $task_data['expert_to'] == $staff['expertId']) echo 'selected'; ?>>
                                                        <?= $staff['expertFullName'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                    </div>
                                    <?php $assigned_to = explode(',', $task_data['assigned_to']); ?>
                                    <div class="col-md-6">
                                        <div class="form-group" id="employee_ajax">
                                            <label for="assigned_to"><?php echo lang('Projects.xin_project_users'); ?></label>
                                            <input type="hidden" value="0" name="assigned_to[]" />
                                            <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project_users'); ?>">

                                                <?php foreach ($staff_info as $staff) { ?>
                                                    <option value="<?= $staff['user_id'] ?>" <?php if (in_array($staff['user_id'], $assigned_to)): ?> selected="selected" <?php endif; ?>>
                                                        <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description"><?php echo lang('Main.xin_description'); ?></label>
                                            <textarea class="form-control editor" placeholder="<?php echo lang('Main.xin_description'); ?>" name="description" cols="30" rows="2" id="description"><?= $task_data['description']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a class="btn btn-danger" href="<?= base_url('erp/project-detail/' . uencode($task_data['project_id'])); ?>">
                                    Back
                                </a>
                                &nbsp;
                                <button type="submit" class="btn btn-info">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
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
    });

    $(document).ready(function() {
        $('#range_grid').on('input', function() {
            var value = $(this).val();
            $('#progres_val').val(value); // Update hidden input
            $('#progress_bar').css('width', value + '%').attr('aria-valuenow', value).text(value + '%');
        });
    });
</script>