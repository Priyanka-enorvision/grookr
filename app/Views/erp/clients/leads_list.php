<?php

use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\ShiftModel;
use App\Models\ConstantsModel;
use App\Models\SystemModel;
use App\Models\LeadConfigModel;
use App\Models\LeadOptions;
use App\Models\OpportunityModel;
use App\Models\Form_model;

$DepartmentModel = new DepartmentModel();
$DesignationModel = new DesignationModel();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$ConstantsModel = new ConstantsModel();
$ShiftModel = new ShiftModel();
$SystemModel = new SystemModel();
$LeadConfig = new LeadConfigModel();
$opportunityModel = new OpportunityModel();
$Form_model = new Form_model();

$LeadOptionsModel = new LeadOptions();
$session = \Config\Services::session();
$usession = $session->get('sup_username');

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$opportunityList = $opportunityModel
->where(['company_id' => $user_info['company_id']])
// ->orWhere('company_id IS NULL')
->orderBy('id', 'ASC')->findAll();




$leadFields = $LeadConfig->groupStart()
  ->where('company_id', $user_info['company_id'])
  ->orWhere('company_id', null)
  ->groupEnd()
  ->orderBy('id', 'ASC')
  ->findAll();

$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
$table_name = 'leads_' . $company_name;


$db = \Config\Database::connect();

