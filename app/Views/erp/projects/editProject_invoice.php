<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\ConstantsModel;
use App\Models\TasksModel;
use App\Models\InvoiceitemsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();
$ConstantsModel = new ConstantsModel();
$TasksModel = new TasksModel();
$InvoiceitemsModel = new InvoiceitemsModel();

$session = \Config\Services::session();
$request = \Config\Services::request();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = erp_company_settings();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $user_info['user_id'];
$segment_id = $request->getUri()->getSegment(3);

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
    // $invoice_items = $InvoiceitemsModel->where('invoice_id', $invoice['invoice_id'])->findAll();
    $invoice_items = $InvoiceitemsModel->where('project_id', $invoice['project_id'])->where('invoice_id', $invoice['invoice_id'])->findAll();
} else {
    $projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->orderBy('project_id', 'ASC')->findAll();
    $clients = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'customer')->findAll();
    $tax_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'tax_type')->findAll();
    // $invoice_items = $InvoiceitemsModel->where('invoice_id', $invoice['invoice_id'])->findAll();
    $invoice_items = $InvoiceitemsModel->where('project_id', $invoice['project_id'])->where('invoice_id', $invoice['invoice_id'])->findAll();
}
$xin_system = erp_company_settings();
?>

<?php $get_animate = ''; ?>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .card-header {
        background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
        color: white;
    }

    .form-label-custom {
        font-size: 12px;
        /* Label size chhota kiya */
        font-weight: 500;
    }

    .input-sm-custom {
        font-size: 14px;
        /* Input box ke text size chhota kiya */
        padding: 5px 10px;
        width: 90%;
        /* Input box ki width kam ki */
    }

    .input-group .input-group-text {
        padding: 5px 8px;
        font-size: 12px;
    }
</style>

