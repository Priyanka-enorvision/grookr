<?php

use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\PlanningEntityModel;
use App\Models\YearPlanningModel;
use App\Models\MonthlyPlanningModel;

$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();
$PlanningEntityModel = new PlanningEntityModel();
$YearPlanningModel = new YearPlanningModel();
$MonthlyPlanningModel = new MonthlyPlanningModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$xin_system = erp_company_settings();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$user_type = $user_info['user_type'];
$company_id = user_company_info();

// $planning_entities = $PlanningEntityModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'valid' => '1'])->findAll();
$planning_entities = $PlanningEntityModel
    ->groupStart()
    ->where('company_id', $company_id)
    ->orWhere('company_id', 0)
    ->groupEnd()
    ->groupStart()
    ->where(['user_type' => $user_type])
    ->orWhere('user_type', '')
    ->groupEnd()
    ->where('valid', '1')
    ->findAll();
$year_planning_data = $YearPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type])->findAll();
$years = array_column($year_planning_data, 'year');

$unique_years = array_unique($years);
rsort($unique_years);

$currentYear = date('Y');
$nextYear = $currentYear + 1;
$financialYear = $currentYear . '-' . substr($nextYear, -2);

$monthlyPlanningRecords = $MonthlyPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'year' => $financialYear])->findAll();
$month = array_column($monthlyPlanningRecords, 'month');

$unique_months = array_unique($month);


$financialYearMonths = [
    'april',
    'may',
    'june',
    'july',
    'august',
    'september',
    'october',
    'november',
    'december',
    'january',
    'february',
    'march'
];

// Extract just the month names from records (without year)
$plannedMonths = array_map(function ($item) {
    return strtolower(explode('-', $item)[0]); // Get "april" from "april-2024"
}, $unique_months);

$remaining_months = array_diff($financialYearMonths, $plannedMonths);


$all_projects = $ProjectsModel->where('company_id', $company_id)->orderBy('project_id', 'ASC')->findAll();



?>
<meta name="csrf-token" content="<?= csrf_token() ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .content-slider {
        margin-left: 15px;
        margin-right: 15px;
    }

    .dashboard-container {

        padding: 20px;
        /* background-color: #f9f9f9; */
        border-radius: 10px;
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); */
        display: flex;
        flex-direction: column;
        overflow: auto;
    }

    .dashboard-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .h6,
    h6 {
        font-size: 14px !important;
    }

    .dashboard-header h1 {
        font-size: 2rem;
        color: #333;
    }

    .cards-container {
        display: flex;
        gap: 20px;
        margin-bottom: 1px;
    }

    .card {
        flex: 1;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        color: #fff;
        text-align: center;
        font-size: 1.2rem;
    }

    .card.sales-target {
        background-color: #ff6b6b;
        /* background-color: #ff230054; */

    }

    .card.clients {
        background-color: #4db6ac;
    }

    .card.employees {
        background-color: #f39c12;
    }

    .card h2 {
        font-size: 1.5rem;
    }

    .card p {
        margin-top: 10px;
    }

    .analytics-section {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 25px;
    }

    .analytics-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .analytics-header h3 {
        font-size: 20px;
        color: #333;
    }

    .year-selector {
        padding: 5px;
        font-size: 1rem;
    }

    .col-auto {
        font-size: 20px !important;
    }

    #DataTables_Table_0_wrapper {
        padding-top: 20px;
        width: 100%;
    }

    .annual-summary {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 15px;
    }

    .monthly-summary {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 15px;
        flex-direction: column;

    }

    .status-item {
        text-align: center;
        padding: 20px;
        flex: 1;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .status-item h6 {
        font-size: 20px;
        color: #333;
    }

    .status-item .status-text {
        font-size: 1rem;
        color: #888;
    }

    /* .month-adjust {
        margin-left: 898px;
        margin-bottom: -45px;
    }  */
    .month-select {
        width: 172px;
        height: 36px;
    }

    .main-border {
        border: solid 1px lightgray;
        border-radius: 6px;
        margin-left: 25px;
        margin-right: 25px;
    }


    .month-heading {
        margin-top: 20px;
        margin-bottom: 10px;
        color: #333;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
    }

    .month-entities-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .status-item {
        background: #f9f9f9;
        padding: 10px;
        border-radius: 5px;
    }
</style>

<style>
    @keyframes scroll {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(-100%);
        }
    }
