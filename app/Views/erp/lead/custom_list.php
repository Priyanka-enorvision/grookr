<?php


use App\Models\UsersModel;

$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<style>
    .error {
        color: red;
        display: none;
    }

    .row {
        display: flex !important;
    }

    .bulk-data {
        margin-left: 780px;
    }

    .row .bulk-data {
        margin-top: -613px;
    }

    .modify {
        background-color: blue !important;
        height: 31px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>

<div class="card user-profile-list">
    <div class="card-header">
        <h5>List All Leads Fields</h5>
        <div class="row card-header-right d-flex justify-content-end align-items-center">
            <?php if (in_array('leads2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <div class="col-auto p-0">
                    <button id="commitButton" class="btn btn-primary text-white modify">
                        <i data-feather="check"></i> Commit
                    </button>
                </div>
                <div class="col-auto">
                    <a data-toggle="modal" data-target="#addLeadModal" class="btn btn-primary text-white modify">
                        <i data-feather="plus"></i> <?= lang('Main.xin_add_new'); ?>
                    </a>
                </div>
            <?php } ?>
        </div>

    </div>

    <div class="card-body">
        <div class="box-datatable table-responsive">
            <table class="datatables-demo table table-striped table-bordered" id="xin_table">
                <thead>
                    <tr>
                        <th>Column Name</th>
                        <th>Data Type</th>
                        <th>Required</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $value) { ?>
                        <tr>
                            <td><?= esc($value['column_name']); ?></td>
                            <td><?= esc($value['type']); ?></td>
                            <td><?= $value['is_required'] ? 'True' : 'False'; ?></td>
                            <td>
                                <?php if ($value['status']): ?>
                                    <a href="<?= base_url('erp/lead-update-status/' . $value['id'] . '/0') ?>" class="btn btn-success">Active</a>
                                <?php else: ?>
                                    <a href="<?= base_url('erp/lead-update-status/' . $value['id'] . '/1') ?>" class="btn btn-danger">Inactive</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($value['company_id'])): ?>
                                    <a class="btn btn-primary" style="background-color: blue !important;" data-toggle="tooltip"
                                        title="View Details" onclick="openModal(<?= $value['id'] ?>);">
                                        <i class="feather icon-edit-2 text-white"></i>
                                    </a>
                                    <a href="<?= base_url('lead/delete-field/' . $value['id']); ?>"
                                        class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this item?');"
                                        data-toggle="tooltip" title="Delete Item">
                                        <i class="feather icon-trash-2"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-primary"
                                        style="background-color: grey !important; cursor: not-allowed;" data-toggle="tooltip"
                                        title="Edit Disabled" disabled>
                                        <i class="feather icon-edit-2 text-white"></i>
                                    </button>
                                    <button class="btn btn-danger"
                                        style="background-color: grey !important; cursor: not-allowed;" data-toggle="tooltip"
                                        title="Delete Disabled" disabled>
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<!-- Add New Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLeadModalLabel">Add Field</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addLeadForm" action="<?= base_url('erp/save-lead'); ?>" method="POST">
                <div class="modal-body">

                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label for="leadName">Field Label</label>
                        <span class="text-danger">*</span>
                        <input type="text" class="form-control" id="leadName" name="field_name" required
                            placeholder="Enter Label Name" onkeypress="return onlyAlphabet(event)">
                    </div>
                    <div class="form-group">
                        <label for="dataType">Type</label>
                        <span class="text-danger">*</span>
                        <select id="inputType" name="input_Type" required class="form-control">
                            <option value="">Choose Input Type</option>
                            <option value="text">Text</option>
                            <option value="number">Number</option>
                            <option value="email">Email</option>
                            <option value="password">Password</option>
                            <option value="file">File</option>
                            <option value="date">Date</option>
                            <option value="time">Time</option>
                            <option value="select">Select (Dropdown)</option>
                        </select>
                    </div>
                    <!-- Hidden input field for adding options (Initially hidden) -->
                    <div class="form-group" id="optionsInputField" style="display:none;">
                        <label for="options">Add Options </label>
                        <input type="text" id="options" name="options" class="form-control"
                            placeholder="Option1, Option2, Option3">
                    </div>

                    <div class="form-group">
                        <label for="isRequired">Is Required</label>
                        <span class="text-danger">*</span>
                        <select class="form-control" id="isRequired" name="is_required" required>
                            <option value="">Select</option>
                            <option value="1">True</option>
                            <option value="0">False</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" style="background-color: blue !important;">Add
                        Column</button>
                </div>
            </form>

        </div>
    </div>
</div>



<!-- Modal Structure -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body" id="modalBody">
                <!-- Add your modal body content here -->
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    $(document).ready(function() {
        $('#commitButton').click(function(e) {
            e.preventDefault();
            $(this).prop('disabled', true).text('Loading...');

            $.ajax({
                url: '<?php echo base_url('erp/create-dynamic-table'); ?>',
                type: 'POST',
                dataType: 'json', // Expect JSON response
                success: function(response) {
                    if (response.error) {
                        toastr.error(response.error, 'Error');
                    } else if (response.success) {
                        // alert(response.success); 
                        toastr.success(response.success, 'Success');
                        setTimeout(function() {
                            location.reload(true);
                        }, 7000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('An error occurred: ' + error);
                },
                complete: function() {
                    $('#commitButton').prop('disabled', false).text('Commit');
                }
            });
        });
    });
</script>



<script>
    var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

    function openModal(id) {
        fetch(base_url + 'lead/getDetails/' + id) // Add base_url to the request
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
        $('#xin_table').DataTable({
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
    $(document).ready(function() {
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
    });
</script>
<script>
    function onlyAlphabet(e) {
        var charCode = e.which || e.keyCode;
        if (charCode == 8 || charCode == 9 || charCode == 13 || charCode == 27 || charCode == 32) {
            return true;
        }

        if ((charCode >= 65 && charCode <= 90) || (charCode >= 97 && charCode <= 122)) {
            return true;
        }
        return false;
    }
</script>
<script>
    var input = document.querySelector('input[name=options]');
    var tagify = new Tagify(input);

    document.getElementById('inputType').addEventListener('change', function() {
        var inputType = this.value;
        var optionsField = document.getElementById('optionsInputField');

        if (inputType === 'select') {
            optionsField.style.display = 'block';
        } else {
            optionsField.style.display = 'none';
        }
    });
</script>