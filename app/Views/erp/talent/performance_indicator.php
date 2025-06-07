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
    $user_ids = [$user_info['user_id']]; // Initialize with current user

    $builder = $db->table('ci_erp_users_details');
    $builder->select('ci_erp_users.first_name, ci_erp_users.last_name, ci_erp_users.user_id');
    $builder->join('ci_erp_users', 'ci_erp_users.user_id = ci_erp_users_details.user_id');
    $builder->where('ci_erp_users_details.reporting_manager', $user_id);
    $result = $builder->get()->getResultArray();
    $get_data = [];

    if (!empty($result)) {
        $user_ids = array_column($result, 'user_id');
        $user_ids[] = $user_info['user_id']; // Add current user to the list
    }

    // Get KPI data
    $get_data = $KpiModel
        ->where('company_id', $user_info['company_id'])
        ->whereIn('user_id', $user_ids)
        ->orderBy('performance_indicator_id', 'ASC')
        ->findAll();

    // Get employee list (now $user_ids is always defined)
    $employee_list = $UsersModel
        ->where('company_id', $user_info['company_id'])
        ->whereIn('user_id', $user_ids)
        ->orderBy('user_id', 'ASC')
        ->findAll();

    // Get designations
    $designations = $DesignationModel
        ->where('company_id', $user_info['company_id'])
        ->orderBy('designation_id', 'ASC')
        ->findAll();

    // Get competencies
    $competencies = $ConstantsModel
        ->where('company_id', $user_info['company_id'])
        ->where('type', 'competencies')
        ->orderBy('constants_id', 'ASC')
        ->findAll();

    // $user_id = $user_info['user_id'];
    // $builder = $db->table('ci_erp_users_details');
    // $builder->select('ci_erp_users.first_name , ci_erp_users.last_name,ci_erp_users.user_id');
    // $builder->join('ci_erp_users', 'ci_erp_users.user_id = ci_erp_users_details.user_id');
    // $builder->where('ci_erp_users_details.reporting_manager', $user_id);
    // $query = $builder->get();
    // $result = $query->getResultArray();
    // $get_data = [];

    // if (!empty($result)) {
    //     $user_ids = array_column($result, 'user_id');
    //     $user_ids[] = $user_info['user_id'];

    //     $get_data = $KpiModel->where('company_id', $user_info['company_id'])
    //         ->whereIn('user_id', $user_ids)
    //         ->orderBy('performance_indicator_id', 'ASC')
    //         ->findAll();
    // } else {
    //     $get_data = $KpiModel->where('company_id', $user_info['company_id'])
    //         ->where('user_id', $user_info['user_id'])
    //         ->orderBy('performance_indicator_id', 'ASC')
    //         ->findAll();
    // }
    // $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->whereIn('user_id', $user_ids)->orderBy('user_id', 'ASC')->findAll();
    // $designations = $DesignationModel->where('company_id', $user_info['company_id'])->orderBy('designation_id', 'ASC')->findAll();
    // $competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
} else {
    $get_data = $KpiModel->where('company_id', $user_info['company_id'])->orderBy('performance_indicator_id', 'ASC')->findAll();
    $designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->orderBy('designation_id', 'ASC')->findAll();
    $competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
    $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->orderBy('user_id', 'ASC')->findAll();
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

    .dropdown-wrapper {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        margin-right: 17px;
    }

    .card-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #ddd;
    }

    .card-header-right {
        margin-top: 5px;
    }

    .form-select-sm {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.2s;
    }

    .form-select-sm:focus {
        box-shadow: 0 0 5px #007bff;
        border-color: #007bff;
    }

    .card .card-header .card-header-right {
        top: 0px !important;
    }

    .project-status-2 {
        display: inline-block;
        padding: 1px 10px;
        border-radius: 12px;
        border: 1px solid;
    }
</style>

