$(document).ready(function () {
    // Initialize table outside the submit handler
    var xin_table = $('#xin_table').DataTable({
        "ajax": {
            url: main_url + "payroll/payslip_list/0/" + $('#month_year').val(),
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
            }
        },
        "fnDrawCallback": function (settings) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    });

    $("#set_salary_details").submit(function (e) {
        e.preventDefault();
        var staff_id = $('#staff_id').val();
        var month_year = $('#month_year').val();
        var laddaButton = Ladda.create(document.querySelector('#search_button'));

        laddaButton.start();

        // Update the DataTable's AJAX URL and reload
        xin_table.ajax.url(main_url + "payroll/payslip_list/" + staff_id + "/" + month_year).load(function() {
            $('#get_date').html(month_year);
            laddaButton.stop();
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
                    xin_table.ajax.reload(function () {
                        toastr.success(JSON.result);
                    }, true);
                    $('input[name="csrf_token"]').val(JSON.csrf_hash);
                    Ladda.stopAll();
                }
            }
        });
    });

    // edit
    $('.payroll-modal-data').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var field_id = button.data('field_id');
        var payment_date = button.data('payment_date');
        var advance_salary = button.data('advance_salary');
        var loan = button.data('loan');
        var modal = $(this);
        $.ajax({
            url: main_url + "read-payroll",
            type: "GET",
            data: 'jd=1&data=payroll&field_id=' + field_id + '&payment_date=' + payment_date + '&advance_salary=' + advance_salary + '&loan=' + loan,
            success: function (response) {
                if (response) {
                    $("#ajax_payroll_modal").html(response);
                }
            }
        });
    });

    $('.view-modal-data').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var user_id = button.data('field_id');
        var modal = $(this);
        $.ajax({
            url: main_url + "users/read",
            type: "GET",
            data: 'jd=1&type=view_user&user_id=' + user_id,
            success: function (response) {
                if (response) {
                    $("#ajax_view_modal").html(response);
                }
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
                    xin_table.ajax.reload(function () {
                        toastr.success(JSON.result);
                    }, true);
                    $('input[name="csrf_token"]').val(JSON.csrf_hash);
                    $('#xin-form')[0].reset(); // To reset form fields
                    $('.add-form').removeClass('show');
                    Ladda.stopAll();
                }
            },
            error: function () {
                toastr.error(JSON.error);
                $('input[name="csrf_token"]').val(JSON.csrf_hash);
                Ladda.stopAll();
            }
        });
    });
});

$(document).on("click", ".delete", function () {
    $('input[name=_token]').val($(this).data('record-id'));
    $('#delete_record').attr('action', main_url + 'delete-payslip/' + $(this).data('record-id'));
});