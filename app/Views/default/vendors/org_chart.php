<?php

use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\StaffdetailsModel;
//$encrypter = \Config\Services::encrypter();
$RolesModel = new RolesModel();
$UsersModel = new UsersModel();
$SystemModel = new SystemModel();
$DepartmentModel = new DepartmentModel();
$DesignationModel = new DesignationModel();
$StaffdetailsModel = new StaffdetailsModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$request = \Config\Services::request();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
if ($user_info['user_type'] == 'staff') {
  $company_id = $user_info['company_id'];
} else {
  $company_id = $usession['sup_user_id'];
}
$user_chart = $UsersModel->where('user_id', $company_id)->first();
$main_department = $DepartmentModel->where('company_id', $company_id)->where('department_head!=', 0)->findAll();
$get_animate = '';
?>

<link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/orgchart/css/jquery.orgchart.css">
<link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/orgchart/css/style.css">
<!--<link rel="stylesheet" href="<?= base_url(); ?>/public/assets/plugins/orgchart/css/jquery.orgchart2.css">-->
<div id="chart-container"></div>

<script type="text/javascript" src="<?= base_url(); ?>assets/plugins/orgchart/js/html2canvas.min.js"></script>
<script type="text/javascript" src="<?= base_url(); ?>assets/plugins/orgchart/js/jquery.orgchart.js"></script>
<script type="text/javascript">
  $(function() {

    var datascource = {
      'profile': '<?= base_url() ?>uploads/users/thumb/<?= !empty($user_chart['profile_photo']) ? $user_chart['profile_photo'] : 'default-user.png' ?>',
      'name': '<?= !empty($user_chart['first_name']) ? htmlspecialchars($user_chart['first_name'] . ' ' . $user_chart['last_name']) : 'Unknown User' ?>',
      'title': '<?= !empty($user_chart['company_name']) ? htmlspecialchars($user_chart['company_name']) : 'Company' ?>',
      'children': [
        <?php foreach ($main_department as $idepartment): ?>
          <?php
          $idep_head = [];
          if (!empty($idepartment['department_head'])) {
            $idep_head = $UsersModel->where('user_id', $idepartment['department_head'])->first();
          }
          ?> {
            'profile': '<?= base_url() ?>uploads/users/thumb/<?= !empty($idep_head['profile_photo']) ? $idep_head['profile_photo'] : 'default-user.png' ?>',
            'name': '<?= !empty($idep_head['first_name']) ? htmlspecialchars($idep_head['first_name'] . ' ' . $idep_head['last_name']) : 'Department Head' ?>',
            'title': '<?= htmlspecialchars($idepartment['department_name']) ?>',
            'children': [
              <?php
              $subdesigns = $DesignationModel->where('department_id', $idepartment['department_id'])->findAll();
              foreach ($subdesigns as $subdesign):
                $staffMembers = $StaffdetailsModel->where('designation_id', $subdesign['designation_id'])->findAll();
                foreach ($staffMembers as $sdesign):
                  $edesignation = $DesignationModel->where('designation_id', $sdesign['designation_id'])->first();
                  $iuser_count = $UsersModel->where('user_id', $sdesign['user_id'])->countAllResults();
                  if ($iuser_count > 0):
                    $iuser = $UsersModel->where('user_id', $sdesign['user_id'])->first();
              ?> {
                      'profile': '<?= base_url() ?>uploads/users/thumb/<?= !empty($iuser['profile_photo']) ? $iuser['profile_photo'] : 'default-user.png' ?>',
                      'name': '<?= htmlspecialchars($iuser['first_name'] . ' ' . $iuser['last_name']) ?>',
                      'title': '<?= !empty($edesignation['designation_name']) ? htmlspecialchars($edesignation['designation_name']) : 'Employee' ?>'
                    },
              <?php
                  endif;
                endforeach;
              endforeach;
              ?>
            ]
          },
        <?php endforeach; ?>
      ]
    };

    var oc = $('#chart-container').orgchart({
      'data': datascource,
      'nodeID': 'profile',
      'nodeTitle': 'name',
      'nodeContent': 'title',
      'pan': true,
      'visibleLevel': 3,
      'exportButton': true,
      'exportFilename': '<?= $user_chart['company_name']; ?>_<?= lang('Dashboard.xin_org_chart_title'); ?>',
      'zoom': true,
      'zoominLimit': 7,
      'direction': 't2b',
      'createNode': function($node, data) {
        var nodePrompt = $('<i>', {
          'class': 'fa fa-info-circle second-menu-icon',
          click: function() {
            $(this).siblings('.second-menu').toggle();
          }
        });
        var secondMenu = '<div class="second-menu"><img class="avatar" src="' + data.profile + '"></div>';
        $node.append(nodePrompt).append(secondMenu);
      }
    });

    oc.$chartContainer.on('touchmove', function(event) {
      event.preventDefault();
    });

  });
</script>