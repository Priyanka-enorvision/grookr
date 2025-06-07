<?php

use App\Models\SystemModel;
use App\Models\RolesModel;
use App\Models\UsersModel;
use App\Models\LanguageModel;

$SystemModel = new SystemModel();
$UserRolesModel = new RolesModel();
$UsersModel = new UsersModel();
$LanguageModel = new LanguageModel();

$session = \Config\Services::session();

$usession = $session->get('sup_username');
$request = \Config\Services::request();
$router = service('router');
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$locale = service('request')->getLocale();
$language = $LanguageModel->where('is_active', 1)->orderBy('language_id', 'ASC')->findAll();
if ($user['user_type'] == 'super_user') {
    $xin_system = $SystemModel->where('setting_id', 1)->first();
} else {
    $xin_system = erp_company_settings();
}
$ci_erp_settings = $SystemModel->where('setting_id', 1)->first();
?>
<?php
$session_lang = $session->lang;
if (!empty($session_lang)):
    $lang_code = $LanguageModel->where('language_code', $session_lang)->first();
    $flg_icn = '<img src="' . base_url() . 'uploads/languages_flag/' . $lang_code['language_flag'] . '">';
    $lg_code = $session_lang;
elseif ($xin_system['default_language'] != ''):
    $lg_code = $xin_system['default_language'];
    $lang_code = $LanguageModel->where('language_code', $xin_system['default_language'])->first();
    $flg_icn = '<img src="' . base_url() . 'uploads/languages_flag/' . $lang_code['language_flag'] . '">';
else:
    $flg_icn = '<img src="' . base_url() . 'uploads/languages_flag/gb.gif">';
endif;
if ($user['user_type'] == 'super_user') {
    $bg_option = 'bg-dark';
} else if ($user['user_type'] == 'company') {
    $bg_option = 'bg-dark';
} else {
    $bg_option = 'bg-success';
}

?>

<style>
    .bg-primary {
        background-color: #ff230054 !important;
    }

    .text-primary {
        color: #ff230054 !important;
    }

    .btn-primary {
        color: #fff;
        background-color: #ff230054 !important;
        border-color: #007bff;
    }

    .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #ff230054;
        border-color: #007bff;
    }

    .page-link {
        position: relative;
        display: block;
        padding: .5rem .75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #ff230054;
        background-color: #fff;
        border: 1px solid #dee2e6;
    }

    .pc-sidebar.light-sidebar .pc-caption {
        color: #ff230054;
    }

    .pc-sidebar.light-sidebar .pc-item:hover>.pc-link,
    .pc-sidebar.light-sidebar .pc-item.active>.pc-link {
        color: #ff230054;
    }

    .pc-sidebar.light-sidebar .pc-link:active,
    .pc-sidebar.light-sidebar .pc-link:focus,
    .pc-sidebar.light-sidebar .pc-link:hover {
        color: #ff230054;
    }

    .logo-lg {
        max-width: 65px !important;
        max-height: 65px !important;
    }

    .pc-header .user-avtar {
        height: 45px;
        width: 46px !important;
    }
</style>

<header class="pc-header <?= $bg_option; ?>">
    <div class="header-wrapper">
        <?php if ($user['user_type'] == 'super_user' || $user['user_type'] == 'company' || $user['user_type'] == 'customer' || $user['user_type'] == 'staff') { ?>
            <div class="m-header d-flex align-items-center">
                <a href="<?= site_url('erp/desk'); ?>" class="b-brand">
                    <!-- <img src="<?= base_url(); ?>/public/uploads/logo/<?= $ci_erp_settings['logo']; ?>" alt="" class="logo logo-lg" height="40" width="138"> -->
                    <?php
                    $logoUrl = !empty($ci_erp_settings['logo'])
                        ? base_url() . 'uploads/logo/' . $ci_erp_settings['logo']
                        : base_url() . 'uploads/logo/India_1.png';
                    ?>
                    <img src="<?= $logoUrl; ?>" alt="Logo" class="logo logo-lg">
                </a>
            </div>
        <?php } ?>

        <div class="ml-auto">
            <ul class="list-unstyled">

                <?php if (in_array('mom_1', staff_role_resource()) || $user['user_type'] == 'company') { ?>
                    <li class="pc-h-item">
                        <a class="pc-head-link mr-0" data-toggle="tooltip" data-placement="top"
                            title="<?= lang('Mom.xin_mom'); ?>" href="<?= site_url('erp/moms-grid'); ?>">
                            <i data-feather="book-open"></i>
                            <span class="sr-only"></span>
                        </a>
                    </li>
                <?php } ?>
                <?php if (in_array('todo_ist', staff_role_resource()) || $user['user_type'] == 'company' || $user['user_type'] == 'customer' || $user['user_type'] == 'super_user') { ?>
                    <li class="pc-h-item">
                        <a class="pc-head-link mr-0" data-toggle="tooltip" data-placement="top"
                            title="<?= lang('Main.xin_todo_ist'); ?>" href="<?= site_url('erp/todo-list'); ?>">
                            <i data-feather="check-circle"></i>
                            <span class="sr-only"></span>
                        </a>
                    </li>
                <?php } ?>
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none mr-0" data-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">

                        <?php
                        $profilePhoto = $user['profile_photo'] ?? '';
                        $dummyImage = base_url('assets/images/images.png');

                        if (!empty($profilePhoto)) {
                            $filePath = FCPATH . $profilePhoto; // FCPATH is the path to public folder
                            if (!file_exists($filePath)) {
                                $profilePhoto = '';
                            }
                        }

                        $finalImage = !empty($profilePhoto) ? base_url($profilePhoto) : $dummyImage;
                        ?>

                        <img src="<?= $finalImage ?>"
                            alt="User Avatar"
                            class="user-avtar"
                            onerror="this.src='<?= $dummyImage ?>'">
                        <span>
                            <span class="user-name"><?= $user['first_name'] . ' ' . $user['last_name']; ?></span>
                            <span class="user-desc"><?= $user['username'] ?></span>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right pc-h-dropdown">
                        <div class=" dropdown-header">
                            <h6 class="text-overflow m-0"><?= lang('Dashboard.xin_welcome'); ?></h6>
                        </div>
                        <a href="<?= site_url('erp/my-profile'); ?>" class="dropdown-item">
                            <i data-feather="user"></i>
                            <span><?= lang('Dashboard.xin_my_account'); ?></span>
                        </a>

                        <a href="<?= site_url('erp/system-logout') ?>" class="dropdown-item">
                            <i data-feather="power"></i>
                            <span><?= lang('Main.xin_logout'); ?></span>
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>