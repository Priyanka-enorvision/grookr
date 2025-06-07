<?php

use App\Models\{
  UsersModel,
  PlanningEntityModel,
  YearPlanningModel
};

$models = [
  'UsersModel' => new UsersModel(),
  'PlanningEntityModel' => new PlanningEntityModel(),
  'YearPlanningModel' => new YearPlanningModel(),
];

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$locale = service('request')->getLocale();
$request = \Config\Services::request();

$segment_id = $ifield_id;
$year_planning_id = $ifield_id;
// $segment_id = $request->uri->getSegment(3);
// $year_planning_id = udecode($segment_id);

$user_info = $models['UsersModel']->where('user_id', $usession['sup_user_id'])->first();
$company_id = $user_info['company_id'];
$user_type = $user_info['user_type'];
$year_planning_data = $models['YearPlanningModel']->where(['company_id' => $company_id, 'user_type' => $user_type])->where('id', $year_planning_id)->first();

if (!empty($year_planning_data)) {
  $planning_entity = $models['PlanningEntityModel']->where('id', $year_planning_data['entities_id'])->first();
} else {
  $planning_entity = $models['PlanningEntityModel']->where('id', $year_planning_id)->first();
  
}
$planning_entity_name = $planning_entity['entity'];

?>

<div class="row">
  <div class="col-lg-12">
    <div class="card hdd-right-inner">
      <div class="card-header">
        <h5>Year Planning Details</h5>
        <?php if (in_array('year_planning2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
          <div class="card-header-right">
            <a href="<?= site_url('erp/year-planning'); ?>" class="btn btn-shadow btn-secondary btn-sm" role="button">
              <i class="mr-2 feather icon-edit"></i> Add Year Planning
            </a>
          </div>
        <?php endif; ?>
      </div>

      <?php $attributes = ['name' => 'update_year_planning_entity', 'id' => 'update_year_planning_entity', 'autocomplete' => 'off']; ?>
      <?php $hidden = ['user_id' => '0']; ?>
      <?= form_open('erp/update-year-planning-entity', $attributes, $hidden); ?>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <input type="hidden" value="<?= $year_planning_id; ?>" id="year_planning_id" name="year_planning_id" />

            <div class="form-group">
              <!-- <label for="entity">Entity Id <span class="text-danger">*</span></label> -->
              <input type="hidden" class="form-control" id="entities_id" name="entities_id" 
                    value="<?= !empty($year_planning_data['entities_id']) ? esc($year_planning_data['entities_id']) : esc('') ?>" 
                    readonly>
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="entity">Entity Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="entity_name" name="entity_name"
                    value="<?= esc($planning_entity_name) ?>" readonly required>
                </div>

                <div class="form-group">
                  <label for="type">Entity Value <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="entity_value" name="entity_value"
                    value="<?= esc($year_planning_data['entity_value'] ?? '') ?>" required>
                </div>
              </div>

              <?php if (!empty($year_planning_data['year'])) { ?>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="year">Financial Year <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="year" name="year" 
                        value="<?= esc($year_planning_data['year'] ?? '') ?>" readonly required>
                </div>
              </div>

              <?php }else{ ?>
              <div class="col-md-6">
                <div class="form-group">
                    <label for="financial_year" class="font-weight-bold">Financial Year (April-March)<span class="text-danger">*</span></label>
                    <select class="form-control" id="financial_year" name="year" required>
                      <option value="">Select Financial Year</option>
                      <?php
                        $currentYear = date('Y');
                        $nextYear = $currentYear + 1;
                        for ($i = 0; $i < 5; $i++) {  
                            $startYear = $currentYear + $i;
                            $endYear = $nextYear + $i;
                            $financialYear = $startYear . '-' . substr($endYear, 2);
                            echo '<option value="' . $financialYear . '">' . $financialYear . ' (April ' . $startYear . ' - March ' . $endYear . ')</option>';
                        }
                      ?>
                    </select>
                    <small class="text-muted">e.g., <?php echo $currentYear."-".$nextYear; ?> means April <?php echo $currentYear;?> to March <?php echo $nextYear;?></small>
                </div>
              </div>

              <?php } ?>
            </div>

          </div>
        </div>
      </div>
      <?php if (in_array('year_planning5', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-primary">
            <?= lang('Main.xin_update_status'); ?>
          </button>
        </div>
      <?php endif; ?>
      <?= form_close(); ?>

      <!-- Include jQuery, Bootstrap, and Toastr -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>

      <script type="text/javascript">
      $(document).ready(function () {
        $('#update_year_planning_entity').on('submit', function (event) {
          event.preventDefault();
          var formData = $(this).serialize();
          var $submitButton = $(this).find('button[type="submit"]');
          
          // Show loading state
          $submitButton.prop('disabled', true);
          $submitButton.html('<i class="fa fa-spinner fa-spin"></i> Updating...');

          $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                  toastr.success('Year planning inserted or  updated successfully!');
                  setTimeout(60000);
                  window.location.href = "<?php base_url('erp/year-planning') ?>";
                   
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                toastr.error('An error occurred: ' + textStatus);
                setTimeout(60000);
                window.location.href = "<?php base_url('erp/year-planning') ?>";
                 
              }
          });
        });
      });
    </script>
    </div>
  </div>
</div>