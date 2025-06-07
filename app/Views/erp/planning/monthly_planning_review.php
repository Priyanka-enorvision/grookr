<?php

use App\Models\{
    SystemModel,
    UsersModel,
    PlanningEntityModel,
    MonthlyPlanningModel,
    MonthlyPlanReviewModel,
    MonthlyAchivedModel
};

$models = [
    'SystemModel' => new SystemModel(),
    'UsersModel' => new UsersModel(),
    'PlanningEntityModel' => new PlanningEntityModel(),
    'MonthlyPlanningModel' => new MonthlyPlanningModel(),
    'MonthlyPlanReviewModel' => new MonthlyPlanReviewModel(),
    'MonthlyAchivedModel' => new MonthlyAchivedModel(),
];

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $models['SystemModel']->where('setting_id', 1)->first();
$locale = service('request')->getLocale();
$request = \Config\Services::request();

// $segment_id = $request->uri->getSegment(3);
// $monthly_planning_id = udecode($segment_id);
$segment_id = $ifield_id;
$monthly_planning_id = $ifield_id;

$user_info = $models['UsersModel']->where('user_id', $usession['sup_user_id'])->first();
$company_id = $user_info['user_type'] == 'staff' ? $user_info['company_id'] : $usession['sup_user_id'];
$user_type = $user_info['user_type'];

$monthly_planning_data = $models['MonthlyPlanningModel']->where(['company_id' => $company_id, 'user_type' => $user_type])->where('id', $monthly_planning_id)->first();
$monthly_achive_data = $models['MonthlyAchivedModel']->where(['company_id' => $company_id, 'user_type' => $user_type,'entities_id'=>$monthly_planning_data['entities_id'],'month'=>$monthly_planning_data['month'],'year'=>$monthly_planning_data['year']])->first();
if(!empty($monthly_achive_data))
{
    $expected_value = $monthly_achive_data['entity_value'];
}else
{
    $expected_value = "";
}

$planning_entity = $models['PlanningEntityModel']->where('id', $monthly_planning_data['entities_id'])->first();
$planning_entity_name = $planning_entity['entity'];
$planning_entity_type = $planning_entity['type'];


$monthly_planning_review_data = $models['MonthlyPlanReviewModel']->where(['company_id' => $company_id, 'monthly_plan_id' => $monthly_planning_id])->findAll();

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.css">

