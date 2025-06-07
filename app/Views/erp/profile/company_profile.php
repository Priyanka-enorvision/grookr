<?php

use App\Models\UsersModel;
use App\Models\CountryModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\VerifyEmployeDocModel;
use App\Models\EmpDocumentItemModel;
use App\Models\DocumentConfigModel;
use App\Models\AssetsModel;

use function PHPUnit\Framework\isEmpty;

$UsersModel = new UsersModel();
$CountryModel = new CountryModel();
$LanguageModel = new LanguageModel();
$ConstantsModel = new ConstantsModel();
$documentModel = new VerifyEmployeDocModel();
$itemDocument = new EmpDocumentItemModel();
$doc_categoryModel = new DocumentConfigModel();
$AssetsModel = new AssetsModel();

$session = \Config\Services::session();
$db = \Config\Database::connect();
$usession = $session->get('sup_username');
$result = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$all_countries = $CountryModel->orderBy('country_id', 'ASC')->findAll();
$company_types = $ConstantsModel->where('type', 'company_type')->orderBy('constants_id', 'ASC')->findAll();
$status = $result['is_active'];
if ($status == 1) {
    $status = '<span class="badge badge-light-success"><em class="icon ni ni-check-circle"></em> ' . lang('Main.xin_employees_active') . '</span>';
} else {
    $status = '<span class="badge badge-light-danger"><em class="icon ni ni-check-circle"></em> ' . lang('Main.xin_employees_inactive') . '</span>';
}
$status_label = '<i class="fas fa-certificate text-success bg-icon"></i><i class="fas fa-check front-icon text-white"></i>';

$builder = $db->table('ci_user_summary');
$existing_record = $builder->where('user_id', $user_info['user_id'])->get()->getRow();

$currency = $ConstantsModel->where('type', 'currency_type')->orderBy('constants_id', 'ASC')->findAll();
$language = $LanguageModel->where('is_active', 1)->orderBy('language_id', 'ASC')->findAll();

$category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();
$singledocument_data = $documentModel->where('user_id', $result['user_id'])->first();

if (!isEmpty($singledocument_data)) {
    $documentoption_item = $itemDocument->where('employe_docu_id', $singledocument_data['id'])->findAll();
} else {
    $documentoption_item = [];
}


$assets_list = $AssetsModel->where('employee_id', $result['user_id'])->findAll();
$xin_system = erp_company_settings();


?>
<meta name="csrf-token" content="<?= csrf_token() ?>">

<style>
    .nav-tabs .nav-item .nav-link {
        background-color: white;
        color: black;
        /* border: 1px solid #ccc; */
    }

    .nav-tabs .nav-item .nav-link.active {
        background-color: lightgray;
        color: black;
        /* border: 1px solid #999; */
    }

    /* Sub-tabs active and inactive styles */
    .sub-nav-tabs .nav-link {
        background-color: white;
        color: black;
        border: 1px solid #ccc;
    }

    .sub-nav-tabs .nav-link.active {
        background-color: gray;
        color: white;
        border: 1px solid #999;
    }

    .nav-tabs .nav-link {
        border-radius: 0 !important;
    }

    .nav-tabs .nav-link.active {
        border-bottom: none;
    }

    .navigatiob-bar {
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-top: 1px solid #dee2e6;
        border-left: 1px solid #dee2e6;

    }

    .navigation-link {
        display: block;
        padding: .5rem 1rem;
        color: gray;
    }

    .nav-item {
        position: relative;
    }

    .navigation-link {
        display: inline-block;
        text-decoration: none;
        padding: 10px 15px;
        color: #333;
        transition: color 0.3s ease;
    }


    .navigation-link.active::after {
        content: "";
        position: absolute;
        bottom: -1px;
        left: 50%;
        transform: translateX(-50%);
        width: 12px;
        height: 8px;
        background-color: black;
        clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
    }

    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-top: 1px solid #dee2e6;
        border-left: 1px solid #dee2e6;
    }

    hr {
        margin-top: 0px !important;
    }