<?php if (in_array('indicator1', staff_role_resource()) || in_array('appraisal1', staff_role_resource()) || in_array('competency1', staff_role_resource()) || in_array('tracking1', staff_role_resource()) || in_array('track_type1', staff_role_resource()) || in_array('track_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <hr class="border-light m-0 mb-3">
<?php } ?>

<div id="main_content">
    <?php
    $session = \Config\Services::session();

    if (empty($competencies)) {
        $session->setFlashdata('error', 'Please add competencies first to fill the performance form!');
    } else {
    }
    ?>


    <!-- List Section -->
    <div id="list_section" class="card">
        <div class="card-header">
            <h5>Performance List</h5>
            <div class="card-header-right d-flex align-items-center justify-content-end gap-3">
                <!-- Employee Dropdown -->
                <?php
                // Ensure variables are set and not null to avoid errors
                $user_type = $user_info['user_type'] ?? null;
                $sup_user_id = $usession['sup_user_id'] ?? null;
                $user_ids = $user_ids ?? [];

                if (
                    $user_type === 'company' ||
                    ($user_type === 'staff' && is_array($user_ids) && in_array($sup_user_id, $user_ids)) ||
                    $user_type !== 'staff'
                ) {
                ?>
                    <div class="dropdown-wrapper">
                        <select id="employee_filter" class="form-control form-select form-select-sm" style="min-width: 167px; border-color: #007bff;">
                            <option value="">Select Employee</option>
                            <?php foreach ($employee_list as $list) { ?>
                                <option value="<?= $list['user_id']; ?>"><?= $list['first_name'] . ' ' . $list['last_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                <?php
                }
                ?>



                <!-- Year Dropdown -->
                <div class="dropdown-wrapper">
                    <select id="year_filter" class="form-control form-select form-select-sm" style="min-width: 150px; border-color: #007bff;">
                        <option value="">Select Year</option>
                        <?php
                        $currentYear = date('Y');
                        for ($year = $currentYear; $year >= ($currentYear - 14); $year--): ?>
                            <option value="<?= $year; ?>"><?= $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Period Dropdown -->
                <div class="dropdown-wrapper">
                    <select id="period_filter" class="form-control form-select form-select-sm" style="min-width: 150px; border-color: #007bff;">
                        <option value="">Select Duration</option>
                        <option value="week">Week</option>
                        <option value="month">Month</option>
                        <option value="quarter">Quarter</option>
                        <option value="half_yearly">Half-Yearly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>

                <!-- Add Performance Button -->
                <button id="add_button" class="btn btn-sm waves-effect waves-light d-flex align-items-center justify-content-center"
                    style="background-color: #007bff; color: white; border-radius: 4px; height: 35px;">
                    <i data-feather="plus" class="me-1"></i>Add Performance
                </button>
            </div>
        </div>


        <div id="DataTables_Table_0_wrapper" class="">
            <table data-last-order-identifier="projects" data-default-order=""
                class="table " id="DataTables_Table_0" role="grid"
                aria-describedby="DataTables_Table_0_info">
                <thead>
                    <tr>
                        <th>S. No</th>
                        <th>Year</th>
                        <th>Review Period</th>
                        <th>Rating</th>
                        <th>Manager Rating</th>
                        <th>Manager Remark</th>
                        <th>Submitted By</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($get_data as $value) { ?>
                        <tr>
                            <td> <?= $i++; ?></td>
                            <td><?= $value['year']; ?></td>
                            <td> <?= $value['review_period']; ?></td>
                            <td>
                                <?= $value['emp_total_rating']; ?>
                                <?php
                                $rating = $value['emp_total_rating'];
                                if ($rating >= 0 && $rating < 2) {
                                    echo '<span class="project-status-2" style="color: red;">Poor</span>';
                                } elseif ($rating >= 2 && $rating < 3) {
                                    echo '<span class="project-status-2" style="color: orange;">Average</span>';
                                } elseif ($rating >= 3 && $rating < 4) {
                                    echo '<span class="project-status-2" style="color: blue;">Good</span>';
                                } elseif ($rating >= 4 && $rating <= 5) {
                                    echo '<span class="project-status-2" style="color: green;">Excellent</span>';
                                } else {
                                    echo '<span class="project-status-2" style="color: gray;">Invalid</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $ManagerRating = isset($value['mang_total_rating']) ? $value['mang_total_rating'] : null;

                                if ($ManagerRating === null) {
                                    echo '-';
                                } else {
                                    echo $ManagerRating . " ";
                                    if ($ManagerRating >= 0 && $ManagerRating < 2) {
                                        echo '<span class="project-status-2" style="color: red;">Poor</span>';
                                    } elseif ($ManagerRating >= 2 && $ManagerRating < 3) {
                                        echo '<span class="project-status-2" style="color: orange;">Average</span>';
                                    } elseif ($ManagerRating >= 3 && $ManagerRating < 4) {
                                        echo '<span class="project-status-2" style="color: blue;">Good</span>';
                                    } elseif ($ManagerRating >= 4 && $ManagerRating <= 5) {
                                        echo '<span class="project-status-2" style="color: green;">Excellent</span>';
                                    } else {
                                        echo '<span class="project-status-2" style="color: gray;">Invalid</span>';
                                    }
                                }
                                ?>
                            </td>


                            <td><?= isset($value['manager_overallRemark']) ? $value['manager_overallRemark'] : '-'; ?></td>
                            <td><?= getClientname($value['updated_by']); ?></td>

                            <?php
                            $currentDate = date('Y-m-d');
                            $createdDate = date('Y-m-d', strtotime($value['created_at']));
                            $showButton = false;
                            switch ($value['review_period']) {
                                case 'week':
                                    $endDate = date('Y-m-d', strtotime($createdDate . ' +6 days'));
                                    break;
                                case 'month':
                                    $endDate = date('Y-m-d', strtotime($createdDate . ' +1 month -1 day'));
                                    break;
                                case 'quarter':
                                    $endDate = date('Y-m-d', strtotime($createdDate . ' +3 months -1 day'));
                                    break;
                                case 'half_yearly':
                                    $endDate = date('Y-m-d', strtotime($createdDate . ' +6 months -1 day'));
                                    break;
                                case 'yearly':
                                    $endDate = date('Y-m-d', strtotime($createdDate . ' +1 year -1 day'));
                                    break;
                                default:
                                    $endDate = $createdDate;
                                    break;
                            }
                            if ($user_info['user_type'] == 'company' || ((strtotime($endDate) - strtotime($currentDate)) <= (2 * 24 * 60 * 60) && strtotime($currentDate) <= strtotime($endDate))) {
                                $showButton = true;
                            }
                            ?>
                            <td>
                                <a href="<?= $showButton ? base_url('erp/view-performances/' . $value['performance_indicator_id']) : '#' ?>"
                                    data-bs-toggle="<?= $showButton ? 'tooltip' : ''; ?>"
                                    title="<?= $showButton ? 'View Details' : ''; ?>">
                                    <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light" <?= !$showButton ? 'disabled' : ''; ?>>
                                        <i class="feather icon-eye"></i>
                                    </button>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>



            </table>
        </div>
    </div>

    <div id="add_form" class="collapse add-form" style="">
        <div class="card">
            <div id="accordion">
                <div class="card-header">
                    <h5>Set New Performance</h5>
                    <div class="card-header-right">
                        <button id="hide_button" class="btn btn-sm waves-effect waves-light m-0" style="background-color: #007bff; color:white;">
                            <i data-feather="minus"></i> Hide
                        </button>
                    </div>
                </div>

                <?php $attributes = array('name' => 'add_performance_indicator', 'id' => '', 'autocomplete' => 'off', 'class' => 'form-hrm'); ?>
                <?php $hidden = array('user_id' => '1'); ?>
                <?php echo form_open('erp/add-indicator', $attributes, $hidden); ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="title">
                                    <?= lang('Dashboard.xin_title'); ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="title" type="text" aria-required="true">
                            </div>
                        </div>
                        <?php if ($user_info['user_type'] == 'company') { ?>
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
                                            <option value="<?= $idesignations['designation_id']; ?>">
                                                <?= $idesignations['designation_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="review_period">
                                    Review Period
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="select2" name="review_period" data-plugin="select_hrm" aria-required="true">
                                    <option value="">Select Duration</option>
                                    <option value="week">Week</option>
                                    <option value="month">Month</option>
                                    <option value="quarter">Quarter</option>
                                    <option value="half_yearly">Half-Yearly</option>
                                    <option value="yearly">Yearly</option>
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
                                            <tr>
                                                <td colspan="3"><?= $itech_comp['category_name']; ?></td>
                                                <td>
                                                    <select class="bar-rating rating-input" name="employee_data[<?= $itech_comp['constants_id']; ?>]" data-id="<?= $itech_comp['constants_id']; ?>" onchange="calculateAverage()">
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <textarea name="employee_remarks[<?= $itech_comp['constants_id']; ?>]" style="width:100%"></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr style="background-color: #b7edc4;">
                                            <td colspan="4" class="text-left"><strong>Total</strong></td>
                                            <td>
                                                <input type="text" id="total-rating" name="employee_total_rating" class="form-control text-center" readonly>
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
                                            <tr>
                                                <td colspan="2">
                                                    <select name="manager_data[<?= $iorg_comp['constants_id']; ?>]" class="bar-rating manager-input" data-id="<?= $iorg_comp['constants_id']; ?>"
                                                        onchange="managerAverage()" id="manager-select-<?= $iorg_comp['constants_id']; ?>"
                                                        <?= $user_info['user_type'] == 'staff' ? 'disabled' : ''; ?>>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </td>

                                                </td>
                                                <td>
                                                    <textarea name="manager_remarks[<?= $iorg_comp['constants_id']; ?>]"
                                                        style="width:100%"
                                                        <?= $user_info['user_type'] == 'staff' ? 'readonly' : ''; ?>></textarea>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <tr style="background-color: #b7edc4;">
                                            <td colspan="2">
                                                <input type="text" class="form-control" name="manager_total_rating" id="manager-total-rating"
                                                    readonly placeholder="Total Rating">
                                            </td>
                                            <td>
                                                <textarea class="form-control" placeholder="Overall Remarks" <?= $user_info['user_type'] == 'staff' ? 'readonly' : ''; ?>
                                                    name="manager_overall_remark"></textarea>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn" style="background-color: #007bff; color:white;">
                        <?= lang('Main.xin_save'); ?>
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
        $('#year_filter, #period_filter, #employee_filter').on('change', function() {
            var year = $('#year_filter').val();
            var period = $('#period_filter').val();
            var employee = $('#employee_filter').val();

            $.ajax({
                url: '<?= base_url('erp/filter-performance'); ?>',
                type: 'GET',
                data: {
                    year_id: year,
                    period: period,
                    employee_id: employee,
                },
                success: function(data) {
                    console.log('Filter successful:', data);
                    $('#DataTables_Table_0').html(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching filtered data:', error);
                }
            });
        });
    });
</script>

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
    $(document).ready(function() {
        $('#DataTables_Table_0').DataTable({
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
    document.getElementById('add_button').addEventListener('click', function() {
        document.getElementById('list_section').style.display = 'none';
        document.getElementById('add_form').style.display = 'block';
    });

    // Hide Add Form and Show List
    document.getElementById('hide_button').addEventListener('click', function() {
        document.getElementById('list_section').style.display = 'block';
        document.getElementById('add_form').style.display = 'none';
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