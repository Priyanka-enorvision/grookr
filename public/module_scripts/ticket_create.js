$(document).ready(function () {
	/* Add data */ /*Form Submit*/
	$("#xin-form").submit(function (e) {
		var fd = new FormData(this);
		var obj = $(this), action = obj.attr('name');
		fd.append("is_ajax", 1);
		fd.append("type", 'add_record');
		fd.append("form", action);
		e.preventDefault();
		$.ajax({
			url: e.target.action,
			type: "POST",
			data: fd,
			contentType: false,
			cache: false,
			processData: false,
			success: function (JSON) {
				if (JSON.error != '') {
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
	jQuery("#department_id").change(function () {
		jQuery.get(main_url + "get-employeeBy-department/" + jQuery(this).val(), function (data, status) {
			jQuery('#staff_ajax').html(data);
		});
	});
});