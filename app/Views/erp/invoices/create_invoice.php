<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ProjectsModel;
use App\Models\ConstantsModel;
use App\Models\TasksModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ProjectsModel = new ProjectsModel();
$ConstantsModel = new ConstantsModel();
$TasksModel = new TasksModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = erp_company_settings();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $user_info['user_id'];

$curl = curl_init();
$url = "http://103.104.73.221:3000/api/V1/global/lead?userId=$user_id";

curl_setopt_array($curl, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_URL => $url,
  CURLOPT_HTTPGET => true,
]);

$response_apply_data = curl_exec($curl);

if (curl_errno($curl)) {
  $applyExpertData = [];
} else {
  $rows = json_decode($response_apply_data, true)['detail']['rows'] ?? [];
  $applyExpertData = array_filter($rows, function ($row) {
    return $row['status'] === 'A';
  });
  $applyExpertDataId = array_column($applyExpertData, 'expertId');
}

curl_close($curl);

if ($user_info['user_type'] == 'staff') {
  $projects = $ProjectsModel->where('company_id', $user_info['company_id'])->orderBy('project_id', 'ASC')->findAll();
  $clients = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'customer')->findAll();
  $tax_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'tax_type')->findAll();
} else {
  $projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->orderBy('project_id', 'ASC')->findAll();
  $clients = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'customer')->findAll();
  $tax_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'tax_type')->findAll();
}
$xin_system = erp_company_settings();
?>

<?php $get_animate = ''; ?>

