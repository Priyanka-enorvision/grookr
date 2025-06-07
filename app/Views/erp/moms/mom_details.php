<?php
use CodeIgniter\I18n\Time;
use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\TasknotesModel;
use App\Models\TaskfilesModel;
use App\Models\TrackgoalsModel;
use App\Models\MomdiscussionModel;
use App\Models\MomsModel;
use App\Models\ProjectsModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();
$TasknotesModel = new TasknotesModel();
$ConstantsModel = new ConstantsModel();
$TaskfilesModel = new TaskfilesModel();
$TrackgoalsModel = new TrackgoalsModel();
$MomdiscussionModel = new MomdiscussionModel();
$MomsModel = new MomsModel();
$ProjectsModel = new ProjectsModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$request = \Config\Services::request();
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();
$request = \Config\Services::request();

$segment_id = $ifield_id;
$mom_id = $ifield_id;;
// $segment_id = $request->uri->getSegment(3);
// $mom_id = udecode($segment_id);

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if($user_info['user_type'] == 'staff'){
	$mom_data = $MomsModel->where('company_id',$user_info['company_id'])->where('id', $mom_id)->first();
	$staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
	$track_goals = $TrackgoalsModel->where('company_id',$user_info['company_id'])->orderBy('tracking_id', 'ASC')->findAll();
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();
} else {
	$mom_data = $MomsModel->where('company_id',$user_info['company_id'])->where('id', $mom_id)->first();
	$staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
	$track_goals = $TrackgoalsModel->where('company_id',$user_info['company_id'])->orderBy('tracking_id', 'ASC')->findAll();
  $staff_info = $UsersModel->where('company_id', $user_info['company_id'])->where('user_type','staff')->findAll();
  $projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();
}


// task discussion
$mom_discussion = $MomdiscussionModel->where('mom_id', $mom_id)->orderBy('id', 'ASC')->findAll();
// get type||variable
$get_type = $request->getVar('type',FILTER_SANITIZE_STRING);

?>

