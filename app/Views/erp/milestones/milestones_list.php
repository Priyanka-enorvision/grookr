
<?php

use App\Models\UsersModel;

$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_type = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <style>
        #DataTables_Table_0_wrapper {
            padding-top: 10px;
            width: 100%;
        }

        .modal-header {
            background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
        }
    </style>
</head>

<body>
    <div id="wrapper" style="min-height: 1020px;">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <?php if ((in_array('milestone2', staff_role_resource())) || $user_type['user_type'] == 'company') { ?>
                                <div class="_buttons">
                                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#milestone_modal">New Milestone</a>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading">
                            <div id="DataTables_Table_0_wrapper"
                                class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <?php
                                $canEdit = in_array('milestone3', staff_role_resource());
                                $canDelete = in_array('milestone4', staff_role_resource());
                                ?>

                                <table class="table table-striped dataTable no-footer" id="DataTables_Table_0"
                                    role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Due Date</th>
                                            <th>Description</th>
                                            <?php if ($canEdit || $canDelete) { ?>
                                                <th>Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($result as $list) { ?>
                                            <tr class="odd" role="row">
                                                <td><?= $i++; ?></td>
                                                <td><?= $list['name']; ?></td>
                                                <td><?= $list['due_date']; ?></td>
                                                <td><?= $list['description']; ?></td>
                                                <?php if ($canEdit || $canDelete) { ?>
                                                    <td>
                                                        <?php if ($canEdit) { ?>
                                                            <span data-toggle="tooltip" title="Edit Project">
                                                                <a onclick="openModal(<?= $list['id'] ?>);">
                                                                    <button type="button"
                                                                        class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                                                        <i class="feather icon-edit"></i>
                                                                    </button>
                                                                </a>
                                                            </span>
                                                        <?php } ?>
                                                        <?php if ($canDelete) { ?>
                                                            <span data-toggle="tooltip" title="Delete Project">
                                                                <button type="button"
                                                                    class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                                                    data-toggle="modal" data-target="#deleteModal"
                                                                    data-record-id="<?= $list['id']; ?>">
                                                                    <i class="feather icon-trash-2"></i>
                                                                </button>
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- delete popup -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Milestone MOdal -->
    <div class="modal fade" id="milestone_modal" tabindex="-1" role="dialog" aria-labelledby="groupsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content" style="width: 110%;">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupsModalLabel" style="color: #fff;">New Milestone </h5> <button
                        type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">&times;</span> </button>
                </div>
                <form action="<?= base_url('erp/Milestones/save'); ?>" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">

                                <div id="additional_milestone"></div>
                                <div class="form-group" app-field-wrapper="project">
                                    <label for="project" class="control-label">
                                        Project <small class="req text-danger">* </small>
                                    </label>
                                    <select id="project" name="project_id" class="form-control" required>
                                        <option value="" disabled selected>Select a Project</option>
                                        <?php foreach ($project as $item): ?>
                                            <option value="<?= $item['project_id'] ?>">
                                                <?= $item['title'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group" app-field-wrapper="name">
                                    <label for="name" class="control-label"><small class="req text-danger">*
                                        </small>Name</label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                <div class="form-group" app-field-wrapper="due_date">
                                    <label for="due_date" class="control-label"><small class="req text-danger">*
                                        </small>Due date</label>
                                    <div class="input-group date">
                                        <input type="text" id="due_date" name="due_date" class="form-control datepicker"
                                            required autocomplete="off">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i
                                                    class="fa fa-calendar calendar-icon"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" app-field-wrapper="description">
                                    <label for="description" class="control-label">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="3"
                                        required></textarea>
                                </div>

                                <div class="form-group" app-field-wrapper="milestone_order">
                                    <label for="milestone_order" class="control-label">Order</label>
                                    <input type="number" id="milestone_order" name="milestone_order"
                                        class="form-control" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer " style="padding:10px;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--  -->

    <!-- view Milestone -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body" id="modalBody" style="padding: 0px;">
                    <!-- Add your modal body content here -->
                </div>
            </div>
        </div>
    </div>
    <!--  -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
</body>

</html>
<script>
    $(document).ready(function () {
        // Display toastr notifications for flash data
        <?php if (session()->getFlashdata('error')): ?>
            toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
                timeOut: 5000
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
            toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
                timeOut: 5000
            });
        <?php endif; ?>

        $('#DataTables_Table_0').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable ordering
            lengthMenu: [10, 25, 50, 100], // Options for number of records to show
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }
        });
    });
</script>

<script>
    var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

    function openModal(id) {
        fetch(base_url + 'erp/Milestones/getdata/' + id) // Add base_url to the request
            .then(response => response.text())
            .then(data => {
                document.getElementById('modalBody').innerHTML = data;
                let modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error loading data:', error);
                alert('Failed to load data. Please try again.');
            });
    }
</script>
<script>
    $(document).ready(function () {
        $(".datepicker").datepicker({
            dateFormat: "yy-mm-dd",
            minDate: new Date(),
        });

        $(".input-group-text").click(function () {
            $(".datepicker").datepicker("show");
        });
    });
</script>

<script>
    $(document).ready(function () {
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('record-id');
            var modal = $(this);
            modal.find('#confirmDeleteBtn').data('record-id', recordId);
        });

        $('#confirmDeleteBtn').on('click', function () {
            var recordId = $(this).data('record-id');
            var URL = '<?= base_url('erp/Milestones/delete') ?>/' + recordId; // Construct the URL with recordId

            // Disable the delete button to prevent multiple clicks
            $('#confirmDeleteBtn').prop('disabled', true);

            // Perform the AJAX request
            $.ajax({
                url: URL,
                type: 'POST',
                dataType: "json",
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>', // Include the CSRF token
                },
                success: function (response) {
                    $('#confirmDeleteBtn').prop('disabled', false); // Re-enable the button
                    if (response.result) {
                        toastr.success(response.result);
                        $('#deleteModal').modal('hide');
                        setTimeout(function () {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                location.reload(); // Fallback to reload if no URL provided
                            }
                        }, 1000);
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash); // Update CSRF token
                },
                error: function (xhr, status, error) {
                    $('#confirmDeleteBtn').prop('disabled', false); // Re-enable the button
                    toastr.error('An error occurred while deleting the record.');
                    console.error("Error deleting record: ", error);
                }
            });
        });
    });
</script>