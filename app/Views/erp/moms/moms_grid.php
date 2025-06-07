<?php
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\MomsModel;
use App\Models\ProjectsModel;


$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$MomsModel = new MomsModel();
$ProjectsModel = new ProjectsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

if ($user_info['user_type'] == 'staff') {
  $id = $usession['sup_user_id'];
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();
  $get_moms = $MomsModel->where('company_id', $user_info['company_id'])->findAll();
  $total_moms = $MomsModel->where('company_id', $user_info['company_id'])->orderBy('id', 'ASC')->countAllResults();
  $in_active = $MomsModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
  $active = $MomsModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
} else {
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();
  $get_moms = $MomsModel->where('company_id', $user_info['company_id'])->findAll();
  $total_moms = $MomsModel->where('company_id', $user_info['company_id'])->orderBy('id', 'ASC')->countAllResults();
  $in_active = $MomsModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
  $active = $MomsModel->where('company_id', $user_info['company_id'])->where('status', 1)->countAllResults();
}
?>

<!-- [ mom-board-right ] start -->
<?php if (in_array('mom_1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

  <div class="row">
    <div class="col-xl-12 col-lg-12 filter-bar">
      <nav class="navbar m-b-30 p-10">
        <ul class="nav">
          <li class="nav-item dropdown"> <a class="nav-link text-secondary" href="#" id="bydate" data-toggle="dropdown"
              aria-haspopup="true" aria-expanded="false"><strong>
                <?= lang('Main.xin_list_all'); ?>
                <?= lang('Mom.xin_mom'); ?>
              </strong></a> </li>
        </ul>
        <div class="nav-item nav-grid f-view"> <span class="m-r-15">
            <?= lang('Projects.xin_view_mode'); ?>
            :</span><a
            href="<?= site_url() . 'erp/moms-grid'; ?>"
            class="btn btn-sm waves-effect waves-light btn-primary btn-icon m-0" data-toggle="tooltip"
            data-placement="top" title="<?= lang('Projects.xin_grid_view'); ?>"> <i class="fas fa-th-large"></i> </a>
          <?php if (in_array('task2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <a data-toggle="collapse" href="#add_form" aria-expanded="false"
              class="collapsed btn waves-effect waves-light btn-primary btn-sm m-0"> <i data-feather="plus"></i>
              <?= lang('Mom.xin_mom'); ?>
            </a>
          <?php } ?>
        </div>
      </nav>
      <?php if (in_array('mom_2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <div id="add_form" class="collapse add-form " data-parent="#accordion" style="">
          <div class="card mb-2">
            <div id="accordion">
              <div class="card-header">
                <h5>
                  <?= lang('Main.xin_add_new'); ?>
                  <?= lang('Mom.xin_mom'); ?>
                </h5>
                <div class="card-header-right"> <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                    class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0"> <i data-feather="minus"></i>
                    <?= lang('Main.xin_hide'); ?>
                  </a> </div>
              </div>
              <?php $attributes = array('name' => 'add_mom', 'id' => 'xin-form', 'type' => 'add_record', 'autocomplete' => 'on'); ?>
              <?php $hidden = array('user_id' => '0'); ?>
              <?php echo form_open('erp/add-mom', $attributes, $hidden); ?>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="title"><?php echo lang('Mom.xin_title'); ?> <span class="text-danger">*</span></label>
                      <input class="form-control" placeholder="<?php echo lang('Mom.xin_title'); ?>" name="title"
                        type="text" value="">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="summary"><?php echo lang('Mom.xin_summary'); ?> <span class="text-danger">*</span></label>
                      <textarea class="form-control" placeholder="<?php echo lang('Mom.xin_summary'); ?>" name="summary"
                        cols="30" rows="1" id="summary"></textarea>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="meeting_date"><?php echo lang('Mom.xin_meeting_date'); ?> <span
                          class="text-danger">*</span></label>
                      <input class="form-control" placeholder="<?php echo lang('Mom.xin_meeting_date'); ?>"
                        name="meeting_date" type="date" value="">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" id="project_ajax">
                      <label for="project_ajax" class="control-label"><?php echo lang('Projects.xin_project'); ?> <span
                          class="text-danger">*</span></label>
                          <input type="hidden" value="0" name="project_id[]" />
                      <select class="form-control" multiple name="project_id[]" data-plugin="select_hrm"
                        data-placeholder="<?php echo lang('Projects.xin_project'); ?>">
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
                      <input type="hidden" value="0" name="assigned_to[]" />
                      <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm"
                        data-placeholder="<?php echo lang('Projects.xin_project_users'); ?>">
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
                      <textarea class="form-control editor" placeholder="<?php echo lang('Mom.xin_description'); ?>"
                        name="description" id="description"></textarea>
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
      
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>
    <?php if (empty($get_moms)) { ?>
    <div class="col-12 text-center">
        <p class="alert alert-warning"><?php echo "No Mom Found?";  ?></p>
    </div>
    <?php } else { ?>
    <div class="row">
          <?php $i = 1;
          foreach ($get_moms as $r) { ?>
            <?php

            if ($r['status'] == 1) {
              $status = '<button class="btn waves-effect waves-light btn-light-warning btn-sm" type="button">' . lang('Mom.xin_not_active') . '</button>';
            } else {
              $status = '<button class="btn waves-effect waves-light btn-light-success btn-sm" type="button">' . lang('Mom.xin_active') . '</button>';
            }
            ?>
            <div class="col-md-4 col-sm-12">
              <div class="card card-border-c-blue">
                <div class="card-header"> <a href="<?= site_url('erp/mom-detail') . '/' . uencode($r['id']); ?>"
                    class="text-secondary">#
                    <?= $i; ?>
                    .
                    <?= $r['title']; ?>
                  </a> <span class="label label-primary float-right">
                    <?= $r['meeting_date']; ?>
                  </span>
                </div>
                <div class="card-body card-task">
                  <div class="row">
                    <div class="col-sm-12">
                      <p class="task-detail">
                        <?= substr($r['summary'], 0, 70); ?>
                      </p>
                    </div>
                  </div>
                  <hr>
                  <div class="task-list-table">
                    <a href="#!"><i class="fas fa-plus"></i></a>
                  </div>
                  <div class="task-board" style="float: inherit;">
                    <div class="dropdown-secondary dropdown"> <a
                        href="<?= site_url('erp/mom-detail') . '/' . $r['id']; ?>">
                        <?= $status ?>
                      </a> </div>
                    <div class="dropdown-secondary dropdown"> <a
                        href="<?= site_url('erp/mom-detail') . '/' . $r['id']; ?>">
                        <button class="btn waves-effect waves-light btn-primary btn-sm b-none txt-muted" type="button"><i
                            data-toggle="tooltip" data-placement="top" title="<?= lang('Mom.xin_view_mom'); ?>"
                            class="fas fa-eye m-0"></i></button>
                      </a> </div>
                    <div class="dropdown-secondary dropdown">
                      <button class="btn waves-effect waves-light btn-primary btn-sm dropdown-toggle b-none txt-muted"
                        type="button" id="dropdown3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                          class="fas fa-bars"></i></button>
                      <div class="dropdown-menu" aria-labelledby="dropdown3" data-dropdown-in="fadeIn"
                        data-dropdown-out="fadeOut">
                        <?php if (in_array('mom_5', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                          <a class="dropdown-item" href="<?= site_url('erp/mom-detail') . '/' . $r['id']; ?>"> <i
                              class="feather icon-eye"></i>
                            <?= lang('Mom.xin_view_mom'); ?>
                          </a>
                        <?php } ?>
                        <?php if (in_array('mom_3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                          <a class="dropdown-item"
                            href="<?= site_url('erp/mom-detail') . '/' . $r['id']; ?>?type=edit"> <i
                              class="feather icon-edit"></i>
                            <?= lang('Mom.xin_edit_mom'); ?>
                          </a>
                        <?php } ?>
                        <?php if (in_array('mom_4', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                          <div class="dropdown-divider"></div>
                          <a href="#!" class="dropdown-item delete" data-toggle="modal" data-target=".delete-modal"
                            data-record-id="<?= $r['id']; ?>"><i class="feather icon-trash-2"></i>
                            <?= lang('Mom.xin_remove_mom'); ?>
                          </a>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="dropdown-secondary dropdown"> </div>
                  </div>
                </div>
              </div>
            </div>
            <?php $i++;
          } ?>
        </div>
      <?php } ?>
    </div>
    <!-- [ mom-board-right ] end -->
  </div>

<?php } ?>
<hr>
