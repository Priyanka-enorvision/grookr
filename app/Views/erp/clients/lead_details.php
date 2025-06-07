<?php

use App\Models\LeadsModel;
use App\Models\UsersModel;
use App\Models\CountryModel;
use App\Models\LeadConfigModel;
use App\Models\OpportunityModel;
use App\Models\LeadOptions;
use App\Models\AccountDetailModel;

$CountryModel = new CountryModel();
$LeadsModel = new LeadsModel();
$UsersModel = new UsersModel();
$LeadConfig = new LeadConfigModel();
$opportunityModel = new OpportunityModel();
$LeadOptionsModel = new LeadOptions();
$AccountDetails = new AccountDetailModel();


$request = \Config\Services::request();
$session = \Config\Services::session();
$usession = $session->get('sup_username');

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$opportunityList = $opportunityModel->where(['company_id' => $user_info['company_id']])->orderBy('id', 'ASC')->findAll();
$leadFields = $LeadConfig->groupStart()
  ->where('company_id', $user_info['company_id'])
  ->orWhere('company_id', null)
  ->groupEnd()
  ->orderBy('id', 'ASC')
  ->findAll();

// $segment_id = $request->uri->getSegment(3);
// $lead_id = udecode($segment_id);
$segment_id = $lead_id;

$result = $leadData;
// $result = $LeadsModel->where('lead_id', $lead_id)->first();

$status = $result['status'];
$status_label = "";

$account_list = $AccountDetails->where(['company_id' => $user_info['company_id'], 'lead_id' => $lead_id])->orderBy('account_id', 'ASC')->findAll();


if ($status  == 1) {
  $status = '<span class="badge badge-light-primary"><em class="icon ni ni-check-circle"></em> ' . lang('Dashboard.xin_lead') . '</span>';
  $status_label = '<i class="fas fa-certificate text-primary bg-icon"></i><i class="fas fa-check front-icon text-white"></i>';
}


