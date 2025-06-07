<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">

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
        text-decoration: underline;
        text-underline-offset: 10px;
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
        text-decoration: underline;
        text-underline-offset: 10px;
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
</style>
<div class="modal-header">
    <h5 class="modal-title" id="groupsModalLabel" style="color: #fff;">Edit Milestone </h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
</div>
<form action="<?= base_url('erp/milestones-update/' . $result['id']); ?>" method="post">
    <?= csrf_field() ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div id="additional_milestone"></div>

                <div class="form-group" app-field-wrapper="project">
                    <label for="project" class="control-label">
                        Project
                    </label>
                    <select id="project" name="project_id" class="form-control">
                        <option value="" disabled selected>Select a Project</option>
                        <?php foreach ($project as $item): ?>
                            <option value="<?= $item['project_id'] ?>" <?= ($item['project_id'] == $result['project_id']) ? 'selected' : '' ?>>
                                <?= $item['title'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" app-field-wrapper="name">
                    <label for="name" class="control-label mb-0">Name <small class="req text-danger">* </small></label>
                    <input type="text" id="name" name="name" class="form-control" required value="<?= $result['name']; ?>">
                </div>
                <div class="form-group" app-field-wrapper="due_date">
                    <label for="due_date" class="control-label mb-0">Due date <small class="req text-danger">* </small></label>
                    <div class="input-group date">
                        <input type="date" id="due_date" name="due_date" class="form-control datepicker" required autocomplete="off"
                            value="<?= $result['due_date']; ?>">

                    </div>
                </div>
                <div class="form-group" app-field-wrapper="description">
                    <label for="description" class="control-label mb-0">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"> <?= $result['description']; ?></textarea>
                </div>

                <div class="form-group" app-field-wrapper="milestone_order">
                    <label for="milestone_order" class="control-label mb-0">Order</label>
                    <input type="number" id="milestone_order" name="milestone_order" class="form-control" min="1" value="<?= $result['orders']; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer " style="padding:10px; padding-bottom: 0px;">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-info">Update</button>
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>