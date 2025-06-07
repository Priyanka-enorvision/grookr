<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\AssetsModel;
use App\Models\AssetscategoryModel;
use App\Models\ConstantsModel;

$session = \Config\Services::session();
$usession = $session->get('sup_username');

$UsersModel = new UsersModel();
$RolesModel = new RolesModel();
$ConstantsModel = new ConstantsModel();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

?>
<?php if (in_array('indicator1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

	<hr class="border-light m-0 mb-3">
	<div class="row m-b-1 animated fadeInRight">
		<div class="col-xl-12 col-lg-12">
			<div class="row mb-4">
				<?php if (in_array('competency2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
					<div class="col-md-4">
						<div class="card">
							<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
										<?= lang('Main.xin_add_new'); ?>
									</strong>
								</span> </div>
							<div class="card-body">
								<?php $attributes = array('name' => 'add_competencies', 'id' => 'xin-form', 'autocomplete' => 'off'); ?>
								<?php $hidden = array('user_id' => '0'); ?>
								<?= form_open('erp/add-competencies', $attributes, $hidden); ?>
								<div class="form-group">
									<label for="name">
										<?= lang('Dashboard.xin_category'); ?>
										<span class="text-danger">*</span> </label>
									<input type="text" class="form-control" name="name"
										placeholder="<?= lang('Dashboard.xin_category'); ?>">
								</div>
							</div>
							<div class="card-footer text-right">
								<button type="submit" class="btn btn-primary"><?= lang('Main.xin_save'); ?></button>
							</div>
							<?= form_close(); ?>
						</div>
					</div>
					<?php $colmdval = 'col-md-8'; ?>
				<?php } else { ?>
					<?php $colmdval = 'col-md-12'; ?>
				<?php } ?>
				<div class="<?= $colmdval; ?>">
					<div class="card user-profile-list">
						<div class="card-header with-elements"> <span class="card-header-title mr-2"><strong>
									<?= lang('Main.xin_list_all'); ?>
								</strong>
								<?= lang('Dashboard.xin_categories'); ?>
							</span> </div>
						<div class="card-body">
							<div class="box-datatable table-responsive">
								<table class="datatables-demo table table-striped table-bordered" id="xin_table"
									style="width:100%;">
									<thead>
										<tr>
											<th><i class="fas fa-braille"></i>
												<?= lang('Dashboard.xin_category'); ?></th>
											<th> <?= lang('Main.xin_created_at'); ?></th>
											<th>Action</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>