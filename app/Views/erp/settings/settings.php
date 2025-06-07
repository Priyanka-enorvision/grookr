<?php

use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\SystemModel;
use App\Models\CountryModel;
use App\Models\ConstantsModel;
use App\Models\AnnualPlanningModel;
use App\Models\YearPlanningModel;
use App\Models\PlanningConfigurationSettingModel;
use App\Models\PerformanceDurationModel;
use App\Models\DocumentConfigModel;
use App\Models\TaxDurationModel;

$LanguageModel = new LanguageModel();
$SystemModel = new SystemModel();
$CountryModel = new CountryModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$AnnualPlanningModel = new AnnualPlanningModel();
$PlanningConfigurationSettingModel = new PlanningConfigurationSettingModel();
$performanceDuration = new PerformanceDurationModel();
$YearPlanningModel = new YearPlanningModel();
$doc_categoryModel = new DocumentConfigModel();
$TaxDurationModel = new TaxDurationModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$router = service('router');

$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$user_company_id = $user['company_id'];
$user_type = $user['user_type'];
$currentYear = date('Y');

$planning_configuration_data = $PlanningConfigurationSettingModel->where(['company_id' => $user_company_id, 'user_type' => $user_type])->findAll();

$year_planning_data = $YearPlanningModel->where(['company_id' => $user_company_id, 'user_type' => $user_type])->findAll();
$years = array_column($year_planning_data, 'year');

$unique_years = array_unique($years);

rsort($unique_years);

$currency = $ConstantsModel->where('type', 'currency_type')->orderBy('constants_id', 'ASC')->findAll();
$language = $LanguageModel->where('is_active', 1)->orderBy('language_id', 'ASC')->findAll();
$xin_system = $SystemModel->where('setting_id', 1)->first();
$logo_details = $SystemModel->where('company_id', $user_company_id)->first();
$all_countries = $CountryModel->orderBy('country_id', 'ASC')->findAll();

$company_types = $ConstantsModel->where('type', 'company_type')->orderBy('constants_id', 'ASC')->findAll();
$document_data = $doc_categoryModel->where('company_id', $user_company_id)->findAll();
$tax_duration = $TaxDurationModel->where('company_id', $user_company_id)->first();


