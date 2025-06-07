<?php

use CodeIgniter\I18n\Time;
use App\Models\{
    SystemModel, UsersModel, TasksModel, Form_model
};

// Load Models
$models = [
    'SystemModel' => new SystemModel(),
    'UsersModel' => new UsersModel(),
    'TasksModel' => new TasksModel(),
    'Form_model' => new Form_model(),
];

// Load Session and Services
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$segment_id = $request->uri->getSegment(3);
$web_lead_id = udecode($segment_id);

// Get Logged-in User Info
$user_info = $models['UsersModel']->where('user_id', $usession['sup_user_id'])->first();

// Fetch Web Lead Data
$web_lead_data = $models['Form_model']->where('id', $web_lead_id)->first();

// Handle Missing Data
$status = isset($web_lead_data['status']) ? $web_lead_data['status'] : 1;
$remark = isset($web_lead_data['remark']) ? $web_lead_data['remark'] : '';

?>

<div class="row">
  <div class="col-lg-12">
    <div class="card hdd-right-inner">
      <div class="card-header">
        <h5>Web Lead Details</h5>
      </div>

      
      <?php 
          $attributes = ['name' => 'update_web_lead', 'id' => 'update_web_lead', 'autocomplete' => 'off'];
          $hidden = ['user_id' => 0]; 
      ?>
      <?= form_open('erp/dashboard/update_web_lead', $attributes, $hidden); ?>
      
      <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" value="<?= esc($web_lead_data['id']) ?>" id="web_lead_id" name="web_lead_id" />

              <div class="form-group">
                <label for="name">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?= esc($web_lead_data['name']) ?>" readonly required>
              </div>

              <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc($web_lead_data['email']) ?>" readonly required>
              </div>

              <div class="form-group">
                <label for="contact">Contact <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="contact" name="contact" value="<?= esc($web_lead_data['contact']) ?>" required readonly>
              </div>

              <div class="form-group">
                <label for="description">Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="description" name="description"  value="<?= esc($web_lead_data['description']) ?>" required>
              </div>

              <div class="form-group">
                <label for="status">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="status" name="status">
                  <option value="1" <?= $status == 1 ? 'selected' : ''; ?>>Cold</option>
                  <option value="0" <?= $status == 0 ? 'selected' : ''; ?>>Hot</option>
                </select>
              </div>

              <!-- <div class="form-group">
                <label for="remark">Remark <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="remark" name="remark" value="<?= esc($remark) ?>" required>
              </div> -->
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
          $('#update_web_lead').on('submit', function (event) {
            event.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function (response) {
                if (response.status === 'success') {
                  toastr.success('Web Lead updated successfully!');
                  setTimeout(function () {
                    window.location.replace("<?= base_url('erp/web-leads-list'); ?>");
                  }, 2000);
                } else {
                  toastr.error(response.message || 'Failed to update lead.');
                }
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR.responseText);
                toastr.error('An error occurred: ' + textStatus);
              }
            });
          });
        });
      </script>
    </div>
  </div>
</div>
