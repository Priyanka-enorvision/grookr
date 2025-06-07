<?php

use App\Models\SystemModel;
use App\Models\UsersModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$UsersModel = new UsersModel();
$SystemModel = new SystemModel();

$xin_system = $SystemModel->where('setting_id', 1)->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


/* Transfers view
*/
$get_animate = '';
if ($request->getGet('type') === 'view_lead' && $request->getGet('field_id')) {
	$transfer_id = $field_id;
	//$result = $TransfersModel->where('transfer_id', $transfer_id)->first();

?>

	<div class="modal-header">
		<h5 class="modal-title">
			<?= lang('Main.xin_change_lead_to_client_text'); ?>
		</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
	</div>
	<?php $attributes = array('name' => 'convert_lead', 'id' => 'convert_lead', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
	<?php $hidden = array('_method' => 'EDIT', 'token' => $field_id); ?>
	<?php echo form_open('erp/convert-lead', $attributes, $hidden); ?>
	<input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
	<div class="modal-body">
		<div class="alert alert-danger" role="alert">
			<?= lang('Main.xin_change_lead_not_restored'); ?>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-light" data-dismiss="modal">
			<?= lang('Main.xin_close'); ?>
		</button>
		<button type="submit" class="btn btn-primary">
			<?= lang('Main.xin_confirm_convert'); ?>
		</button>
	</div>
	<?php echo form_close(); ?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
			$('[data-plugin="select_hrm"]').select2({
				width: '100%'
			});
			Ladda.bind('button[type=submit]');
			/* Edit data */
			$("#convert_lead").submit(function(e) {
				e.preventDefault();
				var obj = $(this),
					action = obj.attr('name');
				$.ajax({
					type: "POST",
					url: e.target.action,
					data: obj.serialize() + "&is_ajax=1&type=edit_record&form=" + action,
					cache: false,
					success: function(response) {


						$('input[name="csrf_token"]').val(response.csrf_hash);
						toastr.success(response.result);
						$('#convert_lead').closest('.modal').modal('hide');
						// setTimeout(function() {
						// 	window.location.href = '<?= site_url('erp/leads-list'); ?>';
						// }, 2000);
					},
					error: function(xhr, status, error) {
						toastr.error('An error occurred while processing your request.');
						console.error('AJAX Error: ', status, error);
						Ladda.stopAll();
					},
					complete: function() {
						submitButton.prop('disabled', false);
						submitButton.html('<?= lang('Main.xin_confirm_convert'); ?>');

						// setTimeout(function() {
						// 	console.log('Redirecting to:', '<?= site_url('erp/leads-list'); ?>');
						// 	window.location.href = '<?= site_url('erp/leads-list'); ?>';
						// }, 2000);
					}

				});
			});
		});
	</script>
<?php }
?>