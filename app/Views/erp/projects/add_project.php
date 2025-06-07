<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\PlanningEntityModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();
$PlanningEntityModel = new PlanningEntityModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

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
    $applyExpertData = [];
    error_log($e->getMessage());
}


if ($user_info['user_type'] == 'staff') {

    $user_id = $user_info['user_id'];
    $company_id = $user_info['company_id'];

    try {
        // Initialize cURL
        $curl = curl_init();
        $url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id"; // API endpoint

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

        curl_close($curl);

        if ($http_status !== 200) {
            throw new Exception("Request failed with status code: $http_status");
        }

        $expert_user_detail = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON Decoding Error: ' . json_last_error_msg());
        }

        if (isset($expert_user_detail['detail']) && isset($expert_user_detail['detail']['id'])) {
            $expert_id = $expert_user_detail['detail']['id'];
        } else {
            throw new Exception("Error: 'id' not found in the response detail.");
        }

        if ($expert_id === null) {
            throw new Exception("No expert ID found.");
        }
    } catch (Exception $e) {
        $expert_id = null;
        error_log($e->getMessage());
    }


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
</style>
<div class="row m-b-1 animated fadeInRight">
    <div class="col-md-12">
        <?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <div id="add_form" class="add-form " data-parent="#accordion" style="">
                <?php $attributes = array('name' => 'add_project', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('user_id' => '0'); ?>
                <?php echo form_open('erp/add-project', $attributes, $hidden); ?>
                <div class="card mb-2">
                    <div id="accordion">
                        <div class="card-header">
                            <h5>
                                <?= lang('Main.xin_add_new'); ?>
                                <?= lang('Projects.xin_project'); ?>
                            </h5>
                        </div>
                        <div class="card-body">


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="title"><?php echo lang('Dashboard.xin_title'); ?> <span class="text-danger">*</span></label>
                                        <input class="form-control <?= session('errors.title') ? 'is-invalid' : '' ?>"
                                            placeholder="<?php echo lang('Dashboard.xin_title'); ?>"
                                            name="title" type="text"
                                            value="<?= old('title') ?>">
                                        <?php if (session('errors.title')) : ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.title')) ?></div>
                                        <?php endif ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="client_id"><?php echo lang('Projects.xin_client'); ?> <span class="text-danger">*</span></label>
                                        <select name="client_id" id="client_id"
                                            class="form-control <?= session('errors.client_id') ? 'is-invalid' : '' ?>"
                                            data-plugin="select_hrm"
                                            data-placeholder="<?php echo lang('Projects.xin_client'); ?>">
                                            <option value=""></option>
                                            <?php foreach ($all_clients as $client) : ?>
                                                <option value="<?= $client['user_id'] ?>"
                                                    <?= old('client_id') == $client['user_id'] ? 'selected' : '' ?>>
                                                    <?= $client['first_name'] . ' ' . $client['last_name'] ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                        <?php if (session('errors.client_id')) : ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.client_id')) ?></div>
                                        <?php endif ?>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="budget_hours"><?php echo lang('Projects.xin_estimated_hour'); ?></label>
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="<?php echo lang('Projects.xin_estimated_hour'); ?>"
                                                name="budget_hours" type="text"
                                                value="<?= old('budget_hours') ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="priority"><?php echo lang('Projects.xin_p_priority'); ?></label>
                                        <select name="priority"
                                            class="form-control select-border-color border-warning"
                                            data-plugin="select_hrm"
                                            data-placeholder="<?php echo lang('Projects.xin_p_priority'); ?>">
                                            <option value="1" <?= old('priority', '3') == '1' ? 'selected' : '' ?>>
                                                <?php echo lang('Projects.xin_highest'); ?>
                                            </option>
                                            <option value="2" <?= old('priority', '3') == '2' ? 'selected' : '' ?>>
                                                <?php echo lang('Projects.xin_high'); ?>
                                            </option>
                                            <option value="3" <?= old('priority', '3') == '3' ? 'selected' : '' ?>>
                                                <?php echo lang('Projects.xin_normal'); ?>
                                            </option>
                                            <option value="4" <?= old('priority', '3') == '4' ? 'selected' : '' ?>>
                                                <?php echo lang('Projects.xin_low'); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date"><?php echo lang('Projects.xin_start_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control date <?= session('errors.start_date') ? 'is-invalid' : '' ?>"
                                                placeholder="<?php echo lang('Projects.xin_start_date'); ?>"
                                                name="start_date" type="text"
                                                value="<?= old('start_date') ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <?php if (session('errors.start_date')) : ?>
                                                <div class="invalid-feedback"><?= esc(session('errors.start_date')) ?></div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="end_date"><?php echo lang('Projects.xin_end_date'); ?> <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input class="form-control date <?= session('errors.end_date') ? 'is-invalid' : '' ?>"
                                                placeholder="<?php echo lang('Projects.xin_end_date'); ?>"
                                                name="end_date" type="text"
                                                value="<?= old('end_date') ?>">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <?php if (session('errors.end_date')) : ?>
                                                <div class="invalid-feedback"><?= esc(session('errors.end_date')) ?></div>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="summary"><?php echo lang('Main.xin_summary'); ?> <span class="text-danger">*</span></label>
                                        <textarea class="form-control <?= session('errors.summary') ? 'is-invalid' : '' ?>"
                                            placeholder="<?php echo lang('Main.xin_summary'); ?>"
                                            name="summary" cols="30" rows="1"
                                            id="summary"><?= old('summary') ?></textarea>
                                        <?php if (session('errors.summary')) : ?>
                                            <div class="invalid-feedback"><?= esc(session('errors.summary')) ?></div>
                                        <?php endif ?>
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
                                                <option value="<?= $company['user_id'] ?>">
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




                                <input type="hidden" value="0" name="expert_to[]" />
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expert_to">
                                            <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                                        </label>
                                        <select multiple name="expert_to[]" class="form-control" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "Select Experts"; ?>">
                                            <option value=""></option>
                                            <?php foreach ($applyExpertData as $staff) : ?>
                                                <option value="<?= $staff['expertId'] ?>"
                                                    <?= in_array($staff['expertId'], old('expert_to', [])) ? 'selected' : '' ?>>
                                                    <?= $staff['expertFullName'] ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" value="0" name="assigned_to[]" />
                                <div class="col-md-6">
                                    <div class="form-group" id="employee_ajax">
                                        <label for="assigned_to"><?php echo lang('Projects.xin_project_users'); ?></label>
                                        <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm"
                                            data-placeholder="<?php echo lang('Projects.xin_project_users'); ?>">
                                            <?php foreach ($staff_info as $staff) : ?>
                                                <option value="<?= $staff['user_id'] ?>"
                                                    <?= in_array($staff['user_id'], old('assigned_to', [])) ? 'selected' : '' ?>>
                                                    <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="billing_for">Billing Type</label>
                                        <select name="billing_type" class="form-control" data-plugin="select_hrm">
                                            <option value="fixed_rate" <?= old('billing_type') == 'fixed_rate' ? 'selected' : '' ?>>Fixed Rate</option>
                                            <option value="project" <?= old('billing_type') == 'project' ? 'selected' : '' ?>>Project Hours</option>
                                            <option value="task" <?= old('billing_type') == 'task' ? 'selected' : '' ?>>Task Hours</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tags"><i class="fa fa-tag" aria-hidden="true"></i> Tags</label>
                                        <select name="tags" class="form-control" data-plugin="select_hrm">
                                            <option value="accounting" <?= old('tags') == 'accounting' ? 'selected' : '' ?>>Accounting</option>
                                            <option value="aggregator" <?= old('tags') == 'aggregator' ? 'selected' : '' ?>>Aggregator</option>
                                            <option value="agriculture" <?= old('tags') == 'agriculture' ? 'selected' : '' ?>>Agriculture</option>
                                            <option value="airline" <?= old('tags') == 'airline' ? 'selected' : '' ?>>Airline</option>
                                            <option value="alternate_constructuction" <?= old('tags') == 'alternate_constructuction' ? 'selected' : '' ?>>Alternate Constructuction</option>
                                            <option value="anatomy" <?= old('tags') == 'anatomy' ? 'selected' : '' ?>>Anatomy</option>
                                            <option value="app_development" <?= old('tags') == 'app_development' ? 'selected' : '' ?>>App Development</option>
                                            <option value="application" <?= old('tags') == 'application' ? 'selected' : '' ?>>Application</option>
                                            <option value="cab_service" <?= old('tags') == 'cab_service' ? 'selected' : '' ?>>Cab Service</option>
                                            <option value="blogging" <?= old('tags') == 'blogging' ? 'selected' : '' ?>>Blogging</option>
                                            <option value="clothing" <?= old('tags') == 'clothing' ? 'selected' : '' ?>>Clothing</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="revenue"><?php echo lang('Main.xin_revenue'); ?></label>
                                        <div class="input-group">
                                            <input class="form-control"
                                                placeholder="<?php echo lang('Main.xin_revenue'); ?>"
                                                name="revenue" type="number"
                                                value="<?= old('revenue') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entities_id">Entities</label>
                                        <select name="entities_id" id="entities_id" class="form-control" data-plugin="select_hrm">
                                            <option value="">Select Entity</option>
                                            <?php foreach ($planning_entities as $entity) : ?>
                                                <option value="<?= esc($entity['id']) ?>"
                                                    <?= old('entities_id', isset($selected_entity_id) ? $selected_entity_id : '') == $entity['id'] ? 'selected' : '' ?>>
                                                    <?= esc($entity['entity']) ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description"><?php echo lang('Main.xin_description'); ?></label>
                                        <textarea class="form-control editor"
                                            placeholder="<?php echo lang('Main.xin_description'); ?>"
                                            name="description" cols="30" rows="2"
                                            id="description"><?= old('description') ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group form-check custom-checkbox">
                                        <input type="checkbox" class="form-check-input"
                                            id="send_email" name="send_email"
                                            <?= old('send_email') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="send_email">Send project created email</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <a class="btn btn-danger" href="<?= base_url('erp/projects-list/'); ?>">Back</a>
                            &nbsp;
                            <button type="submit" class="btn btn-info">Save</button>
                        </div>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
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
</script>
<script>
    $(document).ready(function() {
        $('[data-plugin="select_hrm"]').select2();
        $('#company_id').change(function() {
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
                            $('#employe_id').append(
                                $('<option></option>')
                                .attr('value', employee.id)
                                .text(employee.name)
                            );
                        });

                        // Refresh select2 if you're using it
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
    });
</script>