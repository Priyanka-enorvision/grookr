<?php

use App\Models\UsersModel;
use App\Models\InvestmentTypeModel;

$UsersModel = new UsersModel();
$investmentModel = new InvestmentTypeModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$investmentList = $investmentModel->where('company_id', $usession['sup_user_id'])->orderBy('investment_id', 'desc')->findAll();


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

        .status-label {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            cursor: pointer;
            color: white;

        }

        .status-active {
            border-color: #28a745;
            background-color: #28a745;
        }

        .status-inactive {

            border-color: #dc3545;
            background-color: #dc3545;
        }

        .status-label:hover {
            text-decoration: none;
            color: #fff;
        }
    </style>
</head>

<body>
    <div id="wrapper" style="min-height: 1020px;">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <hr>
                        <div class="panel-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="m-0">List Investment Type </h6>

                                <div class="_buttons">
                                    <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#milestone_modal">Add Investment</a>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading">
                            <div id="DataTables_Table_0_wrapper"
                                class="dataTables_wrapper form-inline dt-bootstrap no-footer">

                                <table class="table table-striped dataTable no-footer" id="DataTables_Table_0"
                                    role="grid">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Investment Name</th>
                                            <th>Section</th>
                                            <th>Limit (₹)</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($investmentList as $list) { ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $list['investment_name']; ?></td>
                                                <td><?= $list['section']; ?></td>
                                                <td><?= $list['limit_amount']; ?></td>
                                                <td>
                                                    <?php if ($list['status'] == 1): ?>
                                                        <a href="<?= base_url('erp/update-status/' . $list['investment_id'] . '/0') ?>" class="status-label status-active">Active</a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('erp/update-status/' . $list['investment_id'] . '/1') ?>" class="status-label status-inactive">Inactive</a>
                                                    <?php endif; ?>
                                                </td>

                                                <td>
                                                    <span data-toggle="tooltip" title="Edit Project">
                                                        <a onclick="openModal(<?= $list['investment_id'] ?>);">
                                                            <button type="button"
                                                                class="btn icon-btn btn-sm btn-light-info waves-effect waves-light">
                                                                <i class="feather icon-edit"></i>
                                                            </button>
                                                        </a>
                                                    </span>
                                                    <span data-toggle="tooltip" title="Delete Project">
                                                        <button type="button"
                                                            class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete"
                                                            data-toggle="modal" data-target="#deleteModal"
                                                            data-record-id="<?= $list['investment_id']; ?>">
                                                            <i class="feather icon-trash-2"></i>
                                                        </button>
                                                    </span>
                                                </td>

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

    <!-- Milestone Modal -->
    <div class="modal fade" id="milestone_modal" tabindex="-1" role="dialog" aria-labelledby="taxDeclarationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document"> <!-- Increased modal width -->
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="taxDeclarationModalLabel">New Investment Type</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('erp/insert-investment'); ?>" method="post">
                    <div class="modal-body">
                        <!-- Investment Name Field -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="investment_name">Investment Name <span class="text-danger">*</span></label>
                                    <input type="text" name="investment_name" id="investment_name" class="form-control" placeholder="Enter investment name" required>
                                </div>
                            </div>
                        </div>
                        <!-- Section Field -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="section">Section <span class="text-danger">*</span></label>
                                    <select id="section" name="section" class="form-control" required>
                                        <option value="" disabled selected>Select Section</option>
                                        <option value="80C">80C</option>
                                        <option value="80D">80D</option>
                                        <option value="80E">80E</option>
                                        <option value="80G">80G</option>
                                        <option value="80TTA">80TTA</option>
                                        <option value="80GG">80GG</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Maximum Limit Field -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="maximum_limit">Maximum Limit (₹) <span class="text-danger">*</span></label>
                                    <input type="text" id="maximum_limit" name="maximum_limit" class="form-control" placeholder="Enter maximum limit" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn" style="background-color: #2989d8; color:white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



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
    $(document).ready(function() {

        <?php if (session()->has('message')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: '<?= session('message') ?>',
                timer: 3000
            });
        <?php endif; ?>

        <?php if (session()->has('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?= session('error') ?>'
            });
        <?php endif; ?>


        $('#DataTables_Table_0').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable ordering
            lengthMenu: [5, 10, 25, 50, 100], // Options for number of records to show
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }
        });
    });
</script>

<script>
    var base_url = '<?= site_url(); ?>';

    function openModal(id) {
        fetch(base_url + 'erp/get-data/' + id) // Add base_url to the request
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
    $(document).ready(function() {
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('record-id');
            var modal = $(this);
            modal.find('#confirmDeleteBtn').data('record-id', recordId);
        });

        $('#confirmDeleteBtn').on('click', function() {
            var recordId = $(this).data('record-id');
            var URL = '<?= base_url('erp/delete-investment') ?>/' + recordId;
            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: URL,
                type: 'DELETE', // Changed to DELETE to match route
                dataType: "json",
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>',
                },
                success: function(response) {
                    $btn.prop('disabled', false);
                    if (response.result) {
                        toastr.success(response.result);
                        $('#deleteModal').modal('hide');
                        setTimeout(function() {
                            if (response.redirect_url) {
                                window.location.href = response.redirect_url;
                            } else {
                                location.reload();
                            }
                        }, 1000);
                    } else if (response.error) {
                        toastr.error(response.error);
                    }
                    $('input[name="<?= csrf_token() ?>"]').val(response.csrf_hash);
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false);
                    toastr.error('An error occurred while deleting the record.');
                    console.error("Error deleting record: ", error);
                    // Update CSRF token if available in error response
                    if (xhr.responseJSON && xhr.responseJSON.csrf_hash) {
                        $('input[name="<?= csrf_token() ?>"]').val(xhr.responseJSON.csrf_hash);
                    }
                }
            });
        });
    });
</script>