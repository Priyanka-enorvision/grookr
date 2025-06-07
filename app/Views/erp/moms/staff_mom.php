<?php

use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\MomsModel;
use App\Models\ProjectsModel;
//$encrypter = \Config\Services::encrypter();
$SystemModel = new SystemModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$MomsModel = new MomsModel();
$moms = $MomsModel->where('company_id', $user_info['company_id'])->findAll();


$staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->findAll();
$projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();

?>



<?php if (in_array('moms_1', staff_role_resource()) || in_array('moms_calendar', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
  <div id="smartwizard-2" class="border-bottom smartwizard-example sw-main sw-theme-default mt-2">
    <ul class="nav nav-tabs step-anchor">
      <?php if (in_array('moms_1', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
        <li class="nav-item active">
          <a href="<?= site_url('erp/moms-list'); ?>" class="mb-3 nav-link">
            <span class="sw-done-icon feather icon-check-circle"></span>
            <span class="sw-icon feather icon-disc"></span><?= lang('Mom.xin_mom'); ?>
            <div class="text-muted small"><?= lang('Main.xin_set_up'); ?> <?= lang('Mom.xin_mom'); ?></div>
          </a>
        </li>
      <?php } ?>
      <?php if (in_array('moms_calendar', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
        <li class="nav-item clickable">
          <a href="<?= site_url('erp/moms-calendar'); ?>" class="mb-3 nav-link">
            <span class="sw-done-icon feather icon-check-circle"></span>
            <span class="sw-icon feather icon-calendar"></span><?= lang('Dashboard.xin_acc_calendar'); ?>
            <div class="text-muted small"><?= lang('Conference.xin_moms_calendar'); ?></div>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>
  <hr class="border-light m-0 mb-3">
<?php } ?>


<div class="row m-b-1 animated fadeInRight">
  <div class="col-md-12">

    <nav class="navbar m-b-30 p-10">
      <ul class="nav">
        <li class="nav-item dropdown"> <a class="nav-link text-secondary" href="#" id="bydate" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><strong>
              <?= lang('Main.xin_list_all'); ?>
              <?= lang('Mom.xin_mom'); ?>
            </strong></a> </li>
      </ul>
      <div class="nav-item nav-grid f-view"> <span class="m-r-15">
          <?= lang('Projects.xin_view_mode'); ?>
          :</span> <a href="<?= site_url() . 'erp/moms-list'; ?>" class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="<?= lang('Projects.xin_list_view'); ?>"> <i class="fas fa-list-ul"></i> </a> <a href="<?= site_url() . 'erp/moms-grid'; ?>" class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip" data-placement="top" title="<?= lang('Projects.xin_grid_view'); ?>"> <i class="fas fa-th-large"></i> </a>
        <?php if (in_array('task2', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
          <a data-toggle="collapse" href="#add_form" aria-expanded="false" class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
            <?= lang('Mom.xin_mom'); ?>
          </a>
        <?php } ?>
      </div>
    </nav>
    <?php if (in_array('mom_2', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
      <div id="add_form" class="collapse add-form " data-parent="#accordion" style="">
        <div class="card mb-2">
          <div id="accordion">

            <?php $attributes = array('name' => 'add_mom', 'id' => 'xin-form', 'type' => 'add_record', 'autocomplete' => 'on'); ?>
            <?php $hidden = array('user_id' => '0'); ?>
            <?php echo form_open('erp/user/add-mom', $attributes, $hidden); ?>
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="title"><?php echo lang('Mom.xin_title'); ?> <span class="text-danger">*</span></label>
                    <input class="form-control" placeholder="<?php echo lang('Mom.xin_title'); ?>" name="title" type="text" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="summary"><?php echo lang('Mom.xin_summary'); ?> <span class="text-danger">*</span></label>
                    <textarea class="form-control" placeholder="<?php echo lang('Mom.xin_summary'); ?>" name="summary" cols="30" rows="1" id="summary"></textarea>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="meeting_date"><?php echo lang('Mom.xin_meeting_date'); ?> <span class="text-danger">*</span></label>
                    <input class="form-control" placeholder="<?php echo lang('Mom.xin_meeting_date'); ?>" name="meeting_date" type="date" value="">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" id="project_ajax">
                    <label for="project_ajax" class="control-label"><?php echo lang('Projects.xin_project'); ?></label>
                    <input type="hidden" value="0" name="project_id[]" />
                    <select class="form-control" multiple name="project_id[]" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project'); ?>">
                      <option value=""></option>
                      <?php foreach ($projects as $iprojects) { ?>
                        <option value="<?= $iprojects['project_id'] ?>">
                          <?= $iprojects['title'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group" id="employee_ajax">
                    <label for="employee"><?php echo lang('Projects.xin_project_users'); ?></label>
                    <input type="hidden" value="0" name="associated_goals[]" />
                    <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project_users'); ?>">
                      <option value=""></option>
                      <?php foreach ($staff_info as $staff) { ?>
                        <option value="<?= $staff['user_id'] ?>">
                          <?= $staff['first_name'] . ' ' . $staff['last_name'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="description"><?php echo lang('Mom.xin_description'); ?></label>
                    <textarea class="form-control editor" placeholder="<?php echo lang('Mom.xin_description'); ?>" name="description" id="description"></textarea>
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
    <?php } ?>
    <div class="card user-profile-list">
      <div class="card-body">
        <div class="box-datatable table-responsive">
          <table class="datatables-demo table table-striped table-bordered" id="xin_table">
            <thead>
              <tr>
                <th><?php echo lang('Mom.xin_title'); ?></th>
                <th><?php echo lang('Mom.xin_summary'); ?></th>
                <th><?php echo lang('Mom.xin_description'); ?></th>
                <!-- <th>Project</th>
                <th>Team</th> -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($moms as $mom): ?>
                <tr>
                  <td><?= $mom['title']; ?></td>
                  <td><?= $mom['summary']; ?></td>
                  <td><?= htmlspecialchars($mom['description']); ?></td>
                  <!-- <td><?= htmlspecialchars($mom['project_id']); ?></td> 
                        <td><?= htmlspecialchars($mom['assigned_to']); ?></td>  -->
                  <td>
                    <?php if (in_array('mom_2', staff_role_resource()) || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'staff') { ?>
                      <a href="<?= site_url('erp/mom-detail') . '/' . uencode($mom['id']); ?>?type=edit"> <i class="feather icon-edit"></i></a>
                      <a href="#!" data-toggle="modal" data-target=".delete-modal" data-record-id="<?= uencode($mom['id']); ?>"><i class="feather icon-trash-2"></i></a>
                    <?php } ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>