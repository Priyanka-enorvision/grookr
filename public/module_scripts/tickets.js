$(document).ready(function () {
	/* Delete data */
	$("#delete_record").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&type=delete_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					alert('frist');
					toastr.error(JSON.error);
				} else {
					toastr.success(JSON.result);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					window.location.href = main_url + 'support-tickets';
				}
			},
			error: function (xhr, status, error) {
				toastr.error('Something went wrong. Please try again.');
				if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
					$('input[name="csrf_token"]').val(xhr.responseJSON.csrf_hash);
				}
			}
		});
	});

	$('.view-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data('field_id');
		var modal = $(this);
		$.ajax({
			url: main_url + "read-ticket",
			type: "GET",
			data: 'jd=1&type=ticket&field_id=' + field_id,
			success: function (response) {
				if (response) {
					$("#ajax_view_modal").html(response);
				}
			}
		});
	});
});
$(document).on("click", ".delete", function () {
	$('input[name=_token]').val($(this).data('record-id'));
	$('#delete_record').attr('action', main_url + 'delete-ticket');
});