?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<hr class="border-light m-0 mb-3">
<?php if (in_array('settings1', staff_role_resource()) || $user['user_type'] == 'company') { ?>
  <div class="row">
    <!-- start -->
    <div class="col-lg-3">
      <div class="card user-card user-card-1">
        <div class="card-body pb-0">
          <div class="media user-about-block align-items-center mt-0 mb-3">
            <div class="position-relative d-inline-block">
              <div class="certificated-badge"> <a href="javascript:void(0)" class="mb-3 nav-link"><span
                    class="sw-icon fas fa-cog"></span></a> </div>
            </div>
            <div class="media-body ml-3">
              <h6 class="mb-1">
                <?= lang('Main.left_settings'); ?>
              </h6>
              <p class="mb-0 text-muted">
                <?= lang('Main.header_configuration'); ?>
              </p>
            </div>
          </div>
        </div>
        <div class="nav flex-column nav-pills list-group list-group-flush list-pills" id="user-set-tab" role="tablist"
          aria-orientation="vertical">

          <a class="nav-link list-group-item list-group-item-action active" id="account-settings-tab" data-toggle="pill"
            href="#account-settings" role="tab" aria-controls="account-settings" aria-selected="true"> <span
              class="f-w-500"><i class="feather icon-disc m-r-10 h5 "></i>
              <?= lang('Main.xin_system'); ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="account-system-logos-tab" data-toggle="pill"
            href="#account-system-logos" role="tab" aria-controls="account-system-logos" aria-selected="false"> <span
              class="f-w-500"><i class="feather icon-image m-r-10 h5 "></i>
              <?= lang('Main.xin_system_logos'); ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="account-payment-tab" data-toggle="pill"
            href="#account-payment" role="tab" aria-controls="account-payment" aria-selected="false"> <span
              class="f-w-500"><i class="feather icon-credit-card m-r-10 h5 "></i>
              <?= lang('Main.xin_acc_payment_gateway'); ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="account-notification-tab" data-toggle="pill"
            href="#account-notification" role="tab" aria-controls="account-notification" aria-selected="false"> <span
              class="f-w-500"><i class="feather icon-crosshair m-r-10 h5 "></i>
              <?= lang('Main.xin_notification_position'); ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="user-set-email-tab" data-toggle="pill"
            href="#user-set-email" role="tab" aria-controls="user-set-email" aria-selected="false"> <span
              class="f-w-500"><i class="feather icon-mail m-r-10 h5 "></i>
              <?= lang('Main.xin_email_notifications'); ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="user-set-planning-configuration-tab"
            data-toggle="pill" href="#user-set-planning-configuration" role="tab"
            aria-controls="user-set-planning-configuration" aria-selected="false"> <span class="f-w-500"><i
                class="feather icon-clock m-r-10 h5 "></i>
              <?php echo "Planning Configuration"; ?></span> <span class="float-right"><i
                class="feather icon-chevron-right"></i></span> </a>

          <a class="nav-link list-group-item list-group-item-action" id="document-category-tab"
            data-toggle="pill" href="#document-category" role="tab" aria-controls="document-category" aria-selected="false">
            <span class="f-w-500"> <i class="feather icon-file-text m-r-10 h5"></i>
              Document Category </span>
            <span class="float-right"> <i class="feather icon-chevron-right"></i> </span></a>

          <a class="nav-link list-group-item list-group-item-action" id="tax-yeardeclaration-tab"
            data-toggle="pill" href="#tax-yeardeclaration" role="tab" aria-controls="tax-yeardeclaration" aria-selected="false">
            <span class="f-w-500">
              <i class="feather icon-calendar m-r-10 h5"></i> Tax Duration
            </span>
            <span class="float-right"> <i class="feather icon-chevron-right"></i> </span></a>
          </a>




        </div>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="tab-content" id="user-set-tabContent">
        <div class="tab-pane fade show active" id="account-settings" role="tabpanel"
          aria-labelledby="account-settings-tab">
          <div class="card">
            <div class="card-header">
              <h5><i data-feather="disc" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                  <?= lang('Main.xin_system'); ?>
                </span></h5>
            </div>
            <div class="card-body">
              <?php $attributes = array('name' => 'system_info', 'id' => 'system_info', 'autocomplete' => 'off'); ?>
              <?php $hidden = array('u_basic_info' => 'UPDATE'); ?>
              <?= form_open('erp/settings/system_info', $attributes, $hidden); ?>
              <div class="bg-white">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="form-label">
                        <?= lang('Main.xin_application_name'); ?>
                        <span class="text-danger">*</span> </label>
                      <input class="form-control" placeholder="<?= lang('Main.xin_application_name'); ?>"
                        name="application_name" type="text" value="<?= $xin_system['application_name']; ?>"
                        id="application_name">
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>

        </div>
        <div class="tab-pane fade" id="account-system-logos" role="tabpanel" aria-labelledby="account-system-logos-tab">
          <div class="card">
            <div class="card-header">
              <h5><i data-feather="image" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                  <?= lang('Main.xin_system_logos'); ?>
                </span></h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-xl-12">
                  <div class="nav-tabs-top mb-4">
                    <ul class="nav nav-tabs">
                      <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#xin_system_logos">
                          <?= lang('Main.xin_system_logos'); ?>
                        </a> </li>
                      <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#xin_payslip_invoice_logo_title">
                          <?= lang('Main.xin_payslip_invoice_logo_title'); ?>
                        </a> </li>
                    </ul>
                    <div class="tab-content">
                      <div class="tab-pane fade active show" id="xin_system_logos">
                        <div class="card-body">
                          <div class="row  mb-4">
                            <div class="col-md-6">
                              <?php $attributes = array('name' => 'logo_info', 'id' => 'logo_info', 'autocomplete' => 'off'); ?>
                              <?php $hidden = array('company_logo' => 'UPDATE'); ?>
                              <?= form_open_multipart('erp/settings/add_logo', $attributes, $hidden); ?>
                              <label for="logo">
                                <?= lang('Main.xin_system_logos'); ?>
                                <span class="text-danger">*</span> </label>
                              <div class="custom-file">
                                <input type="file" class="custom-file-input" name="logo_file">
                                <label class="custom-file-label"><?= lang('Main.xin_choose_file'); ?></label>
                                <div class="mt-3">
                                  <?php if ($xin_system['logo'] != '' && $xin_system['logo'] != 'no file') { ?>
                                    <img src="<?= base_url() . 'uploads/logo/' . $xin_system['logo']; ?>" width="70px"
                                      style="margin-left:30px;" id="u_file_1">
                                  <?php } else { ?>
                                    <img src="<?= base_url() . 'uploads/logo/no_logo.png'; ?>" width="70px"
                                      style="margin-left:30px;" id="u_file_1">
                                  <?php } ?>
                                </div>
                                <div class="mt-3"> <small>-
                                    <?= lang('Main.xin_logo_files_only'); ?>
                                  </small><br />
                                  <small>-
                                    <?= lang('Main.xin_best_main_logo_size'); ?>
                                  </small><br />
                                  <small>-
                                    <?= lang('Main.xin_logo_whit_background_light_text'); ?>
                                  </small>
                                </div>
                              </div>
                              <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                  <?= lang('Main.xin_save'); ?>
                                </button>
                              </div>
                              <?= form_close(); ?>
                            </div>

                            <div class="col-md-6 mb-4">
                              <?php $attributes = array('name' => 'logo_favicon', 'id' => 'logo_favicon', 'autocomplete' => 'off'); ?>
                              <?php $hidden = array('company_logo' => 'UPDATE'); ?>
                              <?= form_open_multipart('erp/settings/add_favicon', $attributes, $hidden); ?>
                              <label for="logo">
                                <?= lang('Main.xin_favicon'); ?>
                                <span class="text-danger">*</span> </label>
                              <div class="custom-file">
                                <input type="file" class="custom-file-input" name="favicon">
                                <label class="custom-file-label"><?= lang('Main.xin_choose_file'); ?></label>
                                <div class="mt-3">
                                  <?php if ($xin_system['favicon'] != '' && $xin_system['favicon'] != 'no file') { ?>
                                    <img src="<?= base_url() . 'uploads/logo/favicon/' . $xin_system['favicon']; ?>"
                                      width="16px" style="margin-left:30px;" id="favicon1">
                                  <?php } else { ?>
                                    <img src="<?= base_url() . 'uploads/logo/no_logo.png'; ?>" width="16px"
                                      style="margin-left:30px;" id="favicon1">
                                  <?php } ?>
                                </div>
                                <div class="mt-3"> <small>-
                                    <?= lang('Main.xin_logo_files_only_favicon'); ?>
                                  </small><br />
                                  <small>-
                                    <?= lang('Main.xin_best_logo_size_favicon'); ?>
                                  </small>
                                </div>
                              </div>
                              <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                  <?= lang('Main.xin_save'); ?>
                                </button>
                              </div>
                              <?= form_close(); ?>
                            </div>
                          </div>

                        </div>

                      </div>
                      <div class="tab-pane fade" id="xin_payslip_invoice_logo_title">

                        <?php $attributes = array('name' => 'iother_logo', 'id' => '', 'autocomplete' => 'off'); ?>
                        <?php $hidden = array('company_logo' => 'UPDATE'); ?>
                        <?= form_open_multipart('erp/settings/add_other_logo', $attributes, $hidden); ?>
                        <div class="card-body">
                          <div class="row">
                            <div class="col-md-6 mb-4">
                              <label for="logo">
                                <?= lang('Main.xin_logo'); ?>
                                <span class="text-danger">*</span> </label>
                              <div class="custom-file">
                                <input type="hidden" class="custom-file-input" name="company_id" value="<?= isset($logo_details['setting_id']) ? $logo_details['setting_id'] : ''; ?>">
                                <input type="file" class="custom-file-input" name="other_logo">
                                <label class="custom-file-label"><?= lang('Main.xin_choose_file'); ?></label>
                                <div class="mt-3">
                                  <?php if (!empty($logo_details)) { ?>
                                    <?php if ($logo_details['other_logo'] != '' && $logo_details['other_logo'] != 'no file') { ?>
                                      <img src="<?= base_url() . 'uploads/logo/other/' . $logo_details['other_logo']; ?>"
                                        width="70px" style="margin-left:30px;" id="u_file3">
                                    <?php } else { ?>
                                      <img src="<?= base_url() . 'uploads/logo/no_logo.png'; ?>" width="70px"
                                        style="margin-left:30px;" id="u_file3">
                                    <?php } ?>
                                  <?php } ?>
                                </div>
                                <div class="mt-3"> <small>-
                                    <?= lang('Main.xin_logo_dark_background_text'); ?>
                                  </small></div>
                                <div> <small>-
                                    <?= lang('Main.xin_logo_files_only'); ?>
                                  </small></div>
                                <div> <small>-
                                    <?= lang('Main.xin_best_signlogo_size'); ?>
                                  </small></div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="card-footer text-right">
                          <button type="submit" class="btn btn-primary">
                            <?= lang('Main.xin_save'); ?>
                          </button>
                        </div>
                        <?= form_close(); ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="account-payment" role="tabpanel" aria-labelledby="account-payment-tab">
          <div class="card">
            <div class="card-header">
              <h5> <i data-feather="credit-card" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                  <?= lang('Main.xin_acc_payment_gateway'); ?>
                </span> <small
                  class="text-muted d-block m-l-25 m-t-5"><?= lang('Main.xin_change_payment_gateway_settings'); ?></small>
              </h5>
            </div>
            <?php $attributes = array('name' => 'payment_gateway', 'id' => 'payment_gateway', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('u_company_info' => 'UPDATE'); ?>
            <?= form_open('erp/settings/update_payment_gateway', $attributes, $hidden); ?>
            <div class="card-body">
              <h5>
                <?= lang('Main.xin_acc_paypal_info'); ?>
              </h5>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label">
                      <?= lang('Main.xin_acc_paypal_email'); ?>
                      <span class="text-danger">*</span> </label>
                    <input class="form-control" placeholder="<?= lang('Main.xin_acc_paypal_email'); ?>" name="paypal_email"
                      type="text" value="<?= $xin_system['paypal_email']; ?>">
                  </div>
                  <div class="form-group">
                    <div class="row">
                      <div class="col-md-6">
                        <label class="form-label">
                          <?= lang('Main.xin_acc_paypal_sandbox_active'); ?>
                          <span class="text-danger">*</span> </label>
                        <select class="form-control" name="paypal_sandbox" data-plugin="xin_select"
                          data-placeholder="<?= lang('Main.paypal_sandbox_active'); ?>">
                          <option value="">
                            <?= lang('Main.xin_select_one'); ?>
                          </option>
                          <option value="yes" <?php if ($xin_system['paypal_sandbox'] == 'yes'): ?> selected="selected" <?php endif; ?>>
                            <?= lang('Main.xin_yes'); ?>
                          </option>
                          <option value="no" <?php if ($xin_system['paypal_sandbox'] == 'no'): ?> selected="selected" <?php endif; ?>>
                            <?= lang('Main.xin_no'); ?>
                          </option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">
                          <?= lang('Main.xin_employees_active'); ?>
                          <span class="text-danger">*</span> </label>
                        <select class="form-control" name="paypal_active" data-plugin="xin_select"
                          data-placeholder="<?= lang('Main.xin_employees_active'); ?>">
                          <option value="">
                            <?= lang('Main.xin_select_one'); ?>
                          </option>
                          <option value="yes" <?php if ($xin_system['paypal_active'] == 'yes'): ?> selected="selected" <?php endif; ?>>
                            <?= lang('Main.xin_yes'); ?>
                          </option>
                          <option value="no" <?php if ($xin_system['paypal_active'] == 'no'): ?> selected="selected" <?php endif; ?>>
                            <?= lang('Main.xin_no'); ?>
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <h5 class="pb-2">
                <?= lang('Main.xin_acc_stripe_info'); ?>
              </h5>
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="form-label">
                      <?= lang('Main.xin_acc_stripe_secret_key'); ?>
                      <span class="text-danger">*</span> </label>
                    <input class="form-control" placeholder="<?= lang('Main.xin_acc_stripe_secret_key'); ?>"
                      name="stripe_secret_key" type="text" value="<?= $xin_system['stripe_secret_key']; ?>">
                  </div>
                  <div class="form-group">
                    <label class="form-label">
                      <?= lang('Main.xin_acc_stripe_publlished_key'); ?>
                      <span class="text-danger">*</span> </label>
                    <input class="form-control" placeholder="<?= lang('Main.xin_acc_stripe_publlished_key'); ?>"
                      name="stripe_publishable_key" type="text" value="<?= $xin_system['stripe_publishable_key']; ?>">
                  </div>
                  <div class="form-group">
                    <label class="form-label">
                      <?= lang('Main.xin_employees_active'); ?>
                      <span class="text-danger">*</span> </label>
                    <select class="form-control" name="stripe_active" data-plugin="xin_select"
                      data-placeholder="<?= lang('Main.xin_employees_active'); ?>">
                      <option value="">
                        <?= lang('Main.xin_select_one'); ?>
                      </option>
                      <option value="yes" <?php if ($xin_system['stripe_active'] == 'yes'): ?> selected="selected" <?php endif; ?>>
                        <?= lang('Main.xin_yes'); ?>
                      </option>
                      <option value="no" <?php if ($xin_system['stripe_active'] == 'no'): ?> selected="selected" <?php endif; ?>>
                        <?= lang('Main.xin_no'); ?>
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
        <div class="tab-pane fade" id="account-notification" role="tabpanel" aria-labelledby="account-notification-tab">
          <div class="card">
            <div class="card-header">
              <h5><i data-feather="crosshair" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                  <?= lang('Main.xin_notification_position'); ?>
                </span></h5>
            </div>

            <?php $attributes = array('name' => 'notification_position_info', 'id' => 'notification_position_info', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('theme_info' => 'UPDATE'); ?>
            <?= form_open('erp/settings/notification_position_info', $attributes, $hidden); ?>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">
                      <?= lang('Main.dashboard_position'); ?>
                      <span class="text-danger">*</span> </label>
                    <select class="form-control" name="notification_position" data-plugin="select_hrm"
                      data-placeholder="<?= lang('Main.dashboard_position'); ?>">
                      <option value="">
                        <?= lang('Main.xin_select_one'); ?>
                      </option>
                      <option value="toast-top-right" <?php if ($xin_system['notification_position'] == 'toast-top-right') { ?>
                        selected <?php } ?>>
                        <?= lang('Main.xin_top_right'); ?>
                      </option>
                      <option value="toast-bottom-right" <?php if ($xin_system['notification_position'] == 'toast-bottom-right') { ?> selected <?php } ?>>
                        <?= lang('Main.xin_bottom_right'); ?>
                      </option>
                      <option value="toast-bottom-left" <?php if ($xin_system['notification_position'] == 'toast-bottom-left') { ?> selected <?php } ?>>
                        <?= lang('Main.xin_bottom_left'); ?>
                      </option>
                      <option value="toast-top-left" <?php if ($xin_system['notification_position'] == 'toast-top-left') { ?>
                        selected <?php } ?>>
                        <?= lang('Main.xin_top_left'); ?>
                      </option>
                      <option value="toast-top-center" <?php if ($xin_system['notification_position'] == 'toast-top-center') { ?> selected <?php } ?>>
                        <?= lang('Main.xin_top_center'); ?>
                      </option>
                    </select>
                    <br />
                    <small class="text-muted"><i class="ft-arrow-up"></i>
                      <?= lang('Main.xin_set_position_for_notifications'); ?>
                    </small>
                  </div>
                </div>
              </div>
              <hr class="pb-3">
              <h6 class="mb-4"><?= lang('Main.xin_close_button'); ?></h6>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="notification_close" name="notification_close"
                  <?php if ($xin_system['notification_close_btn'] == 'true'): ?> checked="checked" <?php endif; ?>
                  value="true">
                <label class="custom-control-label"
                  for="notification_close"><?= lang('Main.xin_enable_notification_close_btn'); ?></label>
              </div>
              <hr class="pb-3">
              <h6 class="mb-4"><?= lang('Main.xin_progress_bar'); ?></h6>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="notification_bar" name="notification_bar" <?php if ($xin_system['notification_bar'] == 'true'): ?> checked="checked" <?php endif; ?> value="true">
                <label class="custom-control-label"
                  for="notification_bar"><?= lang('Main.xin_enable_notification_bar'); ?></label>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
        <div class="tab-pane fade" id="user-set-email" role="tabpanel" aria-labelledby="user-set-email-tab">
          <div class="card">
            <div class="card-header">
              <h5><i data-feather="mail" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                  <?= lang('Main.xin_email_notifications'); ?>
                </span></h5>
            </div>
            <?php $attributes = array('name' => 'email_info', 'id' => 'email_info', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('u_basic_info' => 'UPDATE'); ?>
            <?= form_open('erp/settings/email_info', $attributes, $hidden); ?>
            <div class="card-body">
              <div class="bg-white">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="form-label">
                        <?= lang('Main.xin_mail_type_config'); ?>
                        <span class="text-danger">*</span> </label>
                      <select class="form-control" name="email_type" id="email_type" data-plugin="select_hrm"
                        data-placeholder="<?= lang('Main.xin_mail_type_config'); ?>">
                        <option value="codeigniter" <?php if ($xin_system['email_type'] == 'codeigniter'): ?>
                          selected="selected" <?php endif; ?>>CodeIgniter v4 Mail()</option>
                        <option value="phpmail" <?php if ($xin_system['email_type'] == 'phpmail'): ?> selected="selected"
                          <?php endif; ?>>PHP Mail()</option>
                      </select>
                    </div>
                  </div>
                </div>
                <hr class="pb-3">
                <h6 class="mb-4"><?= lang('Main.xin_email_notification_enable'); ?></h6>
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input" id="email_notification" name="email_notification"
                    <?php if ($xin_system['enable_email_notification'] == 1): ?> checked="checked" <?php endif; ?> value="1">
                  <label class="custom-control-label"
                    for="email_notification"><?= lang('Main.xin_enable_email_notification'); ?></label>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
        <!-- set annual planning  -->
        <div class="tab-pane fade" id="user-set-planning-configuration" role="tabpanel" aria-labelledby="user-set-planning-configuration-tab">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>
                <i data-feather="credit-card" class="icon-svg-primary wid-20"></i>
                <span class="p-l-5"><?php echo "Planning Configuration"; ?></span>
                <small class="text-muted d-block m-l-25 m-t-5"><?php echo "Change Your Planning Configuration" ?></small>
              </h5>
            </div>
            <?php $attributes = array('name' => 'planning_configuration', 'id' => 'planning_configuration', 'autocomplete' => 'off'); ?>
            <?php $hidden = array('user_id' => '0'); ?>
            <?= form_open('erp/planning-configuration', $attributes, $hidden); ?>
            <div class="card-body">
              <div class="row">
                <!-- Financial Year Selection -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="financial_year" class="font-weight-bold">Financial Year<span class="text-danger">*</span></label>
                    <?php

                    $currentYear = (int)date('Y');
                    $currentMonth = (int)date('n');
                    $unique_years = $unique_years ?? [];
                    $financialYearStart = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;
                    $currentFY = sprintf("%d-%02d", $financialYearStart, ($financialYearStart + 1) % 100);
                    ?>

                    <select class="form-control" id="financial_year" name="year" required>
                      <option value="">Select Financial Year</option>
                      <?php if (!empty($unique_years)): ?>
                        <?php foreach ($unique_years as $year): ?>
                          <?php

                          $year = (int)$year;
                          if ($year < 2000 || $year > 2100) continue;

                          $startYear = $year;
                          $endYear = $year + 1;
                          $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                          $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                          $selected = ($fyValue === $currentFY) ? 'selected' : '';
                          ?>
                          <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                            <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <?php  ?>
                        <?php for ($i = 2; $i >= -5; $i--): ?>
                          <?php
                          $startYear = $financialYearStart + $i;
                          $endYear = $startYear + 1;
                          $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                          $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                          $selected = ($fyValue === $currentFY) ? 'selected' : '';
                          ?>
                          <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                            <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                          </option>
                        <?php endfor; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                </div>
                <!-- Month Selection -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="month" class="font-weight-bold">Month<span class="text-danger">*</span></label>
                    <select class="form-control" id="month" name="month" required>
                      <option value="" disabled selected>Select Month</option>
                      <?php
                      $months = [
                        ['name' => 'April', 'year_offset' => 0],
                        ['name' => 'May', 'year_offset' => 0],
                        ['name' => 'June', 'year_offset' => 0],
                        ['name' => 'July', 'year_offset' => 0],
                        ['name' => 'August', 'year_offset' => 0],
                        ['name' => 'September', 'year_offset' => 0],
                        ['name' => 'October', 'year_offset' => 0],
                        ['name' => 'November', 'year_offset' => 0],
                        ['name' => 'December', 'year_offset' => 0],
                        ['name' => 'January', 'year_offset' => 1],
                        ['name' => 'February', 'year_offset' => 1],
                        ['name' => 'March', 'year_offset' => 1]
                      ];

                      foreach ($months as $month) {
                        $year = $currentYear + $month['year_offset'];
                        if ($currentMonth >= 4 && $month['year_offset'] == 1) {
                          $year = $currentYear + 1;
                        } elseif ($currentMonth < 4 && $month['year_offset'] == 0) {
                          $year = $currentYear;
                        }
                        echo "<option value='{$month['name']}-$year'>{$month['name']} $year</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="percentage">Percentage <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="percentage" name="percentage" placeholder="Enter Percentage" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
            <?= form_close(); ?>
            <div class="card user-profile-list">
              <div class="card-header">
                <h5>List All</h5>
              </div>
              <div class="card-body">
                <div class="box-datatable table-responsive">
                  <div id="xin_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <table class="datatables-demo table" id="xin_table">
                      <thead>
                        <tr>
                          <th>Year</th>
                          <th>Month</th>
                          <th>Percentage</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($planning_configuration_data as $data): ?>
                          <tr>
                            <td><?php echo $data['year']; ?></td>
                            <td><?php echo $data['month']; ?></td>
                            <td><?php echo $data['percentage']; ?></td>
                            <td>
                              <span data-toggle="tooltip" title="Edit Project">
                                <a>
                                  <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                    <i class="feather icon-edit"></i>
                                  </button>
                                </a>
                              </span>
                              <span data-toggle="tooltip" title="Delete Planning Configuration">
                                <button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                  data-toggle="modal" data-target="#deleteModal"
                                  data-record-id="<?= $data['id'] ?>"> <!-- Fixed this line -->
                                  <i class="feather icon-trash-2"></i>
                                </button>
                              </span>
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
        </div>

        <!-- set Document Catergory  -->
        <div class="tab-pane fade" id="document-category" role="tabpanel" aria-labelledby="document-category-tab">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>
                <i data-feather="file-text" class="icon-svg-primary wid-20"></i>
                <span class="p-l-5">
                  Document Category </span>
                <small class="text-muted d-block m-l-25 m-t-5"><?php echo "Change Your Document Category" ?></small>
              </h5>
            </div>
            <form action="<?= base_url('erp/settings/save_category') ?>" method="POST">
              <?= csrf_field(); ?> <!-- Add CSRF Token -->
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="category_name">Category Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="category_name" name="category_name" placeholder="Enter Category Name" required>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                  <?= lang('Main.xin_save'); ?>
                </button>
              </div>
            </form>



            <div class="card user-profile-list">
              <div class="card-header">
                <h5>List All</h5>
              </div>
              <div class="card-body">
                <div class="box-datatable table-responsive">
                  <div id="xin_table_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                    <table class="datatables-demo table" id="xin_table">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Category Name</th>
                          <th>Status</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($document_data)) : ?>
                          <?php $i = 1;
                          foreach ($document_data as $data) : ?>
                            <tr>
                              <td><?= $i++; ?></td>
                              <td><?php echo $data['category_name']; ?></td>
                              <td>
                                <?php if ($data['status'] == 1) : ?>
                                  <a href="<?= base_url('erp/settings/update_status/' . $data['id'] . '/0') ?>" class="btn btn-success">Active</a>
                                <?php else : ?>
                                  <a href="<?= base_url('erp/settings/update_status/' . $data['id'] . '/1') ?>" class="btn btn-danger">Inactive</a>
                                <?php endif; ?>
                              </td>
                              <td>
                                <span data-toggle="tooltip" title="Edit Category">
                                  <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light edit-category"
                                    data-id="<?= $data['id']; ?>"
                                    data-name="<?= $data['category_name']; ?>">
                                    <i class="feather icon-edit"></i>
                                  </button>
                                </span>
                                <span data-toggle="tooltip" title="Delete Category">
                                  <a href="<?= base_url('erp/settings/delete_category/' . $data['id']) ?>" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete-category"
                                    onclick="return confirm('Are you sure you want to delete this item?');">
                                    <i class="feather icon-trash-2"></i>
                                  </a>
                                </span>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td colspan="4" class="text-center">No data found</td>
                          </tr>
                        <?php endif; ?>
                      </tbody>

                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- set Tax Year Declaration  -->
        <div class="tab-pane fade" id="tax-yeardeclaration" role="tabpanel" aria-labelledby="tax-yeardeclaration-tab">
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5>
                <i data-feather="calendar" class="icon-svg-primary wid-20"></i>
                <span class="p-l-5">
                  Tax Duration Declaration
                </span>
                <small class="text-muted d-block m-l-25 m-t-5"><?php echo "Change Your  Financial Year Declaration" ?></small>
              </h5>

            </div>
            <form action="<?= base_url('erp/settings/save_taxduration') ?>" method="POST">
              <?= csrf_field(); ?>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="from_date">From Date <span class="text-danger">*</span></label>
                      <input type="hidden" name="duration_id" value="<?= isset($tax_duration['id']) ? $tax_duration['id'] : '' ?>">
                      <input type="text" class="form-control datepicker" id="from_date" name="from_date" placeholder="Enter From Date" required
                        value="<?= $tax_duration['from_date']; ?>">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="to_date">To Date <span class="text-danger">*</span></label>
                      <input type="text" class="form-control datepicker" id="to_date" name="to_date" placeholder="Enter To Date" required
                        value="<?= $tax_duration['to_date']; ?>">
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                  <?= lang('Main.xin_save'); ?>
                </button>
              </div>
            </form>


          </div>
        </div>

        <!-- Edit Modal -->

        <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content" style="margin-top: 35%;">

              <div class="modal-body" id="modalBody" style="padding: 0px;">

              </div>
            </div>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                Are you sure you want to delete this record?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
              </div>
            </div>
          </div>
        </div>

      <?php } ?>
      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
      <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
      <script>
        $(document).ready(function() {
          $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            minDate: 0
          });
        });
      </script>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          document.querySelectorAll(".edit-category").forEach(button => {
            button.addEventListener("click", function() {
              const categoryId = this.getAttribute("data-id");
              const categoryName = this.getAttribute("data-name");
              document.getElementById("category_name").value = categoryName;

              const form = document.querySelector("form[action*='save_category']");
              form.setAttribute("action", `<?= base_url('erp/settings/edit_category') ?>/${categoryId}`);
            });
          });
        });
      </script>
      <script>
        $(document).ready(function() {
          <?php if (session()->getFlashdata('error')): ?>
            toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
              timeOut: 5000
            });
          <?php endif; ?>

          <?php if (session()->getFlashdata('message')): ?>
            toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
              timeOut: 5000
            });
          <?php endif; ?>
        });
      </script>
      <script>
        $(document).ready(function() {
          // Initialize DataTable
          $('#xin_table').DataTable();

          <?php if (session()->getFlashdata('status')): ?>
            var status = '<?= session()->getFlashdata('status') ?>';
            var message = '<?= session()->getFlashdata('message') ?>';
            switch (status) {
              case 'success':
                toastr.success(message);
                break;
              case 'error':
                toastr.error(message);
                break;
              case 'warning':
                toastr.warning(message);
                break;
              case 'info':
                toastr.info(message);
                break;
              default:
                toastr.info(message);
            }
          <?php endif; ?>
        });
      </script>
      <script>
        $(document).ready(function() {
          // Capture the record ID on modal show
          $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('record-id');

            // Store the ID in the modal for later use
            $(this).data('record-id', recordId);
            $('#confirmDeleteBtn').data('record-id', recordId);
          });

          // Handle delete confirmation
          $('#confirmDeleteBtn').on('click', function() {
            var recordId = $(this).data('record-id');

            if (!recordId) {
              toastr.error('No record ID found!');
              return;
            }

            var URL = '<?= base_url('erp/delete-planning-configuration') ?>';
            var $btn = $(this).prop('disabled', true);

            $.ajax({
              url: URL,
              type: 'GET', // Changed to uppercase for consistency
              data: {
                id: recordId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
              },
              dataType: "json",
              success: function(response) {
                $btn.prop('disabled', false);
                if (response.result) {
                  toastr.success(response.result);
                  $('#deleteModal').modal('hide');
                  setTimeout(function() {
                    if (response.redirect_url) {
                      window.location.href = response.redirect_url;
                    } else {
                      location.reload();
                    }
                  }, 1000);
                } else if (response.error) {
                  toastr.error(response.error);
                }
                $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
              },
              error: function(xhr, status, error) {
                $btn.prop('disabled', false);
                toastr.error('An error occurred while deleting the record.');
                console.error("Error deleting record: ", error);
              }
            });
          });
        });
      </script>

      <script>
        var base_url = '<?= site_url(); ?>';

        function openModal(id) {
          fetch(base_url + 'erp/Settings/getdata/' + id)
            .then(response => response.text())
            .then(data => {
              document.getElementById('modalBody').innerHTML = data;
              let modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
              modal.show();
            })
            .catch(error => {
              console.error('Error loading data:', error);
              alert('Failed to load data. Please try again.');
            });
        }
      </script>
      <script>
        $(document).ready(function() {
          $('#duration').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable ordering
            lengthMenu: [10, 25, 50, 100], // Options for number of records to show
            language: {
              search: "_INPUT_",
              searchPlaceholder: "Search records",
            }
          });
        });
      </script>