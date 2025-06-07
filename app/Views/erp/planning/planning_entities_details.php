<?php

use App\Models\{
  UsersModel, 
  PlanningEntityModel
};

$models = [
  'UsersModel' => new UsersModel(),
  'PlanningEntityModel' => new PlanningEntityModel(),
];

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$locale = service('request')->getLocale();
$request = \Config\Services::request();

$segment_id = $ifield_id;
$planning_entity_id = $ifield_id;
// $segment_id = $request->uri->getSegment(3);
// $planning_entity_id = udecode($segment_id);

$user_info = $models['UsersModel']->where('user_id', $usession['sup_user_id'])->first();

$company_id = $user_info['company_id'];
$user_type = $user_info['user_type'];

$planning_entity_data = $models['PlanningEntityModel']->where(['company_id' => $company_id,'user_type'=>$user_type])->where('id', $planning_entity_id)->first();
$Planning_entity = $models['PlanningEntityModel']->where('company_id', $company_id)->findAll();
?>


<div class="row">
  <div class="col-lg-12">
    <div class="card hdd-right-inner">
      <div class="card-header">
        <h5>Planning Entity Details</h5>
        <?php if (in_array('planning_configuration2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
          <div class="card-header-right">
            <a href="<?= site_url('erp/planning_configuration'); ?>" class="btn btn-shadow btn-secondary btn-sm"
              role="button">
              <i class="mr-2 feather icon-edit"></i> Add Planning Entities
            </a>
          </div>
        <?php endif; ?>
      </div>
      <?php $attributes = ['name' => 'update_planning_entity', 'id' => 'update_planning_entity', 'autocomplete' => 'off']; ?>
      <?php $hidden = ['user_id' => '0']; ?>
      <?= form_open('erp/update-planning-entity', $attributes, $hidden); ?>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <input type="hidden" value="<?= $planning_entity_id; ?>" id="planning_entity_id"
              name="planning_entity_id" />
            <div class="form-group">
              <label for="entity">Entity <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="entity" name="entity"
                value="<?= esc($planning_entity_data['entity']) ?>" required>
            </div>
            <div class="form-group">
              <label for="type">Type <span class="text-danger">*</span></label>
              <select class="form-control" id="type" name="type" required>
                <option value="" disabled <?= empty($planning_entity_data['type']) ? 'selected' : '' ?>>Select Type
                </option>
                <option value="text" <?= $planning_entity_data['type'] == 'text' ? 'selected' : '' ?>>Text</option>
                <option value="number" <?= $planning_entity_data['type'] == 'number' ? 'selected' : '' ?>>Number</option>
              </select>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="description">Description</label>
              <input type="text" class="form-control" id="description" name="description"
                value="<?= esc($planning_entity_data['description']) ?>">
            </div>
            <div class="form-group">
              <label for="valid">Valid</label>
              <select class="form-control" id="valid" name="valid">
                <option value="1" <?= $planning_entity_data['valid'] == 1 ? 'selected' : ''; ?>>Valid</option>
                <option value="0" <?= $planning_entity_data['valid'] == 0 ? 'selected' : ''; ?>>Invalid</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary">
          <?= lang('Main.xin_update_status'); ?>
        </button>
      </div>
      <?= form_close(); ?>

      <!-- Include jQuery, Bootstrap, and Toastr -->
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>

      <script type="text/javascript">
        $(document).ready(function () {
          $('#update_planning_entity').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  toastr.success(response.message);
                  setTimeout(2500);
                  window.location.href = "<?= base_url('erp/planning_configuration'); ?>";
                  // location.reload(); 
                }
                else
                {
                  toastr.error(response.message);
                  setTimeout(2500);
                  window.location.href = "<?= base_url('erp/planning_configuration'); ?>";
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                toastr.error('An error occurred: ' + textStatus);
                setTimeout(2500);
                window.location.href = "<?= base_url('erp/planning_configuration'); ?>";
                // location.reload(); 
              }
            });
          });
        });
      </script>
    </div>
  </div>
</div>