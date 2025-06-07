$(document).ready(function () {
	$("#erp-form").submit(function (e) {
		e.preventDefault();
		var obj = $(this),
			action = obj.attr('name'),
			redirect_url = obj.data('redirect'),
			form_table = obj.data('form-table'),
			is_redirect = obj.data('is-redirect');

		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=1&form=" + form_table,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					location.reload();
					Ladda.stopAll();
				} else {
					toastr.clear();
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					toastr.success(JSON.result);
					Ladda.stopAll();
					setTimeout(function () {
						window.location.href = desk_url;
					}, 500);
				}
			},
			error: function (xhr, status, error) {
				toastr.error('An error occurred during login');
				location.reload();
				Ladda.stopAll();
			}
		});
	});
});