<?php
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();

$session = \Config\Services::session();
$request = \Config\Services::request();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$segment_id = $request->getUri()->getSegment(3);
$company_id = udecode($segment_id);


$company_info = $UsersModel->where('user_id', $company_id)->where('user_type', 'company')->first();

$locale = service('request')->getLocale();
?>

<?php if ($session->get('unauthorized_module')) { ?>
<div class="alert alert-danger alert-dismissible fade show">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <?= $session->get('unauthorized_module'); ?>
</div>
<?php } ?>

<?php if (in_array('company3', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
<div id="accordion">
  <div id="edit_form" class="collapse show" data-parent="#accordion">
    <?php $attributes = array('name' => 'edit_company', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
    <?php $hidden = array('user_id' => $company_info['user_id']); ?>
    <?= form_open_multipart('erp/update-company', $attributes, $hidden); ?>
    <div class="row">
      <div class="col-md-8">
        <div class="card mb-2">
          <div class="card-header">
            <h5>
              <?= lang('Main.xin_edit'); ?> <?= lang('Projects.xin_company'); ?>
            </h5>
            <div class="card-header-right"> 
              <a href="#edit_form" data-toggle="collapse" class="btn btn-sm waves-effect waves-light btn-primary m-0">
                <i data-feather="minus"></i> <?= lang('Main.xin_hide'); ?>
              </a> 
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="company_name">
                    <?= lang('Main.xin_employee_first_name'); ?>
                    <span class="text-danger">*</span> 
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input class="form-control" placeholder="<?= lang('Main.xin_employee_first_name'); ?>" name="first_name" type="text" value="<?= $company_info['first_name']; ?>">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="last_name" class="control-label">
                    <?= lang('Main.xin_employee_last_name'); ?>
                    <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input class="form-control" placeholder="<?= lang('Main.xin_employee_last_name'); ?>" name="last_name" type="text" value="<?= $company_info['last_name']; ?>">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="username">
                    <?= lang('Main.dashboard_username'); ?>
                    <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                    <input class="form-control" placeholder="<?= lang('Main.dashboard_username'); ?>" name="username" type="text" value="<?= $company_info['username']; ?>">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="gender">
                    <?= lang('Main.xin_employee_gender'); ?>
                  </label>
                  <select class="form-control" name="gender" data-plugin="select_hrm">
                    <option value="1" <?= ($company_info['gender'] == '1') ? 'selected' : ''; ?>>
                      <?= lang('Main.xin_gender_male'); ?>
                    </option>
                    <option value="2" <?= ($company_info['gender'] == '2') ? 'selected' : ''; ?>>
                      <?= lang('Main.xin_gender_female'); ?>
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="email">
                    <?= lang('Main.xin_email'); ?>
                    <span class="text-danger">*</span> 
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    </div>
                    <input class="form-control" placeholder="<?= lang('Main.xin_email'); ?>" name="email" type="text" value="<?= $company_info['email']; ?>">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="password">
                    <?= lang('Main.xin_employee_password'); ?>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-eye-slash"></i></span>
                    </div>
                    <input class="form-control" placeholder="<?= lang('Main.xin_employee_password'); ?>" name="password" type="password">
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="contact_number">
                    <?= lang('Main.xin_contact_number'); ?>
                    <span class="text-danger">*</span>
                  </label>
                  <input class="form-control" placeholder="<?= lang('Main.xin_contact_number'); ?>" name="contact_number" type="number" value="<?= $company_info['contact_number']; ?>">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="company_name">
                    <?= lang('Main.dashboard_companyname'); ?>
                    <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-user"></i></span></div>
                    <input class="form-control" placeholder="<?= lang('Main.dashboard_companyname'); ?>" name="company_name" type="text" value="<?= $company_info['company_name']; ?>">
                  </div>
                </div>
              </div>
              
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
              <?= lang('Main.xin_update'); ?>
            </button>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h5><?= lang('Main.xin_e_details_profile_picture'); ?></h5>
          </div>
          <div class="card-body py-2">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="logo"><?= lang('Main.xin_attachment'); ?></label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" name="file">
                    <label class="custom-file-label"><?= lang('Main.xin_choose_file'); ?></label>
                    <small><?= lang('Main.xin_company_file_type'); ?></small> 
                  </div>
                  <?php if (!empty($company_info['profile_picture'])) { ?>
                    <img src="<?= base_url('uploads/company/' . $company_info['profile_picture']); ?>" width="100">
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?= form_close(); ?>
  </div>
</div>
<?php } ?>