</style>



<div style="position: relative; overflow: hidden; white-space: nowrap; padding: 15px; border-radius: 5px; border: 2px solid #f1f1f1; background: #ff230054; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"
    class="content-slider">
    <div
        style="display: inline-block; color: #fff; font-size: 17px; font-weight: bold; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);">
        <?php
        if (!empty($remaining_months)) {
            $message = "The following months are not planned for financial year $financialYear: " . implode(', ', $remaining_months) . ". Please consider planning for these months.";
        } else {
            $message = "Great! All months for financial year $financialYear are already planned.";
        }
        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        ?>
    </div>
</div>



<div class="dashboard-container">
    <div class="cards-container">
        <div class="card sales-target">
            <h6>Sales Target</h6>
            <p id="salesTargetRevenue">0.00</p>
        </div>
        <div class="card clients">
            <h6>Achived:</h6>
            <p id="salesTargetPlanned">0.00</p>
        </div>
        <div class="card employees">
            <h6>Not Achived:</h6>
            <p id="salesTargetUnplanned">0.00</p>
        </div>
    </div>

    <div>
        <h4>Yearly Planning</h4>
    </div>
    <div class="annual-summary" id="yearPlanningEntitiesContainers">
        <!-- Dynamic entity list will be added here -->
    </div>



    <div class="row justify-content-between align-items-center mb-5  mr-1">
        <h5 class="col-auto">Annual Planning</h5>
        <?php if (in_array('monthly_planning2', staff_role_resource()) || $user_type == 'company') { ?>
            <button class="btn btn-primary  add-button text-right"><i data-feather="plus"></i>
                Add</button>
        <?php } ?>
    </div>

    <div id="form-container" style="display: none;">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 100%; margin: 0;">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Annual Planning Form</h5>
                    <button type="button" class="close add-button text-white" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if (!empty($planning_entities)): ?>
                        <form id="add-form" method="post" autocomplete="off">
                            <div class="row">
                                <!-- Financial Year Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="financial_year" class="font-weight-bold">Financial Year<span
                                                class="text-danger">*</span></label>
                                        <?php

                                        $currentYear = (int) date('Y');
                                        $currentMonth = (int) date('n');
                                        $unique_years = $unique_years ?? [];
                                        $financialYearStart = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;
                                        $currentFY = sprintf("%d-%02d", $financialYearStart, ($financialYearStart + 1) % 100);
                                        ?>

                                        <select class="form-control" id="financial_year" name="year" required>
                                            <option value="">Select Financial Year</option>
                                            <?php if (!empty($unique_years)): ?>
                                                <?php foreach ($unique_years as $year): ?>
                                                    <?php

                                                    $year = (int) $year;
                                                    if ($year < 2000 || $year > 2100)
                                                        continue;

                                                    $startYear = $year;
                                                    $endYear = $year + 1;
                                                    $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                                                    $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                                                    $selected = ($fyValue === $currentFY) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                                                        <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <?php ?>
                                                <?php for ($i = 2; $i >= -5; $i--): ?>
                                                    <?php
                                                    $startYear = $financialYearStart + $i;
                                                    $endYear = $startYear + 1;
                                                    $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                                                    $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                                                    $selected = ($fyValue === $currentFY) ? 'selected' : '';
                                                    ?>
                                                    <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                                                        <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                                                    </option>
                                                <?php endfor; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Month Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="month" class="font-weight-bold">Month<span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" id="month" name="month" required>
                                            <?php
                                            $months = [
                                                ['name' => 'April', 'year_offset' => 0],
                                                ['name' => 'May', 'year_offset' => 0],
                                                ['name' => 'June', 'year_offset' => 0],
                                                ['name' => 'July', 'year_offset' => 0],
                                                ['name' => 'August', 'year_offset' => 0],
                                                ['name' => 'September', 'year_offset' => 0],
                                                ['name' => 'October', 'year_offset' => 0],
                                                ['name' => 'November', 'year_offset' => 0],
                                                ['name' => 'December', 'year_offset' => 0],
                                                ['name' => 'January', 'year_offset' => 1],
                                                ['name' => 'February', 'year_offset' => 1],
                                                ['name' => 'March', 'year_offset' => 1]
                                            ];

                                            foreach ($months as $month) {
                                                $year = $currentYear + $month['year_offset'];
                                                if ($currentMonth >= 4 && $month['year_offset'] == 1) {
                                                    $year = $currentYear + 1;
                                                } elseif ($currentMonth < 4 && $month['year_offset'] == 0) {
                                                    $year = $currentYear;
                                                }
                                                echo "<option value='{$month['name']}-$year'>{$month['name']} $year</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            </div>

                            <br>
                            <?php $columns = array_keys($planning_entities[0]); ?>

                            <div class="row">
                                <?php foreach ($planning_entities as $index => $entity): ?>
                                    <div class="col-md-6">
                                        <fieldset style="margin-bottom: 15px; padding:10px;">
                                            <input type="hidden" name="entities[<?= $entity['id']; ?>][entities_id]"
                                                value="<?= $entity['id']; ?>">
                                            <?php foreach ($columns as $column): ?>
                                                <?php if ($column === 'entity'): ?>
                                                    <div>
                                                        <label for="entities_<?= $entity['id']; ?>" class="font-weight-bold">
                                                            <?= htmlspecialchars($entity[$column]); ?><span class="text-danger">*</span>
                                                        </label>
                                                        <input type="<?= htmlspecialchars($entity['type']); ?>"
                                                            id="entities_<?= $entity['id']; ?>"
                                                            name="entities[<?= $entity['id']; ?>][entity_value]"
                                                            placeholder="<?= htmlspecialchars($entity[$column]); ?>"
                                                            style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"
                                                            required>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </fieldset>
                                    </div>
                                    <?php if (($index + 1) % 2 == 0): ?>
                                    </div>
                                    <div class="row">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <div class="text-right">
                                <button type="submit" id="submit-btn" class="btn btn-primary text-right"
                                    style="padding: 12px 24px; background-color: #28a745; border: none; color: white; cursor: pointer; font-size: 16px; border-radius: 5px;margin-right: 10px;margin-bottom:10px;">Submit</button>
                            </div>
                        </form>

                    <?php else: ?>
                        <p class="text-center">No planning entities found for the specified company.Please Click
                            On
                            <a href="<?php echo site_url('erp/planning_configuration'); ?>">Create New
                                Entity</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>
<script>
    $(document).ready(function () {

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "timeOut": 30000,
            "extendedTimeOut": 30000
        };

        $('.add-button').click(function () {
            $('#form-container').toggle();
        });

        $('#add-form').submit(function (event) {
            event.preventDefault();
            $('#submit-btn').prop('disabled', true);

            var formData = $(this).serialize();
            console.log("Form Data:", formData);

            $.ajax({
                type: 'POST',
                url: '<?php echo base_url('erp/monthly-achive-submit'); ?>',
                data: formData,
                dataType: 'json',
                encode: true,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?php echo csrf_hash(); ?>'
                }
            })
                .done(function (response) {
                    console.log("Response:", response);
                    if (response.message === 'Form submitted successfully!') {
                        toastr.success(response.message);
                        setTimeout(() => location.reload(), 5000);
                    } else {
                        toastr.error('Error: ' + response.message);
                        setTimeout(() => location.reload(), 5000);
                    }
                })
                .fail(function (response) {
                    console.log("AJAX Error:", response);
                    toastr.error('Error occurred while submitting the form.');
                    setTimeout(() => location.reload(), 5000);
                })
                .always(function () {
                    $('#submit-btn').prop('disabled', false);
                });
        });
    });
