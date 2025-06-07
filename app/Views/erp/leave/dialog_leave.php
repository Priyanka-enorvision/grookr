<?php

use App\Models\LeaveModel;
use App\Models\UsersModel;

$LeaveModel = new LeaveModel();
$UsersModel = new UsersModel();
$request = \Config\Services::request();
$session = \Config\Services::session();

$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


if ($request->getGet('data') === 'leave' && $field_id) {
	$ifield_id = udecode($field_id);
	$result = $LeaveModel->where('leave_id', $ifield_id)->first();
?>

	<div class="modal-header">
		<h5 class="modal-title">
			<?= lang('Leave.xin_edit_leave'); ?>
			<span class="font-weight-light">
				<?= lang('Main.xin_information'); ?>
			</span> <br>
			<small class="text-muted">
				<?= lang('Main.xin_below_required_info'); ?>
			</small>
		</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
	</div>
	<?php $attributes = array('name' => 'edit_leave', 'id' => 'edit_leave', 'autocomplete' => 'off', 'class' => 'm-b-1', 'enctype' => 'multipart/form-data', 'files' => true); ?>
	<?php $hidden = array('_method' => 'EDIT', 'token' => $field_id); ?>
	<?php echo form_open('erp/update-leave', $attributes, $hidden); ?>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="description">
						<?= lang('Recruitment.xin_remarks'); ?> <span class="text-danger">*</span>
					</label>
					<textarea class="form-control textarea" placeholder="<?= lang('Recruitment.xin_remarks'); ?>" name="remarks" cols="30" rows="2"><?php echo $result['remarks']; ?></textarea>
				</div>
			</div>
			<div class="col-md-12">
				<div class="form-group">
					<label for="reason">
						<?= lang('Leave.xin_leave_reason'); ?> <span class="text-danger">*</span>
					</label>
					<textarea class="form-control" placeholder="<?= lang('Leave.xin_leave_reason'); ?>" name="reason" cols="30" rows="5" id="reason"><?php echo $result['reason']; ?></textarea>
				</div>
			</div>
			<?php if ($user_info['user_type'] == 'staff') { ?>
				<div class="col-md-12">
					<div class="form-group">
						<label for="attachment">
							<?= lang('Main.xin_attachment'); ?>
						</label>
						<input type="file" class="form-control" id="attachments" name="attachment">
						<small>
							<?= lang('Leave.xin_leave_file_type'); ?>
						</small>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-light" data-dismiss="modal">
			<?= lang('Main.xin_close'); ?>
		</button>
		<button type="submit" class="btn btn-primary">
			<?= lang('Main.xin_update'); ?>
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

			$("#edit_leave").submit(function(e) {
				e.preventDefault();

				var formData = new FormData(this);

				$('.save').prop('disabled', true);
				$.ajax({
					type: "POST",
					url: e.target.action,
					data: formData,
					cache: false,
					processData: false,
					contentType: false,
					success: function(JSON) {
						if (JSON.error !== '') {
							toastr.error(JSON.error);
						} else {
							toastr.success(JSON.result);
							window.location.href = main_url + 'leave-list';
							$('input[name="csrf_token"]').val(JSON.csrf_hash);
						}
					},
					error: function(xhr, status, error) {
						toastr.error("Error: " + xhr.responseText);
						l.stop();
					}
				});
			});
		});
	</script>

<?php } ?>