<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\PayeesModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$UsersModel = new UsersModel();
$RolesModel = new RolesModel();
$SystemModel = new SystemModel();
$xin_system = erp_company_settings();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

?>
<?php if (in_array('accounts1', staff_role_resource()) || in_array('deposit1', staff_role_resource()) || in_array('expense1', staff_role_resource()) || in_array('transaction1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
  <hr class="border-light m-0 mb-3">
<?php } ?>

<style>
  .modal-content {
    width: 108%;
  }

  .modal-header {
    background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
    border-radius: 6px;
    padding: 18px;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    border-color: transparent;
  }

  .modal-title {
    color: white;
  }
</style>
<div class="row">
  <!-- Accounts Table -->
  <div class="col-md-12" id="accounts-table">
    <div class="card user-profile-list">
      <div class="card-header with-elements">
        <h5>
          <?= lang('Main.xin_list_all'); ?>
          <?= lang('Finance.xin_accounts'); ?>
          <button id="addAccountBtn" class="btn float-right" data-toggle="modal" data-target="#addAccountModal" style="background-color: #007bff;color:white;">
            <?= lang('Main.xin_add_new'); ?>
          </button>
        </h5>
      </div>
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th><?= lang('Employees.xin_account_title'); ?></th>
                <th><?= lang('Employees.xin_account_number'); ?></th>
                <th><?= lang('Finance.xin_balance'); ?></th>
                <th><?= lang('Employees.xin_bank_branch'); ?></th>
                <!-- <th>Action</th>
              </tr>
              <tr>
                <td></td>
              </tr> -->
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal for Add Account -->
  <div class="modal fade" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAccountModalLabel"><?= lang('Main.xin_add_new'); ?> <?= lang('Finance.xin_account'); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php $attributes = array('name' => 'add_account', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
        <?php $hidden = array('user_id' => '1'); ?>
        <?= form_open('erp/add-account', $attributes, $hidden); ?>
        <div class="modal-body">
          <div class="form-group">
            <label for="account_name"><?= lang('Employees.xin_account_title'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="account_name" placeholder="<?= lang('Employees.xin_account_title'); ?>">
          </div>
          <div class="form-group">
            <label for="account_balance"><?= lang('Finance.xin_acc_initial_balance'); ?> <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><?= $xin_system['default_currency']; ?></span>
              </div>
              <input type="text" class="form-control" name="account_balance" placeholder="<?= lang('Finance.xin_acc_initial_balance'); ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="account_number"><?= lang('Employees.xin_account_number'); ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="account_number" placeholder="<?= lang('Employees.xin_account_number'); ?>">
          </div>
          <div class="form-group">
            <label for="branch_code"><?= lang('Finance.xin_acc_branch_code'); ?></label>
            <input type="text" class="form-control" name="branch_code" placeholder="<?= lang('Finance.xin_acc_branch_code'); ?>">
          </div>
          <div class="form-group">
            <label for="description"><?= lang('Employees.xin_bank_branch'); ?></label>
            <textarea class="form-control" name="bank_branch" placeholder="<?= lang('Employees.xin_bank_branch'); ?>" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= lang('Main.xin_close'); ?></button>
          <button type="submit" class="btn" style="background-color: #007bff;;color:white;"><?= lang('Main.xin_save'); ?></button>
        </div>
        <?= form_close(); ?>
      </div>
    </div>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>