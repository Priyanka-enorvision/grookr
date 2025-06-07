<?php

use App\Models\SystemModel;
use App\Models\UsersModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$xin_system = $SystemModel->where('setting_id', 1)->first();
$favicon = base_url() . 'uploads/logo/favicon/' . $xin_system['favicon'];

$session = \Config\Services::session();
$router = service('router');

$username = $session->get('sup_username');
$user_id = $username['sup_user_id'];
$user_info = $UsersModel->where('user_id', $user_id)->first();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $title ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="" />
    <meta name="keywords" content="">
    <meta name="author" content="erp" />
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <!-- ApexCharts CSS (optional) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.35.0/dist/apexcharts.min.css">

    <!-- Favicon icon -->
    <link rel="icon" href="<?= base_url(); ?>uploads/logo/favicon/<?= $xin_system['favicon']; ?>"
        type="image/x-icon">

    <!-- font css -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/font-awsome-pro/css/pro.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/feather.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/fonts/fontawesome.css">

    <!-- vendor css -->
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/customizer.css">

    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/layout-modern.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/select2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/plugins/toastr/toastr.css'); ?>">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/bootstrap-material-datetimepicker/bootstrap-material-datetimepicker.css">
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2022.1.301/styles/kendo.common.min.css">
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2022.1.301/styles/kendo.default.min.css">
    <link rel="stylesheet" href="https://kendo.cdn.telerik.com/2022.1.301/styles/kendo.default-v2.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<?php if ($router->methodName() == 'goal_details' || $router->methodName() == 'task_details' || $router->methodName() == 'project_details') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/ion.rangeSlider/css/ion.rangeSlider.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/plugins/ion.rangeSlider/css/ion.rangeSlider.skinFlat.css">
<?php } ?>
<link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/bars-movie.css">
<link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/css-stars.css">
<link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/bars-1to10.css">
<link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/bootstrap-slider.min.css">
<?php if ($user_info['user_type'] == 'customer') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/layout-advance.css">
<?php } ?>
<link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/fullcalendar.min.css">
<?php if ($router->methodName() == 'tasks_scrum_board' || $router->methodName() == 'projects_scrum_board') { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/dragula/dragula.css">
<?php } ?>
<?php if ($router->controllerName() == '\App\Controllers\Erp\Settings' && $router->methodName() == 'index') { ?>
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/ekko-lightbox.css">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/css/plugins/lightbox.min.css">
<?php } ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@latest/build/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">


</head>