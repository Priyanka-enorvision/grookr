<div class="modal-header bg-primary">
    <h5 class="modal-title text-white" id="taxDeclarationModalLabel">Edit Investment</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<form action="<?= base_url('erp/update-investment/' . $result['investment_id']); ?>" method="post">
    <?= csrf_field(); ?>
    <div class="modal-body">
        <!-- Investment Name Field -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="investment_name">Investment Name <span class="text-danger">*</span></label>
                    <input type="text" name="investment_name" id="investment_name" class="form-control" placeholder="Enter investment name"
                        value="<?= $result['investment_name']; ?>">
                </div>
            </div>
        </div>
        <!-- Section Field -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="section">Section <span class="text-danger">*</span></label>
                    <select id="section" name="section" class="form-control" required>
                        <option value="" disabled <?= empty($result['section']) ? 'selected' : ''; ?>>Select Section</option>
                        <option value="80C" <?= $result['section'] == '80C' ? 'selected' : ''; ?>>80C</option>
                        <option value="80D" <?= $result['section'] == '80D' ? 'selected' : ''; ?>>80D</option>
                        <option value="80E" <?= $result['section'] == '80E' ? 'selected' : ''; ?>>80E</option>
                        <option value="80G" <?= $result['section'] == '80G' ? 'selected' : ''; ?>>80G</option>
                        <option value="80TTA" <?= $result['section'] == '80TTA' ? 'selected' : ''; ?>>80TTA</option>
                        <option value="80GG" <?= $result['section'] == '80GG' ? 'selected' : ''; ?>>80GG</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Maximum Limit Field -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="maximum_limit">Maximum Limit (₹) <span class="text-danger">*</span></label>
                    <input type="text" id="maximum_limit" name="maximum_limit" class="form-control" placeholder="Enter maximum limit"
                        required value="<?= $result['limit_amount']; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn" style="background-color: #2989d8; color:white;">Update</button>
    </div>
</form>