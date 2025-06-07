<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\Form_model;

// Initialize models
$systemModel = new SystemModel();
$usersModel = new UsersModel();
$languageModel = new LanguageModel();
$formModel = new Form_model();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');

$xin_system = $systemModel->where('setting_id', 1)->first();
$user_info = $usersModel->where('user_id', $usession['sup_user_id'])->first();

// Fetch leads
$get_web_leads = $formModel->orderBy('id', 'desc')->findAll();

$locale = service('request')->getLocale();
?>
<?php if ($session->get('unauthorized_module')) { ?>
  <div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <?= $session->get('unauthorized_module'); ?>
  </div>
<?php } ?>


<div class="card user-profile-list">
  <div class="card-header">
    <h5>
      <?php echo "Web Leads"; ?>
    </h5>
    <div class="card-header-right">
    </div>
  </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th><?= lang('Main.xin_name'); ?></th>
            <th><i class="fa fa-user"></i>
              <?= lang('Main.dashboard_username'); ?></th>
            <th><?= lang('Main.xin_contact_number'); ?></th>
            <th><?= lang('Main.xin_country'); ?></th>
            <th><?= lang('Main.dashboard_xin_status'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($get_web_leads as $company): ?>
            <tr>
              <td>
                <?php if (isset($company['company_id']) && isset($company['company_name'])): ?>
                  <a href="<?= site_url('erp/monthly-planning/' . urlencode($company['company_id'])); ?>"
                    title="View Monthly Planning for <?= htmlspecialchars($company['company_name']); ?>">
                    <?= htmlspecialchars($company['company_name']); ?>
                  </a>
                <?php else: ?>
                  <span>No company data available</span>
                <?php endif; ?>
              </td>

              <!-- <td><?= htmlspecialchars($company['company_name']); ?></td> -->
              <td><?= htmlspecialchars($company['username']); ?></td>
              <td><?= htmlspecialchars($company['contact_number']); ?></td>
              <td><?= htmlspecialchars($company['country']); ?></td>
              <td>
                <?php
                if ($company['is_active'] == 1) {
                ?>
                  <a href="<?= base_url('company/update-status/' . base64_encode($company['company_id']) . '/0') ?>" class="badge badge-light-success">Active</a><?php

                                                                                                                                                                } else {
                                                                                                                                                                  ?>
                  <a href="<?= base_url('company/update-status/' . base64_encode($company['company_id']) . '/1') ?>" class="badge badge-light-danger">Not Active</a><?php
                                                                                                                                                                  }
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
  $(document).ready(function() {
    $('.datatables-demo').DataTable({
      "paging": true,
      "searching": true,
      "ordering": true
    });
  });
</script>



<?php if ($session->getFlashdata('success')): ?>
  <script>
    $(document).ready(function() {
      toastr.success("<?= esc($session->getFlashdata('success')) ?>");
    });
  </script>
<?php endif; ?>

<?php if ($session->getFlashdata('error')): ?>
  <script>
    $(document).ready(function() {
      <?php
      $errors = $session->getFlashdata('error');
      if (is_array($errors)) {
        foreach ($errors as $err) {
          echo 'toastr.error("' . esc($err) . '");';
        }
      } else {
        echo 'toastr.error("' . esc($errors) . '");';
      }
      ?>
    });
  </script>
<?php endif; ?>