<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<div class="modal-header">
    <h5 class="modal-title" id="editLeadModalLabel">Edit Details</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="editLeadForm" action="<?= base_url('erp/opportunity-update/' . $result['id']) ?>" method="POST">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-6">
                <label for="leadName">Opportunity Name</label>
                <span class="text-danger">*</span>
                <input type="text" class="form-control" id="leadName" name="opportunity_name" required placeholder="Enter Opportunity Name" onkeypress="return onlyAlphabet(event)"
                    value="<?= $result['opportunity_name']; ?>">
            </div>
            <div class="form-group col-6">
                <label for="opportunityStage">Opportunity Stage</label>
                <span class="text-danger">*</span>
                <select class="form-control" id="opportunityStage" name="opportunity_stage" required>
                    <option value="" disabled>Select Opportunity Stage</option>
                    <option value="prospecting" <?= isset($result['opportunity_stage']) && $result['opportunity_stage'] === 'prospecting' ? 'selected' : ''; ?>>Prospecting</option>
                    <option value="qualification" <?= isset($result['opportunity_stage']) && $result['opportunity_stage'] === 'qualification' ? 'selected' : ''; ?>>Qualification</option>
                    <option value="proposal" <?= isset($result['opportunity_stage']) && $result['opportunity_stage'] === 'proposal' ? 'selected' : ''; ?>>Proposal</option>
                    <option value="closed-won" <?= isset($result['opportunity_stage']) && $result['opportunity_stage'] === 'closed-won' ? 'selected' : ''; ?>>Closed-Won</option>
                    <option value="closed-lost" <?= isset($result['opportunity_stage']) && $result['opportunity_stage'] === 'closed-lost' ? 'selected' : ''; ?>>Closed-Lost</option>
                    <!-- Add more options as needed -->
                </select>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="expectedClosingDate">Expected Closing Date</label>
                <span class="text-danger">*</span>
                <input type="date" class="form-control" id="expectedClosingDate" name="expected_closing_date" required
                    value="<?= $result['expected_closing_date']; ?>">
            </div>
            <div class="form-group col-6">
                <label for="value">Value</label>
                <span class="text-danger">*</span>
                <input type="number" class="form-control" id="value" name="value" required placeholder="Enter Expected value"
                    value="<?= $result['value']; ?>">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="probability">Probability (%)</label>
                <span class="text-danger">*</span>
                <input type="number" class="form-control" id="probability" name="probability" required min="0" max="100" placeholder="Enter Probability"
                    value="<?= $result['probability']; ?>">
            </div>

            <div class="form-group col-6">
                <label for="users">Users</label>
                <span class="text-danger">*</span>
                <select class="form-control js-example-matcher-start" id="users" name="user_id" required>
                    <option value="" disabled selected>Select Users</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= esc($user['user_id']); ?>" <?= (esc($user['user_id']) == esc($result['user_id'])) ? 'selected' : ''; ?>>
                            <?= esc($user['first_name'] . ' ' . $user['last_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>
        <div class="row">
            <div class="form-group col-12">
                <label for="comments">Comments</label>
                <textarea class="form-control" id="comments" name="comments" rows="3" placeholder="Add any comments here..."><?= $result['comments']; ?></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" style="background-color: blue !important;">Update</button>
    </div>
</form>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>



<script>
    function setMinDate() {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const yyyy = today.getFullYear();

        const formattedDate = `${yyyy}-${mm}-${dd}`;

        document.getElementById('expectedClosingDate').setAttribute('min', formattedDate);
    }

    window.onload = setMinDate;
</script>
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
        $.each(data.children, function(idx, child) {
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

    $(document).ready(function() {
        $(".js-example-matcher-start").select2({
            matcher: matchStart, // Ensure this is the matcher you want
            placeholder: 'Select Users',
            allowClear: true,
            width: '100%' // Ensure the width is set correctly
        });
    });
</script>