<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<div class="modal-header" style="display: block !important;">
	<h5 class="modal-title" id="addLeadModalLabel">Edit Department Information
	</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 0px;">
		<span aria-hidden="true">&times;</span>
	</button>
	<p>We need below required information to update this record.</p>

</div>
<form id="editFollowupForm" action="<?= base_url('erp/update-follow-up/'. $result['followup_id']); ?>" method="POST">
    <!-- Add CSRF Token -->
    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    
    <div class="modal-body">
        <div class="form-group">
            <label for="next_follow_up">Next Follow Up</label>
            <span class="text-danger">*</span>
            <input type="date" class="form-control" name="next_follow_up" id="next_follow_up" required
                value="<?= date('Y-m-d', strtotime($result['next_followup'])) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <span class="text-danger">*</span>
            <textarea class="form-control" name="description" id="description" required><?= esc($result['description']) ?></textarea>
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
		document.getElementById('next_follow_up').setAttribute('min', formattedDate);
	}
	window.onload = setMinDate;
</script>