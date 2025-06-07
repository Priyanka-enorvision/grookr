<?php
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\DesignationModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$ConstantsModel = new ConstantsModel();
$DesignationModel = new DesignationModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if ($user_info['user_type'] == 'staff') {
    $designations = $DesignationModel->where('company_id', $user_info['company_id'])->orderBy('designation_id', 'ASC')->findAll();
    $competencies = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
    $competencies2 = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'competencies2')->orderBy('constants_id', 'ASC')->findAll();
} else {
    $designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->orderBy('designation_id', 'ASC')->findAll();
    $competencies = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies')->orderBy('constants_id', 'ASC')->findAll();
    $competencies2 = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'competencies2')->orderBy('constants_id', 'ASC')->findAll();
}
?>
<hr class="border-light m-0 mb-3">
<div id="add_form" class="collapse add-form" data-parent="#accordion" style="">
    <div class="card">
        <div id="accordion">
            <div class="card-header">
                <h5>
                    <?= lang('Performance.xin_role_set'); ?>
                    <?= lang('Performance.xin_performance_indicator'); ?>
                </h5>
                <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                        class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i
                            data-feather="minus"></i>
                        <?= lang('Main.xin_hide'); ?>
                    </a> </div>
            </div>
            <?php $attributes = array('name' => 'add_performance_indicator', 'id' => 'xin-form', 'autocomplete' => 'off', 'class' => 'form-hrm'); ?>
            <?php $hidden = array('user_id' => 1); ?>
            <?php echo form_open('erp/talent/add_indicator', $attributes, $hidden); ?>
            <div class="card-body">
                <div class="row">
                    <!-- <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_name">
                                <?= lang('Dashboard.xin_title'); ?>
                                <span class="text-danger">*</span> </label>
                            <input class="form-control" placeholder="<?= lang('Dashboard.xin_title'); ?>" name="title"
                                type="text" value="">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="company_name">
                                <?= lang('Dashboard.left_designation'); ?>
                                <span class="text-danger">*</span> </label>
                            <select class="select2" data-plugin="select_hrm"
                                data-placeholder="<?php echo lang('Dashboard.left_designation'); ?>"
                                name="designation_id">
                                <option value="">
                                    <?= lang('Dashboard.left_designation'); ?>
                                </option>
                                <?php foreach ($designations as $idesignations): ?>
                                    <option value="<?= $idesignations['designation_id']; ?>">
                                        <?= $idesignations['designation_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div> -->
                </div>
                <div class="row">
                    <!-- Employee Section: col-8 -->
                    <div class="col-md-8 table-border-style">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th colspan="5">Employee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="table-success">
                                        <th colspan="2"><?php echo lang('Dashboard.left_performance_xindicator'); ?>
                                        </th>
                                        <th>Description</th>
                                        <th>Rating</th>
                                        <th>Remarks</th>
                                    </tr>
                                    <?php foreach ($competencies as $itech_comp): ?>
                                        <tr class="m-b-2">
                                            <td scope="row" colspan="2"><?php echo $itech_comp['category_name']; ?></td>
                                            <td><textarea type="text" class=""></textarea></td>
                                            <td>
                                                <select class="bar-rating rating-input"
                                                    name="technical_competencies_value[<?php echo $itech_comp['constants_id']; ?>]"
                                                    autocomplete="off">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                            </td>
                                            <td><textarea type="text" class=""></textarea></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- Total and Average Row -->
                                    <tr class="" style="background-color: #b7edc4;">
                                        <td colspan="4" class="text-right"><strong>Total</strong></td>
                                        <td>
                                            <input type="text" id="total-rating" class="form-control">
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if ($user_info['user_type'] == 'staff') { ?>
                        <!-- Manager Section: col-4 -->
                        <div class="col-md-4 table-border-style">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th colspan="3">Manager</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="table-success">
                                            <th>Rating</th>
                                            <th>Remarks</th>
                                        </tr>
                                        <?php foreach ($competencies as $iorg_comp): ?>
                                            <tr class="m-b-2">
                                                <td>
                                                    <select
                                                        name="organizational_competencies_value[<?php echo $iorg_comp['constants_id']; ?>]"
                                                        class="bar-rating" autocomplete="off" disabled>
                                                        <option value="1" <?= $iorg_comp['value'] == 1 ? 'selected' : '' ?>>1
                                                        </option>
                                                        <option value="2" <?= $iorg_comp['value'] == 2 ? 'selected' : '' ?>>2
                                                        </option>
                                                        <option value="3" <?= $iorg_comp['value'] == 3 ? 'selected' : '' ?>>3
                                                        </option>
                                                        <option value="4" <?= $iorg_comp['value'] == 4 ? 'selected' : '' ?>>4
                                                        </option>
                                                        <option value="5" <?= $iorg_comp['value'] == 5 ? 'selected' : '' ?>>5
                                                        </option>
                                                    </select>
                                                </td>
                                                <td><textarea class=""
                                                        disabled><?= htmlspecialchars($iorg_comp['remarks']) ?></textarea></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tr class="" style="background-color: #b7edc4;">
                                        <td class="text-right"><strong>Total</strong></td>
                                        <td>
                                            <input type="text" id="total-rating" class="form-control"
                                                value="<?= $total_rating ?>" disabled>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    <?php } ?>


                </div>
            </div>
            <div class="card-footer text-right">
                <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse"
                    aria-expanded="false">
                    <?= lang('Main.xin_reset'); ?>
                </button>
                &nbsp;
                <button type="submit" class="btn " style="background-color: #007bff; color:white;">
                    <?= lang('Main.xin_save'); ?>
                </button>
            </div>
            <?= form_close(); ?>

        </div>
    </div>
</div>
<div class="card user-profile-list">
    <div class="card-header">
        <h5>
            <?= lang('Main.xin_list_all'); ?>
            <?= lang('Performance.xin_performance_indicators'); ?>
        </h5>
        <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
                <?= lang('Main.xin_add_new'); ?>
            </a> </div>
    </div>
    <div class="card-body">
        <div class="box-datatable table-responsive">
            <table class="datatables-demo table table-striped table-bordered" id="xin_table">
                <thead>
                    <tr>
                        <th><?php echo lang('Dashboard.xin_title'); ?></th>
                        <th><?php echo lang('Dashboard.left_designation'); ?></th>
                        <th><?php echo lang('Performance.xin_overall_rating'); ?></th>
                        <!-- <th><i class="fa fa-user"></i> <?php echo lang('Main.xin_added_by'); ?></th>
            <th><i class="fa fa-calendar"></i> <?php echo lang('Main.xin_created_at'); ?></th> -->
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>