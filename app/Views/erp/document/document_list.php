<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\VerifyEmployeDocModel;
use App\Models\DocumentConfigModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$documentModel = new VerifyEmployeDocModel();
$doc_categoryModel = new DocumentConfigModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $usession['sup_user_id'];

if ($user_info['user_type'] == 'staff') {
    $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where('user_id', $user_id)->orderBy('user_id', 'ASC')->findAll();
    $document_list = $documentModel->where('user_id', $usession['sup_user_id'])->orderBy('id', 'desc')->findAll();
    $category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();
} else {
    $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->orderBy('user_id', 'ASC')->findAll();
    $document_list = $documentModel->where('company_id', $user_info['company_id'])->orderBy('id', 'desc')->findAll();
    $category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();
}

?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    #DataTables_Table_0_wrapper {
        padding-top: 20px;
        width: 100%;
    }

    .document-row {
        align-items: center;
    }

    .document-row .btn {
        margin-top: 25px;
        /* Align buttons with the inputs */
    }

    .document-row .btn .fas {
        margin: 0;
        /* Center the icon inside the button */
    }

    .border-light {
        border-color: #dcdee0 !important;
    }

    .document-items {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        border-radius: 8px;
        padding: 15px;
        background-color: #ffffff;
        margin-bottom: 20px;
    }

    #add-document-item {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    #add-document-item:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        transform: translateY(-2px);
    }

    .custom-icon-btn {
        position: relative;
        color: inherit;
        transition: color 0.3s ease;
    }



    .custom-icon-btn:hover i {
        color: #ffffff;
    }
</style>
<?php if (in_array('project2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <div class="d-flex justify-content-between mb-4 mt-2">
        <h6>All Document List</h6>
        <!-- Button -->

        <a class="btn btn-info" href="#" data-toggle="modal" data-target="#addDocumentModal">Add Document</a>

    </div>


<?php } ?>


<?php if (in_array('project1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <hr class="border-light m-0 mb-3">
<?php } ?>




<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
    <table data-last-order-identifier="projects" data-default-order=""
        class="table table-projects dataTable no-footer dtr-inline" id="DataTables_Table_0" role="grid"
        aria-describedby="DataTables_Table_0_info">
        <thead>
            <tr>
                <th>#</th>
                <th> Employee ID</th>
                <th> Employee Name</th>
                <th> Designation</th>
                <th> Date</th>
                <th> View Document</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            foreach ($document_list as $list) { ?>
                <tr>
                    <td> <?= $i++; ?></td>
                    <td><?= getEmployeeId($list['user_id']) ?></td>
                    <td><?= getClientname($list['user_id']) ?></td>
                    <td><?= designation($list['user_id']) ?></td>
                    <td><?= date('d M Y', strtotime($list['created_at'])) ?></td>
                    <td>
                        <a href="<?= base_url('view/employe-document/' . $list['id']) ?>">
                            <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light custom-icon-btn">
                                <i class="feather icon-edit"></i>
                            </button>
                        </a>

                    </td>
                </tr>
            <?php } ?>
        </tbody>


    </table>
</div>




<!-- Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" role="dialog" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Use modal-lg for wider modal -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDocumentModalLabel">Add Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('erp/save-document'); ?>" method="POST" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="documentName">Employee <span class="text-danger">*</span></label>
                                <select name="employee_id" class="form-control form-select form-select-sm" style="min-width: 167px; border-color: #007bff;" required>
                                    <option value="">Select Employee</option>
                                    <?php foreach ($employee_list as $list) { ?>
                                        <option value="<?= $list['user_id']; ?>"><?= $list['first_name'] . ' ' . $list['last_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="documentName">Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-control form-select form-select-sm" style="min-width: 167px; border-color: #007bff;" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($category_list as $list) { ?>
                                        <option value="<?= $list['id']; ?>"><?= $list['category_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="document-items">
                        <div class="ci-item-values">
                            <div data-repeater-list="items">
                                <div data-repeater-item="">
                                    <div class="row item-row">
                                        <div class="form-group mb-1 col-md-5">
                                            <label for="item_name" class="form-label-custom">
                                                Document Name <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="text" class="form-control docu_name input-sm-custom" name="docu_name[]" id="docu_name" placeholder="Enter Document Name" required>
                                        </div>

                                        <div class="form-group mb-1 col-md-5">
                                            <label for="item_name" class="form-label-custom">
                                                Document Image <span class="text-danger">*</span>
                                            </label>
                                            <br>
                                            <input type="file" class="form-control docu_image input-sm-custom" name="docu_image[]" id="docu_image" required>
                                        </div>
                                        <div class="form-group col-md-2 col-md-1 text-xs-center mt-2">
                                            <label for="profession" class="form-label-custom">&nbsp;</label>
                                            <br>
                                            <button type="button" class="btn icon-btn btn-sm btn-outline-secondary waves-effect waves-light" data-repeater-delete=""> <span class="fa fa-trash"></span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="item-list"></div>
                        <div class="form-group overflow-hidden1">
                            <div class="col-xs-12">
                                <button type="button" data-repeater-create="" class="btn btn-sm " id="add-document-item"
                                    style="background-color: green !important; color:white;">
                                    <?= lang('Invoices.xin_title_add_item'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn " style="background-color:#007bff ; color:white;">Save Document</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Add new document item
        $('#add-document-item').click(function() {
            let document_items = `
                <div class="row item-row">
                    <div class="form-group mb-1 col-sm-12 col-md-5">
                        <label for="item_name" class="form-label-custom">Document Name</label>
                        <br>
                        <input type="text" class="form-control docu_name input-sm-custom" name="docu_name[]" id="docu_name" placeholder="Item Name">
                    </div>

                    <div class="form-group mb-1 col-sm-12 col-md-5">
                        <label for="item_name" class="form-label-custom">Document Image</label>
                        <br>
                        <input type="file" class="form-control docu_image input-sm-custom" name="docu_image[]" id="docu_image" placeholder="Item Image">
                    </div>

                    <div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">
                        <label for="profession" class="form-label-custom">&nbsp;</label>
                        <br>
                        <button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light remove-invoice-item" data-repeater-delete="">
                            <span class="fa fa-trash"></span>
                        </button>
                    </div>
                </div>`;

            $('#item-list').append(document_items).fadeIn(500);
        });

        // Remove document item on click of the delete button
        $(document).on('click', '.remove-invoice-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
            });
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
    $(document).ready(function() {
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