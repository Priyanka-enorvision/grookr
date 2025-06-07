<?php

use App\Models\SystemModel;

$SystemModel = new SystemModel();

$xin_system = $SystemModel->where('setting_id', 1)->first();

$session = \Config\Services::session();
$router = service('router');

$username = $session->get('sup_username');
$user_id = $username['sup_user_id'];

?>

<style>
  @media (max-width: 1024px) {
    .pc-header .pcm-logo img {
      max-width: 100px !important;
    }
  }
</style>

<?= view('default/htmlheader'); ?>

<body class="modern-layout ">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ Mobile header ] start -->
  <div class="pc-mob-header pc-header">
    <div class="pcm-logo"> <img src="<?= base_url(); ?>uploads/logo/<?= $xin_system['logo']; ?>" alt="" class="logo logo-lg"> </div>
    <div class="pcm-toolbar"> <a href="#!" class="pc-head-link" id="mobile-collapse">
        <div class="hamburger hamburger--arrowturn">
          <div class="hamburger-box">
            <div class="hamburger-inner"></div>
          </div>
        </div>
      </a> <a href="#!" class="pc-head-link" id="headerdrp-collapse"> <i data-feather="align-right"></i> </a> <a
        href="#!" class="pc-head-link" id="header-collapse"> <i data-feather="more-vertical"></i> </a> </div>
  </div>
  <!-- [ Mobile header ] End -->
  <!-- [ navigation menu ] start -->
  <nav class="pc-sidebar light-sidebar">
    <div class="navbar-wrapper">
      <div class="navbar-content">
        <?= view('default/left_menu') ?>
      </div>
    </div>
  </nav>
  <!-- [ navigation menu ] end -->

  <!-- [ Header ] start -->
  <?= view('default/header') ?>
  <!-- [ Header ] end -->

  <!-- [ Main Content ] start -->
  <div class="pc-container">
    <div class="pcoded-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-6">
              <ul class="breadcrumb">
                <?php if ($router->controllerName() == '\App\Controllers\Erp\Dashboard') { ?>
                <?php } else { ?>
                  <li class="breadcrumb-item"><a href="<?= site_url('erp/desk') ?>">
                      <?= lang('Dashboard.dashboard_title'); ?>
                    </a></li>
                  <li class="breadcrumb-item">
                    <?= $breadcrumbs; ?>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- [ breadcrumb ] end -->
      <!-- [ Main Content ] start -->
      <?= $subview; ?>
      <!-- [ Main Content ] end -->
    </div>
  </div>
  <!-- [ Main Content ] end -->
  </div>
  <?= view('default/footer') ?>
  <?= view('default/htmlfooter') ?>
</body>

</html>