<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$get_companies = $UsersModel->where('user_type', 'company')->findAll();

$locale = service('request')->getLocale();
$get_animate="";
?>
<?php if ($session->get('unauthorized_module')) { ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <?= $session->get('unauthorized_module'); ?>
  </div>
<?php } ?>

<?php if (in_array('company2', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
  <div id="accordion">
    <div id="add_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
      <?php $attributes = array('name' => 'add_company', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
      <?php $hidden = array('user_id' => '0'); ?>
      <?= form_open_multipart('erp/add-company', $attributes, $hidden); ?>
      <div class="row">
        <div class="col-md-8">
          <div class="card mb-2">
            <div class="card-header">
              <h5>
                <?= lang('Main.xin_add_new'); ?>
                <?= lang('Projects.xin_company'); ?>
              </h5>
              <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                  <?= lang('Main.xin_hide'); ?>
                </a> </div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="company_name">
                      <?= lang('Main.xin_employee_first_name'); ?>
                      <span class="text-danger">*</span> </label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.xin_employee_first_name'); ?>" name="first_name" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="last_name" class="control-label">
                      <?= lang('Main.xin_employee_last_name'); ?>
                      <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.xin_employee_last_name'); ?>" name="last_name" type="text">
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">
                      <?= lang('Main.dashboard_username'); ?>
                      <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.dashboard_username'); ?>" name="username" type="text">
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="gender" class="control-label">
                      <?= lang('Main.xin_employee_gender'); ?>
                    </label>
                    <select class="form-control" name="gender" data-plugin="select_hrm" data-placeholder="<?= lang('Main.xin_employee_gender'); ?>">
                      <option value="1">
                        <?= lang('Main.xin_gender_male'); ?>
                      </option>
                      <option value="2">
                        <?= lang('Main.xin_gender_female'); ?>
                      </option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-group">
                    <label for="email">
                      <?= lang('Main.xin_email'); ?>
                      <span class="text-danger">*</span> </label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-envelope"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.xin_email'); ?>" name="email" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="website">
                      <?= lang('Main.xin_employee_password'); ?>
                      <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-eye-slash"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.xin_employee_password'); ?>" name="password" type="text">
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contact_number">
                      <?= lang('Main.xin_contact_number'); ?>
                      <span class="text-danger">*</span></label>
                    <input class="form-control" placeholder="<?= lang('Main.xin_contact_number'); ?>" name="contact_number" type="number">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="company_name">
                      <?= lang('Main.dashboard_companyname'); ?>
                      <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                      <input class="form-control" placeholder="<?= lang('Main.dashboard_companyname'); ?>" name="company_name" type="text">
                    </div>
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
        <div class="col-md-4">
          <div class="card">
            <div class="card-header">
              <h5>
                <?= lang('Main.xin_e_details_profile_picture'); ?>
              </h5>
            </div>
            <div class="card-body py-2">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="logo">
                      <?= lang('Main.xin_attachment'); ?>
                    </label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" name="file">
                      <label class="custom-file-label">
                        <?= lang('Main.xin_choose_file'); ?>
                      </label>
                      <small>
                        <?= lang('Main.xin_company_file_type'); ?>
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?= form_close(); ?>
    </div>
  </div>
<?php } ?>
<div class="card user-profile-list">
  <div class="card-header">
    <h5>
      <?= lang('Main.xin_list_all'); ?>
      <?= lang('Projects.xin_companies'); ?>
    </h5>
    <div class="card-header-right">
      <!-- <a href="<?= site_url() . 'erp/companies-grid'; ?>"
        class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top"
        title="<?= lang('Projects.xin_grid_view'); ?>"> <i class="fas fa-th-large"></i> </a> -->
      <?php if (in_array('company2', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
        <a data-toggle="collapse" href="#add_form" aria-expanded="false"
          class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
          <?= lang('Main.xin_add_new'); ?>
        </a>
      <?php } ?>
    </div>
  </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?= lang('Main.xin_name'); ?></th>
            <th><i class="fa fa-user"></i>
              <?= lang('Main.dashboard_username'); ?></th>
            <th><?= lang('Main.xin_contact_number'); ?></th>
            <th><?= lang('Main.xin_country'); ?></th>
            <th><?= lang('Main.dashboard_xin_status'); ?></th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($get_companies as $company): ?>
            <tr>
              <td>
                <?php if (isset($company['company_id']) && isset($company['company_name'])): ?>
                  <a href="<?= site_url('erp/company-monthly-planning/' . urlencode($company['company_id'])); ?>"
                    title="View Monthly Planning for <?= htmlspecialchars($company['company_name']); ?>">
                    <?= htmlspecialchars($company['company_name']); ?>
                  </a>
                <?php else: ?>
                  <span>No company data available</span>
                <?php endif; ?>
              </td>

              <td><?= htmlspecialchars($company['username']); ?></td>
              <td><?= htmlspecialchars($company['contact_number']); ?></td>
              <td><?= htmlspecialchars($company['country']); ?></td>
              <td>
                <?php
                if ($company['is_active'] == 1) {
                ?>
                  <a href="<?= base_url('company/update-status/' . base64_encode($company['company_id']) . '/0') ?>" class="badge badge-light-success">Active</a><?php

                                                                                                                                                                } else {
                                                                                                                                                                  ?>
                  <a href="<?= base_url('company/update-status/' . base64_encode($company['company_id']) . '/1') ?>" class="badge badge-light-danger">Not Active</a><?php
                                                                                                                                                                  }
                                                                                                                                                                    ?>
              </td>
              <td>
                <?php
                $encoded_user_id = uencode($company['user_id']);

                $delete_button = '
                        <button type="button" class="btn btn-sm btn-light-danger delete"
                            data-toggle="modal" data-target=".delete-modal" data-record-id="' . htmlspecialchars($encoded_user_id, ENT_QUOTES, 'UTF-8') . '">
                            <i class="feather icon-trash-2"></i>
                        </button>';

                $edit_button = '
                        <a href="' . site_url('erp/company-detail') . '/' . htmlspecialchars($encoded_user_id, ENT_QUOTES, 'UTF-8') . '" class="btn btn-sm btn-light-primary">
                            <i class="feather icon-edit"></i>
                        </a>';

                echo $delete_button . $edit_button;
                ?>
              </td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function() {
    $('.datatables-demo').DataTable({
      "paging": true,
      "searching": true,
      "ordering": true
    });
  });
</script>

<script>
  $(document).on("click", ".delete", function() {
    var id = $(this).data('record-id');
    $('input[name=_token]').val(id);
    $('#delete_record').attr('action', main_url + 'delete-company');
  });
</script>

<?php if ($session->getFlashdata('success')): ?>
  <script>
    $(document).ready(function() {
      toastr.success("<?= esc($session->getFlashdata('success')) ?>");
    });
  </script>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
  <script>
    $(document).ready(function() {
      <?php
      $errors = $session->getFlashdata('error');
      if (is_array($errors)) {
        foreach ($errors as $err) {
          echo 'toastr.error("' . esc($err) . '");';
        }
      } else {
        echo 'toastr.error("' . esc($errors) . '");';
      }
      ?>
    });
  </script>
<?php endif; ?>