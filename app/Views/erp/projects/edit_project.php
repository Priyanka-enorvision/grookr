<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ProjectsModel;
use App\Models\TrackgoalsModel;
use App\Models\ConstantsModel;
use App\Models\PlanningEntityModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ProjectsModel = new ProjectsModel();
$TrackgoalsModel = new TrackgoalsModel();
$ConstantsModel = new ConstantsModel();
$PlanningEntityModel = new PlanningEntityModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$request = \Config\Services::request();

$segment_id = $request->getUri()->getSegment(3);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
try {
    $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
    if (!$user_info) {
        throw new \RuntimeException('User not found');
    }
    $logged_in_company_id = $user_info['company_id'] ?? null;
    $get_companies = $UsersModel
        ->where('user_type', 'company');

    if ($logged_in_company_id) {
        $get_companies = $get_companies->where('company_id !=', $logged_in_company_id);
    }
    $get_companies = $get_companies->findAll();
} catch (\Exception $e) {
    log_message('error', 'Company filter error: ' . $e->getMessage());
    $get_companies = [];
}

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


    // Retrieve staff and client information
    $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $company_id)->where('user_type', 'customer')->findAll();
    $planning_entities = $PlanningEntityModel
        ->groupStart()
        ->where('company_id', $company_id)
        ->orWhere('company_id', 0)
        ->groupEnd()
        ->groupStart()
        ->where(['user_type' => $user_info['user_type']])
        ->orWhere('user_type', '')
        ->groupEnd()
        ->findAll();
    $project_data = $ProjectsModel->where('company_id', $company_id)->where('project_id', $project_id)->first();
} else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
    $planning_entities = $PlanningEntityModel
        ->groupStart()
        ->where('company_id', $usession['sup_user_id'])
        ->orWhere('company_id', 0)
        ->groupEnd()
        ->groupStart()
        ->where(['user_type' => $user_info['user_type']])
        ->orWhere('user_type', '')
        ->groupEnd()
        ->findAll();

    $project_data = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('project_id', $project_id)->first();
}

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
                <?php $attributes = array('name' => 'update_project', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('token' => $segment_id); ?>
                <?= form_open('erp/update-project', $attributes, $hidden); ?>
                <div class="card mb-2">
                    <div id="accordion">
                        <div class="card-header">
                            <h5>
                                Edit Project
                            </h5>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="title"><?php echo lang('Dashboard.xin_title'); ?> <span class="text-danger">*</span></label>
                                        <input class="form-control" placeholder="<?php echo lang('Dashboard.xin_title'); ?>" name="title" type="text"
                                            value="<?= $project_data['title']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="client_id"><?php echo lang('Projects.xin_client'); ?> <span class="text-danger">*</span></label>
                                        <select name="client_id" id="client_id" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_client'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($all_clients as $client) { ?>
                                                <option value="<?= $client['user_id'] ?>" <?php if (isset($project_data['client_id']) && $project_data['client_id'] == $client['user_id']) echo 'selected'; ?>>
                                                    <?= $client['first_name'] . ' ' . $client['last_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>



                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="budget_hours"><?php echo lang('Projects.xin_estimated_hour'); ?></label>
                                        <div class="input-group">
                                            <input class="form-control" placeholder="<?php echo lang('Projects.xin_estimated_hour'); ?>" name="budget_hours" type="text"
                                                value="<?= $project_data['budget_hours']; ?>">
                                            <div class="input-group-append"><span class="input-group-text"><i class="fas fa-clock"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee"><?php echo lang('Projects.xin_p_priority'); ?></label>
                                        <select name="priority" class="form-control select-border-color border-warning" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_p_priority'); ?>">
                                            <option value="1" <?php if (isset($project_data['priority']) && $project_data['priority'] == 1) echo 'selected'; ?>><?php echo lang('Projects.xin_highest'); ?></option>
                                            <option value="2" <?php if (isset($project_data['priority']) && $project_data['priority'] == 2) echo 'selected'; ?>><?php echo lang('Projects.xin_high'); ?></option>
                                            <option value="3" <?php if (isset($project_data['priority']) && $project_data['priority'] == 3) echo 'selected'; ?>><?php echo lang('Projects.xin_normal'); ?></option>
                                            <option value="4" <?php if (isset($project_data['priority']) && $project_data['priority'] == 4) echo 'selected'; ?>><?php echo lang('Projects.xin_low'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control date" placeholder="<?php echo lang('Projects.xin_start_date'); ?>" name="start_date" type="text"
                                                value="<?= $project_data['start_date']; ?>">
                                            <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control date" placeholder="<?php echo lang('Projects.xin_end_date'); ?>" name="end_date" type="text"
                                                value="<?= $project_data['end_date']; ?>">
                                            <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="summary"><?php echo lang('Main.xin_summary'); ?> <span class="text-danger">*</span></label>
                                        <textarea class="form-control" placeholder="<?php echo lang('Main.xin_summary'); ?>" name="summary" cols="30" rows="1" id="summary"><?= $project_data['summary']; ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-group">
                                        <label for="progress">
                                            <?= lang('Projects.dashboard_xin_progress'); ?>
                                        </label>
                                        <input type="hidden" id="progres_val" name="progres_val" value="<?= $project_data['project_progress']; ?>">
                                        <input type="range" id="range_grid" class="form-range" min="0" max="100" value="<?= $project_data['project_progress']; ?>" step="1">
                                        <div class="progress">
                                            <div id="progress_bar" class="progress-bar" role="progressbar" style="width: <?= $project_data['project_progress']; ?>%;" aria-valuenow="<?= $project_data['project_progress']; ?>" aria-valuemin="0" aria-valuemax="100"><?= $project_data['project_progress']; ?>%</div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-6 mb-3">
                                    <div class="form-group project-status">
                                        <label for="status">
                                            <?= lang('Main.dashboard_xin_status'); ?> <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control demo-movie" name="status">
                                            <option value="0" <?php if ($project_data['status'] == '0'): ?> selected <?php endif; ?>>
                                                <?= lang('Projects.xin_not_started'); ?>
                                            </option>
                                            <option value="1" <?php if ($project_data['status'] == '1'): ?> selected <?php endif; ?>>
                                                <?= lang('Projects.xin_in_progress'); ?>
                                            </option>
                                            <option value="3" <?php if ($project_data['status'] == '3'): ?> selected <?php endif; ?>>
                                                <?= lang('Projects.xin_project_cancelled'); ?>
                                            </option>
                                            <option value="4" <?php if ($project_data['status'] == '4'): ?> selected <?php endif; ?>>
                                                <?= lang('Projects.xin_project_hold'); ?>
                                            </option>
                                            <option value="2" <?php if ($project_data['status'] == '2'): ?> selected <?php endif; ?>>
                                                <?= lang('Projects.xin_completed'); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_id">
                                            <i class="fas fa-building"></i> <?php echo "Companies"; ?>
                                        </label>
                                        <select name="company_id" id="company_id" class="form-control" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "Select Company"; ?>">
                                            <option value=""></option>
                                            <?php foreach ($get_companies as $company) : ?>
                                                <option value="<?= $company['user_id'] ?>"
                                                    <?= (isset($project_data['companies_ID']) && $project_data['companies_ID'] == $company['user_id']) ? 'selected="selected"' : '' ?>>
                                                    <?= $company['company_name'] ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employe_id">
                                            <i class="fas fa-users"></i> <?php echo "Employees"; ?>
                                        </label>
                                        <select name="employe_id" id="employe_id" class="form-control" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "Select Employee"; ?>">
                                            <option value="">First select a company</option>
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
                                                    <?php if (isset($project_data['expert_to']) && $project_data['expert_to'] == $staff['expertId']) echo 'selected'; ?>>
                                                    <?= $staff['expertFullName'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <?php
                                $assigned_to = explode(',', $project_data['assigned_to']);

                                $current_user_id = $usession['sup_user_id'] ?? 0;
                                $assigned_to = array_unique(array_merge($assigned_to, [$current_user_id]));
                                $old_assigned = old('assigned_to', $assigned_to);
                                ?>

                                <div class="col-md-6">
                                    <div class="form-group" id="employee_ajax">
                                        <label for="assigned_to"><?= lang('Projects.xin_project_users') ?></label>

                                        <input type="hidden" name="assigned_to[]" value="<?= $current_user_id ?>">

                                        <select multiple name="assigned_to[]"
                                            class="form-control <?= session('errors.assigned_to') ? 'is-invalid' : '' ?>"
                                            data-plugin="select_hrm"
                                            data-placeholder="<?= lang('Projects.xin_project_users') ?>">
                                            <?php foreach ($staff_info as $staff): ?>
                                                <?php
                                                $is_current_user = ($staff['user_id'] == $current_user_id);
                                                $is_selected = in_array($staff['user_id'], $old_assigned);
                                                ?>

                                                <option value="<?= $staff['user_id'] ?>"
                                                    <?= $is_selected ? 'selected' : '' ?>
                                                    <?= $is_current_user ? 'disabled style="background-color:#f5f5f5;"' : '' ?>>
                                                    <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                                                    <?= $is_current_user ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>

                                        <?php if (session('errors.assigned_to')): ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.assigned_to')) ?></div>
                                        <?php endif ?>

                                        <small class="text-muted">Note: You cannot remove yourself from this project</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_for">Billing Type</label>
                                        <select name="billing_type" class="form-control" data-plugin="select_hrm">
                                            <option value="fixed_rate" <?php if (isset($project_data['billing_type']) && $project_data['billing_type'] == 'fixed_rate') echo 'selected'; ?>>Fixed Rate</option>
                                            <option value="project" <?php if (isset($project_data['billing_type']) && $project_data['billing_type'] == 'project') echo 'selected'; ?>>Project Hours</option>
                                            <option value="task" <?php if (isset($project_data['billing_type']) && $project_data['billing_type'] == 'task') echo 'selected'; ?>>Task Hours</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tags"><i class="fa fa-tag" aria-hidden="true"></i> Tags</label>
                                        <select name="tags" class="form-control" data-plugin="select_hrm">
                                            <option value="accounting" <?php if ($project_data['tags'] == 'accounting') echo 'selected'; ?>>Accounting</option>
                                            <option value="aggregator" <?php if ($project_data['tags'] == 'aggregator') echo 'selected'; ?>>Aggregator</option>
                                            <option value="agriculture" <?php if ($project_data['tags'] == 'agriculture') echo 'selected'; ?>>Agriculture</option>
                                            <option value="airline" <?php if ($project_data['tags'] == 'airline') echo 'selected'; ?>>Airline</option>
                                            <option value="alternate_constructuction" <?php if ($project_data['tags'] == 'alternate_constructuction') echo 'selected'; ?>>Alternate Constructuction</option>
                                            <option value="anatomy" <?php if ($project_data['tags'] == 'anatomy') echo 'selected'; ?>>Anatomy</option>
                                            <option value="app_development" <?php if ($project_data['tags'] == 'app_development') echo 'selected'; ?>>App Development</option>
                                            <option value="application" <?php if ($project_data['tags'] == 'application') echo 'selected'; ?>>Application</option>
                                            <option value="cab_service" <?php if ($project_data['tags'] == 'cab_service') echo 'selected'; ?>>Cab Service</option>
                                            <option value="blogging" <?php if ($project_data['tags'] == 'blogging') echo 'selected'; ?>>Blogging</option>
                                            <option value="clothing" <?php if ($project_data['tags'] == 'clothing') echo 'selected'; ?>>Clothing</option>

                                        </select>
                                    </div>
                                </div>


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="revenue"><?php echo lang('Main.xin_revenue'); ?></label>
                                        <div class="input-group">
                                            <input class="form-control" placeholder="<?php echo lang('Main.xin_revenue'); ?>" name="revenue" type="number"
                                                value="<?= $project_data['revenue']; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entities_id">Entities </label>
                                        <select name="entities_id" id="entities_id" class="form-control" data-plugin="select_hrm">
                                            <option value="">Select Entity</option>
                                            <?php foreach ($planning_entities as $entity) { ?>
                                                <option value="<?= esc($entity['id']) ?>" <?= (isset($project_data['entities_id']) && $project_data['entities_id'] == $entity['id'] ? 'selected' : '') ?>>
                                                    <?= esc($entity['entity']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo lang('Main.xin_description'); ?></label>
                                        <textarea class="form-control editor" placeholder="<?php echo lang('Main.xin_description'); ?>" name="description" cols="30" rows="2" id="description"><?= $project_data['description']; ?></textarea>
                                    </div>
                                </div>
                                <?php $associated_goals = explode(',', $project_data['associated_goals']); ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="employee"><?php echo lang('Main.xin_associated_goals'); ?></label>
                                        <input type="hidden" value="0" name="associated_goals[]" />
                                        <select multiple name="associated_goals[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Main.xin_associated_goals'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($track_goals as $track_goal) { ?>
                                                <?php $tracking_type = $ConstantsModel->where('constants_id', $track_goal['tracking_type_id'])->first(); ?>
                                                <option value="<?= $tracking_type['constants_id'] ?>" <?php if (in_array($tracking_type['constants_id'], $associated_goals)): ?> selected="selected" <?php endif; ?>>
                                                    <?= $tracking_type['category_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a class="btn btn-danger" href="<?= base_url('erp/projects-list/'); ?>">
                                Back
                            </a>
                            &nbsp;
                            <button type="submit" class="btn btn-info">
                                Update
                            </button>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
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



<script>
    $(document).ready(function() {
        $('[data-plugin="select_hrm"]').select2();
        $('#company_id').change(function() {
            var selectedEmployeeId = <?= isset($project_data['employe_ID']) ? $project_data['employe_ID'] : 'null' ?>;
            var company_id = $(this).val();

            if (company_id) {
                $.ajax({
                    url: '<?= base_url('erp/get-employelist'); ?>',
                    type: 'GET',
                    data: {
                        company_id: company_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#employe_id').empty();
                        $('#employe_id').append('<option value="">Select Employee</option>');

                        $.each(data, function(key, employee) {
                            var option = $('<option></option>')
                                .attr('value', employee.id)
                                .text(employee.name);

                            // If this employee is the selected one, mark it as selected
                            if (selectedEmployeeId && employee.id == selectedEmployeeId) {
                                option.attr('selected', 'selected');
                            }

                            $('#employe_id').append(option);
                        });

                        // Refresh select2 to show the selected value
                        $('#employe_id').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching employees: " + error);
                        $('#employe_id').empty();
                        $('#employe_id').append('<option value="">Error loading employees</option>');
                    }
                });
            } else {
                $('#employe_id').empty();
                $('#employe_id').append('<option value="">Select a company first</option>');
            }
        });

        // Trigger the change event on page load if company_id has a value
        if ($('#company_id').val()) {
            $('#company_id').trigger('change');
        }
    });
</script>