<?php
$get_animate = '';
?>

<hr class="border-light m-0 mb-3">
<div id="add_form" class="collapse add-form" data-parent="#accordion" style="">
  <div class="card mb-2">
    <div id="accordion">
      <div class="card-header">
        <h5>
          <?= lang('Main.xin_add_new'); ?>
          <?= lang('Main.xin_employee_role'); ?>
        </h5>
        <div class="card-header-right">
          <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
            <i data-feather="minus"></i>
            <?= lang('Main.xin_hide'); ?>
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row m-b-1">
          <div class="col-md-12">
            <?= form_open('erp/add-role', ['name' => 'add_role', 'id' => 'add_role', 'autocomplete' => 'off', 'class' => 'form-horizontal']); ?>
            <input type="hidden" name="_user" value="0">

            <div class="form-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="role_name">
                          <?= lang('Users.xin_role_name'); ?>
                          <span class="text-danger">*</span>
                        </label>
                        <input class="form-control" placeholder="<?= lang('Users.xin_role_name'); ?>" name="role_name" type="text" value="<?= old('role_name') ?>">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <input type="hidden" name="role_resources[]" value="0">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="role_access">
                          <?= lang('Users.xin_role_access'); ?>
                          <span class="text-danger">*</span>
                        </label>
                        <select class="form-control custom-select" id="role_access" data-plugin="select_hrm" name="role_access" data-placeholder="<?= lang('Users.xin_role_access'); ?>">
                          <option value="">&nbsp;</option>
                          <option value="1" <?= old('role_access') == '1' ? 'selected' : '' ?>>
                            <?= lang('Users.xin_role_all_menu'); ?>
                          </option>
                          <option value="2" <?= old('role_access') == '2' ? 'selected' : '' ?>>
                            <?= lang('Users.xin_role_cmenu'); ?>
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="resources">
                          <?= lang('Staff Apps'); ?>
                        </label>
                        <div id="all_resources">
                          <div class="demo-section k-content">
                            <div>
                              <div id="treeview_r1"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="resources"><?= lang('Company Apps'); ?></label>
                        <div id="all_resources">
                          <div class="demo-section k-content">
                            <div>
                              <div id="treeview_r2"></div>
                            </div>
                          </div>
                        </div>
                      </div>
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
            <?= form_close(); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card user-profile-list">
  <div class="card-header">
    <h5>
      <?= lang('Main.xin_list_all'); ?>
      <?= lang('Users.xin_roles'); ?>
    </h5>
    <div class="card-header-right">
      <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0">
        <i data-feather="plus"></i>
        <?= lang('Main.xin_add_new'); ?>
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?= lang('Users.xin_role_name'); ?></th>
            <th><?= lang('Users.xin_role_menu_per'); ?></th>
            <th><i class="fa fa-calendar"></i> <?= lang('Users.xin_role_added_date'); ?></th>
            <th>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>

<style type="text/css">
  .k-in {
    display: none !important;
  }
</style>

<!-- Include jQuery and Toastr CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
  $(document).ready(function() {
    // Success Message
    <?php if (session()->getFlashdata('success')): ?>
      toastr.success("<?= session()->getFlashdata('success') ?>");
    <?php endif; ?>

    // Error Message
    <?php if (session()->getFlashdata('error')): ?>
      toastr.error("<?= session()->getFlashdata('error') ?>");
    <?php endif; ?>

    // Validation Errors (Loop through errors)
    <?php if (session()->getFlashdata('validation_errors')): ?>
      <?php foreach (session()->getFlashdata('validation_errors') as $error): ?>
        toastr.error("<?= esc($error) ?>");
      <?php endforeach; ?>
    <?php endif; ?>
  });
</script>