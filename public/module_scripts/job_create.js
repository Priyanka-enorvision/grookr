$(document).ready(function () {
	$("#xin-form").submit(function (e) {
		e.preventDefault();
		var form = $(this);
		var formData = new FormData(this);

		$.ajax({
			url: form.attr('action'),
			type: "POST",
			data: formData,
			contentType: false,
			cache: false,
			processData: false,
			dataType: 'json',
			success: function (response) {
				// Update CSRF token
				if (response.csrf_hash) {
					$('input[name="csrf_token"]').val(response.csrf_hash);
				}

				if (response.error) {
					// Handle validation errors
					if (typeof response.error === 'object') {
						// Multiple errors
						$.each(response.error, function (key, value) {
							toastr.error(value);
						});
					} else {
						// Single error
						toastr.error(response.error);
					}
				} else if (response.result) {
					toastr.success(response.result);

					// Redirect after 1.5 seconds to allow toastr to show
					setTimeout(function () {
						window.location.href = main_url + 'jobs-list';
					}, 1500);
				}
			},
			error: function (xhr) {
				toastr.error('Something went wrong. Please try again.');
				if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
					$('input[name="csrf_token"]').val(xhr.responseJSON.csrf_hash);
				}
			}
		});
	});
});