<div class="row <?php echo $get_animate; ?>">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
                        Edit Project Invoice
                    </strong></span> </div>
            <div class="card-body" aria-expanded="true" style="">
                <div class="row m-b-1">
                    <div class="col-md-12">
                        <?php $attributes = array('name' => 'update_invoice', 'id' => '', 'autocomplete' => 'off', 'class' => 'form'); ?>
                        <?php $hidden = array('token' => $segment_id); ?>
                        <?php echo form_open('erp/update-invoice', $attributes, $hidden); ?>
                        <?php $inv_info = generate_random_employeeid(); ?>
                        <div class="bg-white">
                            <div class="box-block">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_date" class="form-label-custom">
                                                <?= lang('Invoices.xin_invoice_number'); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-file-invoice"></i></span></div>
                                                <input class="form-control input-sm-custom" placeholder="<?= lang('Invoices.xin_invoice_number'); ?>" name="invoice_number" type="text"
                                                    value="<?= $invoice['invoice_number']; ?>">

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="client" class="form-label-custom">
                                                <?= lang('Projects.xin_client'); ?>
                                            </label>
                                            <select class="form-control input-sm-custom" name="client" id="client_select" data-placeholder="<?= lang('Projects.xin_client'); ?>">
                                                <option value=""><?= "Select " . lang('Projects.xin_client'); ?></option>
                                                <?php foreach ($clients as $client) { ?>
                                                    <option value="<?= $client['user_id']; ?>" <?= $invoice['client_id'] == $client['user_id'] ? 'selected' : ''; ?>>
                                                        <?= $client['first_name'] . " " . $client['last_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="project" class="form-label-custom">
                                                <?= lang('Projects.xin_project'); ?>
                                            </label>
                                            <input type="text" class="form-control input-sm-custom" id="project_select" data-placeholder="<?= lang('Projects.xin_project'); ?>"
                                                value="<?= getProjectName($invoice['project_id']); ?>">
                                            <input type="hidden" name="project" value="<?= $invoice['project_id']; ?>">

                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="expert_to" class="form-label-custom">
                                                <i class="fa fa-user-tie"></i> <?php echo "Experts"; ?>
                                            </label>
                                            <select name="expert_to" id="expert_select" class="form-control input-sm-custom" data-placeholder="<?php echo "Select Experts"; ?>">
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
                                            <label for="invoice_date" class="form-label-custom">
                                                <?= lang('Invoices.xin_invoice_date'); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input class="form-control date input-sm-custom" placeholder="<?= lang('Invoices.xin_invoice_date'); ?>" name="invoice_date" type="text"
                                                    value="<?= $invoice['invoice_date']; ?>">
                                                <div class="input-group-append"><span class="input-group-text"><i class="fas fa-calendar-alt"></i></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="invoice_due_date" class="form-label-custom">
                                                <?= lang('Invoices.xin_invoice_due_date'); ?> <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input class="form-control date input-sm-custom" placeholder="<?= lang('Invoices.xin_invoice_due_date'); ?>" name="invoice_due_date" type="text"
                                                    value="<?= $invoice['invoice_due_date']; ?>">
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
                                        <div class="desgin" style="background-color: lightgray;">
                                            <h6 class="text-black mb-3 p-1 pl-3">
                                                Add Items
                                            </h6>
                                        </div>
                                        <div class="form-group">
                                            <?php $iv = 1;
                                            foreach ($invoice_items as $item) { ?>
                                                <div class="ci-item-values">
                                                    <div data-repeater-list="items">
                                                        <div data-repeater-item="">
                                                            <div class="row item-row">
                                                                <input type="hidden" name="item[<?php echo $item['invoice_item_id']; ?>]" value="<?php echo $item['invoice_item_id']; ?>" />
                                                                <div class="form-group mb-1 col-sm-12 col-md-5">
                                                                    <label for="item_name" class="form-label-custom">
                                                                        <?= lang('Invoices.xin_title_item'); ?>
                                                                    </label>
                                                                    <br>
                                                                    <input type="text" class="form-control item_name input-sm-custom" name="eitem_name[<?php echo $item['invoice_item_id']; ?>]" id="item_name"
                                                                        placeholder="Item Name" value="<?= $item['item_name']; ?>">
                                                                </div>
                                                                <div class="form-group mb-1 col-sm-12 col-md-2">
                                                                    <label for="qty_hrs" class="cursor-pointer form-label-custom">
                                                                        <?= lang('Invoices.xin_title_qty_hrs'); ?>
                                                                    </label>
                                                                    <br>
                                                                    <input type="text" class="form-control qty_hrs input-sm-custom" name="eqty_hrs[<?php echo $item['invoice_item_id']; ?>]" id="qty_hrs" value="1"
                                                                        value="<?= $item['item_qty']; ?>">
                                                                </div>
                                                                <div class="skin skin-flat form-group mb-1 col-sm-12 col-md-2">
                                                                    <label for="unit_price" class="form-label-custom">
                                                                        <?= lang('Invoices.xin_title_unit_price'); ?>
                                                                    </label>
                                                                    <br>
                                                                    <input class="form-control unit_price input-sm-custom" type="text" name="eunit_price[<?php echo $item['invoice_item_id']; ?>]" value="<?= $item['item_unit_price']; ?>" id="unit_price" />
                                                                </div>
                                                                <div class="form-group mb-1 col-sm-12 col-md-2">
                                                                    <label for="profession" class="form-label-custom">
                                                                        <?= lang('Invoices.xin_subtotal'); ?>
                                                                    </label>
                                                                    <input type="text" class="form-control sub-total-item input-sm-custom" readonly="readonly" name="esub_total_item[<?php echo $item['invoice_item_id']; ?>]" value="<?= $item['item_sub_total']; ?>" />
                                                                    <p style="display:none" class="form-control-static"><span class="amount-html">0</span></p>
                                                                </div>
                                                                <div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">
                                                                    <label for="profession" class="form-label-custom">&nbsp;</label>
                                                                    <br>
                                                                    <?php if ($iv == 1): ?>
                                                                        <button type="button" disabled="disabled" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-repeater-delete=""> <span class="fa fa-trash"></span></button>
                                                                    <?php else: ?>
                                                                        <button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light remove-invoice-item-ol" data-repeater-delete="" data-record-id="<?= uencode($item['invoice_item_id']); ?>"> <span class="fa fa-trash"></span></button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $iv++;
                                            } ?>
                                            <div id="item-list"></div>
                                            <div class="form-group overflow-hidden1">
                                                <div class="col-xs-12">
                                                    <button type="button" data-repeater-create="" class="btn btn-sm " id="add-invoice-item"
                                                        style="background-color: #2989d8 !important; color:#dddddd;">
                                                        <?= lang('Invoices.xin_title_add_item'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php $sc_show = $xin_system['default_currency_symbol']; ?>
                                            <input type="hidden" class="items-sub-total" name="items_sub_total" value="<?= $invoice['sub_total_amount']; ?>" />
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 text-xs-center text-md-left">&nbsp; </div>
                                                <div class="col-md-6 col-sm-12">

                                                    <div style="border: 1px solid #ddd; border-radius: 8px; padding: 16px; background: #f9f9f9;">
                                                        <!-- Subtotal -->
                                                        <div style="margin-bottom: 16px;">
                                                            <span style="font-weight: bold; font-size: 14px;"><?= lang('Invoices.xin_subtotal'); ?></span>
                                                            <span style="float: right; font-size: 14px;"><?php echo $sc_show; ?><span class="sub_total">
                                                                    <?= $invoice['sub_total_amount']; ?></span></span>
                                                        </div>

                                                        <!-- Discount Section -->
                                                        <div style="margin-bottom: 16px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                                                            <div style="display: flex; gap: 16px; align-items: center;">
                                                                <!-- Discount Type -->
                                                                <div style="flex: 1;">
                                                                    <label style="font-size: 12px; margin-bottom: 4px; display: block;"><?= lang('Invoices.xin_discount_type'); ?></label>
                                                                    <select name="discount_type" class="form-control discount_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                                                        <option value="1" <?php if ($invoice['discount_type'] == 1): ?> selected="selected" <?php endif; ?>><?= lang('Invoices.xin_flat'); ?></option>
                                                                        <option value="2" <?php if ($invoice['discount_type'] == 2): ?> selected="selected" <?php endif; ?>><?= lang('Invoices.xin_percent'); ?></option>
                                                                    </select>
                                                                </div>
                                                                <!-- Discount Figure -->
                                                                <div style="flex: 1;">
                                                                    <label style="font-size: 12px; margin-bottom: 4px; display: block;"><?= lang('Invoices.xin_discount'); ?></label>
                                                                    <input type="text" name="discount_figure" class="form-control discount_figure" value="<?= $invoice['discount_figure']; ?>" style="width: 100%; padding: 8px; text-align: right; border: 1px solid #ddd; border-radius: 4px;">
                                                                </div>
                                                                <!-- Discount Amount -->
                                                                <div style="flex: 1;">
                                                                    <label style="font-size: 12px; margin-bottom: 4px; display: block;"><?= lang('Invoices.xin_discount_amount'); ?></label>
                                                                    <input type="text" readonly name="discount_amount" value="<?= $invoice['total_discount']; ?>" class="discount_amount form-control" style="width: 100%; padding: 8px; text-align: right; border: 1px solid #ddd; border-radius: 4px;">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Tax Section -->
                                                        <div style="margin-bottom: 16px; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #fff;">
                                                            <div style="display: flex; gap: 16px; align-items: center;">
                                                                <!-- Tax Type -->
                                                                <div style="flex: 1;">
                                                                    <label style="font-size: 12px; margin-bottom: 4px; display: block;"><?= lang('Dashboard.xin_invoice_tax_type'); ?></label>
                                                                    <select name="tax_type" class="form-control tax_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                                                        <?php foreach ($tax_types as $_tax): ?>
                                                                            <?php
                                                                            $_tax_type = ($_tax['field_two'] == 'percentage') ? $_tax['field_one'] . '%' : number_to_currency($_tax['field_one'], $xin_system['default_currency'], null, 2);
                                                                            ?>
                                                                            <option tax-type="<?php echo $_tax['field_two']; ?>" tax-rate="<?php echo $_tax['field_one']; ?>" value="<?php echo $_tax['constants_id']; ?>" <?php if ($_tax['constants_id'] == $invoice['tax_type']): ?> selected="selected" <?php endif; ?>>
                                                                                <?php echo $_tax['category_name']; ?> (<?php echo $_tax_type; ?>)
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <!-- Tax Rate -->
                                                                <div style="flex: 1;">
                                                                    <label style="font-size: 12px; margin-bottom: 4px; display: block;"><?= lang('Invoices.xin_tax_rate'); ?></label>
                                                                    <input type="text" readonly name="tax_rate" value="<?= $invoice['total_tax']; ?>" class="tax_rate form-control" style="width: 100%; padding: 8px; text-align: right; border: 1px solid #ddd; border-radius: 4px;">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Grand Total -->
                                                        <div style="margin-top: 16px; font-size: 16px; font-weight: bold;">
                                                            <input type="hidden" class="fgrand_total" name="fgrand_total" value="<?= $invoice['grand_total']; ?>" />
                                                            <span><?= lang('Invoices.xin_grand_total'); ?></span>
                                                            <span style="float: right;"><?php echo $sc_show; ?> <span class="grand_total"><?= $invoice['grand_total']; ?></span></span>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="form-group col-xs-12 mb-2 file-repeaters"> </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label for="invoice_note">
                                                        <?= lang('Invoices.xin_invoice_note'); ?>
                                                    </label>
                                                    <textarea name="invoice_note" class="form-control"><?= $invoice['invoice_note']; ?></textarea>
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
                <a class="btn btn-danger" href="<?= base_url('erp/project-detail/' . uencode($invoice['project_id'])) ?>">
                    Back
                </a>
                <button type="submit" name="invoice_submit" class="btn " style="margin-right: 5px; background-color:#226faa; color:white;">
                    Update Project Invoice
                </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
        $("#xin-form").submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            formData.append("is_ajax", 1);
            formData.append("type", 'add_record');
            formData.append("form", 'create_invoice');

            $.ajax({
                url: e.target.action,
                type: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    if (response.error !== '') {
                        toastr.error(response.error);
                    } else {
                        toastr.success(response.message);
                        $("#xin-form")[0].reset();
                        $('#project_select').empty().append('<option value="">Select Project</option>');
                        $('#task_list').empty();
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('An error occurred. Please try again.');
                }
            });
        });

    });
