<?php

use App\Models\UsersModel;

$UsersModel = new UsersModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->where('is_active', 1)->findAll();
?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .modal-header {
        padding: 15px;
    }

    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        transition: box-shadow 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
    }

    .modal-footer .btn {
        border-radius: 0.25rem;
    }

    .modal-body {
        background: #ffffff;
        /* padding: 20px; */
        border-radius: 0.5rem;
    }

    .form-group label {
        font-weight: 600;
        color: #555;
    }

    select.form-control {
        height: auto;
    }

    .modal-footer .btn-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .modal-title {
        color: white;
    }
</style>
<div class="modal-header" style="background-color: #007bff; color: #fff; border-bottom: 2px solid #0056b3;">
    <h5 class="modal-title" id="groupsModalLabel">Edit Timelogs</h5>

</div>
<form action="<?= base_url('erp/timelogs-update/' . $timelog_data['timelogs_id']); ?>" method="POST" style="background: #f9f9f9; border-radius: 0.5rem; box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);">
    <?= csrf_field() ?>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <input type="hidden" name="project_id" value="<?= $timelog_data['project_id']; ?>">

                    <!-- Start Date -->
                    <div class="form-group col-6">
                        <label for="start_date" class="control-label">Start Date <small class="req text-danger">*</small></label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required value="<?= $timelog_data['start_date']; ?>">
                    </div>

                    <!-- Start Time -->
                    <div class="form-group col-6">
                        <label for="start_time" class="control-label">Start Time <small class="req text-danger">*</small></label>
                        <input type="time" id="start_time" name="start_time" class="form-control" required value="<?= $timelog_data['start_time']; ?>">
                    </div>

                    <!-- Due Date -->
                    <div class="form-group col-6">
                        <label for="due_date" class="control-label">Due Date <small class="req text-danger">*</small></label>
                        <input type="date" id="due_date" name="due_date" class="form-control" required value="<?= $timelog_data['end_date']; ?>">
                    </div>

                    <!-- Due Time -->
                    <div class="form-group col-6">
                        <label for="due_time" class="control-label">Due Time <small class="req text-danger">*</small></label>
                        <input type="time" id="due_time" name="due_time" class="form-control" required value="<?= $timelog_data['end_time']; ?>">
                    </div>

                    <!-- Task -->
                    <div class="form-group col-12">
                        <label for="task_id" class="control-label">Task <small class="req text-danger">*</small></label>
                        <select name="task_id" id="task_id" class="form-control" required>
                            <option value="">Select One</option>
                            <?php foreach ($task as $list) { ?>
                                <option value="<?= $list['task_id']; ?>" <?= (isset($timelog_data['task_id']) && $timelog_data['task_id'] == $list['task_id']) ? 'selected' : ''; ?>>
                                    <?= $list['task_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Member -->
                    <?php $assigned_to = explode(',', $timelog_data['employee_id']); ?>
                    <div class="form-group col-12">
                        <label for="memberDropdown">Member</label>
                        <select multiple name="member[]" id="memberDropdown" class="form-control">
                            <option value="">Select Member</option>
                            <?php foreach ($staff_info as $staff) { ?>
                                <option value="<?= $staff['user_id']; ?>" <?= (isset($assigned_to) && in_array($staff['user_id'], $assigned_to)) ? 'selected' : ''; ?>>
                                    <?= $staff['first_name'] . ' ' . $staff['last_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Note -->
                    <div class="form-group col-12">
                        <label for="description" class="control-label">Note</label>
                        <textarea id="description" name="note" class="form-control" placeholder="Enter Note" rows="3"><?= $timelog_data['timelogs_memo']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer">
        <a class="btn btn-danger" href="<?= base_url('erp/project-detail/' . uencode($timelog_data['project_id'])); ?>">Back</a>
        <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff;">Update</button>
    </div>
</form>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#memberDropdown').select2({
            placeholder: "Select Member", // Placeholder text
            allowClear: true, // Allow clearing the selection
            width: '100%' // Full width
        });
    });
</script>