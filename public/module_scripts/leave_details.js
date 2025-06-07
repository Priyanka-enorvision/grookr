$(document).ready(function () {
	/* Edit training data */
	$("#update_status").submit(function (e) {
		e.preventDefault();
		var obj = $(this);

		$.ajax({
			type: "POST",
			url: obj.attr('action'),
			data: obj.serialize(),
			cache: false,
			dataType: 'json',
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
				} else {
					toastr.success(JSON.result);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					window.location.href = main_url + 'leave-list';
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
	$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
	$('[data-plugin="select_hrm"]').select2({ width: '100%' });
});