<div class="row">
 
  <div class="col-lg-12">
    <div class="bg-light card mb-2">
      <div class="card-body">
        <ul class="nav nav-pills mb-0">
          <li class="nav-item m-r-5"> <a href="#pills-overview" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Main.xin_overview');?>
            </button>
            </a> </li>
          <?php if(in_array('mom_3',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <li class="nav-item m-r-5"> <a href="#pills-edit" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Main.xin_edit');?>
            </button>
            </a> </li>
          <?php } ?>
          <?php if(in_array('mom_4',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <!-- <li class="nav-item m-r-5"> <a href="#pills-discussion" data-toggle="tab" aria-expanded="false" class="">
            <button type="button" class="btn btn-shadow btn-secondary text-uppercase">
            <?= lang('Mom.xin_mom_discussion');?>
            </button>
            </a> </li> -->
          <?php } ?>
        </ul>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5><i class="feather icon-lock mr-1"></i>
          <?= lang('Mom.xin_mom');?>
          :
          <?= $mom_data['title'];?>
        </h5>
      </div>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade <?php if($get_type==''):?>show active<?php endif;?>" id="pills-overview" role="tabpanel" aria-labelledby="pills-overview-tab">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table m-b-0 f-14 b-solid requid-table">
                <tbody class="text-muted">
                  <tr>
                    <td><?php echo lang('Dashboard.xin_title');?></td>
                    <td><?= $mom_data['title'];?></td>
                  </tr>
                  <tr>
                    <td><?php echo lang('Mom.xin_meeting_date');?></td>
                    <td><i class="far fa-calendar-alt"></i>&nbsp;
                      <?= $mom_data['meeting_date'];?></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="m-b-20 m-t-20">
              <h6><?php echo lang('Main.xin_associated_goals');?></h6>
              <hr>
              <div class="table-responsive">
              <table class="table table-borderless">
                <tbody class="text-muted">
                <?php $gi =1;foreach($track_goals as $track_goal) {?>
				        <?php $tracking_type = $ConstantsModel->where('constants_id',$track_goal['tracking_type_id'])->first(); ?>
                <tr>
                  <td><a target="_blank" href="<?= site_url('erp/goal-details/').uencode($track_goal['tracking_id']);?>"><?= $tracking_type['category_name'] ?></a></td>
                </tr>
                <?php $gi++; } ?>
                </tbody>
              </table>
            </div>
            </div>
            <div class="m-b-30 m-t-15">
              <h6><?php echo lang('Main.xin_summary');?></h6>
              <hr>
              <?= $mom_data['summary'];?>
            </div>
            <div class="m-b-30 m-t-15">
              <h6><?php echo lang('Main.xin_description');?></h6>
              <hr>
              <?= html_entity_decode($mom_data['description']);?>
            </div>
          </div>
        </div>
        
        <?php if(in_array('mom_3',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <div class="tab-pane fade <?php if($get_type=='edit'):?>show active<?php endif;?>" id="pills-edit" role="tabpanel" aria-labelledby="pills-edit-tab">
          <?php $attributes = array('name' => 'update_mom', 'id' => 'update_mom', 'autocomplete' => 'off');?>
          <?php $hidden = array('token' => $segment_id);?>
          <?php echo form_open('erp/update-mom', $attributes, $hidden);?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="title"><?php echo lang('Dashboard.xin_title');?> <span class="text-danger">*</span></label>
                  <input class="form-control" placeholder="<?php echo lang('Dashboard.xin_title');?>" name="title" type="text" value="<?= $mom_data['title'];?>">
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label for="meeting_date"><?php echo lang('Mom.xin_meeting_date');?>  <span class="text-danger">*</span></label>
                  <input class="form-control" placeholder="<?php echo lang('Mom.xin_meeting_date');?> " name="meeting_date" type="text" value="<?= htmlspecialchars($mom_data['meeting_date']); ?>">
                </div>
              </div>
              <?php $project_to = explode(',',$mom_data['project_id']); ?>
              <div class="col-md-6">
                <div class="form-group" id="project_ajax">
                  <label for="project_ajax" class="control-label"><?php echo lang('Projects.xin_project');?> <span class="text-danger">*</span></label>
                  <input type="hidden" value="0" name="project_id[]" />
                  <select class="form-control" multiple name="project_id[]" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project');?>">
                    <option value=""></option>
                    <?php foreach($projects as $iprojects) {?>
                    <option value="<?= $iprojects['project_id']?>" <?php if(in_array($iprojects['project_id'],$project_to)):?> selected="selected"<?php endif;?>>
                    <?= $iprojects['title'] ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <?php $assigned_to = explode(',',$mom_data['assigned_to']); ?>
              <div class="col-md-6">
                <div class="form-group" id="employee_ajax">
                  <label for="employee"><?php echo lang('Projects.xin_project_users');?></label>
                  <input type="hidden" value="0" name="assigned_to[]" />
                  <select multiple name="assigned_to[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Projects.xin_project_users');?>">
                    <option value=""></option>
                    <?php foreach($staff_info as $staff) {?>
                    <option value="<?= $staff['user_id']?>" <?php if(in_array($staff['user_id'],$assigned_to)):?> selected="selected"<?php endif;?>>
                    <?= $staff['first_name'].' '.$staff['last_name'] ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <input type="hidden" value="0" name="associated_goals[]" />
              <div class="col-md-12">
                <div class="form-group">
                  <label for="employee"><?php echo lang('Main.xin_associated_goals');?></label>
                  <select multiple name="associated_goals[]" class="form-control" data-plugin="select_hrm" data-placeholder="<?php echo lang('Main.xin_associated_goals');?>">
                    <option value=""></option>
                    <?php foreach($track_goals as $track_goal) {?>
                    <?php $tracking_type = $ConstantsModel->where('constants_id',$track_goal['tracking_type_id'])->first(); ?>
                    <option value="<?= $tracking_type['constants_id']?>"
                      selected="selected">
                    <?= $tracking_type['category_name'] ?>
                    </option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="summary"><?php echo lang('Main.xin_summary');?> <span class="text-danger">*</span></label>
                  <textarea class="form-control" placeholder="<?php echo lang('Main.xin_summary');?>" name="summary" cols="30" rows="2"><?= $mom_data['summary'];?></textarea>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="description"><?php echo lang('Main.xin_description');?></label>
                  <textarea class="form-control editor" placeholder="<?php echo lang('Main.xin_description');?>" rows="5" name="description"><?= $mom_data['description'];?>
                  </textarea>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary">
            <?= lang('Mom.xin_update_mom');?>
            </button>
          </div>
          <?= form_close(); ?>
        </div>
        <?php } ?>
        <?php if(in_array('mom_4',staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <!-- <div class="tab-pane fade <?php if($get_type=='discussion'):?>show active<?php endif;?>" id="pills-discussion" role="tabpanel" aria-labelledby="pills-discussion-tab">
          <div class="card-body task-comment">
            <ul class="media-list p-0">
              <?php $tn=0; foreach($mom_discussion as $_discussion){ ?>
              <?php $time = Time::parse($_discussion['created_at']); ?>
              <?php $disc_user = $UsersModel->where('user_id', $_discussion['employee_id'])->first();?>
              <li class="media" id="discussion_option_id_<?= $_discussion['mom_discussion_id'];?>">
                <div class="media-left mr-3"> <a href="#!"> <img class="img-fluid media-object img-radius comment-img" src="<?= staff_profile_photo($_discussion['employee_id']);?>" alt=""> </a> </div>
                <div class="media-body">
                  <h6 class="media-heading txt-primary">
                    <?= $disc_user['first_name'].' '.$disc_user['last_name'];?>
                    <span class="f-12 text-muted ml-1">
                    <?= time_ago($_discussion['created_at']);?>
                    </span></h6>
                  <?= html_entity_decode($_discussion['discussion_text']);?>
                  <div class="mt-2"><a href="#!" data-field="<?= $_discussion['mom_discussion_id'];?>" class="delete_discussion m-r-10 text-secondary"><i class="fas fa-trash-alt text-danger mr-2"></i>
                    <?= lang('Main.xin_delete');?>
                    </a></div>
                </div>
              </li>
              <hr class="discussion_option_id_<?= $_discussion['mom_discussion_id'];?>">
              <?php } ?>
            </ul>
            <?php $attributes = array('name' => 'add_discussion', 'id' => 'add_discussion', 'autocomplete' => 'off');?>
            <?php $hidden = array('token' => $segment_id);?>
            <?= form_open('erp/moms/add_mom_discussion', $attributes, $hidden);?>
            <div class="input-group mb-3">
              <textarea class="form-control editor" name="description"><?= lang('Projects.xin_enter_discussion_msg');?>...</textarea>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
              <?= lang('Main.xin_add');?>
              </button>
            </div>
            <?= form_close(); ?>
          </div>
        </div> -->
        <?php } ?>
    
      </div>
    </div>
  </div>
</div>
