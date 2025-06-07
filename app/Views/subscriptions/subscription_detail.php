<?php

use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\SubscriptionModel;

$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$SubscriptionModel = new SubscriptionModel();

$session = \Config\Services::session();
$request = \Config\Services::request();
$usession = $session->get('sup_username');
$router = service('router');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


$segment_id = $request->uri->getSegment(3);

$company_id = udecode($segment_id);


$subscription_info = $SubscriptionModel->where('id', $company_id)->first();
$get_companies = $UsersModel->where('user_type', 'company')->findAll();


$locale = service('request')->getLocale();
?>

<?php if ($session->get('unauthorized_module')) { ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <?= $session->get('unauthorized_module'); ?>
  </div>
<?php } ?>

<?php if (in_array('subscription3', staff_role_resource()) || $user_info['user_type'] == 'super_user') { ?>
  <div id="accordion">
    <div id="edit_form" class="collapse show" data-parent="#accordion">
      <?php $attributes = array('name' => 'edit_subscription', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
      <?php $hidden = array('id' => $subscription_info['id']); ?>
      <?= form_open_multipart('erp/SubscriptionController/update_subscription', $attributes, $hidden); ?>
      <div class="row">
        <div class="col-md-12">
          <div class="card mb-2">
            <div class="card-header">
              <h5>
                <?= lang('Main.xin_update'); ?>   <?= "Subscription"; ?>
              </h5>
              <div class="card-header-right">
                <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                  class="collapsed btn btn-sm waves-effect waves-light btn-primary m-0">
                  <i data-feather="minus"></i> <?= lang('Main.xin_hide'); ?>
                </a>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Company Selection -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="company" class="control-label"><?= "Company Name"; ?></label>
                    <select class="form-control" name="company_id" data-plugin="select_hrm"
                      data-placeholder="<?= "Select Company"; ?>" id="company_id" required>
                      <option value=""><?= "Select Company"; ?></option>
                      <?php foreach ($get_companies as $get_company): ?>
                        <option value="<?= $get_company['company_id']; ?>"
                          <?= ($get_company['company_id'] == $subscription_info['company_id']) ? 'selected' : ''; ?>>
                          <?= $get_company['company_name']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <!-- Company Email -->
                <div class="col-md-6" id="company_email">
                  <div class="form-group">
                    <label for="email" class="control-label"><?= lang('Main.xin_email'); ?></label>
                    <input type="email" class="form-control" name="email"
                      value="<?= htmlspecialchars($subscription_info['email'], ENT_QUOTES, 'UTF-8'); ?>"
                      placeholder="<?= lang('Main.xin_email'); ?>" disabled>
                  </div>
                </div>

                <!-- Subscription Plan -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="plan" class="control-label"><?= "Subscription Plan"; ?></label>
                    <select class="form-control" name="plan" data-plugin="select_hrm"
                      data-placeholder="<?= "Select Subscription Plan"; ?>" required>
                      <option value="basic" <?= ($subscription_info['plan'] == 'basic') ? 'selected' : ''; ?>>Basic</option>
                      <option value="standard" <?= ($subscription_info['plan'] == 'standard') ? 'selected' : ''; ?>>Standard
                      </option>
                      <option value="premium" <?= ($subscription_info['plan'] == 'premium') ? 'selected' : ''; ?>>Premium
                      </option>
                    </select>
                  </div>
                </div>

                <!-- Start Date -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="start_date" class="control-label"><?= "Start Date"; ?></label>
                    <input type="date" class="form-control" name="start_date"
                      value="<?= isset($subscription_info['start_date']) ? date('Y-m-d', strtotime($subscription_info['start_date'])) : ''; ?>"
                      required>
                  </div>
                </div>

                <!-- End Date -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="end_date" class="control-label"><?= "End Date"; ?></label>
                    <input type="date" class="form-control" name="end_date"
                      value="<?= isset($subscription_info['end_date']) ? date('Y-m-d', strtotime($subscription_info['end_date'])) : ''; ?>"
                      required>
                  </div>
                </div>

                <!-- Payment Status -->
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="payment_status" class="control-label"><?= "Payment Status"; ?></label>
                    <select class="form-control" name="payment_status" data-plugin="select_hrm"
                      data-placeholder="<?= "Select Payment Status"; ?>" required>
                      <option value="paid" <?= ($subscription_info['payment_status'] == 'paid') ? 'selected' : ''; ?>>
                        <?= lang('Main.xin_paid'); ?></option>
                      <option value="pending" <?= ($subscription_info['payment_status'] == 'pending') ? 'selected' : ''; ?>>
                        <?= lang('Main.xin_pending'); ?></option>
                    </select>
                  </div>
                </div>

                <!-- Notes -->
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="notes" class="control-label"><?= "Notes"; ?></label>
                    <textarea class="form-control" name="notes" rows="3"
                      placeholder="<?= "Enter any notes"; ?>"><?= htmlspecialchars($subscription_info['notes'], ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                <?= lang('Main.xin_update'); ?>
              </button>
            </div>
          </div>
        </div>
      </div>
      <?= form_close(); ?>


    </div>
  </div>
<?php } ?>