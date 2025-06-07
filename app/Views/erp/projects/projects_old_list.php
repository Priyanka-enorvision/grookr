<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ProjectsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ProjectsModel = new ProjectsModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

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
    $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->where('is_active', 1)->findAll();
    $all_clients = $UsersModel->where('company_id', $company_id)->where('user_type', 'customer')->where('is_active', 1)->findAll();

    // Count total projects
    $total_projects = $ProjectsModel->where('company_id', $company_id)->groupStart()->where('added_by', $user_id)->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')->groupEnd()->countAllResults();

    // Count projects by status
    $not_started = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 0, $expert_id);
    $in_progress = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 1, $expert_id);
    $completed = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 2, $expert_id);
    $cancelled = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 3, $expert_id);
    $hold = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 4, $expert_id);
} else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->where('is_active', 1)->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->where('is_active', 1)->findAll();
    $total_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
    $not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
    $in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
    $hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
}
?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="d-flex justify-content-end mt-2">
    <div class="dropdown">
        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            View
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="margin-right: 5rem !important;">
            <?php if (in_array('projects_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="nav-item clickable"> <a href="<?= site_url('erp/projects-calendar'); ?>" class="mb-3 nav-link"><span class="sw-icon feather icon-calendar"></span>
                        <?= lang('Dashboard.xin_acc_calendar'); ?>
                        <div class="text-muted small">
                            <?= lang('Projects.xin_projects_calendar'); ?>
                        </div>
                    </a> </li>
            <?php } ?>
            <?php if (in_array('projects_sboard', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="nav-item clickable"> <a href="<?= site_url('erp/projects-scrum-board'); ?>" class="mb-3 nav-link"><span class="sw-icon fas fa-tasks"></span>
                        <?= lang('Dashboard.xin_projects_scrm_board'); ?>
                        <div class="text-muted small">
                            <?= lang('Main.xin_view'); ?>
                            <?= lang('Projects.xin_projects_kanban_board'); ?>
                        </div>
                    </a> </li>
            <?php } ?>
        </div>
    </div>
</div>
<?php if (in_array('project1', staff_role_resource()) || in_array('projects_calendar', staff_role_resource()) || in_array('projects_sboard', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

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
        <?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <div id="add_form" class="collapse add-form <?= $get_animate; ?>" data-parent="#accordion" style="">
                <?php $attributes = array('name' => 'add_project', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('user_id' => 0); ?>
                <?php echo form_open('erp/projects/add_project', $attributes, $hidden); ?>
                <div class="card mb-2">
                    <div id="accordion">
                        <div class="card-header">
                            <h5>
                                <?= lang('Main.xin_add_new'); ?>
                                <?= lang('Projects.xin_project'); ?>
                            </h5>
                            <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                                    <?= lang('Main.xin_hide'); ?>
                                </a> </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="title"><?php echo lang('Dashboard.xin_title'); ?> <span class="text-danger">*</span></label>
                                        <input class="form-control" placeholder="<?php echo lang('Dashboard.xin_title'); ?>" name="title" type="text">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="client_id"><?php echo lang('Projects.xin_client'); ?> <span class="text-danger">*</span></label>
                                        <select name="client_id" id="client_id" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_client'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($all_clients as $client) { ?>
                                                <option value="<?= $client['user_id'] ?>">
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
                                            <input class="form-control" placeholder="<?php echo lang('Projects.xin_estimated_hour'); ?>" name="budget_hours" type="time">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee"><?php echo lang('Projects.xin_p_priority'); ?></label>
                                        <select name="priority" class="form-control select-border-color border-warning" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_p_priority'); ?>">
                                            <option value="1"><?php echo lang('Projects.xin_highest'); ?></option>
                                            <option value="2"><?php echo lang('Projects.xin_high'); ?></option>
                                            <option value="3"><?php echo lang('Projects.xin_normal'); ?></option>
                                            <option value="4"><?php echo lang('Projects.xin_low'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control " placeholder="<?php echo lang('Projects.xin_start_date'); ?>" name="start_date" type="date">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control " placeholder="<?php echo lang('Projects.xin_end_date'); ?>" name="end_date" type="date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="summary"><?php echo lang('Main.xin_summary'); ?> <span class="text-danger">*</span></label>
                                        <textarea class="form-control" placeholder="<?php echo lang('Main.xin_summary'); ?>" name="summary" cols="30" rows="1" id="summary"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="revenue"><?php echo lang('Main.xin_revenue'); ?></label>
                                        <div class="input-group">
                                            <input class="form-control" placeholder="<?php echo lang('Main.xin_revenue'); ?>" name="revenue" type="number">
                                        </div>
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
                                    <div class="form-group" id="employee_ajax">
                                        <label for="assigned_to"><?php echo lang('Projects.xin_project_users'); ?></label>
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
                                        <label for="description"><?php echo lang('Main.xin_description'); ?></label>
                                        <textarea class="form-control editor" placeholder="<?php echo lang('Main.xin_description'); ?>" name="description" cols="30" rows="2" id="description"></textarea>
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
                    </div>
                </div>
                <?= form_close(); ?>
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
        $filter_data = $session->get('project_data');
        $selected_assigned_to = $filter_data['project_user'] ?? '';
        $selected_status = $filter_data['project_status'] ?? '';
        $selected_expert_to = $filter_data['project_expert'] ?? '';
        ?>

        <div class="card user-profile-list">
            <div class="card-header">
                <h5>
                    <?= lang('Main.xin_list_all'); ?>
                    <?= lang('Dashboard.left_projects'); ?>
                </h5>
                <div class="card-header-right">

                    <?php if ($user_info['user_type'] == 'staff') {

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
                                <?php foreach ($applyExpertData as $staff) {
                                    $selected = ($staff['expertId'] == $selected_expert_to) ? 'selected' : ''; ?>
                                    <option value="<?= $staff['expertId'] ?>" <?= $selected ?>>
                                        <?= $staff['expertFullName'] ?>
                                    </option>
                                <?php } ?>
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
                                <?php foreach ($applyExpertData as $staff) {
                                    $selected = ($staff['expertId'] == $selected_expert_to) ? 'selected' : ''; ?>
                                    <option value="<?= $staff['expertId'] ?>" <?= $selected ?>>
                                        <?= $staff['expertFullName'] ?>
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

                        <a href="<?= site_url() . 'erp/projects-scrum-board'; ?>" class="btn btn-sm waves-effect waves-light btn-primary btn-icon" data-toggle="tooltip" data-placement="top" title="<?= lang('Projects.xin_projects_kanban_board'); ?>">
                            <i class="fas fa-th-large"></i>
                        </a>
                    </div>
                    <div class="mr-2">
                        <?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                            <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm">
                                <i data-feather="plus"></i> <?= lang('Main.xin_add_new'); ?>
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
                                <th><?php echo lang('Dashboard.left_projects'); ?></th>
                                <th><?php echo lang('Projects.xin_client'); ?></th>
                                <th><i class="fa fa-calendar"></i> <?php echo lang('Projects.xin_start_date'); ?></th>
                                <th><i class="fa fa-calendar"></i> <?php echo lang('Projects.xin_end_date'); ?></th>
                                <th><i class="fa fa-user"></i> <?php echo lang('Projects.xin_project_users'); ?></th>
                                <th><?php echo lang('Projects.xin_p_priority'); ?></th>
                                <th><?php echo lang('Projects.dashboard_xin_progress'); ?></th>
                                <th>Status</th>
                                <th>Added By</th>
                                <th>Revenue</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>