$(document).ready(function () {
	var xin_table = $('#xin_table').DataTable({
		"bDestroy": true,
		"ajax": {
			url: main_url + "languages-datalist",
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
		"fnDrawCallback": function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		}
	});

	/* Delete data */
	$("#delete_record").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "DELETE",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&type=delete_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					toastr.success(JSON.result);
					window.location.href = main_url + 'all-languages';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);

					Ladda.stopAll();
				}
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
		});
	});
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
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					toastr.success(JSON.result);
					window.location.href = main_url + 'all-languages';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);

					Ladda.stopAll();
				}
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
		});
	});
});
$(document).on("click", ".delete", function () {
	$('input[name=_token]').val($(this).data('record-id'));
	$('#delete_record').attr('action', main_url + 'delete-language/' + $(this).data('record-id'));
});
$(document).on("click", ".active-lang", function () {

	var field_id = $(this).data('field_id');
	var is_active = $(this).data('is_active');
	$.ajax({
		type: "GET",
		url: main_url + "language-status/" + field_id + "/" + is_active,
		success: function (JSON) {
			var xin_table2 = $('#xin_table').dataTable({
				"bDestroy": true,
				"ajax": {
					url: main_url + "languages-datalist",
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
				"fnDrawCallback": function (settings) {
					$('[data-toggle="tooltip"]').tooltip();
				}
			});
			xin_table2.api().ajax.reload(function () {
				toastr.success(JSON.result);
			}, true);
		}
	});
});