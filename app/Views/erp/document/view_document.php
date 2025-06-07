<?php

use CodeIgniter\I18n\Time;
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\DocumentConfigModel;
use App\Models\VerifyEmployeDocModel;
use App\Models\EmpDocumentItemModel;


$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$doc_categoryModel = new DocumentConfigModel();
$documentModel = new VerifyEmployeDocModel();
$itemDocument = new EmpDocumentItemModel();


$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$locale = service('request')->getLocale();
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $usession['sup_user_id'];

if ($user_info['user_type'] == 'staff') {

    $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where('user_id', $user_id)->orderBy('user_id', 'ASC')->findAll();
    $category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();
    $singledocument_data = $documentModel->where('id', $document_id)->first();
    $documentoption_item = $itemDocument->where('employe_docu_id', $document_id)->findAll();
} else {

    $employee_list = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type', 'staff')->orderBy('user_id', 'ASC')->findAll();
    $category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();
    $singledocument_data = $documentModel->where('id', $document_id)->first();
    $documentoption_item = $itemDocument->where('employe_docu_id', $document_id)->findAll();
}
$category_document_counts = [];
foreach ($category_list as $category) {
    $category_document_counts[$category['id']] = 0;
    foreach ($documentoption_item as $document) {
        if ($document['category_id'] == $category['id']) {
            $documentData = json_decode($document['data'], true);
            $category_document_counts[$category['id']] += count($documentData);
        }
    }
}



?>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">