</script>
<script>
    <?php if (session()->getFlashdata('error')) : ?>
        toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
            timeOut: 5000
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')) : ?>
        toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
            timeOut: 5000
        });
    <?php endif; ?>
</script>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#add-invoice-item').click(function() {
            var invoice_items = '<div class="row item-row">' +
                '<hr>' +
                '<div class="form-group mb-1 col-sm-12 col-md-5">' +
                '<label for="item_name" class="form-label-custom">Item</label>' +
                '<br>' +
                '<input type="text" class="form-control item_name input-sm-custom" name="item_name[]" id="item_name" placeholder="Item Name">' +
                '</div>' +
                '<div class="form-group mb-1 col-sm-12 col-md-2">' +
                '<label for="qty_hrs" class="cursor-pointer form-label-custom" >Qty/Hr</label>' +
                '<br>' +
                '<input type="text" class="form-control qty_hrs input-sm-custom" name="qty_hrs[]" id="qty_hrs" value="1">' +
                '</div>' +
                '<div class="skin skin-flat form-group mb-1 col-sm-12 col-md-2">' +
                '<label for="unit_price" class="form-label-custom">Unit Price</label>' +
                '<br>' +
                '<input class="form-control unit_price input-sm-custom" type="text" name="unit_price[]" value="0" id="unit_price" />' +
                '</div>' +
                '<div class="form-group mb-1 col-sm-12 col-md-2">' +
                '<label for="profession" class="form-label-custom">Sub Total</label>' +
                '<input type="text" class="form-control sub-total-item input-sm-custom" readonly="readonly" name="sub_total_item[]" value="0" />' +
                '<p style="display:none" class="form-control-static"><span class="amount-html">0</span></p>' +
                '</div>' +
                '<div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">' +
                '<label for="profession">&nbsp;</label><br><button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light remove-invoice-item" data-repeater-delete=""> <span class="fa fa-trash"></span></button>' +
                '</div>' +
                '</div>'

            $('#item-list').append(invoice_items).fadeIn(500);

        });
    });
</script>