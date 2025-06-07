<?php


use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\DesignationModel;
use App\Models\KpiModel;
use App\Models\KpioptionsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ConstantsModel = new ConstantsModel();
$DesignationModel = new DesignationModel();
$KpiModel = new KpiModel();
$KpioptionsModel = new KpioptionsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$locale = service('request')->getLocale();



$db = \Config\Database::connect();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

if ($user_info['user_type'] == 'staff') {
    $user_id = $user_info['user_id'];
    $builder = $db->table('ci_erp_users_details');
    $builder->select('ci_erp_users.first_name , ci_erp_users.last_name,ci_erp_users.user_id');
    $builder->join('ci_erp_users', 'ci_erp_users.user_id = ci_erp_users_details.user_id');
    $builder->where('ci_erp_users_details.reporting_manager', $user_id);
    $query = $builder->get();
    $result = $query->getResultArray();
    $user_ids = array_column($result, 'user_id');


    // Fetches the results as an array of objects
    $kpiEmployee_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])->where('indicator_type', 'employee')->findAll();
    $kpiManager_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])->where('indicator_type', 'manager')->findAll();
    $designations = $DesignationModel->where('company_id', $user_info['company_id'])->orderBy('designation_id', 'ASC')->findAll();
    $competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
} else {
    $kpiEmployee_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])->where('indicator_type', 'employee')->findAll();
    $kpiManager_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])->where('indicator_type', 'manager')->findAll();
    $designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->orderBy('designation_id', 'ASC')->findAll();
    $competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
}
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<style>
    #DataTables_Table_0_wrapper {
        padding: 12px;
        width: 100%;
    }
</style>