</style>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-body">
            <!-- Profile Section -->
            <div class="row align-items-center">
                <!-- Profile Image -->
                <div class="col-1">
                    <?php
                    $profilePhoto = staff_profile_photo($result['user_id']) ?? '';
                    $dummyImage = base_url('assets/images/images.png');

                    // var_dump($profilePhoto);
                    // die;
                    if (!empty($profilePhoto)) {
                        $filePath = FCPATH . 'uploads/users/' . $profilePhoto;
                        if (!file_exists($filePath)) {
                            $profilePhoto = ''; // Reset if file doesn't exist
                        }
                    }

                    // Determine final image path
                    $finalImage = !empty($profilePhoto) ? base_url('uploads/users/' . $profilePhoto) : $dummyImage;
                    ?>
                    <img src="<?= $finalImage; ?>" alt="Profile Image"
                        style="width: 119px; height: 148px; border: 2px solid #ddd; object-fit: cover;">
                </div>

                <!-- Profile Details -->
                <div class="col-10 ml-5">
                    <div class="d-flex flex-column align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <h4 class="mb-0 mr-3"><?= $result['first_name'] . ' ' . $result['last_name']; ?></h4>
                            <span class=""><?= $status; ?></span>

                        </div>
                        <p class="mb-0 text-muted">@<?= $result['username']; ?></p>
                    </div>

                    <div class="row">

                        <div class="col-md-3">
                            <p class="mb-1"><strong><i class="fas fa-envelope"></i></strong> <?= $result['email']; ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong><i class="fas fa-phone-alt"></i></strong> +91-
                                <?= $result['contact_number']; ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1">
                                <strong><i class="fas fa-map-marker-alt"></i></strong>
                                <?= !empty($result['city']) ? " " . $result['city'] : " Haridwar, Uttarakhand"; ?>
                            </p>
                        </div>

                        <div class="col-md-3">
                            <p class="mb-1"><strong><i class="fas fa-id-card"></i></strong>
                                <?= getEmployeeId($result['user_id']); ?></p>
                        </div>
                    </div>
                    <hr>

                    <!-- Contact Info -->
                    <div class="row">
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Department</strong> <br>
                                <?php
                                if ($result['user_type'] == 'staff') {
                                    department($result['user_id']);
                                }

                                ?>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1"><strong>Designation</strong> <br>
                                <?php if ($result['user_type'] == 'staff') {
                                    designation($result['user_id']);
                                } ?></p>
                        </div>

                    </div>
                </div>
            </div>

            <hr>

            <!-- Navigation Tabs -->
            <ul class="nav navigatiob-bar">
                <li class="nav-item">
                    <a class="navigation-link active" href="#about" data-toggle="tab">ABOUT</a>
                </li>
                <li class="nav-item">
                    <a class="navigation-link" href="#profile" data-toggle="tab">PROFILE </a>
                </li>
                <li class="nav-item">
                    <a class="navigation-link" href="#documents" data-toggle="tab">DOCUMENTS </a>
                </li>
                <li class="nav-item">
                    <a class="navigation-link" href="#assets" data-toggle="tab">ASSETS </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3"> <!-- About Tab -->
                <div class="tab-pane fade show active" id="about">
                    <ul class="nav nav-tabs sub-nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#about-summary" data-toggle="tab">Summary</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="about-summary">

                            <form action="<?= site_url('erp/save-summary'); ?>" method="post">
                                <?= csrf_field(); ?>
                                <p>What I love about my job?</p>
                                <input type="hidden" name="user_id" class="form-control mb-2"
                                    value="<?= isset($existing_record->user_id) ? $existing_record->user_id : ''; ?>">
                                <textarea name="about_job" class="form-control mb-2"
                                    placeholder="Add your response"><?= isset($existing_record->about_job) ? htmlspecialchars($existing_record->about_job) : ''; ?></textarea>


                                <p>Professional Overview</p>
                                <textarea type="text" name="professional_overview" class="form-control mb-2"
                                    placeholder="Describe your professional journey"><?= isset($existing_record->pro_overview) ? $existing_record->pro_overview : ''; ?></textarea>

                                <p>Achievements</p>
                                <input type="text" name="achievements" class="form-control mb-2"
                                    placeholder="List your achievements"
                                    value="<?= isset($existing_record->achievements) ? $existing_record->achievements : ''; ?>">

                                <p>Strengths</p>
                                <input type="text" name="strengths" class="form-control mb-2"
                                    placeholder="Highlight your key strengths"
                                    value="<?= isset($existing_record->strengths) ? $existing_record->strengths : ''; ?>">

                                <p>Current Projects</p>
                                <textarea type="text" name="current_projects" class="form-control mb-2"
                                    placeholder="Mention your current projects"><?= isset($existing_record->current_projects) ? $existing_record->current_projects : ''; ?></textarea>

                                <div class="text-right">
                                    <button type="submit" class="btn"
                                        style="background-color: #007bff; color:white; width: 125px;">
                                        <?= lang('Main.xin_save'); ?>
                                    </button>
                                </div>
                            </form>

                        </div>



                    </div>
                </div>
                <!-- Profile Tab -->
                <div class="tab-pane fade" id="profile">
                    <ul class="nav nav-tabs sub-nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#profile-details"
                                data-toggle="tab">Account Settings</a>
                        </li>
                        <li class="nav-item"> <a class="nav-link" href="#profile-settings" data-toggle="tab">Profile
                                Settings</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="#profile-picture" data-toggle="tab">Profile
                                Picture</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="#company-info" data-toggle="tab">company
                                Info</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="#forgot-password" data-toggle="tab">Forgot
                                Password</a> </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="profile-details">
                            <div class="card">

                                <?= form_open(
                                    'erp/system-info',
                                    ['name' => 'system_info', 'id' => 'system_info', 'autocomplete' => 'off'],
                                    ['token' => uencode($usession['sup_user_id'])]
                                ) ?>

                                <div class="bg-white" style="margin: 30px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <?= lang('Main.xin_ci_default_language'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="default_language"
                                                    data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_ci_default_language'); ?>">
                                                    <?php foreach ($language as $lang): ?>
                                                        <option value="<?= $lang['language_code']; ?>" <?php if ($xin_system['default_language'] == $lang['language_code']) { ?>
                                                            selected <?php } ?>>
                                                            <?= $lang['language_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <?= lang('Main.xin_date_format'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="date_format" data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_date_format'); ?>">
                                                    <option value=""><?= lang('Main.xin_select_one'); ?></option>
                                                    <option value="Y-m-d" <?php if ($xin_system['date_format_xi'] == 'Y-m-d') { ?> selected <?php } ?>>Format: <?= date('Y-m-d'); ?></option>
                                                    <option value="Y-d-m" <?php if ($xin_system['date_format_xi'] == 'Y-d-m') { ?> selected <?php } ?>>Format: <?= date('Y-d-m'); ?></option>
                                                    <option value="d-m-Y" <?php if ($xin_system['date_format_xi'] == 'd-m-Y') { ?> selected <?php } ?>>Format: <?= date('d-m-Y'); ?></option>
                                                    <option value="m-d-Y" <?php if ($xin_system['date_format_xi'] == 'm-d-Y') { ?> selected <?php } ?>>Format: <?= date('m-d-Y'); ?></option>
                                                    <option value="Y/m/d" <?php if ($xin_system['date_format_xi'] == 'Y/m/d') { ?> selected <?php } ?>>Format: <?= date('Y/m/d'); ?></option>
                                                    <option value="Y/d/m" <?php if ($xin_system['date_format_xi'] == 'Y/d/m') { ?> selected <?php } ?>>Format: <?= date('Y/d/m'); ?></option>
                                                    <option value="d/m/Y" <?php if ($xin_system['date_format_xi'] == 'd/m/Y') { ?> selected <?php } ?>>Format: <?= date('d/m/Y'); ?></option>
                                                    <option value="m/d/Y" <?php if ($xin_system['date_format_xi'] == 'm/d/Y') { ?> selected <?php } ?>>Format: <?= date('m/d/Y'); ?></option>
                                                    <option value="Y.m.d" <?php if ($xin_system['date_format_xi'] == 'Y.m.d') { ?> selected <?php } ?>>Format: <?= date('Y.m.d'); ?></option>
                                                    <option value="Y.d.m" <?php if ($xin_system['date_format_xi'] == 'Y.d.m') { ?> selected <?php } ?>>Format: <?= date('Y.d.m'); ?></option>
                                                    <option value="d.m.Y" <?php if ($xin_system['date_format_xi'] == 'd.m.Y') { ?> selected <?php } ?>>Format: <?= date('d.m.Y'); ?></option>
                                                    <option value="m.d.Y" <?php if ($xin_system['date_format_xi'] == 'm.d.Y') { ?> selected <?php } ?>>Format: <?= date('m.d.Y'); ?></option>
                                                    <option value="F j, Y" <?php if ($xin_system['date_format_xi'] == 'F j, Y') { ?> selected <?php } ?>>Format: <?= date('F j, Y'); ?>
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <?= lang('Main.xin_default_currency'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="default_currency"
                                                    data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_default_currency'); ?>">
                                                    <option value="INR" <?php if ($xin_system['default_currency'] == 'INR'): ?> selected="selected"
                                                        <?php endif; ?>>United States Dollars</option>
                                                    <option value="EUR" <?php if ($xin_system['default_currency'] == 'EUR'): ?> selected="selected"
                                                        <?php endif; ?>>Euro</option>
                                                    <option value="GBP" <?php if ($xin_system['default_currency'] == 'GBP'): ?> selected="selected"
                                                        <?php endif; ?>>United Kingdom Pounds</option>
                                                    <option value="CNY" <?php if ($xin_system['default_currency'] == 'CNY'): ?> selected="selected"
                                                        <?php endif; ?>>China Yuan Renmimbi</option>
                                                    <option value="AUD" <?php if ($xin_system['default_currency'] == 'AUD'): ?> selected="selected"
                                                        <?php endif; ?>>Australia Dollars</option>
                                                    <option value="DZD" <?php if ($xin_system['default_currency'] == 'DZD'): ?> selected="selected"
                                                        <?php endif; ?>>Algeria Dinars</option>
                                                    <option value="ARP" <?php if ($xin_system['default_currency'] == 'ARP'): ?> selected="selected"
                                                        <?php endif; ?>>Argentina Pesos</option>
                                                    <option value="ATS" <?php if ($xin_system['default_currency'] == 'ATS'): ?> selected="selected"
                                                        <?php endif; ?>>Austria Schillings</option>
                                                    <option value="BSD" <?php if ($xin_system['default_currency'] == 'BSD'): ?> selected="selected"
                                                        <?php endif; ?>>Bahamas Dollars</option>
                                                    <option value="BBD" <?php if ($xin_system['default_currency'] == 'BBD'): ?> selected="selected"
                                                        <?php endif; ?>>Barbados Dollars</option>
                                                    <option value="BEF" <?php if ($xin_system['default_currency'] == 'BEF'): ?> selected="selected"
                                                        <?php endif; ?>>Belgium Francs</option>
                                                    <option value="BMD" <?php if ($xin_system['default_currency'] == 'BMD'): ?> selected="selected"
                                                        <?php endif; ?>>Bermuda Dollars</option>
                                                    <option value="BRR" <?php if ($xin_system['default_currency'] == 'BRR'): ?> selected="selected"
                                                        <?php endif; ?>>Brazil Real</option>
                                                    <option value="BGL" <?php if ($xin_system['default_currency'] == 'BGL'): ?> selected="selected"
                                                        <?php endif; ?>>Bulgaria Lev</option>
                                                    <option value="CAD" <?php if ($xin_system['default_currency'] == 'CAD'): ?> selected="selected"
                                                        <?php endif; ?>>Canada Dollars</option>
                                                    <option value="CLP" <?php if ($xin_system['default_currency'] == 'CLP'): ?> selected="selected"
                                                        <?php endif; ?>>Chile Pesos</option>

                                                    <option value="CYP" <?php if ($xin_system['default_currency'] == 'CYP'): ?> selected="selected"
                                                        <?php endif; ?>>Cyprus Pounds</option>
                                                    <option value="CSK" <?php if ($xin_system['default_currency'] == 'CSK'): ?> selected="selected"
                                                        <?php endif; ?>>Czech Republic Koruna</option>
                                                    <option value="DKK" <?php if ($xin_system['default_currency'] == 'DKK'): ?> selected="selected"
                                                        <?php endif; ?>>Denmark Kroner</option>
                                                    <option value="NLG" <?php if ($xin_system['default_currency'] == 'NLG'): ?> selected="selected"
                                                        <?php endif; ?>>Dutch Guilders</option>
                                                    <option value="XCD" <?php if ($xin_system['default_currency'] == 'XCD'): ?> selected="selected"
                                                        <?php endif; ?>>Eastern Caribbean Dollars</option>
                                                    <option value="EGP" <?php if ($xin_system['default_currency'] == 'EGP'): ?> selected="selected"
                                                        <?php endif; ?>>Egypt Pounds</option>
                                                    <option value="FJD" <?php if ($xin_system['default_currency'] == 'FJD'): ?> selected="selected"
                                                        <?php endif; ?>>Fiji Dollars</option>
                                                    <option value="FIM" <?php if ($xin_system['default_currency'] == 'FIM'): ?> selected="selected"
                                                        <?php endif; ?>>Finland Markka</option>
                                                    <option value="FRF" <?php if ($xin_system['default_currency'] == 'FRF'): ?> selected="selected"
                                                        <?php endif; ?>>France Francs</option>
                                                    <option value="DEM" <?php if ($xin_system['default_currency'] == 'DEM'): ?> selected="selected"
                                                        <?php endif; ?>>Germany Deutsche Marks</option>
                                                    <option value="XAU" <?php if ($xin_system['default_currency'] == 'XAU'): ?> selected="selected"
                                                        <?php endif; ?>>Gold Ounces</option>
                                                    <option value="GRD" <?php if ($xin_system['default_currency'] == 'GRD'): ?> selected="selected"
                                                        <?php endif; ?>>Greece Drachmas</option>
                                                    <option value="HKD" <?php if ($xin_system['default_currency'] == 'HKD'): ?> selected="selected"
                                                        <?php endif; ?>>Hong Kong Dollars</option>
                                                    <option value="HUF" <?php if ($xin_system['default_currency'] == 'HUF'): ?> selected="selected"
                                                        <?php endif; ?>>Hungary Forint</option>
                                                    <option value="ISK" <?php if ($xin_system['default_currency'] == 'ISK'): ?> selected="selected"
                                                        <?php endif; ?>>Iceland Krona</option>
                                                    <option value="INR" <?php if ($xin_system['default_currency'] == 'INR'): ?> selected="selected"
                                                        <?php endif; ?>>India Rupees</option>
                                                    <option value="IDR" <?php if ($xin_system['default_currency'] == 'IDR'): ?> selected="selected"
                                                        <?php endif; ?>>Indonesia Rupiah</option>
                                                    <option value="IEP" <?php if ($xin_system['default_currency'] == 'IEP'): ?> selected="selected"
                                                        <?php endif; ?>>Ireland Punt</option>
                                                    <option value="ILS" <?php if ($xin_system['default_currency'] == 'ILS'): ?> selected="selected"
                                                        <?php endif; ?>>Israel New Shekels</option>
                                                    <option value="ITL" <?php if ($xin_system['default_currency'] == 'ITL'): ?> selected="selected"
                                                        <?php endif; ?>>Italy Lira</option>
                                                    <option value="JMD" <?php if ($xin_system['default_currency'] == 'JMD'): ?> selected="selected"
                                                        <?php endif; ?>>Jamaica Dollars</option>
                                                    <option value="JPY" <?php if ($xin_system['default_currency'] == 'JPY'): ?> selected="selected"
                                                        <?php endif; ?>>Japan Yen</option>
                                                    <option value="JOD" <?php if ($xin_system['default_currency'] == 'JOD'): ?> selected="selected"
                                                        <?php endif; ?>>Jordan Dinar</option>
                                                    <option value="KRW" <?php if ($xin_system['default_currency'] == 'KRW'): ?> selected="selected"
                                                        <?php endif; ?>>Korea (South) Won</option>
                                                    <option value="LBP" <?php if ($xin_system['default_currency'] == 'LBP'): ?> selected="selected"
                                                        <?php endif; ?>>Lebanon Pounds</option>
                                                    <option value="LUF" <?php if ($xin_system['default_currency'] == 'LUF'): ?> selected="selected"
                                                        <?php endif; ?>>Luxembourg Francs</option>
                                                    <option value="MYR" <?php if ($xin_system['default_currency'] == 'MYR'): ?> selected="selected"
                                                        <?php endif; ?>>Malaysia Ringgit</option>
                                                    <option value="MXP" <?php if ($xin_system['default_currency'] == 'MXP'): ?> selected="selected"
                                                        <?php endif; ?>>Mexico Pesos</option>
                                                    <option value="NLG" <?php if ($xin_system['default_currency'] == 'NLG'): ?> selected="selected"
                                                        <?php endif; ?>>Netherlands Guilders</option>
                                                    <option value="NZD" <?php if ($xin_system['default_currency'] == 'NZD'): ?> selected="selected"
                                                        <?php endif; ?>>New Zealand Dollars</option>
                                                    <option value="NOK" <?php if ($xin_system['default_currency'] == 'NOK'): ?> selected="selected"
                                                        <?php endif; ?>>Norway Kroner</option>
                                                    <option value="PKR" <?php if ($xin_system['default_currency'] == 'PKR'): ?> selected="selected"
                                                        <?php endif; ?>>Pakistan Rupees</option>
                                                    <option value="XPD" <?php if ($xin_system['default_currency'] == 'XPD'): ?> selected="selected"
                                                        <?php endif; ?>>Palladium Ounces</option>
                                                    <option value="PHP" <?php if ($xin_system['default_currency'] == 'PHP'): ?> selected="selected"
                                                        <?php endif; ?>>Philippines Pesos</option>
                                                    <option value="XPT" <?php if ($xin_system['default_currency'] == 'XPT'): ?> selected="selected"
                                                        <?php endif; ?>>Platinum Ounces</option>
                                                    <option value="PLZ" <?php if ($xin_system['default_currency'] == 'PLZ'): ?> selected="selected"
                                                        <?php endif; ?>>Poland Zloty</option>
                                                    <option value="PTE" <?php if ($xin_system['default_currency'] == 'PTE'): ?> selected="selected"
                                                        <?php endif; ?>>Portugal Escudo</option>
                                                    <option value="ROL" <?php if ($xin_system['default_currency'] == 'ROL'): ?> selected="selected"
                                                        <?php endif; ?>>Romania Leu</option>
                                                    <option value="RUR" <?php if ($xin_system['default_currency'] == 'RUR'): ?> selected="selected"
                                                        <?php endif; ?>>Russia Rubles</option>
                                                    <option value="SAR" <?php if ($xin_system['default_currency'] == 'SAR'): ?> selected="selected"
                                                        <?php endif; ?>>Saudi Arabia Riyal</option>
                                                    <option value="XAG" <?php if ($xin_system['default_currency'] == 'XAG'): ?> selected="selected"
                                                        <?php endif; ?>>Silver Ounces</option>
                                                    <option value="SGD" <?php if ($xin_system['default_currency'] == 'SGD'): ?> selected="selected"
                                                        <?php endif; ?>>Singapore Dollars</option>
                                                    <option value="SKK" <?php if ($xin_system['default_currency'] == 'SKK'): ?> selected="selected"
                                                        <?php endif; ?>>Slovakia Koruna</option>
                                                    <option value="ZAR" <?php if ($xin_system['default_currency'] == 'ZAR'): ?> selected="selected"
                                                        <?php endif; ?>>South Africa Rand</option>
                                                    <option value="KRW" <?php if ($xin_system['default_currency'] == 'KRW'): ?> selected="selected"
                                                        <?php endif; ?>>South Korea Won</option>
                                                    <option value="ESP" <?php if ($xin_system['default_currency'] == 'ESP'): ?> selected="selected"
                                                        <?php endif; ?>>Spain Pesetas</option>
                                                    <option value="XDR" <?php if ($xin_system['default_currency'] == 'XDR'): ?> selected="selected"
                                                        <?php endif; ?>>Special Drawing Right (IMF)</option>
                                                    <option value="SDD" <?php if ($xin_system['default_currency'] == 'SDD'): ?> selected="selected"
                                                        <?php endif; ?>>Sudan Dinar</option>
                                                    <option value="SEK" <?php if ($xin_system['default_currency'] == 'SEK'): ?> selected="selected"
                                                        <?php endif; ?>>Sweden Krona</option>
                                                    <option value="CHF" <?php if ($xin_system['default_currency'] == 'CHF'): ?> selected="selected"
                                                        <?php endif; ?>>Switzerland Francs</option>
                                                    <option value="TWD" <?php if ($xin_system['default_currency'] == 'TWD'): ?> selected="selected"
                                                        <?php endif; ?>>Taiwan Dollars</option>
                                                    <option value="THB" <?php if ($xin_system['default_currency'] == 'THB'): ?> selected="selected"
                                                        <?php endif; ?>>Thailand Baht</option>
                                                    <option value="TTD" <?php if ($xin_system['default_currency'] == 'TTD'): ?> selected="selected"
                                                        <?php endif; ?>>Trinidad and Tobago Dollars</option>
                                                    <option value="TRL" <?php if ($xin_system['default_currency'] == 'TRL'): ?> selected="selected"
                                                        <?php endif; ?>>Turkey Lira</option>
                                                    <option value="VEB" <?php if ($xin_system['default_currency'] == 'VEB'): ?> selected="selected"
                                                        <?php endif; ?>>Venezuela Bolivar</option>
                                                    <option value="ZMK" <?php if ($xin_system['default_currency'] == 'ZMK'): ?> selected="selected"
                                                        <?php endif; ?>>Zambia Kwacha</option>
                                                    <option value="XCD" <?php if ($xin_system['default_currency'] == 'XCD'): ?> selected="selected"
                                                        <?php endif; ?>>Eastern Caribbean Dollars</option>
                                                    <option value="XDR" <?php if ($xin_system['default_currency'] == 'XDR'): ?> selected="selected"
                                                        <?php endif; ?>>Special Drawing Right (IMF)</option>
                                                    <option value="XAG" <?php if ($xin_system['default_currency'] == 'XAG'): ?> selected="selected"
                                                        <?php endif; ?>>Silver Ounces</option>
                                                    <option value="XAU" <?php if ($xin_system['default_currency'] == 'XAU'): ?> selected="selected"
                                                        <?php endif; ?>>Gold Ounces</option>
                                                    <option value="XPD" <?php if ($xin_system['default_currency'] == 'XPD'): ?> selected="selected"
                                                        <?php endif; ?>>Palladium Ounces</option>
                                                    <option value="XPT" <?php if ($xin_system['default_currency'] == 'XPT'): ?> selected="selected"
                                                        <?php endif; ?>>Platinum Ounces</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <?= lang('Main.xin_setting_timezone'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="system_timezone"
                                                    data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_setting_timezone'); ?>">
                                                    <option value="">
                                                        <?= lang('Main.xin_select_one'); ?>
                                                    </option>
                                                    <?php foreach (generate_timezone_list() as $tval => $labels): ?>
                                                        <option value="<?= $tval; ?>" <?php if ($xin_system['system_timezone'] == $tval) { ?> selected <?php } ?>>
                                                            <?= $labels; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label">
                                                    <?= lang('Main.xin_invoice_terms_condition'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <textarea class="form-control" name="invoice_terms_condition" rows="3"><?= $xin_system['invoice_terms_condition']; ?>
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right" style="margin-right:30px">
                                    <button type="submit" class="btn"
                                        style="background-color: #007bff; color:white;width: 125px;">
                                        <?= lang('Main.xin_save'); ?>
                                    </button>
                                </div>
                                <?= form_close(); ?>
                            </div>


                        </div>
                        <div class="tab-pane fade" id="profile-settings">
                            <div class="card">

                                <?= form_open(
                                    'erp/update-profile',
                                    ['name' => 'edit_user', 'id' => 'edit_user', 'autocomplete' => 'off'],
                                    ['token' => uencode($usession['sup_user_id'])]
                                ) ?>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="company_name">
                                                    <?= lang('Main.xin_employee_first_name'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_employee_first_name'); ?>"
                                                    name="first_name" type="text" value="<?= $result['first_name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="last_name" class="control-label">
                                                    <?= lang('Main.xin_employee_last_name'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_employee_last_name'); ?>"
                                                    name="last_name" type="text" value="<?= $result['last_name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email">
                                                    <?= lang('Main.xin_email'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <input class="form-control" placeholder="<?= lang('Main.xin_email'); ?>"
                                                    name="email" type="email" value="<?= $result['email']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="email">
                                                    <?= lang('Main.dashboard_username'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.dashboard_username'); ?>"
                                                    name="username" type="text" value="<?= $result['username']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="contact_number">
                                                    <?= lang('Main.xin_contact_number'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_contact_number'); ?>"
                                                    name="contact_number" type="text"
                                                    value="<?= $result['contact_number']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="gender" class="control-label">
                                                    <?= lang('Main.xin_employee_gender'); ?>
                                                </label>
                                                <select class="form-control" name="gender" data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_employee_gender'); ?>">
                                                    <option value="1" <?php if ('1' == $result['gender']): ?>
                                                        selected="selected" <?php endif; ?>>
                                                        <?= lang('Main.xin_gender_male'); ?>
                                                    </option>
                                                    <option value="2" <?php if ('2' == $result['gender']): ?>
                                                        selected="selected" <?php endif; ?>>
                                                        <?= lang('Main.xin_gender_female'); ?>
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="country">
                                                    <?= lang('Main.xin_country'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="country" data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Main.xin_country'); ?>">
                                                    <option value="">
                                                        <?= lang('Main.xin_select_one'); ?>
                                                    </option>
                                                    <?php foreach ($all_countries as $country) { ?>
                                                        <option value="<?= $country['country_id']; ?>" <?php if ($country['country_id'] == $result['country']): ?>
                                                            selected="selected" <?php endif; ?>>
                                                            <?= $country['country_name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address_1">
                                                    <?= lang('Main.xin_address'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_address'); ?>" name="address_1"
                                                    type="text" value="<?= $result['address_1']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="address_2"> &nbsp;</label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_address_2'); ?>" name="address_2"
                                                    type="text" value="<?= $result['address_2']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="city">
                                                    <?= lang('Main.xin_city'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="<?= lang('Main.xin_city'); ?>"
                                                    name="city" type="text" value="<?= $result['city']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state">
                                                    <?= lang('Main.xin_state'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="<?= lang('Main.xin_state'); ?>"
                                                    name="state" type="text" value="<?= $result['state']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="zipcode">
                                                    <?= lang('Main.xin_zipcode'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Main.xin_zipcode'); ?>" name="zipcode"
                                                    type="text" value="<?= $result['zipcode']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" text-right">
                                        <button type="submit" class="btn "
                                            style="background-color: #007bff; color:white;width: 125px;">
                                            <?= lang('Main.xin_save'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>

                        <div class="tab-pane fade " id="profile-picture">
                            <div class="card">

                                <?= form_open_multipart(
                                    'erp/update-profile-photo',
                                    [
                                        'name' => 'edit_user_photo',
                                        'id' => 'edit_user_photo',
                                        'autocomplete' => 'off',
                                        'class' => 'form-horizontal'
                                    ],
                                    [
                                        'token' => uencode($usession['sup_user_id'])
                                    ]
                                ) ?>
                                <div class="card-body pb-2">
                                    <input type="hidden" name="type" value="edit_record">
                                    <input type="hidden" name="is_ajax" value="1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="logo">
                                                    Profile Image
                                                    <span class="text-danger">*</span> </label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="file">
                                                    <label class="custom-file-label">
                                                        <?= lang('Main.xin_choose_file'); ?>
                                                    </label>
                                                    <small>
                                                        <?= lang('Main.xin_company_file_type'); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn ladda-button"
                                            style="background-color: #007bff; color:white;width: 125px;">
                                            <?= lang('Main.xin_save'); ?>
                                        </button>
                                    </div>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>

                        <div class="tab-pane fade " id="company-info">
                            <div class="card">

                                <?= form_open(
                                    'erp/update-company-info',
                                    ['name' => 'company_info', 'id' => 'company_info', 'autocomplete' => 'off'],
                                    ['token' => uencode($usession['sup_user_id'])]
                                ) ?>

                                <div class="form-body" style="margin: 30px;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name">
                                                    <?= lang('Company.xin_company_name'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <input class="form-control"
                                                    placeholder="<?= lang('Company.xin_company_name'); ?>"
                                                    name="company_name" type="text"
                                                    value="<?= $user_info['company_name']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">
                                                    <?= lang('Company.xin_company_type'); ?>
                                                    <span class="text-danger">*</span> </label>
                                                <select class="form-control" name="company_type"
                                                    data-plugin="select_hrm"
                                                    data-placeholder="<?= lang('Company.xin_company_type'); ?>"
                                                    readonly>
                                                    <option value="">
                                                        <?= lang('Main.xin_select_one'); ?>
                                                    </option>
                                                    <?php foreach ($company_types as $ctype) { ?>
                                                        <option value="<?= $ctype['constants_id']; ?>" <?php if ($user_info['company_type_id'] == $ctype['constants_id']) { ?>
                                                            selected="selected" <?php } ?>>
                                                            <?= $ctype['category_name']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="trading_name">
                                                    <?= lang('Company.xin_company_trading'); ?>
                                                </label>
                                                <input class="form-control" readonly
                                                    placeholder="<?= lang('Company.xin_company_trading'); ?>"
                                                    name="trading_name" type="text"
                                                    value="<?= $user_info['trading_name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="xin_gtax">
                                                    <?= lang('Company.xin_gtax'); ?>
                                                </label>
                                                <input class="form-control" readonly
                                                    placeholder="<?= lang('Company.xin_gtax'); ?>" name="xin_gtax"
                                                    type="text" value="<?= $user_info['government_tax']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="registration_no">
                                                    <?= lang('Company.xin_company_registration'); ?>
                                                </label>
                                                <input class="form-control" readonly
                                                    placeholder="<?= lang('Company.xin_company_registration'); ?>"
                                                    name="registration_no" type="text"
                                                    value="<?= $user_info['registration_no']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn"
                                        style="background-color: #007bff; color:white;width: 125px; margin-right:30px">
                                        <?= lang('Main.xin_save'); ?>
                                    </button>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="forgot-password">
                            <div class="card">

                                <?= form_open(
                                    'erp/update-password',
                                    ['name' => 'change_password', 'id' => 'change_password', 'autocomplete' => 'off'],
                                    ['token' => uencode($usession['sup_user_id'])]
                                ) ?>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>
                                                    <?= lang('Main.xin_current_password'); ?>
                                                </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="fas fa-eye"></i></span></div>
                                                    <input type="password" readonly="readonly" class="form-control"
                                                        name="pass"
                                                        placeholder="<?= lang('Main.xin_current_password'); ?>"
                                                        value="********">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>
                                                    <?= lang('Main.xin_new_password'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="fas fa-eye"></i></span></div>
                                                    <input type="password" class="form-control" name="new_password"
                                                        placeholder="<?= lang('Main.xin_new_password'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>
                                                    <?= lang('Main.xin_repeat_new_password'); ?>
                                                    <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="fas fa-eye"></i></span></div>
                                                    <input type="password" class="form-control" name="confirm_password"
                                                        placeholder="<?= lang('Main.xin_repeat_new_password'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit"
                                        class="btn btn-danger"><?= lang('Main.header_change_password'); ?></button>
                                </div>
                                <?= form_close(); ?>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents">
                    <ul class="nav nav-tabs sub-nav-tabs mb-4">
                        <li class="nav-item"> <a class="nav-link active" href="#doc-uploaded"
                                data-toggle="tab">Uploaded</a> </li>
                        <li class="nav-item"> <a class="nav-link" href="#doc-pending" data-toggle="tab">Pending</a>
                        </li>
                    </ul>
                    <div class="tab-content ">
                        <div class="tab-pane fade show active" id="doc-uploaded">
                            <h5>Uploaded Documents</h5>
                            <div id="DataTables_Table_0_wrapper" class=" no-footer">
                                <table data-last-order-identifier="projects" data-default-order=""
                                    class="table table-projects dataTable no-footer dtr-inline" id="" role="grid"
                                    aria-describedby="DataTables_Table_0_info">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Document Name</th>
                                            <th>Category</th>
                                            <th>Uploaded Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1;
                                        if (!isEmpty($documentoption_item)) {
                                            foreach ($documentoption_item as $row) {
                                                $documents = json_decode($row['data'], true);
                                                foreach ($documents as $doc) {
                                                    echo "<tr>";
                                                    echo "<td>" . $counter++ . "</td>";
                                                    echo "<td>" . htmlspecialchars($doc['docu_name']) . "</td>";
                                                    echo "<td>" . getcategoryName($row['category_id']) . "</td>";
                                                    echo "<td>" . date('d M Y', strtotime($row['created_at'])) . "</td>";

                                                    // Download button with tooltip
                                                    echo "<td>
                                                    <a href='" . base_url('/public/uploads/employe_document/') . $doc['docu_image'] . "' 
                                                       class='btn btn-sm' style='background: none; color: inherit;' download title='Download Document'>
                                                       <i class='fas fa-download' style='color: #28a745;'></i>
                                                    </a>
                                                    <a href='" . base_url('view/employe-document/' . base64_encode($singledocument_data['id'])) . "' 
                                                       class='btn btn-sm' style='background: none; color: inherit;' title='View Document'>
                                                       <i class='fas fa-eye' style='color: #007bff;'></i>
                                                    </a>
                                                  </td>";
                                                    echo "</tr>";
                                                }
                                            }
                                        }
                                        ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="doc-pending">
                            <h5>Pending Documents</h5>
                            <p class="text-muted">Below are the documents that need to be submitted:</p>
                            <ul class="list-group">
                                <?php
                                if (!isEmpty($documentoption_item)) {
                                    $matched_category_ids = array_column($documentoption_item, 'category_id');
                                    $has_pending_categories = false;

                                    foreach ($category_list as $category) {
                                        if (!in_array($category['id'], $matched_category_ids)) {
                                            $has_pending_categories = true;
                                            echo "<li class='list-group-item d-flex justify-content-between align-items-center' style='background-color:#e9e6e6;'>";
                                            echo htmlspecialchars($category['category_name']);
                                            echo "</li>";
                                        }
                                    }

                                    if (!$has_pending_categories) {
                                        echo "<li class='list-group-item' style='background-color:lightgreen;'>";
                                        echo "No documents are pending.";
                                        echo "</li>";
                                    }
                                }
                                ?>


                            </ul>
                        </div>


                    </div>

                </div>
                <!-- Assets Tab -->
                <div class="tab-pane fade" id="assets">
                    <ul class="nav nav-tabs sub-nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" href="#assets-list" data-toggle="tab">List</a>
                        </li>

                    </ul>
                    <div class="tab-content">
                        <!-- Assets List Tab -->
                        <div class="tab-pane fade show active" id="assets-list">
                            <h5>Assets List</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Asset Picture</th>
                                        <th>Asset Name</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1;
                                    foreach ($assets_list as $list) { ?>
                                        <tr>
                                            <td><?= $i++; ?></td>
                                            <td><img src="<?= base_url('public/uploads/asset_image/' . $list['asset_image']) ?>"
                                                    style="height:50px; width:50px;"></td>
                                            <td><?= $list['name']; ?></td>
                                            <td><?= assestCategoryname($list['assets_category_id']) ?></td>
                                            <td><?= getBrandname($list['brand_id']) ?></td>
                                            <td style="color: <?= $list['is_working'] == 1 ? 'green' : 'red'; ?>">
                                                <?= $list['is_working'] == 1 ? 'Working' : 'Not Working'; ?>
                                            </td>

                                            <td>
                                                <a href="<?= base_url('erp/asset-view/' . uencode($list['assets_id'])) ?>"
                                                    class="btn btn-sm" style='background: none; color: inherit;'
                                                    title='View Document'>
                                                    <i class='fas fa-eye' style='color: #007bff;'></i>
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
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#DataTables_Table_0').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable ordering
            lengthMenu: [3, 5, 10, 25, 50, 100], // Options for number of records to show
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Display toastr notifications for flash data
        <?php if (session()->getFlashdata('error')): ?>
            toastr.error(<?= json_encode(esc(session()->getFlashdata('error'))) ?>, 'Error', {
                timeOut: 5000,
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right"
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            toastr.success(<?= json_encode(esc(session()->getFlashdata('success'))) ?>, 'Success', {
                timeOut: 5000,
                closeButton: true,
                progressBar: true,
                positionClass: "toast-top-right"
            });
        <?php endif; ?>
    });
</script>