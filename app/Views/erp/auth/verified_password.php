<?php

use App\Models\SystemModel;
use App\Models\UsersModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$xin_system = $SystemModel->where('setting_id', 1)->first();

?>
<!DOCTYPE html>
<html lang="en">

<head>

	<title><?= $title; ?></title>
	
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="" />
	<meta name="keywords" content="">
	<meta name="author" content="TIMEHRM" />

	<!-- Favicon icon -->
	<link rel="icon" href="<?= base_url(); ?>assets/images/favicon.svg" type="image/x-icon">

	<!-- font css -->
	<link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/font-awsome-pro/css/pro.min.css">
	<link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/feather.css">
	<link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/fontawesome.css">

	<!-- vendor css -->
	<link rel="stylesheet" href="<?= base_url(); ?>assets/css/style.css">
	<link rel="stylesheet" href="<?= base_url(); ?>assets/css/customizer.css">
	<link rel="stylesheet" href="<?= base_url('assets/plugins/toastr/toastr.css'); ?>">

</head>

<body>
	<!-- Content -->
	<div class="auth-wrapper">
		<!-- [ verified-password ] start -->
		<div class="auth-content">
			<div class="card">
				<div class="row align-items-center text-center">
					<div class="col-md-12">
						<div class="card-body">
							<img src="<?= base_url(); ?>assets/images/logo-dark.svg" alt="" class="img-fluid mb-4">
							<div class="input-group mb-4">
								<div class="alert alert-success" role="alert">
									We have sent you a new password to your mail.
								</div>
							</div>
							<p class="mb-0 text-muted">Get your password and <a href="<?= site_url('/'); ?>" class="f-w-400">Login here</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- [ verified-password ] end -->
	</div>

	<!-- Required Js -->
	<script src="<?= base_url('assets/js/vendor-all.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/plugins/bootstrap.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/plugins/feather.min.js'); ?>"></script>
	<script src="<?= base_url('assets/js/pcoded.min.js'); ?>"></script>
	<script src="<?= base_url(); ?>assets/plugins/toastr/toastr.js"></script>
	<script src="<?= base_url(); ?>assets/plugins/sweetalert2/sweetalert2@10.js"></script>
	<link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/ladda/ladda.css">
	<script src="<?= base_url(); ?>assets/plugins/spin/spin.js"></script>
	<script src="<?= base_url(); ?>assets/plugins/ladda/ladda.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			Ladda.bind('button[type=submit]');
			toastr.options.closeButton = <?= $xin_system['notification_close_btn']; ?>;
			toastr.options.progressBar = <?= $xin_system['notification_bar']; ?>;
			toastr.options.timeOut = 3000;
			toastr.options.preventDuplicates = true;
			toastr.options.positionClass = "<?= $xin_system['notification_position']; ?>";
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function() {

			/* Add data */
			/*Form Submit*/
			$("#hrm-form").submit(function(e) {
				e.preventDefault();
				var obj = $(this),
					action = obj.attr('name');
				$.ajax({
					type: "POST",
					url: e.target.action,
					data: obj.serialize() + "&is_ajax=1&add_type=forgot_password&form=" + action,
					cache: false,
					success: function(JSON) {
						if (JSON.error != '') {
							toastr.error(JSON.error);
							$('input[name="csrf_token"]').val(JSON.csrf_hash);
							Ladda.stopAll();
						} else {
							toastr.success(JSON.result);
							$('input[name="csrf_token"]').val(JSON.csrf_hash);
							Ladda.stopAll();
						}
					}
				});
			});
		});
	</script>
</body>

</html>