<div class="row">
    <div class="col-lg-12">
        <div class="card hdd-right-inner">
            <div class="card-header">
                <h5>Monthly Planning Review</h5>
                <?php if (in_array('monthly_planning2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
                    <div class="card-header-right">
                        <a href="<?= site_url('erp/monthly-planning'); ?>" class="btn btn-shadow btn-secondary btn-sm"
                            role="button">
                            <i class="mr-2 feather icon-edit"></i> Add Monthly Planning
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <br>
            <?php $attributes = ['name' => 'review_monthly_planning_entity', 'id' => 'review_monthly_planning_entity', 'autocomplete' => 'off']; ?>
            <?php $hidden = ['user_id' => '0']; ?>
            <?= form_open('erp/review-monthly-planning-entity', $attributes, $hidden); ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Review Monthly Planning Entity</h5>
                </div>
                <div class="card-body p-4">
                    <input type="hidden" value="<?= $monthly_planning_id; ?>" id="monthly_planning_id"
                        name="monthly_planning_id" />
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="entity_name">Entity Name <span class="text-danger">*</span></label>
                                <input type="<?php esc($planning_entity_type) ?>" class="form-control" id="entity_name"
                                    name="entity_name" value="<?= esc($planning_entity_name) ?>" readonly required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="year">Financial Year <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="year" name="year"
                                    value="<?= esc($monthly_planning_data['year']) ?>" readonly required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="month">Month <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="month" name="month"
                                    value="<?= esc($monthly_planning_data['month']) ?>" readonly required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="real_value">Planned Value <span class="text-danger">*</span></label>
                                <input type="<?= esc($planning_entity_type) ?>" class="form-control" id="real_value"
                                    name="real_value" value="<?= esc($monthly_planning_data['entity_value']) ?>"
                                    readonly required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expected_value">Achived Value <span class="text-danger">*</span></label>
                                <input type="<?= esc($planning_entity_type) ?>" class="form-control" id="expected_value"
                                    name="expected_value" value="<?= esc($expected_value) ?>" readonly required >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">Select Status</option>
                                    <option value="satisfy">Satisfy</option>
                                    <option value="not_satisfy">Not Satisfy</option>
                                    <option value="commit">Commit</option>
                                    <option value="over_commit">Over Commit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="comment">Comment <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="comment" name="comment">
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (in_array('monthly_planning6', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
                    <div class="card-footer bg-light text-right">
                        <button type="submit" class="btn btn-primary">
                            <?= lang('Main.xin_update_status'); ?>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?= form_close(); ?>
            <br>

        </div>
    </div>
</div>
<div class="row">
    <div class="col-xl-12 col-md-12">
        <div class="box-datatable table-responsive">
            <table class="table table-striped table-bordered" id="monthlyPlanReviewTable">
                <thead>
                    <tr>
                        <th>Monthly Plan Entity Name</th>
                        <th>Real Value</th>
                        <th>Expected Value</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monthly_planning_review_data)) { ?>
                        <?php foreach ($monthly_planning_review_data as $row): ?>
                            <tr>
                                <td class="normal-text">
                                    <?php
                                    $monthly_planning_record = $models['MonthlyPlanningModel']->where(['company_id' => $company_id, 'user_type' => $user_type, 'id' => $row['monthly_plan_id']])->first();
                                    $entity_id = $monthly_planning_record['entities_id'];
                                    $planning_entity = $models['PlanningEntityModel']->where('id', $entity_id)->first();
                                    $planning_entity_name = $planning_entity['entity'];
                                    echo $planning_entity_name;
                                    ?>
                                </td>
                                <td class="normal-text"><?= $row['real_value']; ?></td>
                                <td class="normal-text"><?= $row['expected_value']; ?></td>
                                <td class="normal-text"><?= $row['status']; ?></td>
                                <td class="normal-text"><?= $row['comment']; ?></td>
                                <td class="normal-text"><?= $row['created_at']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
<!-- Include jQuery, Bootstrap, and Toastr -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>

<script type="text/javascript">
   

    $(document).ready(function () {
        $('#review_monthly_planning_entity').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            // Configure toastr to appear on top-right
            toastr.options = {
                "positionClass": "toast-top-right",
                "timeOut": 3000,
                "extendedTimeOut": 1000,
                "progressBar": true,
                "closeButton": true,
                "newestOnTop": true,
                "preventDuplicates": true
            };

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        toastr.success(response.message || 'Monthly planning processed successfully!');
                        
                        // Auto-reload after 2 seconds
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 2000);
                        
                    } else {
                        if (response.errors) {
                            $.each(response.errors, function(field, error) {
                                toastr.error(error);
                            });
                            // setTimeout(function() {
                            //     location.reload();
                            // }, 2000);
                        } else {
                            toastr.warning(response.message || 'Operation completed with warnings');
                            // setTimeout(function() {
                            //     location.reload();
                            // }, 2000);
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    let errorMsg = 'An error occurred';
                    
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        errorMsg = jqXHR.responseJSON.message;
                    } else if (textStatus === 'timeout') {
                        errorMsg = 'Request timeout. Please try again.';
                    } else if (textStatus === 'parsererror') {
                        errorMsg = 'Invalid server response.';
                    }

                    toastr.error(errorMsg);
                    
                    // Auto-reload after 3 seconds for errors
                    // setTimeout(function() {
                    //     location.reload();
                    // }, 3000);
                }
            });
        });
    });
</script>