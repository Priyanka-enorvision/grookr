<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">

<div class="modal-header">
    <h5 class="modal-title" id="addLeadModalLabel">Edit Account Details</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 0;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="addLeadForm" action="<?= base_url('erp/update-client-account/' . $result['account_id']); ?>" method="POST" style="margin: 0;">
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <div class="modal-body">
        <div class="form-group">
            <label for="name">Account Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="account_name" id="name" required placeholder="Enter Account Name" value="<?= $result['account_name']; ?>">
        </div>

        <div class="form-group">
            <label for="office_address">Office Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="office_address" id="office_address" required placeholder="Enter Office Address" value="<?= $result['office_address']; ?>">
        </div>

        <div class="form-group">
            <label for="pincode">Pincode <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="pincode" id="pincode" required placeholder="Enter Pincode" minlength="6" maxlength="6" value="<?= $result['pincode']; ?>">
        </div>

        <div class="form-group">
            <label for="gst_no">GST No <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="gst_no" id="gst_no" required placeholder="Enter GST No" minlength="15" maxlength="15" value="<?= $result['gst_no']; ?>">
        </div>

        <div class="form-group">
            <label for="pan_no">PAN Card No <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="pan_no" id="pan_no" required placeholder="Enter PAN Card No" minlength="10" maxlength="10" value="<?= $result['pan_card_no']; ?>">
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
    function setMinDate() {
        const today = new Date();
        const dd = String(today.getDate()).padStart(2, '0');
        const mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
        const yyyy = today.getFullYear();
        const formattedDate = `${yyyy}-${mm}-${dd}`;

        // Assuming you are using this for a date input
        const followUpInput = document.getElementById('next_follow_up');
        if (followUpInput) {
            followUpInput.setAttribute('min', formattedDate);
        }
    }

    // Set minimum date on window load
    window.onload = setMinDate;
</script>