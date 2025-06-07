<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\Form_model;
use App\Models\OpportunityModel;

// Initialize models
$systemModel = new SystemModel();
$usersModel = new UsersModel();
$formModel = new Form_model();
$opportunityModel = new OpportunityModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');

// Check user authentication
if (!$usession || !isset($usession['sup_user_id'])) {
    throw new \RuntimeException('User not authenticated');
}

$xin_system = $systemModel->where('setting_id', 1)->first();
$user_info = $usersModel->where('user_id', $usession['sup_user_id'])->first();

if (!$user_info) {
    throw new \RuntimeException('User not found');
}

$username = $user_info['username'];
$get_web_leads = [];
$opportunityList = [];

try {
    // Get leads based on username
    if ($username === "hbfdirect001") {
        $get_web_leads = $formModel->orderBy('id DESC, created_at ASC')
                          ->where('request', 'https://hbfdirect.com/')
                          ->findAll();
        
        $opportunityList = $opportunityModel->where('company_id', $user_info['company_id'])
                                          ->orderBy('id', 'ASC')
                                          ->findAll();
    } elseif ($username == "indusexperts001") {
        
        $get_web_leads = $formModel->orderBy('id DESC, created_at ASC')
                                  ->where('request', 'https://www.indusexperts.com/')
                                  ->findAll();
        
        if (!empty($get_web_leads)) {
            $opportunityList = $opportunityModel->where('id', $get_web_leads[0]['opportunity_id'])
                                              ->orderBy('id', 'ASC')
                                              ->findAll();
        }
    }
} catch (\Exception $e) {
    log_message('error', 'Error fetching leads: ' . $e->getMessage());
}


?>


<?php if ($session->get('unauthorized_module')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?= esc($session->get('unauthorized_module')); ?>
    </div>
<?php endif; ?>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<div class="card user-profile-list">
    <div class="card-header">
        <h5><?= lang('Main.xin_list_all'); ?> Web Leads</h5>
        <div class="card-header-right">
            <?php if (in_array('web_leads_2', staff_role_resource()) || $user_info['user_type'] == 'super_user'): ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="box-datatable table-responsive">
            <table id="webLeadsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th><?= lang('Main.xin_name'); ?></th>
                        <th><?= lang('Main.xin_contact_number'); ?></th>
                        <th><?= lang('Main.xin_email'); ?></th>
                        <th><?= lang('Main.xin_description'); ?></th>
                        <th><?= lang('Main.xin_created_at'); ?></th>
                        <th><?php echo "Status"; ?></th>
                        <th><?php echo "Source"; ?></th>
                        <th><?= lang('Main.xin_lead_status'); ?></th>
                        <th><?= lang('Main.xin_action'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($get_web_leads as $index => $lead): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>

                            <td>
                                <a href="<?= base_url('erp/web-lead-detail/' . uencode($lead['id'])); ?>">
                                    <?= esc($lead['name']); ?>
                                </a>
                            </td>
                            <td><?= esc($lead['contact']); ?></td>
                            <td><?= esc($lead['email']); ?></td>
                            <td><?= esc(substr($lead['description'], 0, 20)) . '...'; ?></td>

                            <td><?= esc(date('Y-m-d H:i:s', strtotime($lead['created_at']))); ?></td>
                            <td>
                                <span class="badge badge-light-<?= $lead['status'] == 0 ? 'primary' : 'success'; ?>">
                                    <?= lang('Main.xin_' . ($lead['status'] == 0 ? 'hot' : 'cold')); ?>
                                </span>
                            </td>
                            <td><?= esc($lead['source']); ?></td>
                            <td>
                                <span class="badge badge-light-<?php 
                                    if ($lead['lead_status'] == 0) {
                                        echo 'primary';
                                    } elseif ($lead['lead_status'] == 1) {
                                        echo 'success';
                                    } else {
                                        echo 'danger';
                                    }
                                    ?>">
                                    <?= $lead['lead_status'] == 0 ? 'lead' : ($lead['lead_status'] == 1 ? 'client' : 'unknown'); ?>
                                </span>
                            </td>
                            
                            <td>
                                <span data-toggle="tooltip" data-placement="top" title="<?= lang('Main.xin_edit'); ?>">
                                    <a href="<?= base_url('erp/web-lead-detail/' . uencode($lead['id'])); ?>" class="btn icon-btn btn-sm btn-light-primary">
                                        <i class="feather icon-edit"></i>
                                    </a>
                                </span>
                                <?php if ($lead['lead_status'] == 0): ?>
                                    <!-- <span data-toggle="tooltip" data-placement="top" title="<?= lang('Main.xin_change_to_client'); ?>">
                                        <button type="button" class="btn icon-btn btn-sm btn-light-info waves-effect waves-light view-modal" data-toggle="modal" data-target=".view-modal-client" data-field_id="<?= uencode($lead['id']); ?>">
                                            <i class="feather icon-shuffle"></i>
                                        </button>
                                    </span> -->
                                <?php endif; ?>
                                <span data-toggle="tooltip" data-placement="top" title="<?= lang('Main.xin_delete'); ?>">
                                    <button type="button" class="btn icon-btn btn-sm btn-light-danger waves-effect waves-light delete" data-toggle="modal" data-target=".delete-modal" data-record-id="<?= uencode($lead['id']); ?>">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
$(document).ready(function() {
    
    $('#webLeadsTable').DataTable();

    $('.view-modal-client').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data('field_id');
		var modal = $(this);
	$.ajax({
		url :  main_url+"dashboard/read_web_leads",
		type: "GET",
		data: 'jd=1&type=view_lead&field_id='+field_id,
		success: function (response) {
			if(response) {
				$("#ajax_view_modal").html(response);
			}
		}
		});
	});
   

    $(document).on("click", ".delete", function() {
        const recordId = $(this).data('record-id');
        $('input[name=_token]').val(recordId);
        $('#delete_record').attr('action', main_url + 'dashboard/delete_web_lead');
    });

    $('#delete_record').submit(function(e) {
        e.preventDefault(); 

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    toastr.success(response.result);
                    window.location.href = '<?= site_url('erp/web-leads-list'); ?>'; 
                } else {
                    toastr.error(response.error); 
                }
            },
            error: function(xhr, status, error) {
                console.error("Error deleting lead: ", error);
                toastr.error('An error occurred while deleting the lead.'); 
            }
        });
    });
});
</script>

<?php if($session->getFlashdata('result')): ?>
  <script>
    $(document).ready(function() {
      toastr.success("<?= $session->getFlashdata('result'); ?>");
    });
  </script>
<?php endif; ?>

<?php if($session->getFlashdata('error')): ?>
  <script>
    $(document).ready(function() {
      toastr.error("<?= $session->getFlashdata('error'); ?>");
    });
  </script>
<?php endif; ?>
