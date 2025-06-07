$(document).ready(function () {

	var xin_table = $('#xin_table').DataTable({
		"bDestroy": true,
		"ajax": {
			url: main_url + "departments-data-list",
			type: 'GET',
			dataType: 'json',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
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
			}
		},
		"fnDrawCallback": function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		}
	});


	/* Delete data */
	$("#delete_record").submit(function (e) {
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');

		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&type=delete_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error !== '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$('.delete-modal').modal('toggle');
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();

					if (typeof xin_table !== "undefined") {
						xin_table.ajax.reload(function () {
							toastr.success(JSON.result);
						}, false); // reload without resetting pagination
					} else {
						toastr.success(JSON.result);
					}
				}
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
		});
	});


	// edit
	$('.view-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data('field_id');
		var modal = $(this);

		$.ajax({
			url: main_url + "read_department",
			type: "GET",
			data: {
				jd: 1,
				data: 'department',
				field_id: field_id
			},
			success: function (response) {
				if (response) {
					$("#ajax_view_modal").html(response);
				}
			}
		});
	});

	/* Add data */ /*Form Submit*/
	$("#xin-form").submit(function (e) {
		e.preventDefault();

		var fd = new FormData(this);
		var obj = $(this),
			action = obj.attr('name');

		fd.append("is_ajax", 1);
		fd.append("type", 'add_record');
		fd.append("form", action);

		$.ajax({
			url: obj.attr('action'),
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

					if (typeof xin_table !== 'undefined') {
						xin_table.ajax.reload(null, false);
					}
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					$('#xin-form')[0].reset();

					setTimeout(function () {
						window.location.href = main_url + 'departments-list';
					}, 1000);
				}
			},
			error: function (xhr, status, error) {
				toastr.error('Something went wrong. Please try again.');
				if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
					$('input[name="csrf_token"]').val(xhr.responseJSON.csrf_hash);
				}
				setTimeout(function () {
					window.location.href = main_url + 'departments-list';
				}, 1000);
			}
		});
	});

});
$(document).on("click", ".delete", function () {
	$('input[name=_token]').val($(this).data('record-id'));
	$('#delete_record').attr('action', main_url + 'delete_department');
});
