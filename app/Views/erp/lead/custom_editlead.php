<?php

use App\Models\LeadOptions;

$LeadOptions = new LeadOptions();
$leadOption = $LeadOptions->where('lead_config_id', $result['id'])->first();

$optionsArray = isset($leadOption['options']) ? json_decode($leadOption['options'], true) : [];

$optionValues = [];
if (!empty($optionsArray)) {
    foreach ($optionsArray as $option) {
        $optionValues[] = $option['value'];
    }
}

$tagifyValue = implode(',', $optionValues);

?>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<div class="modal-header">
    <h5 class="modal-title" id="addLeadModalLabel">Edit Details</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form id="addLeadForm" action="<?= base_url('erp/Lead-config-updatelead/' . $result['id']); ?>" method="POST">
    <div class="modal-body">

        <?= csrf_field() ?>
        <div class="form-group">
            <label for="leadName">Field Label</label>
            <span class="text-danger">*</span>
            <input type="text" class="form-control" id="leadName" name="field_name" required placeholder="Enter Label Name"
                value="<?= $result['column_name']; ?>" onkeypress="return onlyAlphabet(event)">
        </div>

        <div class="form-group">
            <label for="dataType">Type</label>
            <span class="text-danger">*</span>
            <select id="inputType" name="input_Type" required class="form-control">
                <option value="">Choose Input Type</option>
                <option value="text" <?= $result['type'] == 'text' ? 'selected' : ''; ?>>Text</option>
                <option value="number" <?= $result['type'] == 'number' ? 'selected' : ''; ?>>Number</option>
                <option value="email" <?= $result['type'] == 'email' ? 'selected' : ''; ?>>Email</option>
                <option value="password" <?= $result['type'] == 'password' ? 'selected' : ''; ?>>Password</option>
                <option value="file" <?= $result['type'] == 'file' ? 'selected' : ''; ?>>File</option>
                <option value="date" <?= $result['type'] == 'date' ? 'selected' : ''; ?>>Date</option>
                <option value="time" <?= $result['type'] == 'time' ? 'selected' : ''; ?>>Time</option>
                <option value="select" <?= $result['type'] == 'select' ? 'selected' : ''; ?>>Select (Dropdown)</option>
            </select>
        </div>


        <div class="form-group" id="optionsInputField" style="<?= $result['type'] == 'select' ? '' : 'display:none;' ?>">
            <label for="options">Add Options </label>
            <input type="text" id="options" name="options" class="form-control" placeholder="Option1, Option2, Option3"
                value="<?= esc($tagifyValue) ?>">
        </div>



        <div class="form-group">
            <label for="isRequired">Is Required</label>
            <span class="text-danger">*</span>
            <select class="form-control" id="isRequired" name="is_required" required>
                <option value="">Select</option>
                <option value="1" <?= $result['is_required'] == '1' ? 'selected' : ''; ?>>True</option>
                <option value="0" <?= $result['is_required'] == '0' ? 'selected' : ''; ?>>False</option>
            </select>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" style="background-color: blue !important;">Update</button>
    </div>
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    $(document).ready(function() {
        var input = document.querySelector('input[name=options]');
        var tagify = new Tagify(input);

        var existingTags = input.value.split(',');
        tagify.addTags(existingTags);

        function toggleOptionsField() {
            var inputType = $('#inputType').val(); // Get the selected input type
            var optionsField = $('#optionsInputField'); // Get the options input field container

            if (inputType === 'select') {
                optionsField.show();
            } else {
                optionsField.hide();
            }
        }
        toggleOptionsField();
        $('#inputType').on('change', function() {
            toggleOptionsField();
        });
    });
</script>