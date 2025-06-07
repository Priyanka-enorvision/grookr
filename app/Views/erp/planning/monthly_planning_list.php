<?php

use App\Models\UsersModel;
use App\Models\PlanningEntityModel;
use App\Models\MonthlyPlanningModel;
use App\Models\YearPlanningModel;

//$encrypter = \Config\Services::encrypter();
$UsersModel = new UsersModel();
$PlanningEntityModel = new PlanningEntityModel();
$MonthlyPlanningModel = new MonthlyPlanningModel();
$YearPlanningModel = new YearPlanningModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_type = $user_info['user_type'];
$company_id = user_company_info();
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

$years = array_column($year_planning_data, 'year');

$unique_years = array_unique($years);

rsort($unique_years);

$monthly_planning_data = $MonthlyPlanningModel->where(['company_id' => $company_id, 'user_type' => $user_type])->findAll();

$planned_entity_ids = array_column($monthly_planning_data, 'entities_id');


$unplanned_entities = array_filter($planning_entities, function ($entity) use ($planned_entity_ids) {
  return !in_array($entity['id'], $planned_entity_ids);
});
$record = [];

if (!empty($monthly_planning_data)) {
  $currentYear = (int)date('Y');
  $currentMonth = (int)date('n');
  $monthNames = ['january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12];
  $entityCache = [];
  $i = 0;

  foreach ($monthly_planning_data as $r) {
    $recordMonthName = strtolower(explode('-', $r['month'])[0]);
    $recordMonth = $monthNames[$recordMonthName] ?? 0;
    $recordYear = (int)substr($r['year'], 0, 4);

    // Check if record is editable (current or next month)
    $isEditable = ($recordYear == $currentYear && $recordMonth == $currentMonth) ||
      ($recordYear == $currentYear && $recordMonth == $currentMonth + 1) ||
      ($currentMonth == 1 && $recordYear == $currentYear + 1 && $recordMonth == 12);

    // Generate action buttons
    $actions = '';
    if (in_array('monthly_planning4', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= '<button type="button" class="btn btn-sm btn-light-danger delete-monthly-planning" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $r['id'] . '" title="Delete"><i class="feather icon-trash-2"></i></button>';
    }
    if (in_array('monthly_planning3', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= $isEditable
        ? '<a href="' . site_url('erp/monthly-planning-detail/' . $r['id']) . '" class="btn btn-sm btn-light-primary" title="Edit"><i class="feather icon-edit"></i></a>'
        : '<button class="btn btn-sm btn-light-secondary" disabled title="Editing Disabled"><i class="feather icon-edit"></i></button>';
    }
    if (in_array('monthly_planning5', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= '<a href="' . site_url('erp/monthly-planning-review/' . $r['id']) . '" class="btn btn-sm btn-light-warning" title="Review"><i class="feather icon-eye"></i></a>';
    }

    // Cache entity data to reduce queries
    if (!isset($entityCache[$r['entities_id']])) {
      $entity_data = $PlanningEntityModel->where('id', $r['entities_id'])->first();
      $entityCache[$r['entities_id']] = $entity_data['entity'] ?? 'N/A';
    }
    $i++;
    $record[] = [
      $i,
      $entityCache[$r['entities_id']],
      ($entityCache[$r['entities_id']] == 'revenue') ? '₹' . $r['entity_value'] : $r['entity_value'],
      $r['year'],
      $r['month'],
      $actions
    ];
  }

  foreach ($unplanned_entities as $entity) {
    $actions = '';
    if (in_array('monthly_planning3', staff_role_resource()) || $user_info['user_type'] == 'company') {
      $actions .= $isEditable
        ? '<a href="' . site_url('erp/monthly-planning-detail/' . uencode($entity['id'])) . '" class="btn btn-sm btn-light-primary" title="Edit"><i class="feather icon-edit"></i></a>'
        : '<button class="btn btn-sm btn-light-secondary" disabled title="Editing Disabled"><i class="feather icon-edit"></i></button>';
    }

    $record[] = [
      $entity['id'],
      $entity['entity'],
      '-',
      '-',
      '-',
      $actions
    ];
  }
}

?>

<?php if (in_array('monthly_planning1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>


  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.css">

  <div class="annual-planning">
    <div class="row">
      <div class="col-xl-12 col-md-12 settle">
        <div class="container">
          <div class="row justify-content-between align-items-center mb-3">
            <h3 class="col-auto">Monthly Planning</h3>
            <?php if (in_array('monthly_planning2', staff_role_resource()) || $user_type == 'company') { ?>
              <!-- <a href="<?= site_url('erp/monthly-planning'); ?>" class="btn btn-primary col-auto text-right">
              <i data-feather="plus"></i> Add
            </a> -->
              <button class="btn btn-primary  add-button text-right"><i data-feather="plus"></i>
                Add</button>
            <?php } ?>
          </div>
          <div id="form-container" style="display: none;">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document"
              style="max-width: 100%; margin-top: -50px; margin-bottom: -50px;">
              <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                  <h5 class="modal-title">Add Annual Planning Form</h5>
                  <button type="button" class="close add-button text-white" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <?php if (!empty($planning_entities)): ?>
                    <form id="add-form" method="post" autocomplete="off">
                      <div class="row">
                        <!-- Financial Year Selection -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="financial_year" class="font-weight-bold">Financial Year<span class="text-danger">*</span></label>
                            <?php

                            $currentYear = (int)date('Y');
                            $currentMonth = (int)date('n');
                            $unique_years = $unique_years ?? [];
                            $financialYearStart = ($currentMonth >= 4) ? $currentYear : $currentYear - 1;
                            $currentFY = sprintf("%d-%02d", $financialYearStart, ($financialYearStart + 1) % 100);
                            ?>

                            <select class="form-control" id="financial_year" name="year" required>
                              <option value="">Select Financial Year</option>
                              <?php if (!empty($unique_years)): ?>
                                <?php foreach ($unique_years as $year): ?>
                                  <?php

                                  $year = (int)$year;
                                  if ($year < 2000 || $year > 2100) continue;

                                  $startYear = $year;
                                  $endYear = $year + 1;
                                  $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                                  $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                                  $selected = ($fyValue === $currentFY) ? 'selected' : '';
                                  ?>
                                  <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                                    <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                                  </option>
                                <?php endforeach; ?>
                              <?php else: ?>
                                <?php  ?>
                                <?php for ($i = 2; $i >= -5; $i--): ?>
                                  <?php
                                  $startYear = $financialYearStart + $i;
                                  $endYear = $startYear + 1;
                                  $fyValue = sprintf("%d-%02d", $startYear, $endYear % 100);
                                  $fyDisplay = sprintf("April %d - March %d", $startYear, $endYear);
                                  $selected = ($fyValue === $currentFY) ? 'selected' : '';
                                  ?>
                                  <option value="<?= htmlspecialchars($fyValue) ?>" <?= $selected ?>>
                                    <?= htmlspecialchars("$fyValue ($fyDisplay)") ?>
                                  </option>
                                <?php endfor; ?>
                              <?php endif; ?>
                            </select>
                          </div>
                        </div>
                        <!-- Month Selection -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <label for="month" class="font-weight-bold">Month<span class="text-danger">*</span></label>
                            <select class="form-control" id="month" name="month" required>
                              <option value="" disabled selected>Select Month</option>
                              <?php
                              $months = [
                                ['name' => 'April', 'year_offset' => 0],
                                ['name' => 'May', 'year_offset' => 0],
                                ['name' => 'June', 'year_offset' => 0],
                                ['name' => 'July', 'year_offset' => 0],
                                ['name' => 'August', 'year_offset' => 0],
                                ['name' => 'September', 'year_offset' => 0],
                                ['name' => 'October', 'year_offset' => 0],
                                ['name' => 'November', 'year_offset' => 0],
                                ['name' => 'December', 'year_offset' => 0],
                                ['name' => 'January', 'year_offset' => 1],
                                ['name' => 'February', 'year_offset' => 1],
                                ['name' => 'March', 'year_offset' => 1]
                              ];

                              foreach ($months as $month) {
                                $year = $currentYear + $month['year_offset'];
                                if ($currentMonth >= 4 && $month['year_offset'] == 1) {
                                  $year = $currentYear + 1;
                                } elseif ($currentMonth < 4 && $month['year_offset'] == 0) {
                                  $year = $currentYear;
                                }
                                echo "<option value='{$month['name']}-$year'>{$month['name']} $year</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                      </div>

                      <!-- Planning Entities -->
                      <div class="row">
                        <?php if (!empty($planning_entities)): ?>
                          <?php foreach ($planning_entities as $index => $entity): ?>
                            <div class="col-md-6">
                              <div class="form-group">
                                <fieldset style="margin-bottom: 15px;">
                                  <input type="hidden" name="entities[<?= $entity['id'] ?>][entities_id]" value="<?= $entity['id'] ?>">
                                  <label for="entities_<?= $entity['id'] ?>" class="font-weight-bold">
                                    <?= htmlspecialchars($entity['entity'] ?? '') ?><span class="text-danger">*</span>
                                  </label>
                                  <input type="<?= htmlspecialchars($entity['type'] ?? 'text') ?>"
                                    class="form-control"
                                    id="entities_<?= $entity['id'] ?>"
                                    name="entities[<?= $entity['id'] ?>][entity_value]"
                                    placeholder="<?= htmlspecialchars($entity['entity'] ?? '') ?>"
                                    required
                                    <?= ($entity['type'] == 'number') ? 'step="any"' : '' ?>>
                                </fieldset>
                              </div>
                            </div>

                            <?php if (($index + 1) % 2 == 0): ?>
                      </div>
                      <div class="row">
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                      </div>

                      <div class="text-right">
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
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

          <br>
          <div class="row">
            <div class="col-xl-12 col-md-12">

              <div class="box-datatable table-responsive">
                <table class="table table-striped table-bordered" id="monthlyPlanningTable">
                  <thead>
                    <tr>
                      <th>Entity Id</th>
                      <th>Entity Name</th>
                      <th>Entity Value</th>
                      <th>Year</th>
                      <th>Month</th>
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
                          <td class="normal-text"><?= $row[5]; ?></td>
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
    $(document).ready(function() {
      toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": 3000,
        "extendedTimeOut": 3000
      };

      $('.add-button').click(function() {
        $('#form-container').toggle();
      });

      $('#add-form').submit(function(event) {
        event.preventDefault();
        $('#submit-btn').prop('disabled', true);

        var formData = $(this).serialize();
        console.log("Form Data:", formData);

        $.ajax({
            type: 'POST',
            url: '<?php echo base_url('erp/monthly-plan-submit'); ?>',
            data: formData,
            dataType: 'json',
            encode: true,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': '<?php echo csrf_hash(); ?>'
            }
          })
          .done(function(response) {
            console.log("Response:", response);
            if (response.message === 'Form submitted successfully!') {
              toastr.success(response.message);
              setTimeout(() => location.reload(), 2000);
            } else {
              toastr.error('Error: ' + response.message);
              setTimeout(() => location.reload(), 2000);
            }
          })

          .always(function() {
            $('#submit-btn').prop('disabled', false);
          });
      });
    });
  </script>

  <script>
    $(document).ready(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });


    $(document).on("click", ".delete-monthly-planning", function() {
      $('input[name=_token]').val($(this).data('record-id'));
      $('#delete_record').attr('action', main_url + 'delete-monthly-planning');
    });
  </script>

  <?php if ($session->getFlashdata('success')): ?>
    <script>
      $(document).ready(function() {
        toastr.success("<?= $session->getFlashdata('success'); ?>");
      });
    </script>
  <?php endif; ?>

  <?php if ($session->getFlashdata('error')): ?>
    <script>
      $(document).ready(function() {
        toastr.error("<?= $session->getFlashdata('error'); ?>");
      });
    </script>
  <?php endif; ?>


<?php } ?>