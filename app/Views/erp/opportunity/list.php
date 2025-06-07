<?php

use App\Models\UsersModel;

$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

?>


<?php if (in_array('opportunity1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


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
            <h5>List All Opportunity</h5>
            <div class="card-header-right">
                <?php if (in_array('opportunity2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                    <a data-toggle="modal" data-target="#addLeadModal" class="btn btn-primary text-white modify">
                        <i data-feather="plus"></i> <?= lang('Main.xin_add_new'); ?>
                    </a>
                <?php } ?>
            </div>

        </div>

        <div class="card-body">
            <div class="box-datatable table-responsive">
                <table class="datatables-demo table table-striped table-bordered" id="xin_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Opportunity Name</th>
                            <th>Opportunity Stage</th>
                            <th>Expected Closing Date</th>
                            <th>Probability</th>
                            <th>Comments</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($result as $value) { ?>
                     
                            <tr>
                                <td><?= $i++; ?></td>
                                <td>
                                    <a href="javascript:void(0);" onclick="setOpportunitySession('<?= esc($value['id']); ?>')">
                                        <?= esc($value['opportunity_name']); ?>
                                    </a>
                                </td>


                                <td><?= esc($value['opportunity_stage']); ?></td>
                                <?php
                                $date = new DateTime($value['expected_closing_date']);

                                ?>
                                <td><?= esc($date->format('j M Y')); ?></td>
                                <td><?= esc($value['probability']); ?></td>
                                <td><?= esc($value['comments']); ?></td>
                                <td>
                                    <?php
                                        $isActive = $value['status'] == 1;
                                        $newStatus = $isActive ? 0 : 1;
                                        $statusText = $isActive ? 'Active' : 'Inactive';
                                        $btnClass = $isActive ? 'success' : 'danger';
                                        $toggleText = $isActive ? 'Deactivate' : 'Activate';
                                    ?>

                                    <a href="<?= base_url('opportunity/update-status/' . $value['id'] . '/' . $newStatus) ?>"
                                    class="btn btn-<?= $btnClass ?>">
                                        <?= $statusText ?>
                                    </a>

                                </td>
                                <td>
                                    <?php if (in_array('opportunity3', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

                                        <a class="btn btn-primary" style="background-color: blue !important;" data-toggle="tooltip"
                                            title="View Details" onclick="openModal(<?= $value['id'] ?>);">
                                            <i class="feather icon-edit-2 text-white"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if (in_array('opportunity4', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

                                        <a href="<?= base_url('erp/opportunity-delete' .'/'.$value['id']); ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this item?');"
                                            data-toggle="tooltip" title="Delete Item">
                                            <i class="feather icon-trash-2"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    <!-- Add New Opportunity Modal -->
    <div class="modal fade" id="addLeadModal" tabindex="-1" role="dialog" aria-labelledby="addLeadModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document"> <!-- Changed to modal-lg -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeadModalLabel">Add Opportunity</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addLeadForm" action="<?= base_url('erp/opportunity-save'); ?>" method="POST">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="leadName">Opportunity Name</label>
                                <span class="text-danger">*</span>
                                <input type="text" class="form-control" id="leadName" name="opportunity_name" required
                                    placeholder="Enter Opportunity Name" onkeypress="return onlyAlphabet(event)">
                            </div>
                            <div class="form-group col-6">
                                <label for="opportunityStage">Opportunity Stage</label>
                                <span class="text-danger">*</span>
                                <select class="form-control" id="opportunityStage" name="opportunity_stage" required>
                                    <option value="" disabled selected>Select Opportunity Stage</option>
                                    <option value="prospecting">Prospecting</option>
                                    <option value="qualification">Qualification</option>
                                    <option value="proposal">Proposal</option>
                                    <option value="closed-won">Closed-Won</option>
                                    <option value="closed-lost">Closed-Lost</option>
                                    <!-- Add more options as needed -->
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="expectedClosingDate">Expected Closing Date</label>
                                <span class="text-danger">*</span>
                                <input type="date" class="form-control" id="expectedClosingDate"
                                    name="expected_closing_date" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="value">Value</label>
                                <span class="text-danger">*</span>
                                <input type="number" class="form-control" id="value" name="value" required
                                    placeholder="Enter Expected value">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="probability">Probability (%)</label>
                                <span class="text-danger">*</span>
                                <input type="number" class="form-control" id="probability" name="probability" required min="0"
                                    max="100" placeholder="Enter Probability">
                            </div>

                            <div class="form-group col-6">
                                <label for="users">Users</label>
                                <span class="text-danger">*</span>
                                <select class="form-control js-example-matcher-start" id="users" name="user_id" required>
                                    <option value="" disabled selected>Select Users</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user['user_id']); ?>">
                                            <?= esc($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="comments">Comments</label>
                                <textarea class="form-control" id="comments" name="comments" rows="3"
                                    placeholder="Add any comments here..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"
                            style="background-color: blue !important;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!--View Opportunity Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-body" id="modalBody">
                    <!-- Add your modal body content here -->
                </div>
            </div>
        </div>
    </div>



<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    function matchStart(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }

        // Check if the data has children elements (used for grouped options)
        if (typeof data.children === 'undefined') {
            if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
                return data; // Match anywhere in the string
            }
            return null;
        }

        var filteredChildren = [];
        $.each(data.children, function (idx, child) {
            if (child.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
                filteredChildren.push(child);
            }
        });

        if (filteredChildren.length) {
            var modifiedData = $.extend({}, data, true);
            modifiedData.children = filteredChildren;
            return modifiedData;
        }

        return null;
    }

    $(document).ready(function () {
        $(".js-example-matcher-start").select2({
            matcher: matchStart, // Ensure this is the matcher you want
            placeholder: 'Select Users',
            allowClear: true,
            width: '100%' // Ensure the width is set correctly
        });
    });
</script>
<script>
    var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

    function openModal(id) {
        fetch(base_url + 'Opportunity/getdata/' + id) // Add base_url to the request
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
    function setOpportunitySession(id) {
        $.ajax({
            url: '<?= base_url('erp/clients/set_opportunity_session'); ?>',
            type: 'POST',
            data: {
                opportunity_id: id
            },
            success: function () {
                window.location.href = '<?= base_url('erp/leads-list'); ?>';
            }
        });
    }
</script>

<script>
    // Function to set minimum date for the date input to today
    function setMinDate() {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
        const yyyy = today.getFullYear();

        // Format the date as YYYY-MM-DD
        const formattedDate = `${yyyy}-${mm}-${dd}`;

        // Set the min attribute of the date input
        document.getElementById('expectedClosingDate').setAttribute('min', formattedDate);
    }

    // Call the function on page load
    window.onload = setMinDate;
</script>


<script>
    $(document).ready(function () {
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
    $(document).ready(function () {
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

    document.getElementById('inputType').addEventListener('change', function () {
        var inputType = this.value;
        var optionsField = document.getElementById('optionsInputField');

        if (inputType === 'select') {
            optionsField.style.display = 'block';
        } else {
            optionsField.style.display = 'none';
        }
    });
</script>

<?php } ?>