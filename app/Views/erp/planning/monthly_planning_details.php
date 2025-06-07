<?php

use App\Models\{
  SystemModel,
  UsersModel,
  PlanningEntityModel,
  MonthlyPlanningModel,
  YearPlanningModel
};

$models = [
  'SystemModel' => new SystemModel(),
  'UsersModel' => new UsersModel(),
  'PlanningEntityModel' => new PlanningEntityModel(),
  'MonthlyPlanningModel' => new MonthlyPlanningModel(),
  'YearPlanningModel' => new YearPlanningModel(),
];

$session = \Config\Services::session();
$usession = $session->get('sup_username');


$router = service('router');
$request = \Config\Services::request();
$locale = service('request')->getLocale();

$xin_system = $models['SystemModel']->where('setting_id', 1)->first();

$segment_id = $ifield_id;
$monthly_planning_id = $ifield_id;
// $segment_id = $request->uri->getSegment(3);
// $monthly_planning_id = udecode($segment_id);

$user_info = $models['UsersModel']->where('user_id', $usession['sup_user_id'])->first();
$company_id = $user_info['user_type'] == 'staff' ? $user_info['company_id'] : $usession['sup_user_id'];
$user_type = $user_info['user_type'];

$year_planning_data = $models['YearPlanningModel']
    ->where('company_id', $company_id)
    ->where('user_type', $user_type)
    ->findAll();

$years = array_column($year_planning_data, 'year');

$unique_years = array_unique($years);

rsort($unique_years);

$monthly_planning_data = $models['MonthlyPlanningModel']->where('company_id', $company_id)->where('id', $monthly_planning_id)->first();

if(!empty($monthly_planning_data))
{
  $planning_entity = $models['PlanningEntityModel']->where('id', $monthly_planning_data['entities_id'])->first();
  $planning_entity_name = $planning_entity['entity'];
}
else
{
  $planning_entity = $models['PlanningEntityModel']->where('id', $monthly_planning_id)->first();
  $planning_entity_name = $planning_entity['entity'];
}

$planning_entity_type = $planning_entity['type'];

$monthly_plannings = $models['MonthlyPlanningModel']->where('company_id', $company_id)->findAll();

?>

<div class="row">
  <div class="col-lg-12">
    <div class="card hdd-right-inner">
      <div class="card-header">
        <h5>Monthly Planning Details</h5>
      </div>
      <br>
      <?php if (in_array('project5', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
        <?php $attributes = ['name' => 'update_monthly_planning_entity', 'id' => 'update_monthly_planning_entity', 'autocomplete' => 'off']; ?>
        <?php $hidden = ['user_id' => '0']; ?>
        <?= form_open('erp/update-monthly-planning-entity', $attributes, $hidden); ?>
        <div class="card shadow-sm border-0">
          <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Update Monthly Planning Entity</h5>
          </div>
          <div class="card-body p-4">
            <input type="hidden" value="<?= $monthly_planning_id; ?>" id="monthly_planning_id"
              name="monthly_planning_id" />

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="entity_name">Entity Name <span class="text-danger">*</span></label>
                  <input type="<?php esc($planning_entity_type) ?>" class="form-control" id="entity_name"
                    name="entity_name" value="<?= esc($planning_entity_name) ?>" readonly required>

                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="entity_value">Entity Value <span class="text-danger">*</span></label>
                  <input type="<?= esc($planning_entity_type) ?>" class="form-control" id="entity_value"
                    name="entity_value" value="<?= esc($monthly_planning_data['entity_value']) ? esc($monthly_planning_data['entity_value']) : "" ;?>" required>
                </div>
              </div>
              
            </div>
            <?php if (!empty($monthly_planning_data)) { ?>
            <div class="row mt-3">
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="month">Month <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="month" name="month"
                    value="<?= esc($monthly_planning_data['month']) ?>" readonly required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="year">Financial Year <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="year" name="year" 
                      value="<?= esc($monthly_planning_data['year'] ?? '') ?>" readonly required>
                </div>
              </div>
            </div>
            <?php }else{ ?>
            <div class="row mt-3">
              <!-- Financial Year Selection -->
              <div class="col-md-6">
                  <div class="form-group">
                      <label for="financial_year" class="font-weight-bold">Financial Year<span class="text-danger">*</span></label>
                      <?php
                                    
                        $currentYear = (int)date('Y');
                        $currentMonth = (int)date('n');
                        $unique_years = $unique_years ?? []; 
                        $financialYearStart = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;
                        $currentFY = sprintf("%d-%02d", $financialYearStart, ($financialYearStart + 1) % 100);
                        ?>

                        <select class="form-control" id="financial_year" name="year" required>
                            <option value="">Select Financial Year</option>
                            <?php if (!empty($unique_years)): ?>
                                <?php foreach ($unique_years as $year): ?>
                                    <?php
                                    
                                    $year = (int)$year;
                                    if ($year < 2000 || $year > 2100) continue;
                                    
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
                                <?php  ?>
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
                                  <label for="month" class="font-weight-bold">Month<span class="text-danger">*</span></label>
                                  <select class="form-control" id="month" name="month" required>
                                      <option value="" disabled selected>Select Month</option>
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
            <?php } ?>
            
          </div>
          <div class="card-footer bg-light text-right">
            <button type="submit" class="btn btn-primary">
              <?= lang('Main.xin_update_status'); ?>
            </button>
          </div>
        </div>
        <?= form_close(); ?>

      <?php endif; ?>

      <!-- Include jQuery, Bootstrap, and Toastr -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>

      <script type="text/javascript">
        $(document).ready(function () {
          $('#update_monthly_planning_entity').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  toastr.success('Monthly planning inserted or  updated successfully!');
                  setTimeout(60000);
                  window.location.href = "<?php base_url('erp/monthly-planning-list') ?>";
                  // location.reload(); 
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                toastr.error('An error occurred: ' + textStatus);
                setTimeout(60000);
                window.location.href = "<?php base_url('erp/monthly-planning-list') ?>";
                // location.reload(); 
              }
            });
          });
        });
      </script>
    </div>
  </div>
</div>