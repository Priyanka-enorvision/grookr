<?php

use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\ShiftModel;
use App\Models\ConstantsModel;
use App\Models\SystemModel;

$DepartmentModel = new DepartmentModel();
$DesignationModel = new DesignationModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$ShiftModel = new ShiftModel();
$SystemModel = new SystemModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
// echo '<pre>';
// print_r($user_info);
// die;
if ($user_info['user_type'] == 'staff') {
    $departments = $DepartmentModel->where('company_id', $user_info['company_id'])->orderBy('department_id', 'ASC')->findAll();
    $designations = $DesignationModel->where('company_id', $user_info['company_id'])->orderBy('designation_id', 'ASC')->findAll();
    $office_shifts = $ShiftModel->where('company_id', $user_info['company_id'])->orderBy('office_shift_id', 'ASC')->findAll();
    $leave_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
} else {
    $departments = $DepartmentModel->where('company_id', $usession['sup_user_id'])->orderBy('department_id', 'ASC')->findAll();
    $designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->orderBy('designation_id', 'ASC')->findAll();
    $office_shifts = $ShiftModel->where('company_id', $usession['sup_user_id'])->orderBy('office_shift_id', 'ASC')->findAll();
    $leave_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
}

$roles = $RolesModel->orderBy('role_id', 'ASC')->findAll();
$xin_system = $SystemModel->where('setting_id', 1)->first();

$employee_id = generate_random_employeeid();
?>
<style>
    .row {
        display: flex !important;
    }

    .bulk-data {
        margin-left: 780px;
    }

    .row .bulk-data {
        margin-top: -613px;
    }
</style>
<?php if (in_array('leads2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <div id="accordion">
        <div id="add_form" class="collapse add-form <?php echo $get_animate; ?>" data-parent="#accordion" style="">
            <div class="row">
                <?php $attributes = array('name' => 'add_lead', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('user_id' => 0); ?>
                <?= form_open_multipart('erp/clients/add_lead', $attributes, $hidden); ?>
                <div class="col-md-8">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h5>
                                <?= lang('Main.xin_add_new'); ?>
                                <?= lang('Dashboard.xin_lead'); ?>
                            </h5>
                            <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                                    <?= lang('Main.xin_hide'); ?>
                                </a> </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="company_name">
                                            <?= lang('Main.xin_employee_first_name'); ?>
                                            <span class="text-danger">*</span> </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                            <!-- <input class="form-control" placeholder="<?= lang('Main.xin_employee_first_name'); ?>" name="first_name" type="text"> -->
                                            <input class="form-control" placeholder="<?= lang('Main.xin_employee_first_name'); ?>" name="first_name" id="first_name" type="text" onkeypress="return onlyAlphabet(event)" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="last_name" class="control-label">
                                            <?= lang('Main.xin_employee_last_name'); ?>
                                            <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                            <input class="form-control" placeholder="<?= lang('Main.xin_employee_last_name'); ?>" onkeypress="return onlyAlphabet(event)" name="last_name" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
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
                                        <label for="organisation_name">
                                            <?= lang('Main.xin_organisation_name'); ?>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                            <input class="form-control" placeholder="<?= lang('Main.xin_organisation_name'); ?>" name="organisation_name" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="designation" class="control-label">
                                            <?= lang('Main.xin_designation'); ?>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                                            <input class="form-control" placeholder="<?= lang('Main.xin_designation'); ?>" name="designation" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="contact_number">
                                            <?= lang('Main.xin_contact_number'); ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input class="form-control" placeholder="<?= lang('Main.xin_contact_number'); ?>" name="contact_number" type=" "
                                            id="contact_number" pattern="\d{10}" minlength="10" maxlength="10" required>
                                    </div>
                                </div>


                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="lead_status" class="control-label">
                                            <?= lang('Main.xin_lead_status'); ?>
                                        </label>
                                        <select class="form-control" name="lead_status" data-plugin="select_hrm" data-placeholder="<?= lang('Main.xin_employee_gender'); ?>" required>
                                            <option value="1">
                                                <?= lang('Main.xin_hot'); ?>
                                            </option>
                                            <option value="2">
                                                <?= lang('Main.xin_cold'); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
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
                                                <label for="logo"><?= lang('Main.xin_attachment'); ?></label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="customFile" name="file">
                                                    <label class="custom-file-label" for="customFile"><?= lang('Main.xin_choose_file'); ?></label>
                                                    <small><?= lang('Main.xin_company_file_type'); ?></small>
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
                    </div>
                </div>
                <?= form_close(); ?>
                <?php $attributes = array('name' => 'add_bulk_lead', 'id' => 'xin-bulk-form', 'autocomplete' => 'off'); ?>
                <?php $hidden = array('user_id' => 0); ?>
                <?= form_open_multipart('erp/clients/add_bulk_lead', $attributes, $hidden); ?>
                <div class="col-md-4 bulk-data">
                    <div class="card mb-2">
                        <div class="card-header d-flex" style="justify-content:space-between;">
                            <h5>
                                <?= lang('Main.xin_e_bulk_lead'); ?>
                            </h5>
                            <a href="<?= base_url('public/uploads/sample_files/sample_file.csv') ?>" download class="download-icon">
                                &#128190;
                            </a>
                        </div>
                        <div class="card-body py-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="logo">
                                            <?= lang('Main.xin_attachment'); ?>
                                        </label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="bulk_file">
                                            <label class="custom-file-label">
                                                <?= lang('Main.xin_choose_file'); ?>
                                            </label>
                                            <small>
                                                <?= lang('Main.xin_bulk_upload_file_type'); ?>
                                            </small>
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
                <?= form_close(); ?>



            </div>
        </div>
    <?php } ?>
    <div class="card user-profile-list">
        <div class="card-header">
            <h5>
                <?= lang('Main.xin_list_all'); ?>
                <?= lang('Dashboard.xin_leads'); ?>
            </h5>
            <div class="card-header-right">
                <?php if (in_array('leads2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                    <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
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
                            <th><?= lang('Main.xin_contact_number'); ?></th>
                            <th><?= lang('Main.xin_employee_gender'); ?></th>
                            <th><?= lang('Main.xin_organisation_name'); ?></th>
                            <th><?= lang('Main.xin_designation'); ?></th>
                            <th><?= lang('Main.xin_lead_status'); ?></th>
                            <th><?= lang('Main.dashboard_xin_status'); ?></th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#xin-bulk-form').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.error) {
                            alert(response.error);
                            window.location.href = "<?= site_url('erp/leads-list'); ?>";
                        } else {
                            alert(response.result);
                            window.location.href = "<?= site_url('erp/leads-list'); ?>";

                        }
                        $('[name=csrf_token_name]').val(response.csrf_hash);
                    },
                    error: function(xhr, status, error) {
                        alert('Form submission failed!');
                        console.error(error);
                        window.location.href = "<?= site_url('erp/leads-list'); ?>";


                    }
                });
            });
        });
    </script>
    <!-- <script type="text/javascript" src="<?= base_url(); ?>/public/module_scripts/1leads.js"></script> -->