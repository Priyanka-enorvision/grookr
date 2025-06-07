<?php

use App\Models\UsersModel;
use App\Models\Tax_declarationModel;

$UsersModel = new UsersModel();
$tax_declarationModel = new Tax_declarationModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$tax_declarationlist = $tax_declarationModel->where('company_id', $usession['sup_user_id'])->orderBy('id', 'desc')->findAll();
$employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where(['user_type' => 'staff', 'is_active' => 1])->orderBy('user_id', 'ASC')->findAll();


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

        .modal-subtitle {
            margin-top: 5px;
            font-size: 0.875rem;
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
                                <h6 class="m-0">List Tax Declaration </h6>
                                <?php if ($user_info['user_type'] == 'staff') { ?>
                                    <div class="_buttons">
                                        <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#milestone_modal">Tax Declaration</a>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="clearfix"></div>
                            <hr class="hr-panel-heading">
                            <div id="DataTables_Table_0_wrapper"
                                class="dataTables_wrapper form-inline dt-bootstrap no-footer">

                                <table class="table table-striped dataTable no-footer" id="DataTables_Table_0"
                                    role="grid">
                                    <thead>
                                        <tr role="row">
                                            <th>#</th>
                                            <th>Tax Rule</th>
                                            <th>Name </th>
                                            <th>Code </th>
                                            <th>Max Amount </th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($tax_declarationlist as $list) { ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $list['tax_rules']; ?></td>
                                                <td><?= $list['name']; ?></td>
                                                <td><?= $list['code']; ?></td>
                                                <td><?= $list['max_amount']; ?></td>
                                                <td><?= $list['description']; ?></td>
                                                <td>
                                                    <?php if ($list['status'] == 1): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url('edit/' . $list['id']); ?>" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('delete/' . $list['id']); ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
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

    <!-- Milestone Modal -->
    <div class="modal fade" id="milestone_modal" tabindex="-1" role="dialog" aria-labelledby="taxDeclarationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Increased modal width -->
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <div>
                        <h4 class="modal-title text-white" id="taxDeclarationModalLabel">Tax Declaration</h4>
                        <p class="modal-subtitle text-white mb-0">Submit your planned investments and expenses for tax exemption purposes.</p>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <form action="<?= base_url('erp/Finance/save_declaration'); ?>" method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">

                            <!-- Tax Rule Selection -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tax_rule">Tax Rules <span class="text-danger">*</span></label>
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="investment_name">Investment Name <span class="text-danger">*</span></label>
                                    <select id="investment_name" name="name" class="form-control" required>
                                        <option value="" disabled selected>Select Investment Name</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <!-- Max Amount -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_declaration_amount">Max Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="max_declaration_amount" name="max_declaration_amount" class="form-control" placeholder="Enter maximum amount" required>
                                </div>
                            </div>
                            <!-- Declared Amount -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="declared_amount">Declared Amount <span class="text-danger">*</span></label>
                                    <input type="number" id="declared_amount" name="declared_amount" class="form-control" placeholder="Enter declared amount" required>
                                </div>
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="declaration_date">Date of Declaration <span class="text-danger">*</span></label>
                                    <input type="date" id="declaration_date" name="declaration_date" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Proof Upload -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="proof_file">Upload Proof <span class="text-danger">*</span></label>
                                    <input type="file" id="proof_file" name="proof_file" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
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
    <script>
        $(document).ready(function() {
            $('#section').change(function() {
                var section = $(this).val();

                if (section) {
                    $.ajax({
                        url: '<?= base_url('erp/finance/getInvestmentname') ?>',
                        type: 'POST',
                        dataType: 'json', // Expect JSON response
                        data: {
                            section: section,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>' // Include the CSRF token
                        },
                        success: function(response) {
                            $('#investment_name').empty(); // Clear existing options
                            $('#investment_name').append('<option value="" disabled selected>Select Investment Name</option>');

                            $.each(response, function(index, investment) {
                                $('#investment_name').append('<option value="' + investment.investment_id + '">' + investment.investment_name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            alert('Error fetching investment names: ' + error);
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#investment_name').change(function() {
                var investment_name = $(this).val();
                // alert(investment_name);2

                if (investment_name) {
                    $.ajax({
                        url: '<?= base_url('erp/finance/getLimitedAmount') ?>',
                        type: 'POST',
                        dataType: 'json', // Expect JSON response
                        data: {
                            name: investment_name,
                            <?= csrf_token() ?>: '<?= csrf_hash() ?>' // Include CSRF token
                        },
                        success: function(response) {
                            // Clear the current options
                            $('#investment_name').empty();
                            $('#investment_name').append('<option value="" disabled selected>Select Investment Name</option>');

                            // Populate new options
                            $.each(response, function(index, investment) {
                                $('#investment_name').append('<option value="' + investment.investment_id + '">' + investment.investment_name + '</option>');
                            });

                            // Check if response contains max_amount and update the input field
                            if (response.length > 0 && response[0].max_amount) {
                                $('#max_declaration_amount').val(response[0].max_amount); // Update max amount field
                            } else {
                                $('#max_declaration_amount').val(''); // Clear if no amount is found
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error fetching investment names: ' + error);
                        }
                    });
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
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