<?php

use App\Models\UsersModel;
use App\Models\PlanningEntityModel;
use App\Models\YearPlanningModel;

$UsersModel = new UsersModel();
$PlanningEntityModel = new PlanningEntityModel();
$YearPlanningModel = new YearPlanningModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$user_type = $user_info['user_type'];
$company_id = $user_info['company_id'];

// $planning_entities = $PlanningEntityModel->where(['company_id' => $company_id, 'user_type' => $user_type, 'valid' => '1'])->findAll();
$planning_entities = $PlanningEntityModel
  ->groupStart()
  ->where('company_id', $company_id)
  ->orWhere('company_id', 0)
  ->groupEnd()
  ->groupStart()
  ->where(['user_type' => $user_type])
  ->orWhere('user_type', '')
  ->groupEnd()
  ->where('valid', '1')
  ->findAll();

$year_planning_data = $YearPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type])->findAll();

$planned_entity_ids = array_column($year_planning_data, 'entities_id');

$unplanned_entities = array_filter($planning_entities, function($entity) use ($planned_entity_ids) {
    return !in_array($entity['id'], $planned_entity_ids);
});


if (!empty($year_planning_data)) {
  $record = [];

  $i=0;
  foreach ($year_planning_data as $r) {


    $actions = '';
    if (in_array('year_planning4', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= '<button type="button" class="btn btn-sm btn-light-danger delete-year-planning" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r['id'] . '"><i class="feather icon-trash-2"></i></button>';
    }
    if (in_array('year_planning3', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= '<a href="' . site_url('erp/year-planning-detail') . '/' . $r['id'] . '" class="btn btn-sm btn-light-primary"><i class="feather icon-edit"></i></a>';
    }
    $entity_id = $r['entities_id'];
    $entity_data = $PlanningEntityModel->where('id', $entity_id)->first();
    $entity_name = $entity_data['entity'];

    if ($entity_name == 'revenue') {
      $entity_value = '₹' . $r['entity_value'];
    } else {
      $entity_value = $r['entity_value'];
    }
    $i++;

    $year = $r['year'];
    $record[] = [
      $i,
      $entity_name,
      $entity_value,
      $year,
      $actions
    ];
  }

  foreach ($unplanned_entities as $entity) {
    $actions = '';
    if (in_array('year_planning3', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= '<a href="' . site_url('erp/year-planning-detail') . '/' . $entity['id'] . '" class="btn btn-sm btn-light-primary"><i class="feather icon-edit"></i></a>';
    }

    $record[] = [
        $entity['id'],
        $entity['entity'],
        '-',
        date('Y'), 
        $actions
    ];
  }

  
}

?>
<?php if (in_array('year_planning1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

  <div class="annual-planning">
    <div class="row">
      <div class="col-xl-12 col-md-12 settle">
        <div class="container">
          <div class="row justify-content-between align-items-center mb-3">
            <h3 class="col-auto">Year Planning</h3>
            <?php if (in_array('year_planning2', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
              <button class="btn btn-primary col-auto add-button text-right"><i data-feather="plus"></i> Add</button>
            <?php } ?>
          </div>

          <div>
            <div id="form-container" style="display: none;">
              <div class="modal-dialog modal-dialog-centered modal-lg" role="document"
                style="max-width: 100%; margin-top:-50px; margin-bottom: -50px; ">
                <div class="modal-content">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Yearly Planning Form</h5>
                    <button type="button" class="close add-button text-white" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">

                    <form id="add-form" method="post" autocomplete="off">
                      <?php $columns = array_keys($planning_entities[0]); ?>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label for="financial_year" class="font-weight-bold">Financial Year (April-March)<span class="text-danger">*</span></label>
                            <select class="form-control" id="financial_year" name="year" required>
                              <option value="">Select Financial Year</option>
                              <?php
                                $currentYear = date('Y');
                                $nextYear = $currentYear + 1;

                                for ($i = 0; $i < 5; $i++) {  
                                    $startYear = $currentYear + $i;
                                    $endYear = $nextYear + $i;
                                    $financialYear = $startYear . '-' . substr($endYear, 2);
                                    echo '<option value="' . $financialYear . '">' . $financialYear . ' (April ' . $startYear . ' - March ' . $endYear . ')</option>';
                                }
                              ?>
                            </select>
                            <small class="text-muted">e.g., <?php echo $currentYear."-".$nextYear; ?> means April <?php echo $currentYear;?> to March <?php echo $nextYear;?></small>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <?php if (!empty($planning_entities)): ?>
                          <?php foreach ($planning_entities as $index => $entity): ?>
                            <div class="col-md-6">
                              <div class="form-group">
                                <fieldset>
                                  <input type="hidden" name="entities[<?= $entity['id']; ?>][entities_id]" value="<?= $entity['id']; ?>">
                                  <?php if (in_array('entity', $columns)): ?>
                                    <label for="entities_<?= $entity['id']; ?>" class="font-weight-bold">
                                      <?= htmlspecialchars($entity['entity']); ?><span class="text-danger">*</span>
                                    </label>
                                    <input type="<?= htmlspecialchars($entity['type']); ?>" class="form-control"
                                      id="entities_<?= $entity['id']; ?>" 
                                      name="entities[<?= $entity['id']; ?>][entity_value]"
                                      placeholder="<?= htmlspecialchars($entity['entity']); ?>" required>
                                  <?php endif; ?>
                                </fieldset>
                              </div>
                            </div>

                            <?php if (($index + 1) % 2 == 0): ?>
                              </div><div class="row">
                            <?php endif; ?>
                          <?php endforeach; ?>
                      </div>

                      <div class="text-right">
                        <button type="submit" id="submit-btn" class="btn btn-primary mt-3">Submit</button>
                      </div>
                    </form>
                    <?php else: ?>
                      <p class="text-center">No planning entities found for the specified company.Please Click
                        On
                        <a href="<?php echo site_url('erp/planning_configuration'); ?>">Create New
                          Entity</a>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <br>
          <div class="row">
            <div class="col-xl-12 col-md-12">

              <div class="box-datatable table-responsive">
                <table class="table table-striped table-bordered" id="yearPlanningTable">
                  <thead>
                    <tr>
                      <th>Entity Id</th>
                      <th>Entity Name</th>
                      <th>Entity Value</th>
                      <th>Year</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($record)) { ?>
                      <?php foreach ($record as $row): ?>
                        <tr>
                          <td class="normal-text"><?= $row[0]; ?></td>
                          <td class="normal-text"><?= $row[1]; ?></td>
                          <td class="normal-text"><?= $row[2]; ?></td>
                          <td class="normal-text"><?= $row[3]; ?></td>
                          <td class="normal-text"><?= $row[4]; ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.add-button').click(function () {
        $('#form-container').toggle();
      });
      $('#add-form').submit(function (event) {
        event.preventDefault();
        $('#submit-btn').prop('disabled', true);
        var formData = $(this).serialize();
        console.log("Form Data:", formData);
        $.ajax({
          type: 'POST',
          url: '<?php echo base_url('erp/year_planning_submit'); ?>',
          data: formData,
          dataType: 'json',
          encode: true,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '<?php echo csrf_hash(); ?>'
          }
        })
          .done(function (response) {
            console.log("Response:", response);
            if (response.message === 'Form submitted successfully!') {
              toastr.success(response.message);
              setTimeout(60000);
              location.reload();

            } else {
              toastr.error('Error: ' + response.message);
              setTimeout(60000);
              location.reload();

            }
          })
          .fail(function (response) {
            console.log("AJAX Error:", response);
            toastr.error('Error occurred while submitting the form.');
            setTimeout(60000);
            location.reload();

          })
          .always(function () {
            $('#submit-btn').prop('disabled', false);
          });
      });
    });
  </script>

  <script>

    $(document).on("click", ".delete-year-planning", function () {
      $('input[name=_token]').val($(this).data('record-id'));
      $('#delete_record').attr('action', main_url + 'delete-year-planning');
    });
  </script>

  <?php if ($session->getFlashdata('success')): ?>
    <script>
      $(document).ready(function () {
        toastr.success("<?= $session->getFlashdata('success'); ?>");
      });
    </script>
  <?php endif; ?>

  <?php if ($session->getFlashdata('error')): ?>
    <script>
      $(document).ready(function () {
        toastr.error("<?= $session->getFlashdata('error'); ?>");
      });
    </script>
  <?php endif; ?>


<?php } ?>