</script>


<div class="analytics-section">
    <div class="analytics-header">
        <h6>Performance Analytics</h6>
        <select id="yearSelectPerformance" class="year-selector">
            <?php
            $currentYear = date('Y');
            $startYear = $currentYear - 15;
            if (!empty($unique_years)) {

                foreach ($unique_years as $year) {
                    echo "<option value='$year' " . ($year == $currentYear ? 'selected' : '') . ">$year</option>";
                }
            } else {
                echo "<option value='$currentYear' selected>$currentYear</option>";
            }
            ?>
        </select>

    </div>
    <div class="row pb-2">
        <div class="col-auto" style="line-height:0.5">
            <h6>Planned:</h6>
            <p id="totalRevenue">0.00</p>
        </div>
        <div class="col-auto" style="line-height:0.5">
            <h6>Achieved:</h6>
            <p id="achievedRevenue">0.00</p>
        </div>
    </div>
    <div id="chart-container">
        <canvas id="performanceChart"></canvas>
    </div>
</div>


<style>
    .month-heading {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 8px;
        margin-top: 20px;
        margin-bottom: 15px;
    }

    .month-entities-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .status-item {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        transition: all 0.3s ease;
        border-left: 4px solid #4e73df;
    }

    .status-item:hover {
        background: #e9ecef;
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .status-text {
        color: #6c757d;
        font-size: 0.9rem;
    }

    .bold {
        font-weight: 600;
        margin-bottom: 8px;
        color: #343a40;
    }
</style>

<!-- Your existing month selector (unchanged) -->
<div class="card-header-right d-flex align-items-center justify-content-between mt-5">
    <div class="monthly-planning-text">
        <h5 class="mb-0 font-weight-bold">Monthly Planning</h5>
    </div>

    <div class="form-group row align-items-center month-adjust mb-0">
        <div class="col-md-4 p-0">
            <select class="form-control month-select" id="month-select" name="month" required>
                <option value="" disabled selected>Select Month</option>
                <?php
                $months = [
                    ['name' => 'April', 'value' => '04'],
                    ['name' => 'May', 'value' => '05'],
                    ['name' => 'June', 'value' => '06'],
                    ['name' => 'July', 'value' => '07'],
                    ['name' => 'August', 'value' => '08'],
                    ['name' => 'September', 'value' => '09'],
                    ['name' => 'October', 'value' => '10'],
                    ['name' => 'November', 'value' => '11'],
                    ['name' => 'December', 'value' => '12'],
                    ['name' => 'January', 'value' => '01'],
                    ['name' => 'February', 'value' => '02'],
                    ['name' => 'March', 'value' => '03']
                ];

                $currentYear = date('Y');
                $currentMonth = date('m');

                $financialYearStart = ($currentMonth >= '04') ? $currentYear : $currentYear - 1;
                $financialYearEnd = $financialYearStart + 1;

                foreach ($months as $month) {
                    $year = ($month['value'] >= '04') ? $financialYearStart : $financialYearEnd;
                    $displayText = $month['name'] . ' ' . $year;
                    $value = $month['value'] . '-' . $year;
                    $selected = ($month['value'] == $currentMonth && $year == $currentYear) ? 'selected' : '';
                    echo "<option value='{$value}' {$selected}>{$displayText}</option>";
                }
                ?>
            </select>
        </div>
    </div>
</div>

<!-- Entities container -->
<div class="monthly-summary" id="monthlyPlanningEntitiesContainers">
    <!-- Will be populated by JavaScript -->
</div>
<!--  -->


<?php $all_projects = $ProjectsModel->where('company_id', $company_id)->orderBy('project_id', 'ASC')->findAll(); ?>
<div class="main-border">
    <div class="card-header d-flex justify-content-between align-items-center pt-1 pb-1">
        <h6>List All Project</h6>
    </div>

    <div class="card-body pt-0">

        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper">
            <table class="table table-projects dataTable no-footer dtr-inline" id="DataTables_Table_0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Client</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Project Users</th>
                        <th>Priority</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($all_projects)) {

                        $counter = 1;
                        foreach ($all_projects as $r) {

                            // Check user role and project status to display delete button
                            if (in_array('project4', staff_role_resource()) || $user_info['user_type'] == 'company') {
                                $delete = '<span data-toggle="tooltip" data-placement="top" data-state="danger" title="' . lang('Main.xin_delete') . '"><button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . uencode($r['project_id']) . '"><i class="feather icon-trash-2"></i></button></span>';
                            } else {
                                $delete = '';
                            }

                            $view = '<span data-toggle="tooltip" data-placement="top" data-state="primary" title="' . lang('Main.xin_view_details') . '"><a href="' . site_url('erp/project-detail') . '/' . uencode($r['project_id']) . '"><button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light"><i class="feather icon-edit"></i></button></a></span>';

                            // Assigned users
                            $assigned_to = explode(',', $r['assigned_to']);
                            $multi_users = multi_user_profile_photo($assigned_to);

                            // Date formatting
                            $start_date = set_date_format($r['start_date']);
                            $end_date = set_date_format($r['end_date']);

                            // Project progress
                            if ($r['project_progress'] <= 20) {
                                $progress_class = 'bg-danger';
                            } else if ($r['project_progress'] > 20 && $r['project_progress'] <= 50) {
                                $progress_class = 'bg-warning';
                            } else if ($r['project_progress'] > 50 && $r['project_progress'] <= 75) {
                                $progress_class = 'bg-info';
                            } else {
                                $progress_class = 'bg-success';
                            }

                            $progress_bar = '<div class="progress" style="height: 10px;"><div class="progress-bar ' . $progress_class . ' progress-bar-striped" role="progressbar" style="width: ' . $r['project_progress'] . '%;" aria-valuenow="' . $r['project_progress'] . '" aria-valuemin="0" aria-valuemax="100">' . $r['project_progress'] . '%</div></div>';

                            // Status labels
                            if ($r['status'] == 0) {
                                $status = '<span class="label label-warning">' . lang('Projects.xin_not_started') . '</span>';
                            } else if ($r['status'] == 1) {
                                $status = '<span class="label label-primary">' . lang('Projects.xin_in_progress') . '</span>';
                            } else if ($r['status'] == 2) {
                                $status = '<span class="label label-success">' . lang('Projects.xin_completed') . '</span>';
                            } else if ($r['status'] == 3) {
                                $status = '<span class="label label-danger">' . lang('Projects.xin_project_cancelled') . '</span>';
                            } else {
                                $status = '<span class="label label-danger">' . lang('Projects.xin_project_hold') . '</span>';
                            }

                            // Priority labels
                            if ($r['priority'] == 1) {
                                $priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_highest') . '</span>';
                            } else if ($r['priority'] == 2) {
                                $priority = '<span class="badge badge-light-danger">' . lang('Projects.xin_high') . '</span>';
                            } else if ($r['priority'] == 3) {
                                $priority = '<span class="badge badge-light-primary">' . lang('Projects.xin_normal') . '</span>';
                            } else {
                                $priority = '<span class="badge badge-light-success">' . lang('Projects.xin_low') . '</span>';
                            }

                            // $project_revenue = $r['revenue'] ?? 0;
                            // // $project_revenue = number_to_currency($project_revenue, $xin_system['default_currency'], null, 2);
                            // if (!empty($r['revenue']) && is_numeric($r['revenue'])) {
                            //     $project_revenue = number_to_currency((float)$r['revenue'], 'INR', null, 2);
                            // } else {
                            //     $project_revenue = 'N/A';
                            // }
                    
                            $project_summary = $r['title'];

                            // Created by (User info)
                            $created_by = $UsersModel->where('user_id', $r['added_by'])->first();
                            $u_name = $created_by['first_name'] . ' ' . $created_by['last_name'];

                            // Client info
                            $client_info = $UsersModel->where('user_id', $r['client_id'])->where('user_type', 'customer')->first();
                            $iclient = $client_info['first_name'] . ' ' . $client_info['last_name'];

                            // Combine actions
                            $combhr = $view . $delete;

                            // Table data
                            echo "<tr data-start-date='{$r['start_date']}' data-end-date='{$r['end_date']}'>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>" . $project_summary . "</td>";
                            echo "<td>" . $iclient . "</td>";
                            echo "<td>" . $start_date . "</td>";
                            echo "<td>" . $end_date . "</td>";
                            echo "<td>" . $multi_users . "</td>";
                            echo "<td>" . $priority . "</td>";
                            echo "<td>" . $progress_bar . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "<td>" . $combhr . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let chart;
        const currencyFormatter = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        });

        let responseData;

        document.getElementById('yearSelectPerformance').addEventListener('change', loadChartData);
        document.getElementById('month-select').addEventListener('change', filterMonthlyEntities);
        setTimeout(loadChartData, 700);

        function loadChartData() {
            const selectedYear = document.getElementById('yearSelectPerformance').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            $.ajax({
                url: main_url + 'month-plan-chart',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    year: selectedYear
                },
                success: function (response) {
                    responseData = response;

                    if (response && response.invoice_month && response.paid_invoice && response.unpaid_invoice) {
                        document.getElementById('salesTargetRevenue').textContent = currencyFormatter.format(response.total_revenue || 0);
                        document.getElementById('salesTargetPlanned').textContent = currencyFormatter.format(response.achieved || 0);
                        document.getElementById('totalRevenue').textContent = currencyFormatter.format(response.total_revenue || 0);
                        document.getElementById('achievedRevenue').textContent = currencyFormatter.format(response.achieved || 0);
                        document.getElementById('salesTargetUnplanned').textContent = currencyFormatter.format((parseFloat(response.total_revenue) - parseFloat(response.achieved)) || 0);

                        if (Array.isArray(response.year_planning_entities)) {
                            const yearPlanningEntitiesContainers = document.getElementById('yearPlanningEntitiesContainers');
                            yearPlanningEntitiesContainers.innerHTML = '';
                            response.year_planning_entities.forEach(entity => {
                                const entityDiv = document.createElement('div');
                                entityDiv.classList.add('status-item');
                                entityDiv.style.cursor = 'pointer';
                                entityDiv.addEventListener('click', function () {
                                    storeEntityAndRedirect(entity.id, entity.entity_name);
                                });

                                const entityName = document.createElement('h5');
                                entityName.id = "saleTargetStatus";
                                entityName.classList.add('bold');
                                entityName.textContent = `${capitalizeFirstLetter(entity.entity_name)}`;

                                const entityValue = document.createElement('span');
                                entityValue.classList.add('status-text');
                                entityValue.innerHTML = `
                                    <input type="hidden" name="entities[${entity.id}][entities_id]" value="${entity.id}">
                                    ${entity.entity_value || 'N/A'}
                                `;

                                entityDiv.appendChild(entityName);
                                entityDiv.appendChild(entityValue);
                                yearPlanningEntitiesContainers.appendChild(entityDiv);
                            });
                        }

                        displayMonthlyEntities(response.monthly_planning_entities, getCurrentFinancialMonth());

                        const options = {
                            type: 'line',
                            data: {
                                labels: response.invoice_month,
                                datasets: [{
                                    label: response.paid_inv_label,
                                    data: response.paid_invoice,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2
                                },
                                {
                                    label: response.unpaid_inv_label,
                                    data: response.unpaid_invoice,
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 2
                                }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Month'
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: 'Revenue (INR)'
                                        },
                                        beginAtZero: true
                                    }
                                }
                            }
                        };

                        if (chart) {
                            chart.data = options.data;
                            chart.options = options.options;
                            chart.update();
                        } else {
                            const ctx = document.getElementById('performanceChart').getContext('2d');
                            chart = new Chart(ctx, options);
                        }

                    } else {
                        console.error("Response data is not in the expected format.");
                    }
                },
                error: function (xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                }
            });
        }

        function displayMonthlyEntities(monthlyEntities, currentMonth = null) {
            const monthlyPlanningEntitiesContainers = document.getElementById('monthlyPlanningEntitiesContainers');
            monthlyPlanningEntitiesContainers.innerHTML = '';

            const monthOrder = ['april', 'may', 'june', 'july', 'august', 'september',
                'october', 'november', 'december', 'january', 'february', 'march'
            ];

            let found = false;

            monthOrder.forEach(month => {
                if (currentMonth && month !== currentMonth) return;

                if (monthlyEntities && monthlyEntities[month] && monthlyEntities[month].length > 0) {
                    found = true;
                    const monthHeading = document.createElement('h4');
                    monthHeading.classList.add('month-heading');
                    monthHeading.textContent = capitalizeFirstLetter(month);
                    monthlyPlanningEntitiesContainers.appendChild(monthHeading);

                    const monthContainer = document.createElement('div');
                    monthContainer.classList.add('month-entities-container');

                    monthlyEntities[month].forEach(entity => {
                        const entityDiv = document.createElement('div');
                        entityDiv.classList.add('status-item');
                        entityDiv.style.cursor = 'pointer';
                        entityDiv.addEventListener('click', function () {
                            storeEntityAndRedirect(entity.id, entity.entity_name);
                        });

                        const entityName = document.createElement('h5');
                        entityName.id = "saleTargetStatus";
                        entityName.classList.add('bold');
                        entityName.textContent = `${capitalizeFirstLetter(entity.entity_name)}`;

                        const entityValue = document.createElement('span');
                        entityValue.classList.add('status-text');
                        entityValue.innerHTML = `
                            <input type="hidden" name="entities[${entity.id}][entities_id]" value="${entity.id}">
                            ${entity.entity_value || 'N/A'}
                        `;

                        entityDiv.appendChild(entityName);
                        entityDiv.appendChild(entityValue);
                        monthContainer.appendChild(entityDiv);
                    });

                    monthlyPlanningEntitiesContainers.appendChild(monthContainer);
                }
            });

            if (!found) {
                monthlyPlanningEntitiesContainers.innerHTML = `<p style="text-align:center;font-weight:bold;">No data found for the selected month.</p>`;
            }
        }

        function filterMonthlyEntities() {
            const selectedValue = document.getElementById('month-select').value;
            if (!selectedValue || !responseData || !responseData.monthly_planning_entities) return;

            const [monthNum, year] = selectedValue.split('-');
            const monthNames = {
                '01': 'january',
                '02': 'february',
                '03': 'march',
                '04': 'april',
                '05': 'may',
                '06': 'june',
                '07': 'july',
                '08': 'august',
                '09': 'september',
                '10': 'october',
                '11': 'november',
                '12': 'december'
            };

            const monthKey = monthNames[monthNum];
            const monthlyEntities = {};

            if (responseData.monthly_planning_entities[monthKey]) {
                monthlyEntities[monthKey] = responseData.monthly_planning_entities[monthKey];
            }

            displayMonthlyEntities(monthlyEntities, monthKey);
        }

        function getCurrentFinancialMonth() {
            const monthIndex = new Date().getMonth(); // 0 to 11
            const monthMap = ['january', 'february', 'march', 'april', 'may', 'june',
                'july', 'august', 'september', 'october', 'november', 'december'
            ];
            const currentMonth = monthMap[monthIndex];

            // Reorder month to financial year format (April to March)
            const fyOrder = ['april', 'may', 'june', 'july', 'august', 'september',
                'october', 'november', 'december', 'january', 'february', 'march'
            ];
            return fyOrder.includes(currentMonth) ? currentMonth : 'april';
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function storeEntityAndRedirect(entityId) {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: main_url + 'set-entityid-session',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    entityId: entityId
                },
                success: function () {
                    window.location.href = '<?= base_url('erp/projects-list'); ?>';
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('Failed to set entity. Please try again.');
                }
            });
        }
    });
</script>