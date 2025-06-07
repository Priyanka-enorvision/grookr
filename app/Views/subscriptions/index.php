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
?>
<?php if ($session->get('unauthorized_module')) { ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?= $session->get('unauthorized_module'); ?>
    </div>
<?php } ?>


<?php if (in_array('subscription2', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
    <div id="accordion">
        <div id="add_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
            <?php $attributes = array('name' => 'add_subscription', 'id' => 'xin-form', 'autocomplete' => 'off', 'onsubmit' => 'enableEmailField()'); ?>
            <?php $hidden = array('user_id' => 0); ?>
            <?= form_open_multipart('erp/SubscriptionController/add_subscription', $attributes, $hidden); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>
                                <?= lang('Main.xin_add_new'); ?>
                                <?= "Subscription"; ?>
                            </h5>
                            <div class="card-header-right">
                                <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                                    class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
                                    <i data-feather="minus"></i>
                                    <?= lang('Main.xin_hide'); ?>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company" class="control-label"><?php echo "Company Name"; ?></label>
                                        <select class="form-control" name="company_id" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "Company Name"; ?>" id="company_id">

                                            <option value=""><?php echo "Company List" ?></option>
                                            <?php foreach ($get_companies as $get_company): ?>
                                                <option value="<?= $get_company['company_id']; ?>">
                                                    <?= $get_company['company_name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6" id="company_email">
                                    <div class="form-group">
                                        <label for="email" class="control-label"><?= lang('Main.xin_email'); ?></label>
                                        <input type="email" class="form-control" name="email" disabled="disabled"
                                            placeholder="<?= lang('Main.xin_email'); ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="plan" class="control-label"><?php echo "subscription Plan"; ?></label>
                                        <select class="form-control" name="plan" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "subscription Plan"; ?>">
                                            <option value="basic">Basic</option>
                                            <option value="standard">Standard</option>
                                            <option value="premium">Premium</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date" class="control-label"><?php echo "Start Date"; ?></label>
                                        <input type="date" class="form-control" name="start_date">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date" class="control-label"><?php echo "End Date"; ?></label>
                                        <input type="date" class="form-control" name="end_date">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_status"
                                            class="control-label"><?php echo "Payment Status"; ?></label>
                                        <select class="form-control" name="payment_status" data-plugin="select_hrm"
                                            data-placeholder="<?php echo "Payment Status"; ?>">
                                            <option value="paid"><?= lang('Main.xin_paid'); ?></option>
                                            <option value="pending"><?= lang('Main.xin_pending'); ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes" class="control-label"><?php echo "Notes"; ?></label>
                                        <textarea class="form-control" name="notes" rows="3"
                                            placeholder="<?php echo "Notes"; ?>"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse"
                                aria-expanded="false">
                                <?= lang('Main.xin_reset'); ?>
                            </button>
                            &nbsp;
                            <button type="submit" class="btn btn-primary">
                                <?= lang('Main.xin_save'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?= form_close(); ?>
        </div>
    </div>
<?php } ?>

<script>
    function enableEmailField() {
        document.getElementsByName('email')[0].disabled = false;
    }
</script>


<div class="card user-profile-list">
    <div class="card-header">
        <h5>
            <?= lang('Main.xin_list_all'); ?>
            <?= "Subscriptions"; ?>
        </h5>
        <div class="card-header-right">
            <a href="<?= site_url() . 'erp/companies-grid'; ?>"
                class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip"
                data-placement="top" title="<?= lang('Projects.xin_grid_view'); ?>">
                <i class="fas fa-th-large"></i>
            </a>
            <?php if (in_array('subscription2', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
                <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                    class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0">
                    <i data-feather="plus"></i>
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
                        <th>ID</th>
                        <th>Plan Name</th>
                        <th>Company Name</th>
                        <th>Email</th>
                        <th>Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Payment Status</th>
                        <th>Note</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subscriptions as $subscription): ?>
                        <?php
                        // Format dates
                        $start_date = date('d M Y', strtotime($subscription['start_date']));
                        $end_date = date('d M Y', strtotime($subscription['end_date']));

                        // Determine status
                        $current_date = date('Y-m-d');
                        if ($subscription['payment_status'] == 'paid') {
                            $status = "<span class='badge badge-success'>Paid</span>";
                        } elseif (strtotime($subscription['end_date']) < strtotime($current_date)) {
                            $status = "<span class='badge badge-danger'>Expired</span>";
                        } else {
                            $status = "<span class='badge badge-warning'>Pending</span>";
                        }
                        ?>
                        <tr>
                            <td><?= $subscription['id'] ?></td>
                            <td><?= htmlspecialchars($subscription['plan']) ?></td>
                            <td>
                                <?php
                                $company_data = $UsersModel->where('company_id', $subscription['company_id'])->first();
                                $company_name = $company_data['company_name'] ?? 'N/A';
                                ?>
                                <?= htmlspecialchars($company_name) ?>
                            </td>

                            <td><?= htmlspecialchars($subscription['email']) ?></td>
                            <td><?= htmlspecialchars($subscription['price']) ?></td>
                            <td><?= $start_date ?></td>
                            <td><?= $end_date ?></td>
                            <td><?= $status ?></td>
                            <td><?= htmlspecialchars($subscription['notes']) ?></td>
                            <td>
                                <?php
                                $encoded_user_id = uencode($subscription['id']);
                                $delete_button = '
                                <button type="button" class="btn btn-sm btn-light-danger delete" 
                                    data-toggle="modal" data-target=".delete-modal" data-record-id="' . htmlspecialchars($encoded_user_id, ENT_QUOTES, 'UTF-8') . '">
                                    <i class="feather icon-trash-2"></i>
                                </button>';

                                $edit_button = '
                                <a href="' . site_url('erp/subscription-detail') . '/' . htmlspecialchars($encoded_user_id, ENT_QUOTES, 'UTF-8') . '" class="btn btn-sm btn-light-primary">
                                    <i class="feather icon-edit"></i>
                                </a>';

                                echo  $edit_button . $delete_button;
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
    $(document).ready(function () {
        $('.datatables-demo').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true
        });
    });
</script>

<script>
    $(document).on("click", ".delete", function () {
        var id = $(this).data('record-id');
        $('input[name=_token]').val(id);
        $('#delete_record').attr('action', main_url + 'SubscriptionController/delete_subscription');
    });
</script>

<?php
if ($session->getFlashdata('success')): ?>
    <script>
        $(document).ready(function () {
            toastr.success("<?= $session->getFlashdata('success'); ?>");
        });
    </script>
<?php endif; ?>


<?php
if ($session->getFlashdata('error')): ?>
    <script>
        $(document).ready(function () {
            toastr.error("<?= $session->getFlashdata('error'); ?>");
        });
    </script>
<?php endif; ?>