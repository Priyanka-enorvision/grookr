$(document).ready(function() {
    $("#delete_record").submit(function (e) {
		e.preventDefault(); 

		$.ajax({
			type: "POST",
			url: e.target.action,
			data: $(this).serialize() + "&type=delete_record",
			dataType: "json",
			success: function (response) {
				if (response.result) {
					toastr.success(response.result);
					$('.delete-modal').modal('toggle');

					setTimeout(function () {
						window.location.href = response.redirect_url;
					}, 1000);

				} else if (response.error) {
					toastr.error(response.error);
				}
				$('input[name="csrf_token"]').val(response.csrf_hash);
			},
			error: function (xhr, status, error) {
				console.error("Error deleting project: ", error);
				toastr.error('An error occurred while deleting the project.');
				setTimeout(function () {
					window.location.href = response.redirect_url;
				}, 2000);
			}
		});
	});
 
    
});


$(document).on("click", ".delete", function () {
    $('input[name=_token]').val($(this).data('record-id'));
    $('#delete_record').attr('action', main_url + 'dashboard/delete_project');
});