<div class="row <?php echo $get_animate; ?>">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
            <?= lang('Invoices.xin_create_new_invoices'); ?>
          </strong></span> </div>
      <div class="card-body" aria-expanded="true" style="">
        <div class="row m-b-1">
          <div class="col-md-12">
            <?php $attributes = array('name' => 'create_invoice', 'id' => 'xin-form', 'autocomplete' => 'off', 'class' => 'form'); ?>
            <?php $hidden = array('user_id' => '0'); ?>
            <?php echo form_open('erp/add-new-invoice', $attributes, $hidden); ?>
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash()?>">
            <?php $inv_info = generate_random_employeeid(); ?>
            <div class="bg-white">
              <div class="box-block">
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="invoice_date">
                        <?= lang('Invoices.xin_invoice_number'); ?> <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-file-invoice"></i></span></div>
                        <input class="form-control" placeholder="<?= lang('Invoices.xin_invoice_number'); ?>" name="invoice_number" type="text" value="<?= $inv_info; ?>">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="client">
                        <?= lang('Projects.xin_client'); ?>
                      </label>
                      <select class="form-control" name="client" id="client_select" data-placeholder="<?= lang('Projects.xin_client'); ?>">
                        <option value=""><?= "Select " . lang('Projects.xin_client'); ?></option>
                        <?php foreach ($clients as $client) { ?>
                          <option value="<?= $client['user_id']; ?>"><?= $client['first_name'] . " " . $client['last_name']; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="project">
                        <?= lang('Projects.xin_project'); ?>
                      </label>
                      <select class="form-control" name="project" id="project_select" data-placeholder="<?= lang('Projects.xin_project'); ?>">
                        <option value=""><?= lang('Projects.xin_project'); ?></option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="expert_to">
                        <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                      </label>
                      <select name="expert_to" id="expert_select" class="form-control" data-placeholder="<?php echo "Select Experts"; ?>">
                        <option value=""><?php echo "Select Experts"; ?></option>
                        <?php foreach ($applyExpertData as $staff) { ?>
                          <option value="<?= $staff['expertId'] ?>">
                            <?= $staff['expertFullName'] ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="invoice_date">
                        <?= lang('Invoices.xin_invoice_date'); ?> <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input class="form-control date" placeholder="<?= lang('Invoices.xin_invoice_date'); ?>" name="invoice_date" type="text" value="">
                        <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="invoice_due_date">
                        <?= lang('Invoices.xin_invoice_due_date'); ?> <span class="text-danger">*</span>
                      </label>
                      <div class="input-group">
                        <input class="form-control date" placeholder="<?= lang('Invoices.xin_invoice_due_date'); ?>" name="invoice_due_date" type="text" value="">
                        <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <!-- Task list -->
                <div id="task_list">
                  <!-- Tasks will be dynamically loaded here -->
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <div class="ci-item-values">
                        <div data-repeater-list="items">
                          <div data-repeater-item="">
                            <div class="row item-row">
                              <div class="form-group mb-1 col-sm-12 col-md-5">
                                <label for="item_name">
                                  <?= lang('Invoices.xin_title_item'); ?>
                                </label>
                                <br>
                                <input type="text" class="form-control item_name" name="item_name[]" id="item_name" placeholder="Item Name">
                              </div>
                              <div class="form-group mb-1 col-sm-12 col-md-2">
                                <label for="qty_hrs" class="cursor-pointer">
                                  <?= lang('Invoices.xin_title_qty_hrs'); ?>
                                </label>
                                <br>
                                <input type="text" class="form-control qty_hrs" name="qty_hrs[]" id="qty_hrs" value="1">
                              </div>
                              <div class="skin skin-flat form-group mb-1 col-sm-12 col-md-2">
                                <label for="unit_price">
                                  <?= lang('Invoices.xin_title_unit_price'); ?>
                                </label>
                                <br>
                                <input class="form-control unit_price" type="text" name="unit_price[]" value="0" id="unit_price" />
                              </div>
                              <div class="form-group mb-1 col-sm-12 col-md-2">
                                <label for="profession">
                                  <?= lang('Invoices.xin_subtotal'); ?>
                                </label>
                                <input type="text" class="form-control sub-total-item" readonly="readonly" name="sub_total_item[]" value="0" />
                              </div>
                              <div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">
                                <label for="profession">&nbsp;</label>
                                <br>
                                <button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-repeater-delete=""> <span class="fa fa-trash"></span></button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div id="item-list"></div>
                      <div class="form-group overflow-hidden1">
                        <div class="col-xs-12">
                          <button type="button" data-repeater-create="" class="btn btn-sm btn-primary" id="add-invoice-item">
                            <?= lang('Invoices.xin_title_add_item'); ?>
                          </button>
                        </div>
                      </div>
                      <?php $sc_show = $xin_system['default_currency_symbol']; ?>
                      <input type="hidden" class="items-sub-total" name="items_sub_total" value="0" />
                      <div class="row">
                        <div class="col-md-6 col-sm-12 text-xs-center text-md-left">&nbsp; </div>
                        <div class="col-md-6 col-sm-12">
                          <div class="table-responsive">
                            <table class="table">
                              <tbody>
                                <tr>
                                  <td><?= lang('Invoices.xin_subtotal'); ?></td>
                                  <td class="text-xs-right"><?php echo $sc_show; ?><span class="sub_total">0</span></td>
                                </tr>
                                <tr>
                                  <td colspan="2" style="border-bottom:1px solid #dddddd; padding:0px !important; text-align:left">
                                    <table class="table table-bordered">
                                      <thead>
                                        <tr>
                                          <th width="30%" style="border-bottom:1px solid #dddddd; text-align:left"><?= lang('Invoices.xin_discount_type'); ?></th>
                                          <th style="border-bottom:1px solid #dddddd; text-align:center"><?= lang('Invoices.xin_discount'); ?></th>
                                          <th style="border-bottom:1px solid #dddddd; text-align:left"><?= lang('Invoices.xin_discount_amount'); ?></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td>
                                            <div class="form-group">
                                              <select name="discount_type" class="form-control discount_type">
                                                <option value="1"><?= lang('Invoices.xin_flat'); ?></option>
                                                <option value="2"><?= lang('Invoices.xin_percent'); ?></option>
                                              </select>
                                            </div>
                                          </td>
                                          <td align="right">
                                            <div class="form-group">
                                              <input style="text-align:right" type="text" name="discount_figure" class="form-control discount_figure" value="0" data-valid-num="required">
                                            </div>
                                          </td>
                                          <td align="right">
                                            <div class="form-group">
                                              <input type="text" style="text-align:right" readonly="" name="discount_amount" value="0" class="discount_amount form-control">
                                            </div>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <tr>
                                  <td colspan="2" style="border-bottom:1px solid #dddddd; padding:0px !important; text-align:left">
                                    <table class="table table-bordered">
                                      <thead>
                                        <tr>
                                          <th width="50%" style="border-bottom:1px solid #dddddd; text-align:left"><?= lang('Dashboard.xin_invoice_tax_type'); ?></th>
                                          <th style="border-bottom:1px solid #dddddd; text-align:left"><?= lang('Invoices.xin_tax_rate'); ?></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <tr>
                                          <td>
                                            <div class="form-group">
                                              <select name="tax_type" class="form-control tax_type">
                                                <?php foreach ($tax_types as $_tax): ?>
                                                  <?php
                                                  $_tax_type = ($_tax['field_two'] == 'percentage') ? $_tax['field_one'] . '%' : number_to_currency($_tax['field_one'], $xin_system['default_currency'], null, 2);
                                                  ?>
                                                  <option tax-type="<?php echo $_tax['field_two']; ?>" tax-rate="<?php echo $_tax['field_one']; ?>" value="<?php echo $_tax['constants_id']; ?>">
                                                    <?php echo $_tax['category_name']; ?> (<?php echo $_tax_type; ?>)
                                                  </option>
                                                <?php endforeach; ?>
                                              </select>
                                            </div>
                                          </td>
                                          <td align="right">
                                            <div class="form-group">
                                              <input type="text" style="text-align:right" readonly="" name="tax_rate" value="0" class="tax_rate form-control">
                                            </div>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                <input type="hidden" class="fgrand_total" name="fgrand_total" value="0" />
                                <tr>
                                  <td><?= lang('Invoices.xin_grand_total'); ?></td>
                                  <td class="text-xs-right"><?php echo $sc_show; ?> <span class="grand_total">0</span></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="form-group col-xs-12 mb-2 file-repeaters"> </div>
                      <div class="row">
                        <div class="col-lg-12">
                          <label for="invoice_note">
                            <?= lang('Invoices.xin_invoice_note'); ?>
                          </label>
                          <textarea name="invoice_note" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="invoice-footer">
                  <div class="row">
                    <div class="col-md-7 col-sm-12">
                      <h6>
                        <?= lang('Invoices.xin_terms_condition'); ?>
                      </h6>
                      <p>
                        <?= $xin_system['invoice_terms_condition']; ?>
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" name="invoice_submit" class="btn btn-primary pull-right my-1" style="margin-right: 5px;">
          <?= lang('Invoices.xin_create_new_invoices'); ?>
        </button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function() {
    $('#client_select').change(function() {
      var client_id = $(this).val();

      $('#project_select').empty().append('<option value="">Select Project</option>');

      if (client_id) {
        $.ajax({
          url: '<?= base_url('erp/invoices/getProjectsByClient'); ?>',
          type: 'POST',
          data: {
            client_id: client_id
          },
          dataType: 'json',
          success: function(data) {
            if (data.length > 0) {
              $.each(data, function(index, project) {
                $('#project_select').append('<option value="' + project.project_id + '">' + project.title + '</option>');
              });
            }
          }
        });
      }
    });
    $('#expert_select').change(function() {
      var expert_id = $(this).val();

      $('#project_select').empty().append('<option value="">Select Project</option>');

      if (expert_id) {
        $.ajax({
          url: '<?= base_url('erp/invoices/getProjectsByExpert'); ?>',
          type: 'POST',
          data: {
            expert_id: expert_id
          },
          dataType: 'json',
          success: function(data) {
            if (data.length > 0) {
              $.each(data, function(index, project) {
                $('#project_select').append('<option value="' + project.project_id + '">' + project.title + '</option>');
              });
            }
          }
        });
      }
    });
    $('#project_select').change(function() {
      var project_id = $(this).val();
      $('#task_list').empty();
      if (project_id) {
        $.ajax({
          url: '<?= base_url('erp/invoices/getTasksByProject'); ?>',
          type: 'POST',
          data: {
            project_id: project_id
          },
          dataType: 'json',
          success: function(data) {
            if (data.length > 0) {
              $.each(data, function(index, task) {

                function calculateSubtotal(row) {
                  var qty = parseFloat(row.find('.qty_hrs').val()) || 0;
                  var price = parseFloat(row.find('.unit_price').val()) || 0;
                  var subtotal = qty * price;
                  row.find('.sub_total_item').val(subtotal.toFixed(2));
                }

                $('#task_list').append(
                  '<div class="row item-row">' +
                  '<div class="form-group mb-1 col-sm-12 col-md-5">' +
                  '<label for="item_name">Item</label>' +
                  '<br>' +
                  '<input type="text" class="form-control item_name" name="item_name[]" value="' + task.task_name + '" >' +
                  '</div>' +
                  '<div class="form-group mb-1 col-sm-12 col-md-2">' +
                  '<label for="qty_hrs" class="cursor-pointer">Qty/Hr</label>' +
                  '<br>' +
                  '<input type="text" class="form-control qty_hrs" name="qty_hrs[]" value="0" >' +
                  '</div>' +
                  '<div class="form-group mb-1 col-sm-12 col-md-2">' +
                  '<label for="unit_price">Unit Price</label>' +
                  '<br>' +
                  '<input type="text" class="form-control unit_price" name="unit_price[]" value="0" >' +
                  '</div>' +
                  '<div class="form-group mb-1 col-sm-12 col-md-2">' +
                  '<label for="sub_total_item">Sub Total</label>' +
                  '<input type="text" class="form-control sub_total_item" readonly="readonly" name="sub_total_item[]" value="0" />' +
                  '</div>' +
                  '<div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">' +
                  '<label for="profession">&nbsp;</label>' +
                  '<br>' +
                  '<button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-repeater-delete=""><span class="fa fa-trash"></span></button>' +
                  '</div>' +
                  '</div>'
                );
                $('#task_list').on('input', '.qty_hrs, .unit_price', function() {
                  var row = $(this).closest('.item-row');
                  calculateSubtotal(row);
                });
              });
            }
          }
        });
      }
    });

  });
</script>
