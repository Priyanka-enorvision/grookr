$(document).ready(function () {
	// On page load > documents
	var xin_table_document = $('#xin_table_document').DataTable({
		"bDestroy": true,
		"ajax": {
			url: main_url + "system-documents-list/did/" + $('#depval').val(),
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
	/* Add data */ /*Form Submit*/
	$("#system_document").submit(function (e) {
		e.preventDefault();
		var fd = new FormData(this);
		var obj = $(this), action = obj.attr('name');
		fd.append("is_ajax", 1);
		fd.append("type", 'add_record');
		fd.append("form", action);
		var dep_id = $('#depval').val();

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
					window.location.href = main_url + 'upload-files';
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
	// get department files
	$(".department-file").click(function () {
		var dep_id = $(this).data('department-id');
		var xin_table_documents = $('#xin_table_document').dataTable({
			"bDestroy": true,
			"ajax": {
				url: main_url + "system-documents-list/did/" + dep_id,
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
	});
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
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$('.delete-modal').modal('toggle');
					toastr.success(JSON.result);
					window.location.href = main_url + 'upload-files';
					$('input[name="csrf_token"]').val(JSON.csrf_hash);

					Ladda.stopAll();


				}
			},
			error: function (xhr, error, thrown) {
				console.log("AJAX Error: ", xhr.responseText);
			}
		});
	});

	// view
	$('.view-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data('field_id');
		var modal = $(this);
		$.ajax({
			url: main_url + "read-document",
			type: "GET",
			data: 'jd=1&data=document&field_id=' + field_id,
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
	$('#delete_record').attr('action', main_url + 'delete-document');
});