if (!$db->tableExists($table_name)) {
  echo "<pre>";
  echo '<a href="' . site_url('erp/customization-lead') . '">This Table does not exist. Please go to Setting > Customization > Lead</a>';
  echo "</pre>";
  log_message('error', 'Table ' . $table_name . ' does not exist');
  return false;
} else {


  if ($session->has('opportunity_id')) {
    $opportunity_id = $session->get('opportunity_id');
    $builder = $db->table($table_name);
    $builder->where('opportunity_id', $opportunity_id);
    $query = $builder->get();
    $get_leadList = $query->getResult();

  } else {

    $builder = $db->table($table_name);
    $query = $builder->get();
    $get_leadList = $query->getResult();
  }


  if ($user_info['user_type'] == 'staff') {
    $departments = $DepartmentModel->where('company_id', $user_info['company_id'])->orderBy('department_id', 'ASC')->findAll();
    $designations = $DesignationModel->where('company_id', $user_info['company_id'])->orderBy('designation_id', 'ASC')->findAll();
    $office_shifts = $ShiftModel->where('company_id', $user_info['company_id'])->orderBy('office_shift_id', 'ASC')->findAll();
    $leave_types = $ConstantsModel->where('company_id', $user_info['company_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
  } else {
    $departments = $DepartmentModel->where('company_id', $usession['sup_user_id'])->orderBy('department_id', 'ASC')->findAll();
    $designations = $DesignationModel->where('company_id', $usession['sup_user_id'])->orderBy('designation_id', 'ASC')->findAll();
    $office_shifts = $ShiftModel->where('company_id', $usession['sup_user_id'])->orderBy('office_shift_id', 'ASC')->findAll();
    $leave_types = $ConstantsModel->where('company_id', $usession['sup_user_id'])->where('type', 'leave_type')->orderBy('constants_id', 'ASC')->findAll();
  }

  $roles = $RolesModel->orderBy('role_id', 'ASC')->findAll();
  $xin_system = $SystemModel->where('setting_id', 1)->first();

  $employee_id = generate_random_employeeid();
}
$get_animate = "";
?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<style>
  .row {
    display: flex !important;
  }

  .bulk-data {
    margin-left: 800px;
  }

  .row .bulk-data {
    margin-top: -600px;
  }
</style>

<?php if (in_array('leads2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
  <div id="accordion">
    <div id="add_form" class="collapse add-form <?= $get_animate; ?>" data-parent="#accordion">
      <div class="row">
        <form id="addLeadForm" action="<?= base_url('erp/clients-insert-lead'); ?>" method="POST"
          enctype="multipart/form-data">
          <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>"></div>>
          <div class="col-md-8">
            <div class="card mb-2">
              <div class="card-header">
                <h5>
                  <?= lang('Main.xin_add_new'); ?>
                  <?= lang('Dashboard.xin_lead'); ?>
                </h5>
                <div class="card-header-right">
                  <a data-toggle="collapse" href="#add_form" aria-expanded="false"
                    class="btn btn-sm waves-effect waves-light btn-primary m-0">
                    <i data-feather="minus"></i> <?= lang('Main.xin_hide'); ?>
                  </a>
                </div>
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
                          <?php foreach ($opportunityList as $list) { ?>
                            <option value="<?= $list['id']; ?>"><?= $list['opportunity_name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <!-- Dynamic Fields -->
                  <?php foreach ($leadFields as $index => &$field): ?>
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
                              name="<?= rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_'); ?>"
                              id="<?= esc($field['column_name']); ?>" <?= $field['is_required'] ? 'required' : ''; ?>>
                              <option value=""><?= 'Select ' . esc($field['column_name']); ?></option>
                              <?php foreach ($field['options'] as $option): ?>
                                <option value="<?= esc($option['value']); ?>"><?= esc($option['value']); ?></option>
                              <?php endforeach; ?>
                            </select>
                          <?php else: ?>
                            <input class="form-control" placeholder="<?= 'Enter ' . esc($field['column_name']); ?>"
                              name="<?= rtrim(strtolower(str_replace(' ', '_', esc($field['column_name']))), '_'); ?>"
                              id="<?= esc($field['column_name']); ?>" type="<?= esc($field['type']); ?>"
                              <?= $field['is_required'] ? 'required' : ''; ?>>
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
                          <option value="hot">Hot</option>
                          <option value="cold">Cold</option>
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
                          <option value="instagram">Instagram</option>
                          <option value="facebook">Facebook</option>
                          <option value="twitter">Twitter</option>
                          <option value="google">Google</option>
                          <!-- Add more options as needed -->
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary"> Save </button>
              </div>
            </div>
          </div>
        </form>


        <form action="<?= base_url('erp/add-bulk-lead'); ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
          <div class="col-md-4 bulk-data">
            <div class="card mb-2">
              <div class="card-header d-flex" style="justify-content:space-between;">
                <h5>
                  <?= lang('Main.xin_e_bulk_lead'); ?>
                </h5>

                <a download class="download-icon" id="download-link" onclick="generateAndDownloadExcel(event)"
                  style="cursor:pointer">
                  &#128190;
                </a>
              </div>
              <div class="card-body py-4">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="logo">
                        <?= lang('Main.xin_attachment'); ?>
                      </label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" name="bulk_file" id="fileInput">
                        <label class="custom-file-label">
                          <?= lang('Main.xin_choose_file'); ?>
                        </label>
                        <small>
                          <?= lang('Main.xin_bulk_upload_file_type'); ?>
                        </small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-footer text-right">
                <button type="reset" class="btn btn-light" href="#add_form" data-toggle="collapse" aria-expanded="false">
                  <?= lang('Main.xin_reset'); ?>
                </button>
                &nbsp;
                <button type="submit" class="btn btn-primary">
                  <?= lang('Main.xin_save'); ?>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

  <?php } ?>
  <div class="card user-profile-list <?php echo $get_animate; ?>">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5>
        <?= lang('Main.xin_list_all'); ?>
        <?= lang('Dashboard.xin_leads'); ?>
      </h5>

      <div class="d-flex align-items-center">
        <!-- Assigned To Filter Dropdown -->
        <div class="mr-4">
          <select class="form-control" name="opportunity" id="opportunity_to_filter">
            <option value="">All Opportunity</option>
            <?php foreach ($opportunityList as $list) { ?>
              <option value="<?= $list['id']; ?>"><?= $list['opportunity_name']; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="mr-3">
          <select class="form-control" name="status" id="status_filter" style="min-width: 125px;">
            <option value="">All Status</option>
            <option value="hot">Hot</option>
            <option value="cold">Cold</option>
          </select>
        </div>


        <!-- Add New Button -->
        <?php if (in_array('leads2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <a data-toggle="collapse" href="#add_form" aria-expanded="false"
            class="collapsed btn waves-effect waves-light btn-primary btn-sm">
            <i data-feather="plus"></i>
            <?= lang('Main.xin_add_new'); ?>
          </a>
        <?php } ?>
      </div>
    </div>




    <div class="card-body">
      <div class="box-datatable table-responsive">
        <table class="datatables-demo table table-striped table-bordered" id="lead_table">
          <thead>
            <tr>
              <th>#</th>
              <th>Opportunity</th>
              <?php
              $columnNames = array_column($leadFields, 'column_name');

              foreach ($columnNames as $columnName) { ?>
                <th><?= $columnName; ?></th>
              <?php } ?>
              <th>Lead Status</th>
              <th>Sources</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>


          <tbody>
            <?php $i = 1;
            foreach ($get_leadList as $list) { ?>
              <tr>
                <td><?= $i++; ?></td>
                <td><?= getOpportunityName($list->opportunity_id); ?></td>
                <?php foreach ($columnNames as $field) {
                  $field_name = strtolower(str_replace(' ', '_', trim($field)));
                  ?>
                  <td>
                    <?php
                    if (isset($list->$field_name)) {
                      if (strpos($field_name, 'image') !== false) {
                        $image_path = 'uploads/leads/' . htmlspecialchars($list->$field_name, ENT_QUOTES, 'UTF-8');

                        if (!empty($list->$field_name) && file_exists($image_path)) {
                          echo '<img src="' . base_url($image_path) . '" alt="Profile Image" width="50" height="50">';
                        } else {
                          echo '<img src="' . base_url('uploads/leads/dummy-image.jpg') . '" alt="Dummy Image" width="50" height="50">';
                        }
                      } elseif (strpos($field_name, 'date') !== false) {
                        echo date('d M Y', strtotime($list->$field_name));
                      } else {
                        echo htmlspecialchars($list->$field_name, ENT_QUOTES, 'UTF-8');
                      }
                    } else {
                      echo '-';
                    }

                    ?>
                  </td>
                <?php } ?>
                <td>
                  <?php if ($list->lead_status == 'hot') { ?>
                    <span class="badge badge-light-primary">Hot</span>
                  <?php } else { ?>
                    <span class="badge badge-light-success">Cold</span>
                  <?php } ?>
                </td>
                <td><?= $list->sources_name; ?> </td>
                <td>
                  <?php if ($list->status == 1) { ?>
                    <span class="badge badge-light-primary">Lead</span>
                  <?php } else { ?>
                    <span class="badge badge-light-success">Client</span>
                  <?php } ?>
                </td>
                <td>
                  <a href="<?= base_url('erp/view-lead-info/'.$list->id); ?>" class="btn btn-primary"
                    style="background-color: blue !important;" data-toggle="tooltip" title="View Details">
                    <i class="feather icon-edit-2 text-white"></i>
                  </a>
                  <?php if ($list->status == 1) { ?>
                    <button type="button" class="btn btn-info" title="Change to Client" data-toggle="modal"
                      data-target=".view-modal-data" data-field_id="<?= $list->id ?>">
                      <i class="feather icon-shuffle"></i>
                    </button>
                  <?php } ?>
                  <a href="<?= base_url('erp/delete-leads/'.$list->id); ?>" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this item?');" data-toggle="tooltip"
                    title="Delete Item">
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#opportunity_to_filter, #status_filter').on('change', function () {
        var opportunityElement = document.getElementById('opportunity_to_filter');
        var statusElement = document.getElementById('status_filter');

        var opportunityId = opportunityElement.options[opportunityElement.selectedIndex].value;
        var status = statusElement.options[statusElement.selectedIndex].value;

        $.ajax({
          url: '<?= base_url('erp/filter-leads'); ?>',
          type: 'GET',
          data: {
            opportunity_id: opportunityId,
            status: status
          },
          success: function (data) {
            console.log(data);
            $('#lead_table').html(data);
          },
          error: function (xhr, status, error) {
            console.error('Error fetching filtered leads:', error);
          }
        });
      });
    });
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
      $('#xin-bulk-form').on('submit', function (e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          success: function (response) {
            response = JSON.parse(response);
            if (response.error) {
              alert(response.error);
              window.location.href = "<?= site_url('erp/leads-list'); ?>";
            } else {
              alert(response.result);
              window.location.href = "<?= site_url('erp/leads-list'); ?>";

            }
            $('[name=csrf_token_name]').val(response.csrf_hash);
          },
          error: function (xhr, status, error) {
            alert('Form submission failed!');
            console.error(error);
            window.location.href = "<?= site_url('erp/leads-list'); ?>";
          }
        });
      });
    });
  </script>
  <script>
    $(document).ready(function () {
      $('#lead_table').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        lengthMenu: [10, 25, 50, 100],
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search records",
        }
      });
    });
  </script>

  <script>
    function generateAndDownloadExcel(event) {
      event.preventDefault();
      const leadFields = <?php echo json_encode($leadFields); ?>;

      if (!Array.isArray(leadFields) || leadFields.length === 0) {
        console.error("No lead fields provided.");
        return;
      }

      const headers = ['opportunity_id', 'status', 'lead_status', 'sources_name', ...leadFields.map(field => field.column_name)];

      const wsData = [headers];

      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.aoa_to_sheet(wsData);
      XLSX.utils.book_append_sheet(wb, ws, "Leads");

      const wbout = XLSX.write(wb, {
        bookType: "xlsx",
        type: "binary"
      });

      const blob = new Blob([s2ab(wbout)], {
        type: "application/octet-stream"
      });
      const url = URL.createObjectURL(blob);

      const link = document.createElement('a');
      link.href = url;
      link.download = 'leads.xlsx';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link); // Clean up the DOM

      setTimeout(() => {
        URL.revokeObjectURL(url);
      }, 100);
    }

    function s2ab(s) {
      const buf = new ArrayBuffer(s.length);
      const view = new Uint8Array(buf);
      for (let i = 0; i < s.length; i++) {
        view[i] = s.charCodeAt(i) & 0xFF;
      }
      return buf;
    }
  </script>


  <script>
    document.getElementById('fileInput').addEventListener('change', function (event) {
      const file = event.target.files[0];
      const fileNameLabel = document.getElementById('file-name');

      if (file) {
        fileNameLabel.textContent = `Selected file: ${file.name}`;
      } else {
        fileNameLabel.textContent = 'No file chosen';
      }
    });
  </script>

  <?php
  $session = \Config\Services::session();
  $session->remove('opportunity_id');
  ?>