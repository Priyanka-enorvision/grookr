$(document).ready(function () {
	/* Edit travel data */
	$("#update_travel").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=3&type=edit_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error !== '') {
					toastr.error(JSON.error);
				} else {
					toastr.success(JSON.result);
					window.location.href = main_url + 'business-travel';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
				}
			},
			error: function (xhr, status, error) {
				toastr.error("Error: " + xhr.responseText);
				l.stop();
			}
		});
	});
	$("#update_travel_status").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=3&type=edit_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error !== '') {
					toastr.error(JSON.error);
				} else {
					toastr.success(JSON.result);
					window.location.href = main_url + 'business-travel';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
				}
			},
			error: function (xhr, status, error) {
				toastr.error("Error: " + xhr.responseText);
				l.stop();
			}
		});
	});
	$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
	$('[data-plugin="select_hrm"]').select2({ width: '100%' });
});