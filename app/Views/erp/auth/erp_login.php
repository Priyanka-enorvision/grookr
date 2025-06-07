<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$xin_system = $SystemModel->where('setting_id', 1)->first();
$favicon = base_url().'uploads/logo/favicon/'.$xin_system['favicon'];
$session = \Config\Services::session();
$request = \Config\Services::request();
$username = $session->get('sup_username');
$user_id = $session->get('sup_user_id');
$user_info = $UsersModel->where('user_id',$user_id)->first();
?>
<?= doctype();?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>
<?= $title; ?>
</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="description" content="" />
<meta name="keywords" content="">
<meta name="author" content="erp" />

<!-- Favicon icon -->
<link rel="icon" type="image/x-icon" href="<?= $favicon;?>">

<!-- font css -->
<link rel="stylesheet" href="<?= base_url('assets/fonts/font-awsome-pro/css/pro.min.css');?>">
<link rel="stylesheet" href="<?= base_url('assets/fonts/feather.css');?>">
<link rel="stylesheet" href="<?= base_url('assets/fonts/fontawesome.css');?>">

<!-- vendor css -->
<link rel="stylesheet" href="<?= base_url('assets/css/style.css');?>">
<link rel="stylesheet" href="<?= base_url('assets/css/customizer.css');?>">
<link rel="stylesheet" href="<?= base_url('assets/plugins/toastr/toastr.css');?>">
<style>

html body
{
  height:100%;
  width:100%;
}

  .auth-wrapper.auth-v3 {
    background: #ffffff; 
  }
  .auth-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    min-width: 100%;
    min-height: 80vh;
    background: #ffffff;
  }
  
.auth-wrapper .auth-content:not(.container) {
    width: 90% !important;
}

.auth-wrapper.auth-v3 .auth-content .card .card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 95% !important;
 }


.insideForm{
  margin-bottom: 8rem;
  margin-left: 4rem ;
  width:80%;

}

.inputs{
  height:3.2rem
}



@media only screen and (max-width: 600px) {

  .insideForm {
    margin-bottom: 8rem;
    margin-left: 1.8rem ;
    width:80%;
  }

  
  .inputs{
    height:2.5rem
  }


  .float-right {
      margin-right: 45px !important;
  }

  .auth-wrapper.auth-v3 .img-card-side {
      display: block;
    }
  .img-fluid {
      max-width: 100%;
      height: 75%;
      margin-top: -7rem;
      margin-bottom: 2rem;
  }

  
  .auth-wrapper .auth-content:not(.container) {
    width: 90% !important;
}

}

</style>
</head>

<div class="auth-wrapper auth-v3">
  <div class="auth-content">
    <?php if($session->get('err_not_logged_in')){?>
    <div class="alert alert-danger alert-dismissible fade show">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <?= $session->get('err_not_logged_in');?>
    </div>
    <?php } ?>
    <?php if($request->getVar('v')){?>
    <div class="alert alert-success alert-dismissible fade show">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <?= lang('Frontend.xin_your_account_is_verified');?>
    </div>
    <?php } ?>
    <div class="card">
      <div class="row align-items-stretch text-center">
        <div class="col-md-6">
          <div class="card-body">
            <div class="text-left">
              <h4 class="mb-1 f-w-400">
                <span class="text-primary">
                <?= $xin_system['company_name'];?>
                </span></h4>
            </div>
            <div class="text-center">
              <p class="text-muted" style="font-size:1.5rem; margin-top:2rem">
                  <?= lang('Login.xin_welcome_back_please_login');?>
              </p>
            </div>

            <?php $attributes = ['class' => 'form-timehrm', 'name' => 'erp-form', 'id' => 'erp-form', 'autocomplete' => 'off'];?>
            <?php $hidden = ['user_id' => '0'];?>
            <?= form_open('erp/auth/login', $attributes, $hidden);?>
            <div class="insideForm">
              <div class="input-group mb-3  ">
                  <div class="input-group-prepend">
                      <span class="input-group-text">
                          <i data-feather="user"></i>
                      </span>
                  </div>
                  <input type="text" class="form-control inputs" id="iusername" name="iusername" placeholder="<?= lang('Login.xin_login_username'); ?>" required>
              </div>

              <div class="input-group mb-3  ">
                  <div class="input-group-prepend">
                      <span class="input-group-text">
                          <i data-feather="lock"></i>
                      </span>
                  </div>
                  <input type="password" class="form-control inputs" id="ipassword" name="password" placeholder="<?= lang('Login.xin_login_enter_password'); ?>" required>
              </div>

              <div class="form-group text-left my-3">
                  <div class="float-right">
                      <a href="<?= site_url('erp/forgot-password'); ?>" class="text-primary">
                          <span><?= lang('Login.xin_forgot_password_link'); ?></span>
                      </a>
                  </div>
                  <div class="d-inline-block">
                      <strong class="text-success">&nbsp;</strong>
                  </div>
              </div>
              <div class="text-center mt-3">
                  <button type="submit" class="btn btn-primary mt-2">
                      <i class="fas fa-user-lock"></i>
                      <?= lang('Login.xin_login'); ?>
                  </button>
              </div>
            </div>
            <?= form_close(); ?>
          </div>
        </div>
        <!-- <div class="col-md-6 img-card-side"> <img src="<?= base_url('assets/images/auth/'.$xin_system['auth_background']);?>" alt="" class="img-fluid"> </div> -->
        <div class="col-md-6 img-card-side"> <img src="<?= base_url('assets/images/auth/'.'3714960'.'.jpg');?>" alt="" class="img-fluid"> </div>
      </div>
    </div>
  </div>
</div>
<!-- [ auth-sign ] end --> 
<!-- Required Js --> 
<script src="<?= base_url('assets/js/vendor-all.min.js');?>"></script> 
<script src="<?= base_url('assets/js/plugins/bootstrap.min.js');?>"></script> 
<script src="<?= base_url('assets/js/plugins/feather.min.js');?>"></script> 
<script src="<?= base_url('assets/js/pcoded.min.js');?>"></script> 
<script src="<?= base_url();?>assets/plugins/toastr/toastr.js"></script> 
<script src="<?= base_url();?>assets/plugins/sweetalert2/sweetalert2@10.js"></script>
<link rel="stylesheet" href="<?= base_url();?>assets/plugins/ladda/ladda.css">
<script src="<?= base_url();?>assets/plugins/spin/spin.js"></script> 
<script src="<?= base_url();?>assets/plugins/ladda/ladda.js"></script> 
<script type="text/javascript">
$(document).ready(function(){
	Ladda.bind('button[type=submit]');
	toastr.options.closeButton = <?= $xin_system['notification_close_btn'];?>;
	toastr.options.progressBar = <?= $xin_system['notification_bar'];?>;
	toastr.options.timeOut = 3000;
	toastr.options.preventDuplicates = true;
	toastr.options.positionClass = "<?= $xin_system['notification_position'];?>";
});
</script> 
<script type="text/javascript">

  

var desk_url = '<?php echo site_url('erp/desk'); ?>';

var processing_request = '<?= lang('Login.xin_processing_request');?>';</script></script> 
<script type="text/javascript" src="<?= base_url();?>module_scripts/ci_erp_login.js"></script>
<script type="text/javascript">
$(document).ready(function(){
$(".login-as").click(function(){
		var uname = jQuery(this).data('demo_user');
		var password = jQuery(this).data('demo_password');
		jQuery('#iusername').val(uname);
		jQuery('#ipassword').val(password);
	});
});	
</script>
</body></html>
