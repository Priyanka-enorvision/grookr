<?php
use App\Models\{
    UsersModel,
    PlanningEntityModel
};

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$UsersModel = new UsersModel();
$PlanningEntityModel = new PlanningEntityModel();

$user_info = $UsersModel->find($usession['sup_user_id']);
$company_id = $user_info['company_id'];
$user_type = $user_info['user_type'];

$planning_entities = $PlanningEntityModel
    ->groupStart() 
        ->where('company_id', $company_id)
        ->orWhere('company_id', 0)
    ->groupEnd()
    ->groupStart() 
        ->where(['user_type' => $user_type])
        ->orWhere('user_type', '')
    ->groupEnd()
    ->findAll();

$data = [];
if (!empty($planning_entities)) {
    foreach ($planning_entities as $entity) {
        $actions = generateActions($entity, $user_info);

        $status = $entity['valid'] == 1
            ? '<span class="label label-success">Valid</span>'
            : '<span class="label label-danger">Invalid</span>';

        $data[] = [
            $entity['entity'],
            $entity['description'],
            $entity['type'],
            $status,
            $entity['created_at'],
            $actions,
        ];
    }
}

/**
 * Generate action buttons for an entity based on permissions and conditions.
 *
 * @param array $entity The planning entity data.
 * @param array $user_info The current user information.
 * @return string The generated HTML for action buttons.
 */
function generateActions($entity, $user_info) {
    $actions = '';

    $canDelete = in_array('planning_configuration4', staff_role_resource()) || $user_info['user_type'] == 'company';
    $canEdit = in_array('planning_configuration3', staff_role_resource()) || $user_info['user_type'] == 'company';

    if ($entity['company_id'] != 0 && $entity['user_type'] != '') {
        if ($canDelete) {
            $actions .= '<button type="button" class="btn btn-sm btn-light-danger delete" data-toggle="modal" data-target=".delete-modal" data-record-id="' . $entity['id'] . '">
                            <i class="feather icon-trash-2"></i>
                        </button>';
        }
        if ($canEdit) {
            $actions .= '<a href="' . site_url('erp/planning-configuration-detail') . '/' . $entity['id'] . '" class="btn btn-sm btn-light-primary">
                            <i class="feather icon-edit"></i>
                        </a>';
        }
    } else {
        if ($canDelete) {
            $actions .= '<button type="button" class="btn btn-sm btn-light-danger" disabled>
                            <i class="feather icon-trash-2"></i>
                        </button>';
        }
        if ($canEdit) {
            $actions .= '<button type="button" class="btn btn-sm btn-light-primary" disabled>
                            <i class="feather icon-edit"></i>
                        </button>';
        }
    }

    return $actions;
}
?>


<?php if (in_array('planning_configuration1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.css">

  <section>
    <div class="annual-planning">
      <div class="container">
        <div class="row d-flex justify-content-between mr-3">
          <h3 style="margin-left:35px;">Planning Entities</h3>
          <?php if (in_array('planning_configuration2', staff_role_resource()) || $user_info['user_type'] == 'company'): ?>
            <button class="btn btn-primary add-button"><i data-feather="plus"></i> Add</button>
          <?php endif; ?>
        </div>
        <div id="form-container" style="display: none;">
          <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 100%;">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Entities Form</h5>
                <button type="button" class="close add-button text-white" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <?php
                $attributes = ['name' => 'add_planning_entities', 'id' => 'planningEntitiesForm', 'autocomplete' => 'off'];
                $hidden = ['user_id' => '0'];
                echo form_open('erp/add-planning-entities', $attributes, $hidden);
                ?>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="entity">Entity <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="entity" name="entity" placeholder="Enter Entity"
                          required>
                        <small class="form-text text-muted">Please enter the name of the entity.</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="type">Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="type" name="type" required>
                          <option value="" disabled selected>Select Type</option>
                          <option value="text">Text</option>
                          <option value="number">Number</option>
                        </select>
                        <small class="form-text text-muted">Choose the type of the entity.</small>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" 
                          placeholder="Enter Description"></textarea>
                        <small class="form-text text-muted">Provide additional details about the entity.</small>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary">Save</button>
                </div>
                <?= form_close(); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12 col-md-12">
            <div class="card-body">
              <div class="box-datatable table-responsive">
                <table class="table table-striped table-bordered" id="planningEntitiesTable">
                  <thead>
                    <tr>
                      <th>Entity</th>
                      <th>Description</th>
                      <th>Type</th>
                      <th>Valid</th>
                      <th>Created At</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($data)) { ?>
                      <?php foreach ($data as $row): ?>
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
  </section>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.add-button').click(function () {
        $('#form-container').toggle();
      });

      $('#planningEntitiesForm').on('submit', function (event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: formData,
          dataType: 'json',
          success: function (response) {
            if (response.status === 'success') {
              toastr.success(response.message);
              $('#form-container').hide();
              setTimeout(function () {
                location.reload();
              }, 1000);
            } else {
              toastr.error(response.message);
              setTimeout(function () {
                location.reload();
              }, 1000);
            }
          },
          error: function (jqXHR, textStatus, errorThrown) {
            toastr.error('An error occurred: ' + textStatus);
            setTimeout(function () {
                location.reload();
              }, 1000);
          }
        });
      });
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