<style>
    .nav-pills .nav-link {
        border-radius: 0.25rem;
        color: #6c757d;
        background-color: #f8f9fa;
        margin-right: 5px;
        transition: background-color 0.3s, color 0.3s;
    }

    .nav-pills .nav-link.active {
        color: #007bff;
        position: relative;
        background-color: transparent;
    }

    .nav-pills .nav-link.active::after,
    .nav-pills .nav-link:hover::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -3px;
        height: 2px;
        background-color: #007bff;
        width: 120%;
        transform: translateX(-10%);
    }

    .nav-pills .nav-link:hover {
        color: #007bff;
        /* text-decoration: underline; */
        /* text-underline-offset: 10px; */
        position: relative;
    }

    .card {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding-left: 15px !important;
        padding-right: 10px !important;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }

    .note-editor {
        margin-bottom: 8px !important;
        height: 185px !important;
    }


    .panel_s .panel-body {
        background: #fff;
        border: 1px solid #dce1ef;
        border-radius: 4px;
        padding: 20px;
        position: relative;
    }






    .row {
        margin-right: -15px;
        margin-left: -15px;
    }

    .project-overview-left {
        margin-top: -20px;
        padding-top: 20px;
    }

    .border-right {
        border-right: 1px solid #f0f0f0;
    }



    h5 {
        font-size: 13px;
        margin-bottom: 18px;
    }

    .modal-header {
        background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
    }

    /* Dropdown Menu Styles */
    .custom-dropdown-menu {
        background-color: #ffffff;
        padding: 0;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        min-width: 160px;
    }

    .custom-dropdown-menu li a {
        display: block;
        padding: 10px 20px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s ease;
        border-bottom: 1px solid #eee;
    }

    .custom-dropdown-menu li a:last-child {
        border-bottom: none;
    }

    .custom-dropdown-menu li a:hover {
        /* background-color: #f0f0f0; */
        color: #333;
        cursor: pointer;
        border-radius: 8px;
    }

    .custom-dropdown-menu li a.active {
        background-color: #007bff;
        color: #ffffff;
        font-weight: bold;
    }

    tbody {
        font-size: 13px;
    }

    tbody td {
        padding: 8px 12px;
        color: #333;
    }

    tbody td.bold {
        font-weight: bold;
    }

    tbody a {
        color: #007bff;
        text-decoration: none;
    }

    tbody a:hover {
        text-decoration: underline;
    }

    .nav {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        padding-left: 0;
        margin-bottom: 0;
        list-style: none;
        gap: 30px;
    }

    .nav-pills-custom .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        color: #000;
        font-weight: bold;
        transition: color 0.2s;
    }

    .nav-pills-custom .nav-link i {
        border: 2px solid #e0e0e0;
        border-radius: 50%;
        padding: 8px;
        font-size: 20px;
        color: #6c757d;
        margin-right: 8px;
        transition: background-color 0.2s, color 0.2s;
    }

    .nav-pills-custom .nav-link.active i {
        border-color: #007bff;
        background-color: #e0e0ff;
        color: #6c5ce7;
    }

    .nav-pills-custom .nav-link.active {
        color: #6c5ce7;
    }

    .nav-pills-custom .nav-link div {
        font-weight: normal;
        color: #6c757d;
        font-size: 12px;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .custom-row {
        border: 1px solid #ddd;
        padding: 10px;
        margin-top: 15px;
        border-radius: 5px;
    }

    .col-3 {
        display: flex;
        align-items: center;
    }

    .fa-cloud-arrow-down {
        font-size: 20px;
    }

    .shadow-row {
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
        /* border-radius: 8px; */
        padding: 10px;
        margin-bottom: 15px;
    }

    .list-group-item {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }

    #dynamic-content .list-group-item {
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* border-radius: 8px; */
        margin-bottom: 10px;
        transition: transform 0.3s ease;
    }



    #dynamic-content h6 {
        font-size: 16px;
        margin: 0;
        color: #333;
    }

    #dynamic-content small {
        font-size: 12px;
        color: #6c757d;
    }

    .btn-outline-primary {
        color: #007bff;
        border-color: #007bff;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
    }

    .icon-circle {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: skyblue;
        color: white;
        font-size: 1.2rem;
        margin-right: 14px;
    }

    .icon-circle::before,
    .icon-circle::after {
        content: none;
    }


    .category-name {
        font-size: 15px;
    }

    .list-group-item {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .document-count {
        font-size: 0.9rem;
        color: #6c757d;
        /* Light gray color */
        margin-top: 2px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="bg-light card mb-2" style="font-size: 13px;">
            <div class="card-body">
                <ul class="nav nav-pills" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-overview-tab" data-toggle="pill" href="#pills-overview" role="tab" aria-controls="pills-overview" aria-selected="true">
                            <i class="fas fa-file-download" style="margin-right: 4px;"></i>Download Docx</a>
                    </li>
                    <?php if ($user_info['user_type'] !== 'staff'): ?>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-milestones-tab" data-toggle="pill" href="#pills-milestones" role="tab" aria-controls="pills-milestones" aria-selected="false">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <br>

                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade active show" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="container">
                                    <div class="row">
                                        <!-- Sidebar: Categories -->
                                        <div class="col-md-4" style="border-right: 1px solid #ddd;">
                                            <!-- <h5 class="mb-3">Category</h5> -->
                                            <div class="list-group-item mb-3" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); ">
                                                <div class="mb-3">
                                                    <h6 class="mb-1" style="font-size: 20px;">Category Employee Document</h6>
                                                </div>
                                            </div>


                                            <ul class="list-group">
                                                <?php foreach ($category_list as $list) { ?>
                                                    <li class="list-group-item d-flex align-items-start flex-column" data-id="<?= $list['id']; ?>" onclick="loadCategoryData(<?= $list['id']; ?>)">
                                                        <div class="d-flex align-items-center">
                                                            <span class="icon-circle me-3">
                                                                <i class="fas fa-folder"></i>
                                                            </span>
                                                            <div class="d-flex flex-column">
                                                                <span class="category-name"><?= $list['category_name']; ?></span>
                                                                <span class="document-count">
                                                                    <?= $category_document_counts[$list['id']] ?> documents
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>



                                        </div>

                                        <!-- Dynamic Content -->
                                        <div class="col-md-8" id="dynamic-content">

                                            <div class="text-center mt-5">
                                                <p class="text-muted">Select a category to see its documents.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="tab-pane fade" id="pills-milestones" role="tabpanel" aria-labelledby="pills-milestones-tab">
                        <div class="panel_s">
                            <div class="panel-body">
                                <div class="d-flex justify-content-between mb-4 mt-2">
                                    <h5>Edit Document File</h5>

                                </div>
                                <div class="modal-body">
                                    <form action="<?= base_url('erp/documentfiles-updates'); ?>" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="documentName">Employee <span class="text-danger">*</span></label>
                                                    <input type="hidden" name="document_id" value="<?= $singledocument_data['id'] ?>">
                                                    <input type="text" class="form-control docu_name input-sm-custom" name="employe_id" value="<?= getClientname($singledocument_data['user_id']) ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label for="documentName">Category <span class="text-danger">*</span></label>
                                                    <select name="category_id" class="form-control form-select form-select-sm" style="min-width: 167px; border-color: #007bff;" onchange="showDocumentData(this.value)">
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
                                                        <div id="dynamic-itemshow"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="item-list"></div>
                                            <div class="form-group overflow-hidden1">
                                                <div class="col-xs-12">
                                                    <button type="button" data-repeater-create="" class="btn btn-sm" id="add-document-item" style="background-color: green !important; color:white;">
                                                        <?= lang('Invoices.xin_title_add_item'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn" style="background-color:#007bff; color:white;">Update Document</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
        const documentOptionItem = <?= json_encode($documentoption_item); ?>;
        const basePath = '<?= base_url() ?>' + 'uploads/employe_document/';

        function loadCategoryData(categoryId) {
            const contentDiv = document.getElementById('dynamic-content');
            const filteredData = documentOptionItem.filter(item => item.category_id == categoryId);

            if (filteredData.length > 0) {
                let contentHtml = `
                <div class="list-group-item mb-4">
                    <div class="mb-3">
                        <h5 class="mb-1" style="font-size: 17px;">Documents List</h5>
                        <p class="text-muted" style="margin-top: 0;">List of documents that need to be uploaded or verified:</p>
                    </div>
                </div>

            <ul class="list-group mt-3">
            `;
                filteredData.forEach(data => {
                    const documentData = JSON.parse(data.data);
                    documentData.forEach(doc => {
                        contentHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i> ${doc.docu_name}
                                </h6>
                            </div>
                            <a href="${basePath}${doc.docu_image}" download="${doc.docu_image}" class="btn btn-sm btn-outline-primary">
                                 Download <i class="fa-solid fa-cloud-arrow-down"></i>
                            </a>
                        </li>
                    `;
                    });
                });
                contentHtml += '</ul>';
                contentDiv.innerHTML = contentHtml;
            } else {
                contentDiv.innerHTML = `
                <div class="list-group-item mb-4">
                    <div class="mb-3">
                        <h5 class="mb-1" style="font-size: 17px;">Documents List</h5>
                        <p class="text-muted" style="margin-top: 0;">List of documents that need to be uploaded or verified:</p>
                    </div>
                </div>
            <div class="text-center mt-5">
                <p class="text-muted">No documents found for this category.</p>
            </div>`;
            }
        }

        // Load data for the first category by default
        document.addEventListener('DOMContentLoaded', function() {
            if (documentOptionItem.length > 0) {
                loadCategoryData(documentOptionItem[0].category_id);
            }
        });
    </script>



    <script>
        const documentOptionItems = <?= json_encode($documentoption_item); ?>;
        const basePaths = '<?= base_url() ?>' + 'uploads/employe_document/';

        function showDocumentData(categoryId) {
            console.log('showDocumentData function called with categoryId:', categoryId);
            const contentDiv = document.getElementById('dynamic-itemshow');
            if (!contentDiv) {
                console.error('dynamic-itemshow element not found');
                return;
            }

            const filteredDatas = documentOptionItems.filter(item => item.category_id == categoryId);
            console.log('Filtered Data:', filteredDatas);

            if (filteredDatas.length > 0) {
                let contentHtml = '<div class="row item-row">';
                filteredDatas.forEach(data => {
                    const documentData = JSON.parse(data.data);
                    documentData.forEach(doc => {
                        contentHtml += `
                        <div class="form-group mb-1 col-md-4">
                            <label for="item_name" class="form-label-custom">Document Name</label>
                            <br>
                            <input type="text" class="form-control docu_name input-sm-custom" name="docu_name[]" id="docu_name" value="${doc.docu_name}">
                        </div>

                        <div class="form-group mb-1 col-md-4">
                            <label for="item_name" class="form-label-custom">Document Image </label>
                            <br>
                            <input type="file" class="form-control docu_image input-sm-custom" name="docu_image[]" id="docu_image">
                            <input type="hidden" class="form-control docu_image input-sm-custom" name="docu_image_hidden[]" value="${doc.docu_image}">
                        </div>

                        <div class="form-group col-sm-12 col-md-2 text-xs-center mt-2">
                            <div class="mt-2">
                                <img src="${basePaths}${doc.docu_image}" alt="Document Image" style="width: 50px; height: 50px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                        </div>

                        <div class="form-group col-sm-12 col-md-2 text-xs-center mt-2">
                            <label for="profession" class="form-label-custom">&nbsp;</label>
                            <br>
                            <button type="button" class="btn icon-btn btn-sm btn-outline-danger waves-effect waves-light remove-invoice-item" data-repeater-delete="">
                                <span class="fa fa-trash"></span>
                            </button>
                        </div>`;
                    });
                });
                contentHtml += '</div>';
                contentDiv.innerHTML = contentHtml;
            } else {
                contentDiv.innerHTML = '<div class="row item-row shadow-row"> No data found for this category.</div>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded event fired');
            const initialCategoryId = document.querySelector('select[name="category_id"]').value;
            if (initialCategoryId) {
                showDocumentData(initialCategoryId);
            }
        });
    </script>


    <script>
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
    </script>