<div id="main_content">

    <!-- List Section -->
    <div id="add_form" class="add-form">
        <div class="card">
            <div id="accordion">
                <div class="card-header">
                    <h5>Edit Performance</h5>

                </div>

                <?php $attributes = array('name' => 'update_indicator', 'id' => 'update_indicator', 'autocomplete' => 'off', 'class' => 'form-hrm'); ?>
                <?php $hidden = array('token' => $performances['performance_indicator_id']); ?>
                <?php echo form_open('erp/update-indicator', $attributes, $hidden); ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="title">
                                    <?= lang('Dashboard.xin_title'); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="title" type="text" aria-required="true"
                                    value="<?= $performances['title']; ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="designation_id">
                                    <?= lang('Dashboard.left_designation'); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="select2" data-plugin="select_hrm" name="designation_id" aria-required="true">
                                    <option value="">
                                        <?= lang('Dashboard.left_designation'); ?>
                                    </option>
                                    <?php foreach ($designations as $idesignations): ?>
                                        <option value="<?= $idesignations['designation_id']; ?>"
                                            <?= ($idesignations['designation_id'] == $performances['designation_id']) ? 'selected' : ''; ?>>
                                            <?= $idesignations['designation_name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="review_period">
                                    Review Period
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="select2" name="review_period" data-plugin="select_hrm" aria-required="true" readonly>
                                    <option value="">Select Duration</option>
                                    <option value="week" <?= ($performances['review_period'] == 'week') ? 'selected' : ''; ?>>Week</option>
                                    <option value="month" <?= ($performances['review_period'] == 'month') ? 'selected' : ''; ?>>Month</option>
                                    <option value="quarter" <?= ($performances['review_period'] == 'quarter') ? 'selected' : ''; ?>>Quarter</option>
                                    <option value="half_yearly" <?= ($performances['review_period'] == 'half_yearly') ? 'selected' : ''; ?>>Half-Yearly</option>
                                    <option value="yearly" <?= ($performances['review_period'] == 'yearly') ? 'selected' : ''; ?>>Yearly</option>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Employee Section: col-8 -->
                        <div class="col-md-8 table-border-style">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th colspan="5">Employee</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-success">
                                            <th colspan="3"><?= lang('Dashboard.left_performance_xindicator'); ?></th>
                                            <th>Rating</th>
                                            <th>Remarks</th>
                                        </tr>
                                        <?php foreach ($competencies as $itech_comp): ?>
                                            <?php $kpiEmp_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])->where('indicator_option_id', $itech_comp['constants_id'])->first(); ?>
                                            <tr>
                                                <td colspan="3"><?= $itech_comp['category_name']; ?></td>
                                                <td>
                                                    <select class="bar-rating rating-input" name="employee_data[<?= $itech_comp['constants_id']; ?>]" data-id="<?= $itech_comp['constants_id']; ?>" onchange="calculateAverage()">
                                                        <option value="1" <?php if ($kpiEmp_Option['indicator_option_value'] == 1): ?> selected="selected" <?php endif; ?>>1</option>
                                                        <option value="2" <?php if ($kpiEmp_Option['indicator_option_value'] == 2): ?> selected="selected" <?php endif; ?>>2</option>
                                                        <option value="3" <?php if ($kpiEmp_Option['indicator_option_value'] == 3): ?> selected="selected" <?php endif; ?>>3</option>
                                                        <option value="4" <?php if ($kpiEmp_Option['indicator_option_value'] == 4): ?> selected="selected" <?php endif; ?>>4</option>
                                                        <option value="5" <?php if ($kpiEmp_Option['indicator_option_value'] == 5): ?> selected="selected" <?php endif; ?>>5</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="employee_remarks[<?= $itech_comp['constants_id']; ?>]" style="width:100%"><?= $kpiEmp_Option['remarks']; ?></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <tr style="background-color: #b7edc4;">
                                            <td colspan="4" class="text-left"><strong>Total</strong></td>
                                            <td>
                                                <input type="text" id="total-rating" name="employee_total_rating" class="form-control text-center" readonly
                                                    value="<?= $performances['emp_total_rating'] ?>">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Manager Section: col-4 -->
                        <div class="col-md-4 table-border-style">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Manager</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-success">
                                            <th colspan="2">Rating</th>
                                            <th>Remarks</th>
                                        </tr>
                                        <?php foreach ($competencies as $iorg_comp): ?>
                                            <?php
                                            $kpiManager_Option = $KpioptionsModel->where('indicator_id', $performances['performance_indicator_id'])
                                                ->where('indicator_option_id', $iorg_comp['constants_id'])
                                                ->where('indicator_type', 'manager')
                                                ->first();
                                            // Determine if the manager fields should be enabled
                                            $is_editable = ($user_info['user_type'] == 'company' || ($user_info['user_type'] == 'staff' && in_array($performances['user_id'], $user_ids)) || $user_info['user_type'] != 'staff');
                                            ?>
                                            <tr>
                                                <td colspan="2">
                                                    <select name="manager_data[<?= $iorg_comp['constants_id']; ?>]" class="bar-rating manager-input" data-id="<?= $iorg_comp['constants_id']; ?>"
                                                        <?= !$is_editable ? 'disabled' : ''; ?> onchange="managerAverage()">
                                                        <option value="1" <?php if ($kpiManager_Option['indicator_option_value'] == 1): ?> selected="selected" <?php endif; ?>>1</option>
                                                        <option value="2" <?php if ($kpiManager_Option['indicator_option_value'] == 2): ?> selected="selected" <?php endif; ?>>2</option>
                                                        <option value="3" <?php if ($kpiManager_Option['indicator_option_value'] == 3): ?> selected="selected" <?php endif; ?>>3</option>
                                                        <option value="4" <?php if ($kpiManager_Option['indicator_option_value'] == 4): ?> selected="selected" <?php endif; ?>>4</option>
                                                        <option value="5" <?php if ($kpiManager_Option['indicator_option_value'] == 5): ?> selected="selected" <?php endif; ?>>5</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="manager_remarks[<?= $iorg_comp['constants_id']; ?>]" style="width:100%"
                                                        <?= !$is_editable ? 'disabled' : ''; ?>><?= $kpiManager_Option['remarks']; ?>
                                                         </textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <tr style="background-color: #b7edc4;">
                                            <td colspan="2">
                                                <input type="text" class="form-control" name="manager_total_rating" id="manager-total-rating"
                                                    readonly placeholder="Total Rating" value="<?= $performances['mang_total_rating'] ?>">
                                            </td>
                                            <td>
                                                <textarea class="form-control" placeholder="Overall Remarks" <?= !$is_editable ? 'disabled' : ''; ?>
                                                    name="manager_overall_remark"> <?= $performances['manager_overallRemark'] ?></textarea>
                                            </td>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <a class="btn btn-danger" href="<?= base_url('erp/performance-indicator-list') ?>">
                        Back
                    </a>
                    <button type="submit" class="btn" style="background-color: #007bff; color:white;">
                        update
                    </button>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Handle filter changes and display data accordingly
        $('#filterForm').on('change', function() {
            const filters = $(this).serialize(); // Serialize form data
            console.log('Selected Filters:', filters); // Debugging: Print filters to console

            // Example logic: Submit form or filter data via AJAX
            $.ajax({
                url: '<?= base_url("erp/talent/filter_static_data"); ?>', // Adjust the route
                method: 'POST',
                data: filters,
                success: function(response) {
                    $('#filteredData').html(response); // Display filtered data in the table
                }
            });
        });
    });
</script>




<script>
    function calculateAverage() {
        let total = 0;
        let count = 0;

        $('.rating-input').each(function() {
            const value = parseInt($(this).val());
            if (!isNaN(value)) {
                total += value;
                count++;
            }
        });

        const average = count > 0 ? (total / count).toFixed(2) : 0;
        $('#total-rating').val(average);

    }
</script>
<script>
    function managerAverage() {
        let total = 0,
            count = 0;

        const inputs = document.querySelectorAll('.manager-input');

        for (const select of inputs) {
            const value = Number(select.value); // Convert to number
            if (!isNaN(value) && value !== 0) { // Check if the value is a valid number and not zero
                total += value;
                count++;
            }
        }

        const average = count === 0 ? 0 : (total / count).toFixed(2);
        document.getElementById('manager-total-rating').value = average;
    }
</script>
<script>
    $(document).ready(function() {
        // Display toastr notifications for flash data
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