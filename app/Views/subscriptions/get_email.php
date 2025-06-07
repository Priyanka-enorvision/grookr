<?php
use App\Models\UsersModel;

$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$company_data = $UsersModel->where('company_id', $company_id)->first();
$company_email = $company_data['email'] ?? ''; 
?>

<div class="col-md-12" id="company_email">
    <div class="form-group">
        <label for="email" class="control-label"><?= lang('Main.xin_email'); ?></label>
        <span class="text-danger">*</span>
        <input 
            type="email" 
            class="form-control" 
            name="email" 
            aria-label="<?= lang('Main.xin_email'); ?>" 
            placeholder="<?= lang('Main.xin_email'); ?>" 
            value="<?= esc($company_email); ?>" 
            disabled="disabled"
        >
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-plugin="select_hrm"]').select2({
            width: '100%',
            placeholder: $(this).attr('data-placeholder')
        });
    });
</script>