?>
<?php if ($result['profile_image'] != '' || $result['profile_image'] != 'no-file') { ?>
  <?php
  $imageProperties = [
    'src' => base_url() . 'uploads/clients/thumb/' . $result['profile_image'],
    'class' => 'd-block img-radius img-fluid wid-80',
    'width' => '50',
    'height' => '50',
  ];
?>
<?php } ?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<div class="row">
  <div class="col-lg-4">
    <div class="card user-card user-card-1">
      <div class="card-body pb-0">
        <div class="float-right">
          <?= $status; ?>
        </div>
        <input type="hidden" id="client_id" value="<?= $segment_id; ?>" />
        <div class="media user-about-block align-items-center mt-0 mb-3">
          <div class="position-relative d-inline-block">
              
              <?php
              $imagePath = $imageProperties['src'] ?? null;

              $dummyImage = base_url('uploads/leads/dummy-image.jpg');
              $imageSrc = (isset($imagePath) && file_exists(FCPATH . ltrim(parse_url($imagePath, PHP_URL_PATH), '/')))
                ? img($imageProperties)
                : img(['src' => $dummyImage, 'alt' => 'Default Image', 'width' => '50', 'height' => '50', 'class' => 'd-block img-radius img-fluid wid-80']);
              ?>

              <?= $imageSrc; ?>

            <div class="certificated-badge">
              <?= $status_label; ?>
            </div>
          </div>
          <div class="media-body ml-3">
            <h6 class="mb-1">
              <?= $leadData['name']; ?>
            </h6>
          </div>
        </div>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item"> <span class="f-w-500"><i class="feather icon-mail m-r-10"></i>
            <?= lang('Main.xin_email'); ?>
          </span> <a href="mailto:<?= $leadData['email']; ?>" class="float-right text-body">
            <?= $leadData['email']; ?>
          </a> </li>
        <li class="list-group-item"> <span class="f-w-500"><i class="feather icon-phone-call m-r-10"></i>
            <?= lang('Main.xin_contact_number'); ?>
          </span> <a href="#" class="float-right text-body">
            <?= $leadData['contact']; ?>
          </a> </li>
      </ul>

      <div class="nav flex-column nav-pills list-group list-group-flush list-pills" id="user-set-tab" role="tablist"
        aria-orientation="vertical"> 
        <a class="nav-link list-group-item list-group-item-action active"
          id="user-basic-tab" data-toggle="pill" href="#user-edit-account" role="tab" aria-controls="user-edit-account"
          aria-selected="false"> <span class="f-w-500"><i class="feather icon-user m-r-10 h5 "></i>
            <?= lang('Main.xin_personal_info'); ?>
          </span> <span class="float-right"><i class="feather icon-chevron-right"></i></span> </a>
        <a class="nav-link list-group-item list-group-item-action" id="user-profile-picture-tab" data-toggle="pill"
          href="#user-profile-picture" role="tab" aria-controls="user-profile-picture" aria-selected="false">
          <span class="f-w-500"> <i class="feather icon-settings m-r-10 h5"></i>
            Account Details </span> <span class="float-right"><i class="feather icon-chevron-right"></i></span> </a>

        <a class="nav-link list-group-item list-group-item-action" id="user-follow_up-tab" data-toggle="pill"
          href="#user-follow_up" role="tab" aria-controls="user-follow_up" aria-selected="false"> <span
            class="f-w-500"><i class="feather icon-layers m-r-10 h5 "></i>
            <?= lang('Main.xin_follow_up'); ?>
          </span> <span class="float-right"><i class="feather icon-chevron-right"></i></span> </a>
      </div>
    </div>
  </div>
  <div class="col-xl-8 col-lg-12">
    <div class="tab-content" id="pills-tabContent">
      <div class="tab-pane fade active show" id="user-edit-account" role="tabpanel"
        aria-labelledby="user-edit-account-tab">
        <div class="card">
          <?php $attributes = array('name' => 'update_lead', 'id' => 'update_lead', 'autocomplete' => 'off', 'enctype' => 'multipart/form-data', 'class' => 'm-b-1'); ?>
          <?php $hidden = array('_method' => 'EDIT', 'token' => $segment_id); ?>
          <?= form_open('erp/update-lead', $attributes, $hidden); ?>
          <div class="card-header">
            <h5><i data-feather="user" class="icon-svg-primary wid-20"></i><span class="p-l-5">
                <?= lang('Main.xin_personal_info'); ?>
              </span></h5>
          </div>

          <div class="card-body">

            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="opportunity_name">Opportunity </label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                    </div>
                    <select class="form-control" name="opportunity_id" id="opportunity_id" required>
                      <option value="">Select Opportunity Name</option>
                      <?php foreach ($opportunityList as $list): ?>
                        <option value="<?= $list['id']; ?>" <?= ($leadData['opportunity_id'] == $list['id']) ? 'selected' : ''; ?>><?= $list['opportunity_name']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
              <!-- Dynamic Fields -->
              <?php foreach ($leadFields as $index => $field): ?>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="<?= esc($field['column_name']); ?>">
                      <?= esc($field['column_name']); ?>
                      <?php if ($field['is_required']): ?>
                        <span class="text-danger">*</span>
                      <?php endif; ?>
                    </label>

                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                      </div>

                      <?php if ($field['type'] == 'select'): ?>
                        <?php
                        if (!isset($field['options'])) {
                          $optionsData = $LeadOptionsModel->where('lead_config_id', $field['id'])->first();
                          $field['options'] = !empty($optionsData['options']) ? json_decode($optionsData['options'], true) : [];
                        }
                        ?>
                        <select class="form-control"
                          name="<?= strtolower(str_replace(' ', '_', esc($field['column_name']))); ?>"
                          id="<?= esc($field['column_name']); ?>" <?= $field['is_required'] ? 'required' : ''; ?>>
                          <option value=""><?= 'Select ' . esc($field['column_name']); ?></option>
                          <?php foreach ($field['options'] as $option): ?>
                            <option value="<?= esc($option['value']); ?>" <?= (isset($leadData[strtolower(str_replace(' ', '_', $field['column_name']))]) && $leadData[strtolower(str_replace(' ', '_', $field['column_name']))] == $option['value']) ? 'selected' : ''; ?>>
                              <?= esc($option['value']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      <?php else: ?>
                        <input class="form-control" placeholder="<?= 'Enter ' . esc($field['column_name']); ?>"
                          name="<?= rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_'); ?>"
                          id="<?= esc($field['column_name']); ?>" type="<?= esc($field['type']); ?>"
                          value="<?= isset($leadData[rtrim(strtolower(str_replace(' ', '_', $field['column_name'])), '_')])
                            ? esc($leadData[rtrim(strtolower(str_replace(' ', '_', $field['column_name'])), '_')]) : ''; ?>" <?= $field['is_required'] ? 'required' : ''; ?>>

                        <?php if ($field['type'] == 'file'): ?>
                          <input type="hidden" class="form-control" id="old_image" name="old_image"
                            value="<?= esc($leadData[rtrim(strtolower(str_replace(' ', '_', $field['column_name'])), '_')]); ?>"
                            readonly>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
              <!-- Lead Status Dropdown -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lead_status">Lead Status</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-flag"></i></span>
                    </div>
                    <select class="form-control" name="lead_status" id="lead_status" required>
                      <option value="">Select Lead Status</option>
                      <option value="hot" <?= ($leadData['lead_status'] == 'hot') ? 'selected' : ''; ?>>Hot</option>
                      <option value="cold" <?= ($leadData['lead_status'] == 'cold') ? 'selected' : ''; ?>>Cold</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- Source Name Dropdown -->
              <div class="col-md-6">
                <div class="form-group">
                  <label for="source_name">Source Name</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-share-alt"></i></span>
                    </div>
                    <select class="form-control" name="source_name" id="source_name" required>
                      <option value="">Select Source Name</option>
                      <option value="instagram" <?= ($leadData['sources_name'] == 'instagram') ? 'selected' : ''; ?>>
                        Instagram</option>
                      <option value="facebook" <?= ($leadData['sources_name'] == 'facebook') ? 'selected' : ''; ?>>Facebook
                      </option>
                      <option value="twitter" <?= ($leadData['sources_name'] == 'twitter') ? 'selected' : ''; ?>>Twitter
                      </option>
                      <option value="google" <?= ($leadData['sources_name'] == 'google') ? 'selected' : ''; ?>>Google
                      </option>
                      <!-- Add more options as needed -->
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                Update
              </button>
            </div>
          </div>
          <?= form_close(); ?>
        </div>
      </div>


      <div class="tab-pane fade" id="user-profile-picture" role="tabpanel" aria-labelledby="user-profile-picture-tab">
        <div class="card">
          <div class="card-header">
            <h5><i class="feather icon-settings m-r-10 h5"></i> <span class="p-l-5">Account Details</span></h5>
          </div>

          <!-- Account Details Table -->
          <div class="card-body pb-2">

            <!-- Add Button -->
            <div class="text-right mb-3">
              <button id="toggle-form" class="btn btn-success">Add Details</button>
            </div>

            <!-- Add Account Form (Initially Hidden) -->
            <div id="add-account-form" style="display: none;">
              <form action="<?= base_url('erp/save_client-account-details'); ?>" method="POST">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <input type="hidden" name="lead_id" value="<?= $lead_id; ?>">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name">Account Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="account_name" id="name" required
                        placeholder="Enter Your Name">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="office_address">Office Address <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="office_address" id="office_address" required
                        placeholder="Enter Office Address">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pincode">Pincode <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="pincode" id="pincode" required
                        placeholder="Enter Pincode" minlength="6" maxlength="6">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="gst_no">GST No <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="gst_no" id="gst_no" required placeholder="GST No"
                        minlength="15" maxlength="15">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="pan_no">PAN Card No <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="pan_no" id="pan_no" required
                        placeholder="PAN Card No" minlength="10" maxlength="10">
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary">Save</button>
                  <button type="button" id="cancel-form" class="btn btn-secondary">Cancel</button>
                </div>
              </form>
            </div>

            <div class="row">
              <div class="col-md-12 box-datatable table-responsive">
                <table class="datatables-demo table table-striped " id="account_table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Account Name</th>
                      <th>Office Address</th>
                      <th>Pincode</th>
                      <th>GST No</th>
                      <th>PAN Card No</th>
                      <th>Action </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i = 1;
                    foreach ($account_list as $list) { ?>
                      <tr>
                        <td><?= $i++; ?></td>
                        <td><?= $list['account_name']; ?></td>
                        <td><?= $list['office_address']; ?></td>
                        <td><?= $list['pincode']; ?></td>
                        <td><?= $list['gst_no']; ?></td>
                        <td><?= $list['pan_card_no']; ?></td>
                        <td>
                          <a class="btn btn-primary" style="background-color: blue !important;" data-toggle="tooltip"
                            title="View Details" onclick="viewModal(<?= $list['account_id'] ?>);">
                            <i class="feather icon-edit-2 text-white"></i>
                          </a>

                          <a href="<?= base_url('erp/delete-client-account-record/' . $list['account_id']); ?>"
                            class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"
                            data-toggle="tooltip" title="Delete Item">
                            <i class="feather icon-trash-2"></i>
                          </a>
                        </td>

                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>


          </div>
        </div>
      </div>

      <script>
        document.getElementById('toggle-form').addEventListener('click', function () {
          var form = document.getElementById('add-account-form');
          if (form.style.display === 'none') {
            form.style.display = 'block';
            this.textContent = 'Hide ';
          } else {
            form.style.display = 'none';
            this.textContent = 'Add Details';
          }
        });

        document.getElementById('cancel-form').addEventListener('click', function () {
          var form = document.getElementById('add-account-form');
          form.style.display = 'none';
          document.getElementById('toggle-form').textContent = 'Add Details';
        });
      </script>




      <div class="tab-pane fade" id="user-follow_up" role="tabpanel" aria-labelledby="user-follow_up-tab">
        <div class="card user-profile-list">
          <div class="card-header with-elements"> <span class="card-header-title mr-2"><strong><i data-feather="layers"
                  class="icon-svg-primary wid-20"></i> <?php echo lang('Main.xin_follow_up'); ?></strong></span> </div>
          <div class="card-body">
            <div class="box-datatable table-responsive">
              <table class="datatables-demo table table-striped table-bordered" id="followUp_table" width="100%">
                <thead>
                  <tr>
                    <th width="200"><?php echo lang('Main.xin_next_follow_up'); ?></th>
                    <th><?php echo lang('Main.xin_description'); ?></th>
                    <th><?php echo lang('Main.xin_created_at'); ?></th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($followup as $follow) { ?>
                    <tr>
                      <td><?= $follow['next_followup']; ?></td>
                      <td><?= $follow['description']; ?></td>
                      <td><?= $follow['created_at']; ?></td>
                      <td>
                        <a class="btn btn-primary" style="background-color: blue !important;" data-toggle="tooltip"
                          title="View Details" onclick="openModal(<?= $follow['followup_id'] ?>);">
                          <i class="feather icon-edit-2 text-white"></i>
                        </a>

                        <a href="<?= base_url('erp/delete-follow/' . $follow['followup_id']); ?>"
                          class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?');"
                          data-toggle="tooltip" title="Delete Item">
                          <i class="feather icon-trash-2"></i>
                        </a>


                      </td>
                    </tr>
                  <?php } ?>
                </tbody>

              </table>
            </div>
          </div>
          <div class="card-header">
            <h5><span class="p-l-5">
                <?= lang('Main.xin_new_follow_up'); ?>
              </span></h5>
          </div>

          <?php $attributes = array('name' => 'add_followup', 'id' => 'followup_info', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
          <?php $hidden = array('_method' => 'EDIT', 'token' => $segment_id); ?>
          <?= form_open('erp/add-client-followup', $attributes, $hidden); ?>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="company_name">
                    <?= lang('Main.xin_next_follow_up'); ?>
                    <span class="text-danger">*</span> </label>
                  <div class="input-group">
                    <input class="form-control date" placeholder="<?= lang('Main.xin_next_follow_up'); ?>"
                      name="next_follow_up" type="text">
                    <div class="input-group-append"><span class="input-group-text"><i
                          class="fas fa-calendar-alt"></i></span></div>
                  </div>
                </div>
              </div>

              <div class="col-md-8">
                <div class="form-group">
                  <label for="address_1">
                    <?= lang('Main.xin_description'); ?> <span class="text-danger">*</span>
                  </label>
                  <input class="form-control" placeholder="<?= lang('Main.xin_description'); ?>" name="description"
                    type="text">
                </div>
              </div>
            </div>
            <div class="card-footer text-right">
              <button type="submit" class="btn btn-primary">
                <?= lang('Main.xin_save'); ?>
              </button>
            </div>
          </div>
          <?= form_close(); ?>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Modal Structure -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" role="dialog" aria-labelledby="viewDetailsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body" id="modalBody">
        <!-- Add your modal body content here -->
      </div>
    </div>
  </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="accountDetailsModal" tabindex="-1" role="dialog" aria-labelledby="accountDetailsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-body" id="viewBody">
        <!-- Add your modal body content here -->
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
  var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

  function openModal(id) {
    fetch(base_url + 'erp/follow-up-view/' + id) // Add base_url to the request
      .then(response => response.text())
      .then(data => {
        document.getElementById('modalBody').innerHTML = data;
        let modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
        modal.show();
      })
      .catch(error => {
        console.error('Error loading data:', error);
        alert('Failed to load data. Please try again.');
      });
  }
</script>

<script>
  var base_url = '<?= site_url(); ?>'; // Use site_url() for dynamic routes

  function viewModal(id) {
    fetch(base_url + 'erp/account-view-details/' + id) // Add base_url to the request
      .then(response => response.text())
      .then(data => {
        document.getElementById('viewBody').innerHTML = data;
        let modal = new bootstrap.Modal(document.getElementById('accountDetailsModal'));
        modal.show();
      })
      .catch(error => {
        console.error('Error loading data:', error);
        alert('Failed to load data. Please try again.');
      });
  }
</script>


<script>
  $(document).ready(function () {
    <?php if (session()->getFlashdata('error')): ?>
      toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
        timeOut: 5000
      });
    <?php endif; ?>

    <?php if (session()->getFlashdata('message')): ?>
      toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
        timeOut: 5000
      });
    <?php endif; ?>
  });
</script>
<script>
  $(document).ready(function () {
    $('#followUp_table').DataTable({
      paging: true, // Enable pagination
      searching: true, // Enable search
      ordering: true, // Enable ordering
      lengthMenu: [10, 25, 50, 100], // Options for number of records to show
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search records",
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#account_table').DataTable({
      paging: true, // Enable pagination
      searching: true, // Enable search
      ordering: true, // Enable ordering
      lengthMenu: [10, 25, 50, 100], // Options for number of records to show
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search records",
      }
    });
  });
</script>