<?php

use App\Models\EmailtemplatesModel;

$request = \Config\Services::request();
if ($request->getGet('data') === 'email_template' && $request->getGet('field_id')) {
	$template_id = udecode($field_id);
	$EmailtemplatesModel = new EmailtemplatesModel();
	$result = $EmailtemplatesModel->where('template_id', $template_id)->first();

?>

	<div class="modal-header">
		<h5 class="modal-title">
			<?= lang('Main.xin_edit_email_template'); ?>
			<span class="font-weight-light">
				<?= lang('Main.xin_information'); ?>
			</span> <br>
			<small class="text-muted">
				<?= lang('Main.xin_below_required_info'); ?>
			</small>
		</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
	</div>
	<?php $attributes = array('name' => 'update_template', 'id' => 'update_template', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
	<?php $hidden = array('_method' => 'EDIT', 'token' => $field_id); ?>
	<?php echo form_open('erp/settings/update_template', $attributes, $hidden); ?>
	<div class="modal-body">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="name">
						<?= lang('Main.xin_template_name'); ?>
						<span class="text-danger">*</span></label>
					<input class="form-control" name="name" type="text" value="<?php echo $result['name']; ?>">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="subject">
						<?= lang('Main.xin_subject'); ?>
						<span class="text-danger">*</span></label>
					<input class="form-control" name="subject" type="text" value="<?php echo $result['subject']; ?>">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="status">
						<?= lang('Main.dashboard_xin_status'); ?>
						<span class="text-danger">*</span></label>
					<select class="form-control" name="status" data-plugin="select_hrm" data-placeholder="<?= lang('Main.dashboard_xin_status'); ?>">
						<option value=""></option>
						<option value="1" <?php if ($result['status'] == 1): ?> selected="selected" <?php endif; ?>>
							<?= lang('Main.xin_employees_active'); ?>
						</option>
						<option value="0" <?php if ($result['status'] == 0): ?> selected="selected" <?php endif; ?>>
							<?= lang('Main.xin_employees_inactive'); ?>
						</option>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label for="message">
						<?= lang('Main.xin_message'); ?>
					</label>
					<textarea class="form-control meditor" placeholder="<?= lang('Main.xin_message'); ?>" name="message" rows="10" style="height:350px;">
		  <?php echo $result['message']; ?></textarea>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">

					<?php echo html_entity_decode($result['message']); ?>
				</div>
			</div>
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

			/* Edit data */
			$("#update_template").submit(function(e) {
				e.preventDefault();
				var obj = $(this),
					action = obj.attr('action');
				$.ajax({
					type: "POST",
					url: action,
					data: obj.serialize() + "&is_ajax=1&type=edit_record&form=update_template",
					cache: false,
					dataType: 'json',
					success: function(JSON) {
						if (JSON.error != '') {
							toastr.error(JSON.error);
							$('input[name="csrf_token"]').val(JSON.csrf_hash);
							Ladda.stopAll();
						} else {
							var xin_email_table = $('#xin_table').dataTable({
								"bDestroy": true,
								"ajax": {
									url: "<?php echo site_url("erp/email-template-list") ?>",
									type: 'GET'
								},
								"language": {
									"lengthMenu": dt_lengthMenu,
									"zeroRecords": dt_zeroRecords,
									"info": dt_info,
									"infoEmpty": dt_infoEmpty,
									"infoFiltered": dt_infoFiltered,
									"search": dt_search,
									"paginate": {
										"first": dt_first,
										"previous": dt_previous,
										"next": dt_next,
										"last": dt_last
									},
								},
								"iDisplayLength": 20,
								"aLengthMenu": [
									[20, 30, 50, 100, -1],
									[20, 30, 50, 100, "All"]
								],
								"fnDrawCallback": function(settings) {
									$('[data-toggle="tooltip"]').tooltip();
								}
							});
							xin_email_table.api().ajax.reload(function() {
								toastr.success(JSON.result);
							}, true);
							$('input[name="csrf_token"]').val(JSON.csrf_hash);
							$('.edit-modal-data').modal('toggle');
							Ladda.stopAll();
						}
					},
					error: function(xhr, status, error) {

						toastr.error('An error occurred: ' + error);
						Ladda.stopAll();
					}
				});
			});
		});
	</script>


<?php } ?>