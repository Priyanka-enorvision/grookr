$(document).ready(function () {
	var xin_table = $('#xin_table').DataTable({
		"bDestroy": true,
		"ajax": {
			url: main_url + "attendance-datalist",
			type: 'GET',
			dataType: 'JSON',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
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

	$('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
	$('[data-plugin="select_hrm"]').select2({ width: '100%' });


	/* Add data */ /*Form Submit*/
	$("#xin-form").submit(function (e) {
		e.preventDefault();
		var obj = $(this);
		var user = $('#employee_id').val();
		var attendance_date = $('#attendance_date').val();

		// For GET request, we'll use URL parameters instead of FormData
		var params = {
			is_ajax: 1,
			type: 'report_record',
			form: obj.attr('name'),
			user: user,
			date: attendance_date
		};

		Ladda.create(document.querySelector('.btn-primary')).start();

		$.ajax({
			url: e.target.action,
			type: "GET",
			data: params,
			dataType: "json",
			success: function (response) {
				if (response.error) {
					toastr.error(response.error);
				} else {
					// Initialize or refresh DataTable
					if ($.fn.DataTable.isDataTable('#xin_table')) {
						$('#xin_table').DataTable().destroy();
					}

					$('#xin_table').DataTable({
						data: response.data,
						columns: [
							{ title: "Employee" },
							{ title: "Date" },
							{ title: "Clock In" },
							{ title: "Clock Out" },
							{ title: "Total Work" }
						],
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

					toastr.success("Attendance data loaded successfully");
				}
				$('input[name="csrf_token"]').val(response.csrf_hash);
				Ladda.stopAll();
			},
			error: function (xhr) {
				try {
					var errorResponse = JSON.parse(xhr.responseText);
					toastr.error(errorResponse.error || 'An error occurred');
					$('input[name="csrf_token"]').val(errorResponse.csrf_hash);
				} catch (e) {
					toastr.error('An error occurred while processing your request');
				}
				Ladda.stopAll();
			}
		});
	});
	/* Delete data */
	$("#delete_record").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=true&type=delete_record&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$('.delete-modal').modal('toggle');
					toastr.success(JSON.result);
					window.location.href = main_url + 'manual-attendance';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);

					Ladda.stopAll();
				}
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
		});
	});

	// add attendance
	$('.view-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var modal = $(this);
		$.ajax({
			url: main_url + 'update-attendance-add',
			type: "GET",
			data: 'jd=1&is_ajax=9&mode=modal&data=add_attendance&type=add_attendance&field_id=1',
			success: function (response) {
				if (response) {
					$("#ajax_view_modal").html(response);
				}
			}
		});
	});

	// edit
	$('.edit-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data('field_id');
		var modal = $(this);
		$.ajax({
			url: main_url + "update-attendance-add",
			type: "GET",
			data: 'jd=1&is_ajax=1&mode=modal&data=edit_attendance&type=edit_attendance&field_id=' + field_id,
			success: function (response) {
				if (response) {
					$("#ajax_modal").html(response);
				}
			}
		});
	});
});
$(document).on("click", ".delete", function () {
	$('input[name=_token]').val($(this).data('record-id'));
	$('#delete_record').attr('action', main_url + 'delete-attendance');
});
