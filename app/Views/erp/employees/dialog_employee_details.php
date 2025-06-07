<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\ContractModel;
use App\Models\UserdocumentsModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$UsersModel = new UsersModel();
$ContractModel = new ContractModel();
$SystemModel = new SystemModel();
$UserdocumentsModel = new UserdocumentsModel();
$get_animate = '';
$xin_system = $SystemModel->where('setting_id', 1)->first();
if ($request->getGet('data') === 'user_allowance' && $request->getGet('field_id')) {
  $ifield_id = udecode($field_id);
  $result = $ContractModel->where('contract_option_id', $ifield_id)->first();
  //$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>

  <div class="modal-header">
    <h5 class="modal-title">
      <?= lang('Employees.xin_edit_allowances'); ?>
      <span class="font-weight-light">
        <?= lang('Main.xin_information'); ?>
      </span> <br>
      <small class="text-muted">
        <?= lang('Main.xin_below_required_info'); ?>
      </small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  </div>
  <?php $attributes = array('name' => 'edit_allowance', 'id' => 'edit_allowance', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
  <?php $hidden = array('_method' => 'EDIT', 'token' => $field_id); ?>
  <?= form_open('erp/update-allowance', $attributes, $hidden); ?>
  <?= csrf_field() ?>
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="is_allowance_taxable">
            <?= lang('Employees.xin_allowance_option'); ?>
            <span class="text-danger">*</span></label>
          <select name="contract_tax_option" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['contract_tax_option'] == 1 ? 'selected="selected"' : '' ?>>
              <?= lang('Employees.xin_salary_allowance_non_taxable'); ?>
            </option>
            <option value="2" <?= $result['contract_tax_option'] == 2 ? 'selected="selected"' : '' ?>>
              <?= lang('Employees.xin_fully_taxable'); ?>
            </option>
            <option value="3" <?= $result['contract_tax_option'] == 3 ? 'selected="selected"' : '' ?>>
              <?= lang('Employees.xin_partially_taxable'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="amount_option">
            <?= lang('Employees.xin_amount_option'); ?>
            <span class="text-danger">*</span></label>
          <select name="is_fixed" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['is_fixed'] == 1 ? 'selected="selected"' : '' ?>>
              <?= lang('Employees.xin_title_tax_fixed'); ?>
            </option>
            <option value="2" <?= $result['is_fixed'] == 2 ? 'selected="selected"' : '' ?>>
              <?= lang('Employees.xin_title_tax_percent'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_title">
            <?= lang('Dashboard.xin_title'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="option_title" type="text" value="<?= $result['option_title']; ?>" id="edit_allowance_title">
            <div id="edit_allowance_name_error" class="text-danger" style="display: none;">Title name should only contain letters and spaces.</div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_number">
            <?= lang('Invoices.xin_amount'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">
                <?= $xin_system['default_currency']; ?>
              </span></div>
            <input class="form-control" placeholder="<?= lang('Invoices.xin_amount'); ?>" name="contract_amount" type="number" value="<?= $result['contract_amount']; ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">
      <?= lang('Main.xin_close'); ?>
    </button>
    <button type="submit" class="btn btn-primary">
      <?= lang('Main.xin_update'); ?>
    </button>
  </div>
  <?= form_close(); ?>
  <script type="text/javascript">
    $(document).ready(function() {
      $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
      $('[data-plugin="select_hrm"]').select2({
        width: '100%'
      });
      Ladda.bind('button[type=submit]');

      // Title validation
      $('#edit_allowance_title').on('input', function() {
        var regex = /^[A-Za-z\s]*$/;
        if (!regex.test($(this).val())) {
          $('#edit_allowance_name_error').show();
        } else {
          $('#edit_allowance_name_error').hide();
        }
      });

      /* Edit data */
      $("#edit_allowance").submit(function(e) {
        e.preventDefault();
        var obj = $(this),
          action = obj.attr('action');
        $.ajax({
          type: "POST",
          url: action,
          data: obj.serialize() + "&is_ajax=1&type=edit_record&form=edit_allowance",
          cache: false,
          dataType: 'json',
          success: function(JSON) {
            if (JSON.error != '') {
              toastr.error(JSON.error);
              $('input[name="csrf_token"]').val(JSON.csrf_hash);
              Ladda.stopAll();
            } else {
              var xin_table_allowances = $('#xin_table_all_allowances').dataTable({
                "bDestroy": true,
                "ajax": {
                  url: "<?= site_url("erp/allowance-list/") . $request->getGet('uid'); ?>",
                  type: 'GET'
                },
                "fnDrawCallback": function(settings) {
                  $('[data-toggle="tooltip"]').tooltip();
                }
              });
              xin_table_allowances.api().ajax.reload(function() {
                toastr.success(JSON.result);
                $('input[name="csrf_token"]').val(JSON.csrf_hash);
              }, true);
              $('.view-modal-data').modal('toggle');
              Ladda.stopAll();
            }
          },
          error: function(xhr, status, error) {
            toastr.error('An error occurred: ' + error);
            Ladda.stopAll();
          }
        });
      });
    });
  </script>
<?php } else if ($request->getGet('data') === 'user_commission' && $request->getGet('field_id')) {
  $ifield_id = udecode($field_id);
  $result = $ContractModel->where('contract_option_id', $ifield_id)->first();
  //$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>
  <div class="modal-header">
    <h5 class="modal-title">
      <?= lang('Employees.xin_edit_commissions'); ?>
      <span class="font-weight-light">
        <?= lang('Main.xin_information'); ?>
      </span> <br>
      <small class="text-muted">
        <?= lang('Main.xin_below_required_info'); ?>
      </small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  </div>
  <?= form_open('erp/update-commission', ['name' => 'edit_commission', 'id' => 'edit_commission', 'autocomplete' => 'off', 'class' => 'm-b-1']); ?>
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="EDIT">
  <input type="hidden" name="token" value="<?= $field_id ?>">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="is_allowance_taxable">
            <?= lang('Employees.xin_salary_commission_options'); ?>
            <span class="text-danger">*</span></label>
          <select name="contract_tax_option" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['contract_tax_option'] == 1 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_salary_allowance_non_taxable'); ?>
            </option>
            <option value="2" <?= $result['contract_tax_option'] == 2 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_fully_taxable'); ?>
            </option>
            <option value="3" <?= $result['contract_tax_option'] == 3 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_partially_taxable'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="amount_option">
            <?= lang('Employees.xin_amount_option'); ?>
            <span class="text-danger">*</span></label>
          <select name="is_fixed" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['is_fixed'] == 1 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_fixed'); ?>
            </option>
            <option value="2" <?= $result['is_fixed'] == 2 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_percent'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_title">
            <?= lang('Dashboard.xin_title'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="option_title" type="text" value="<?= $result['option_title']; ?>" id="edit_commission_title">
            <div id="edit_commission_name_error" class="text-danger" style="display: none;"><?= lang('Employees.xin_title_validation_error'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_number">
            <?= lang('Invoices.xin_amount'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">
                <?= $xin_system['default_currency']; ?>
              </span></div>
            <input class="form-control" placeholder="<?= lang('Invoices.xin_amount'); ?>" name="contract_amount" type="number" step="0.01" value="<?= $result['contract_amount']; ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">
      <?= lang('Main.xin_close'); ?>
    </button>
    <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
      <?= lang('Main.xin_update'); ?>
    </button>
  </div>
  <?= form_close(); ?>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2
      $('[data-plugin="select_hrm"]').select2({
        width: '100%'
      });

      // Initialize Ladda
      Ladda.bind('.ladda-button');

      // Title validation
      $('#edit_commission_title').on('input', function() {
        var regex = /^[A-Za-z0-9\s\-_.,()&]*$/;
        if (!regex.test($(this).val())) {
          $('#edit_commission_name_error').show();
        } else {
          $('#edit_commission_name_error').hide();
        }
      });

      // Form submission
      $("#edit_commission").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var l = Ladda.create(form.find('button[type=submit]')[0]);
        l.start();

        // Additional client-side validation
        if ($('#edit_commission_title').val().trim() === '') {
          toastr.error('<?= lang("Main.xin_error_field_text") ?>');
          l.stop();
          return false;
        }

        $.ajax({
          type: "POST",
          url: form.attr('action'),
          data: form.serialize() + "&is_ajax=1&type=edit_record",
          dataType: 'json',
          success: function(response) {
            l.stop();
            if (response.error) {
              toastr.error(response.error);
            } else {
              toastr.success(response.result);
              $('#xin_table_all_commissions').DataTable().ajax.reload(null, false);
              $('.view-modal-data').modal('hide');
            }
            $('input[name="csrf_token"]').val(response.csrf_hash);
          },
          error: function(xhr) {
            l.stop();
            try {
              var response = JSON.parse(xhr.responseText);
              toastr.error(response.error || '<?= lang("Main.xin_error_msg") ?>');
              if (response.csrf_hash) {
                $('input[name="csrf_token"]').val(response.csrf_hash);
              }
            } catch (e) {
              toastr.error('<?= lang("Main.xin_connection_error") ?>');
            }
          }
        });
      });
    });
  </script>
<?php } else if ($request->getGet('data') === 'user_statutory' && $request->getGet('field_id')) {
  $ifield_id = udecode($field_id);
  $result = $ContractModel->where('contract_option_id', $ifield_id)->first();
  //$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>
  <div class="modal-header">
    <h5 class="modal-title">
      <?= lang('Employees.xin_edit_satatutory_deductions'); ?>
      <span class="font-weight-light">
        <?= lang('Main.xin_information'); ?>
      </span> <br>
      <small class="text-muted">
        <?= lang('Main.xin_below_required_info'); ?>
      </small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  </div>
  <?= form_open('erp/update-statutory', ['name' => 'edit_statutory', 'id' => 'edit_statutory', 'autocomplete' => 'off', 'class' => 'm-b-1']); ?>
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="EDIT">
  <input type="hidden" name="token" value="<?= $field_id ?>">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="amount_option">
            <?= lang('Employees.xin_salary_sd_options'); ?>
            <span class="text-danger">*</span></label>
          <select name="is_fixed" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['is_fixed'] == 1 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_fixed'); ?>
            </option>
            <option value="2" <?= $result['is_fixed'] == 2 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_percent'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_title">
            <?= lang('Dashboard.xin_title'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="option_title" type="text" value="<?= $result['option_title']; ?>" id="edit_statutory_title">
            <div id="edit_statutory_name_error" class="text-danger" style="display: none;"><?= lang('Employees.xin_title_validation_error'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="form-group">
          <label for="account_number">
            <?= lang('Invoices.xin_amount'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">
                <?= $xin_system['default_currency']; ?>
              </span></div>
            <input class="form-control" placeholder="<?= lang('Invoices.xin_amount'); ?>" name="contract_amount" type="number" step="0.01" value="<?= $result['contract_amount']; ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">
      <?= lang('Main.xin_close'); ?>
    </button>
    <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
      <?= lang('Main.xin_update'); ?>
    </button>
  </div>
  <?= form_close(); ?>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2
      $('[data-plugin="select_hrm"]').select2({
        width: '100%'
      });

      // Initialize Ladda
      Ladda.bind('.ladda-button');

      // Title validation
      $('#edit_statutory_title').on('input', function() {
        var regex = /^[A-Za-z0-9\s\-_.,()&]*$/;
        if (!regex.test($(this).val())) {
          $('#edit_statutory_name_error').show();
        } else {
          $('#edit_statutory_name_error').hide();
        }
      });

      // Form submission
      $("#edit_statutory").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var l = Ladda.create(form.find('button[type=submit]')[0]);
        l.start();

        // Additional client-side validation
        if ($('#edit_statutory_title').val().trim() === '') {
          toastr.error('<?= lang("Main.xin_error_field_text") ?>');
          l.stop();
          return false;
        }

        $.ajax({
          type: "POST",
          url: form.attr('action'),
          data: form.serialize() + "&is_ajax=1&type=edit_record",
          dataType: 'json',
          success: function(response) {
            l.stop();
            if (response.error) {
              toastr.error(response.error);
            } else {
              toastr.success(response.result);
              $('#xin_table_all_statutory_deductions').DataTable().ajax.reload(null, false);
              $('.view-modal-data').modal('hide');
            }
            $('input[name="csrf_token"]').val(response.csrf_hash);
          },
          error: function(xhr) {
            l.stop();
            try {
              var response = JSON.parse(xhr.responseText);
              toastr.error(response.error || '<?= lang("Main.xin_error_msg") ?>');
              if (response.csrf_hash) {
                $('input[name="csrf_token"]').val(response.csrf_hash);
              }
            } catch (e) {
              toastr.error('<?= lang("Main.xin_connection_error") ?>');
            }
          }
        });
      });
    });
  </script>
<?php } else if ($request->getGet('data') === 'user_other_payments' && $request->getGet('field_id')) {
  $ifield_id = udecode($field_id);
  $result = $ContractModel->where('contract_option_id', $ifield_id)->first();
  //$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>
  <div class="modal-header">
    <h5 class="modal-title">
      <?= lang('Employees.xin_edit_reimbursements'); ?>
      <span class="font-weight-light">
        <?= lang('Main.xin_information'); ?>
      </span> <br>
      <small class="text-muted">
        <?= lang('Main.xin_below_required_info'); ?>
      </small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  </div>
  <?= form_open('erp/update-other-payments', ['name' => 'edit_other_payments', 'id' => 'edit_other_payments', 'autocomplete' => 'off', 'class' => 'm-b-1']); ?>
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="EDIT">
  <input type="hidden" name="token" value="<?= $field_id ?>">
  <div class="modal-body">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label for="is_allowance_taxable">
            <?= lang('Employees.xin_reimbursements_option'); ?>
            <span class="text-danger">*</span></label>
          <select name="contract_tax_option" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['contract_tax_option'] == 1 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_salary_allowance_non_taxable'); ?>
            </option>
            <option value="2" <?= $result['contract_tax_option'] == 2 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_fully_taxable'); ?>
            </option>
            <option value="3" <?= $result['contract_tax_option'] == 3 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_partially_taxable'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="amount_option">
            <?= lang('Employees.xin_amount_option'); ?>
            <span class="text-danger">*</span></label>
          <select name="is_fixed" class="form-control" data-plugin="select_hrm">
            <option value="1" <?= $result['is_fixed'] == 1 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_fixed'); ?>
            </option>
            <option value="2" <?= $result['is_fixed'] == 2 ? 'selected' : '' ?>>
              <?= lang('Employees.xin_title_tax_percent'); ?>
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_title">
            <?= lang('Dashboard.xin_title'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="option_title" type="text" value="<?= $result['option_title']; ?>" id="edit_other_payments_title">
            <div id="edit_other_payments_name_error" class="text-danger" style="display: none;"><?= lang('Employees.xin_title_validation_error'); ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <label for="account_number">
            <?= lang('Invoices.xin_amount'); ?>
            <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend"><span class="input-group-text">
                <?= $xin_system['default_currency']; ?>
              </span></div>
            <input class="form-control" placeholder="<?= lang('Invoices.xin_amount'); ?>" name="contract_amount" type="number" step="0.01" value="<?= $result['contract_amount']; ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">
      <?= lang('Main.xin_close'); ?>
    </button>
    <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
      <?= lang('Main.xin_update'); ?>
    </button>
  </div>
  <?= form_close(); ?>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize Select2
      $('[data-plugin="select_hrm"]').select2({
        width: '100%'
      });

      // Initialize Ladda
      Ladda.bind('.ladda-button');

      // Title validation
      $('#edit_other_payments_title').on('input', function() {
        var regex = /^[A-Za-z0-9\s\-_.,()&]*$/;
        if (!regex.test($(this).val())) {
          $('#edit_other_payments_name_error').show();
        } else {
          $('#edit_other_payments_name_error').hide();
        }
      });

      // Form submission
      $("#edit_other_payments").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var l = Ladda.create(form.find('button[type=submit]')[0]);
        l.start();

        // Additional client-side validation
        if ($('#edit_other_payments_title').val().trim() === '') {
          toastr.error('<?= lang("Main.xin_error_field_text") ?>');
          l.stop();
          return false;
        }

        $.ajax({
          type: "POST",
          url: form.attr('action'),
          data: form.serialize() + "&is_ajax=1&type=edit_record",
          dataType: 'json',
          success: function(response) {
            l.stop();
            if (response.error) {
              toastr.error(response.error);
            } else {
              toastr.success(response.result);
              $('#xin_table_all_other_payments').DataTable().ajax.reload(null, false);
              $('.view-modal-data').modal('hide');
            }
            $('input[name="csrf_token"]').val(response.csrf_hash);
          },
          error: function(xhr) {
            l.stop();
            try {
              var response = JSON.parse(xhr.responseText);
              toastr.error(response.error || '<?= lang("Main.xin_error_msg") ?>');
              if (response.csrf_hash) {
                $('input[name="csrf_token"]').val(response.csrf_hash);
              }
            } catch (e) {
              toastr.error('<?= lang("Main.xin_connection_error") ?>');
            }
          }
        });
      });
    });
  </script>
<?php } else if ($request->getGet('data') === 'user_document' && $request->getGet('field_id')) {
  $ifield_id = udecode($field_id);
  $result = $UserdocumentsModel->where('document_id', $ifield_id)->first();
  //$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
?>
  <div class="modal-header">
    <h5 class="modal-title">
      <?= lang('Employees.xin_edit_documents'); ?>
      <span class="font-weight-light">
        <?= lang('Main.xin_information'); ?>
      </span> <br>
      <small class="text-muted">
        <?= lang('Main.xin_below_required_info'); ?>
      </small>
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
  </div>
  <?= form_open_multipart('erp/update-document', ['name' => 'edit_document', 'id' => 'edit_document', 'autocomplete' => 'off', 'class' => 'm-b-1']); ?>
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="EDIT">
  <input type="hidden" name="token" value="<?= $field_id ?>">
  <div class="modal-body">
    <div class="row">
      <div class="col-sm-6">
        <div class="form-group">
          <label for="date_of_expiry" class="control-label">
            <?= lang('Employees.xin_document_name'); ?>
            <span class="text-danger">*</span></label>
          <input class="form-control" placeholder="<?= lang('Employees.xin_document_name'); ?>" name="document_name" type="text" value="<?= $result['document_name']; ?>">
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group">
          <label for="title" class="control-label">
            <?= lang('Employees.xin_document_type'); ?>
            <span class="text-danger">*</span></label>
          <input class="form-control" placeholder="<?= lang('Employees.xin_document_eg_payslip_etc'); ?>" name="document_type" type="text" value="<?= $result['document_type']; ?>">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="logo">
            <?= lang('Employees.xin_document_file'); ?>
          </label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="document_file" id="document_file">
            <label class="custom-file-label" for="document_file">
              <?= lang('Main.xin_choose_file'); ?>
            </label>
            <small class="form-text text-muted">
              <?= lang('Employees.xin_e_details_d_type_file'); ?>
              (Max size: 5MB, Allowed: jpg, jpeg, png, gif, pdf, doc, docx)
            </small>
            <?php if ($result['document_file']): ?>
              <div class="mt-2">
                <a href="<?= base_url('public/uploads/documents/' . $result['document_file']) ?>" target="_blank" class="text-primary">
                  <i class="fas fa-file-download"></i> <?= lang('Main.xin_download'); ?> <?= $result['document_file'] ?>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-light" data-dismiss="modal">
      <?= lang('Main.xin_close'); ?>
    </button>
    <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
      <?= lang('Main.xin_update'); ?>
    </button>
  </div>
  <?= form_close(); ?>

  <script type="text/javascript">
    $(document).ready(function() {
      // Initialize file input label
      $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
      });

      // Initialize Ladda
      Ladda.bind('.ladda-button');

      // Form submission
      $("#edit_document").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var l = Ladda.create(form.find('button[type=submit]')[0]);
        l.start();

        // Client-side validation
        if ($('input[name="document_name"]').val().trim() === '') {
          toastr.error('<?= lang("Employees.xin_error_document_name") ?>');
          l.stop();
          return false;
        }
        if ($('input[name="document_type"]').val().trim() === '') {
          toastr.error('<?= lang("Employees.xin_error_document_type") ?>');
          l.stop();
          return false;
        }

        var formData = new FormData(this);
        formData.append('is_ajax', 1);
        formData.append('type', 'edit_record');

        $.ajax({
          url: form.attr('action'),
          type: "POST",
          data: formData,
          contentType: false,
          cache: false,
          processData: false,
          dataType: 'json',
          success: function(response) {
            l.stop();
            if (response.error) {
              toastr.error(response.error);
            } else {
              toastr.success(response.result);
              $('#xin_table_document').DataTable().ajax.reload(null, false);
              $('.view-modal-data').modal('hide');
            }
            $('input[name="csrf_token"]').val(response.csrf_hash);
          },
          error: function(xhr) {
            l.stop();
            try {
              var response = JSON.parse(xhr.responseText);
              toastr.error(response.error || '<?= lang("Main.xin_error_msg") ?>');
              if (response.csrf_hash) {
                $('input[name="csrf_token"]').val(response.csrf_hash);
              }
            } catch (e) {
              toastr.error('<?= lang("Main.xin_connection_error") ?>');
            }
          }
        });
      });
    });
  </script>
<?php }
?>