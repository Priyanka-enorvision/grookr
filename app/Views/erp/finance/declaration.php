<?php


use CodeIgniter\I18n\Time;
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\CountryModel;
use App\Models\LanguageModel;
use App\Models\ConstantsModel;
use App\Models\MembershipModel;
use App\Models\CompanymembershipModel;
use App\Models\VerifyEmployeDocModel;
use App\Models\EmpDocumentItemModel;
use App\Models\DocumentConfigModel;
use App\Models\AssetsModel;
use App\Models\Tax_declarationModel;
use App\Models\PayrollModel;
use App\Models\TaxDurationModel;
use App\Models\ContractModel;

$UsersModel = new UsersModel();
$SystemModel = new SystemModel();
$CountryModel = new CountryModel();
$LanguageModel = new LanguageModel();
$ConstantsModel = new ConstantsModel();
$MembershipModel = new MembershipModel();
$CompanymembershipModel = new CompanymembershipModel();
$documentModel = new VerifyEmployeDocModel();
$itemDocument = new EmpDocumentItemModel();
$doc_categoryModel = new DocumentConfigModel();
$AssetsModel = new AssetsModel();
$Taxdeclarationlist = new Tax_declarationModel();
$PayrollModel = new PayrollModel();
$TaxDurationModel = new TaxDurationModel();
$ContractModel = new ContractModel();

$session = \Config\Services::session();
$db = \Config\Database::connect();
$usession = $session->get('sup_username');
$result = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$builder = $db->table('ci_user_summary');
$existing_record = $builder->where('user_id', $user_info['user_id'])->get()->getRow();

$currency = $ConstantsModel->where('type', 'currency_type')->orderBy('constants_id', 'ASC')->findAll();
$language = $LanguageModel->where('is_active', 1)->orderBy('language_id', 'ASC')->findAll();

$category_list = $doc_categoryModel->where(['company_id' => $user_info['company_id'], 'status' => 1])->findAll();

// $singledocument_data = $documentModel->where('user_id', $user_info['user_id'])->first();

// $documentoption_item = $itemDocument->where('employe_docu_id', $singledocument_data['id'])->findAll();
$singledocument_data = $documentModel->where('user_id', $user_info['user_id'])->first();

if ($singledocument_data) {
    $documentoption_item = $itemDocument->where('employe_docu_id', $singledocument_data['id'])->findAll();
} else {
    $documentoption_item = [];
}

$assets_list = $AssetsModel->where('employee_id', $result['user_id'])->findAll();
$xin_system = erp_company_settings();

$db  = \Config\Database::connect();
$getsalary = $db->table('ci_erp_users_details');

if ($user_info['user_type'] == 'staff') {
    $tax_list = $Taxdeclarationlist->where('company_id', $user_info['company_id'])->where('employee_id', $user_info['user_id'])->orderBy('id', 'desc')->findAll();
    $payslip = $PayrollModel->where('company_id', $user_info['company_id'])
        ->where('staff_id', $employe_id)
        ->orderBy('payslip_id', 'ASC')
        ->findAll();
    $Applytax_duration = $TaxDurationModel->where('company_id', $user_info['company_id'])->first();

    $getsalary->where('user_id', $user_info['user_id']);
    $query = $getsalary->get();
    $getgrossSalary = $query->getRowArray();
    $HRA_Exemption = $ContractModel->where(['option_title' => 'HRA', 'user_id' => $user_info['user_id']])->first();
} else {
    $tax_list = $Taxdeclarationlist->where('company_id', $user_info['company_id'])->where('employee_id', $employe_id)->orderBy('id', 'desc')->findAll();
    $payslip = $PayrollModel->where('company_id', $user_info['company_id'])
        ->where('staff_id', $usession['sup_user_id'])
        ->orderBy('payslip_id', 'ASC')
        ->findAll();

    $getsalary->where('user_id', $employe_id);
    $query = $getsalary->get();
    $getgrossSalary = $query->getRowArray();

    $HRA_Exemption = $ContractModel->where(['user_id' => $employe_id, 'option_title' => 'HRA'])->first();
}

// var_dump($getgrossSalary['basic_salary']);
// var_dump($HRA_Exemption['contract_amount']);
// die;
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">


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

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0px !important;
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

    .section-title {
        font-size: 22px;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }

    .square-box {
        width: 253px;
        height: 142px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin: 10px;
        transition: all 0.3s ease;
        border-radius: 8px;
    }

    .square-box:hover {
        background-color: rgb(203, 203, 203);
    }

    .square-container {
        display: flex;
        flex-wrap: wrap;

    }

    .chart-container {
        margin-top: 30px;
        text-align: center;
    }

    .chart-container h3 {
        margin-bottom: 20px;
    }

    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f9fafb;
        color: #333;
    }

    .container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    li::marker {
        color: #f6aa0f;
    }


    .navigation-link:hover {
        text-decoration: none;
    }

    #financialYearFilter {
        width: 150px;
        border-radius: 0px;
        height: 44px;
    }
</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    rel="stylesheet">



<ul class="nav navigatiob-bar">
    <li class="nav-item">
        <a class="navigation-link active" href="#about" data-toggle="tab">SUMMARY</a>
    </li>
    <li class="nav-item">
        <a class="navigation-link" href="#profile" data-toggle="tab">MY PAY </a>
    </li>
    <li class="nav-item">
        <a class="navigation-link" href="#documents" data-toggle="tab">MANAGE TAX </a>
    </li>

    <li class="nav-item ml-auto">
        <?php
        $currentYear = date('Y');
        $options = [];
        for ($i = 0; $i <= 5; $i++) {
            $startYear = $currentYear - $i;
            $endYear = $startYear + 1;
            $options[] = $startYear . '-' . $endYear;
        }
        ?>
        <select class="form-control" id="financialYearFilter">
            <option value="">Select Year</option>
            <?php foreach ($options as $option) : ?>
                <option value="<?= $option ?>"><?= $option ?></option>
            <?php endforeach; ?>
        </select>
    </li>

</ul>

<!-- Tab Content -->
<div class="tab-content"> <!-- About Tab -->
    <div class="tab-pane fade show active" id="about">
        <ul class="nav nav-tabs sub-nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="#about-summary" data-toggle="tab">Summary</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="about-summary">

                <div class="container">

                    <div class="square-container">
                        <div class="square-box">
                            <div>
                                <h2>Total Revenue</h2>
                                <p>$250,000</p>
                            </div>
                        </div>
                        <div class="square-box">
                            <div>
                                <h2>Total Expenses</h2>
                                <p>$120,000</p>
                            </div>
                        </div>
                        <div class="square-box">
                            <div>
                                <h2>Net Profit</h2>
                                <p>$130,000</p>
                            </div>
                        </div>
                        <div class="square-box">
                            <div>
                                <h2>Tax Savings</h2>
                                <p>$10,000</p>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="chart-container" style="display: flex; align-items: center; justify-content: space-between; gap: 20px;">
                        <!-- Left Side Content -->
                        <div class="content" style="flex: 1; padding: 20px;">

                            <p class="text-left">
                                This chart represents the comparison between revenue and expenses.
                                You can analyze trends and understand how efficiently the financial
                                resources are being managed.
                            </p>
                            <p class="text-left"> <strong>Revenue:</strong> Total income generated.</p>
                            <p class="text-left"> <strong>Expenses:</strong> Total outgoing costs.</p>
                        </div>

                        <!-- Right Side Chart -->
                        <div class="chart" style="flex: 1; display: flex; justify-content: center; align-items: center;">
                            <canvas id="financeChart" width="280" height="200"></canvas>
                        </div>
                    </div>

                </div>

            </div>



        </div>
    </div>
    <!-- Profile Tab -->
    <div class="tab-pane fade" id="profile">
        <ul class="nav nav-tabs sub-nav-tabs mb-3">
            <!-- <li class="nav-item"> <a class="nav-link active" href="#profile-details" data-toggle="tab">My Salary</a> </li> -->
            <li class="nav-item"> <a class="nav-link active" href="#profile-settings" data-toggle="tab">Pay slips</a> </li>
            <li class="nav-item"> <a class="nav-link" href="#profile-picture" data-toggle="tab">Income Tax</a> </li>
        </ul>
        <div class="tab-content">

            <div class="tab-pane fade show active" id="profile-settings" role="tabpanel">
                <div class="card">
                    <div class="panel_s">
                        <div class="panel-body">

                            <hr>
                            <div class="card-body manage">
                                <div class="box-datatable table-responsive">
                                    <table class="table" id="payroll" style="width: 100% !important;">
                                        <thead>
                                            <tr>
                                                <th><?= lang('Dashboard.dashboard_employee'); ?></th>
                                                <th><?= lang('Payroll.xin_net_payable'); ?></th>
                                                <th><?= lang('Payroll.xin_salary_month'); ?></th>
                                                <th><?= lang('Payroll.xin_pay_date'); ?></th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($payslip)) { ?>
                                                <?php foreach ($payslip as $r) {
                                                    $user_detail = $UsersModel->where('user_id', $r['staff_id'])->first();
                                                    $name = $user_detail['first_name'] . ' ' . $user_detail['last_name'];
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-inline-block align-middle">
                                                                <img src="<?= base_url('uploads/users/thumb/' . $user_detail['profile_photo']); ?>"
                                                                    alt="user image" class="img-radius align-top m-r-15" style="width:40px;">
                                                                <div class="d-inline-block">
                                                                    <h6 class="m-b-0"><?= esc($name); ?></h6>
                                                                    <p class="m-b-0"><?= esc($user_detail['email']); ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <h6 class="text-success"><?= number_to_currency($r['net_salary'], $xin_system['default_currency'], null, 2); ?></h6>
                                                        </td>
                                                        <td><?= date('F, Y', strtotime($r['salary_month'])); ?></td>
                                                        <td>
                                                            <h6 class="text-success"><?= number_to_currency($r['net_salary'], $xin_system['default_currency'], null, 2); ?></h6>
                                                        </td>
                                                        <td>
                                                            <span data-toggle="tooltip" data-placement="top" data-state="primary"
                                                                title="<?= lang('Payroll.xin_view_payslip'); ?>">
                                                                <a target="_blank" href="<?= site_url('erp/payroll-view/' . uencode($r['payslip_id'])); ?>">
                                                                    <button type="button" class="btn icon-btn btn-sm btn-light-primary waves-effect waves-light">
                                                                        <i class="feather icon-arrow-right"></i>
                                                                    </button>
                                                                </a>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="4" class="text-center">Data not found</td>
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

            <div class="tab-pane fade" id="profile-picture" role="tabpanel">
                <div class="card">
                    <div class="panel_s">
                        <div class="panel-body" style="background-color :#f1f1f1; padding: 22px;">
                            <p class="mb-0" style="font-size: 24px;">Income Tax Computation</p>
                            <p class="text-muted mb-5">View the complete breakup or the payment deduction declaration to analyze how income tax is being calculated and what is the TDS every month.</p>


                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="mt-3">
                                        <i class="fas fa-lightbulb" style="color: orange;"></i> Important
                                    </h6>
                                    <ul>
                                        <li style="font-size: 14px;margin-bottom: 5px;">The current income tax calculation is considering the declared amount of investment declaration, regardless of the approval status.</li>
                                        <li style="font-size: 14px;">Your income tax and liability are being computed as per the old tax regime. To learn more and switch to the new tax regime, click here.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6>Income Tax Details</h6>
                                    <div class="row">
                                        <div class="col-3">
                                            <p>Annual Taxable Income: <strong>$50,000</strong></p>
                                        </div>
                                        <div class="col-3">
                                            <p>Tax Paid: <strong>$5,000</strong></p>
                                        </div>
                                        <div class="col-3">
                                            <p>Remaining Tax Liability: <strong>$2,000</strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Documents Tab -->
    <div class="tab-pane fade" id="documents">
        <ul class="nav nav-tabs sub-nav-tabs mb-4">
            <li class="nav-item"> <a class="nav-link active" href="#doc-uploaded" data-toggle="tab">Declaration</a> </li>
            <li class="nav-item"> <a class="nav-link" href="#forms-tab" data-toggle="tab">Forms</a> </li>
            <li class="nav-item"> <a class="nav-link" href="#doc-pending" data-toggle="tab">Tax Filling</a> </li>
            <li class="nav-item"> <a class="nav-link" href="#tax-saving-investment" data-toggle="tab">Tax saving Investment</a> </li>
        </ul>
        <div class="tab-content ">
            <div class="tab-pane fade show active" id="doc-uploaded" role="tabpanel">
                <div class="card">
                    <div class="panel_s">
                        <div class="panel-body" style="background-color :#f1f1f1; padding: 22px;">
                            <p class="mb-0" style="font-size: 24px;">Income Tax Declaration</p>
                            <p class="text-muted ">Submit your investment and expense declarations to ensure accurate computation of income tax and TDS. Provide details of eligible deductions such as insurance premiums, housing loan interest, and other exemptions to minimize tax liability.</p>
                        </div>

                    </div>
                    <?php
                    $totalDeclared = 0;
                    $totalApproved = 0;
                    $totalRejected = 0;
                    $totalPending = 0;

                    foreach ($tax_list as $list) {
                        $totalDeclared += $list['declared_amount'];

                        if ($list['status'] == 'Approved') {
                            $totalApproved += $list['declared_amount'];
                        } elseif ($list['status'] == 'Rejected') {
                            $totalRejected += $list['declared_amount'];
                        } elseif ($list['status'] == 'Pending') {
                            $totalPending += $list['declared_amount'];
                        }
                    }
                    ?>

                    <div class="row">
                        <div class="col-2">
                            <div class="box">
                                <h5> Taxable Amount </h5>
                                <p id="taxableAmount">INR </p>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="box">
                                <h5> Declared </h5>
                                <p>INR <?= number_format($totalDeclared); ?></p>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="box">
                                <h5>Auto Approved </h5>
                                <p>INR <?= number_format($totalApproved); ?></p>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="box">
                                <h5> Pending</h5>
                                <p> INR <?= number_format($totalPending); ?></p>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="box">
                                <h5> Rejected</h5>
                                <p> INR <?= number_format($totalRejected); ?></p>
                            </div>
                        </div>

                        <div class="col-2">
                            <div class="box">
                                <h5> Total Salary</h5>
                                <p id="taxableIncome"> INR </p>
                            </div>
                        </div>
                    </div>

                    <script>
                        var month_salary = <?php echo isset($getgrossSalary) ? $getgrossSalary['basic_salary'] : 0; ?>;
                        var HRA_exemption = <?php echo isset($HRA_Exemption) ? $HRA_Exemption['contract_amount'] : 0; ?>;
                        var taxList = <?php echo json_encode($tax_list); ?>;

                        document.addEventListener('DOMContentLoaded', (event) => {
                            const monthlySalary = month_salary;
                            const annualSalary = monthlySalary * 12;

                            const section = taxList.find(tax => tax.status === 'Approved' && tax.declared_amount).declared_amount || 0;
                            const HRAExemption = HRA_exemption || 0;
                            let taxableIncome = annualSalary - (section + HRAExemption);


                            taxableIncome = taxableIncome < 0 ? 0 : taxableIncome;

                            document.getElementById('taxableIncome').innerText = `INR ${taxableIncome.toFixed(2)}`;

                            let tax;
                            if (taxableIncome <= 250000) {
                                tax = 0;
                            } else if (taxableIncome <= 500000) {
                                tax = (taxableIncome - 250000) * 0.05;
                            } else if (taxableIncome <= 1000000) {
                                tax = 250000 * 0.05 + (taxableIncome - 500000) * 0.20;
                            } else {
                                tax = 250000 * 0.05 + 500000 * 0.20 + (taxableIncome - 1000000) * 0.30;
                            }
                            const cess = tax * 0.04;
                            const totalTax = tax + cess;
                            document.getElementById('taxableAmount').innerText = `INR ${totalTax.toFixed(2)}`;
                        });
                    </script>



                    <hr style="margin-bottom: 0px;">

                    <!-- Second Row: Status and Search -->
                    <div class="row" style="height: 50px;">
                        <div class="col-3 pr-0">
                            <select id="status" class="form-select" style="width: -webkit-fill-available;border-color: lightgray;height: -webkit-fill-available;">
                                <option value="all">Status</option>
                                <option value="approved">Approved</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-8 p-0">
                            <input type="text" class="form-control" placeholder="Search..." aria-label="Search"
                                style="height: 50px; width: 111%; border-radius: 0px;">

                        </div>
                    </div>

                    <hr style="margin-bottom: 0px;">

                    <style>
                        .table th,
                        .table td {
                            vertical-align: middle;
                        }

                        .status-badge {
                            font-size: 0.9rem;
                            padding: 5px 10px;
                            border-radius: 15px;
                        }

                        .status-approved {
                            background-color: #d4edda;
                            color: #155724;
                        }

                        .status-pending {
                            background-color: #fff3cd;
                            color: #856404;
                        }

                        .status-rejected {
                            background-color: #f8d7da;
                            color: #721c24;
                        }

                        .action-icons i {
                            cursor: pointer;
                            margin: 0 5px;
                            font-size: 1.2rem;
                        }

                        .box {
                            background-color: white;
                            /* border: 1px solid #dee2e6; */
                            border-radius: 5px;
                            text-align: center;
                            padding: 15px;
                        }

                        .box h5 {
                            margin: 0;
                            font-size: 1.1rem;
                        }

                        .box p {
                            margin: 0;
                            font-size: 1rem;
                            color: #6c757d;
                        }

                        .search-section {
                            display: flex;
                            align-items: center;
                            gap: 15px;
                            margin-top: 20px;
                        }
                    </style>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Section</th>
                                    <th>Deduction Name</th>
                                    <th>Declaration (₹)</th>
                                    <th>Proof</th>
                                    <th>Status</th>

                                    <?php if ($user_info['user_type'] == 'staff') { ?>
                                        <th>Actions</th>
                                    <?php } ?>

                                </tr>
                            </thead>
                            <tbody id="declarationTableBody">
                                <?php foreach ($tax_list as $list) { ?>
                                    <tr id="row-<?= $list['id']; ?>">
                                        <td class="editable" data-field="section"><?= $list['section']; ?></td>
                                        <td class="editable" data-field="invest_name"><?= $list['invest_name']; ?></td>
                                        <td class="editable" data-field="declared_amount"> <?= $list['declared_amount']; ?></td>
                                        <td class="editable" data-field="proof">
                                            <?php if ($list['proof'] == 'Proof uploaded') { ?>
                                                <?= getfilescount($list['id']) . '  files uploaded' ?>
                                            <?php } else {
                                                echo $list['proof']; ?>

                                            <?php }  ?>
                                        </td>

                                        <td class="status">
                                            <?php if ($user_info['user_type'] == 'company') { ?>
                                                <select id="statusDropdown" class="status-dropdown form-control" name="status" data-id="<?= $list['id']; ?>">
                                                    <option value="">Select status</option>
                                                    <option value="Approved" <?= $list['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                                    <option value="Pending" <?= $list['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Rejected" <?= $list['status'] == 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                            <?php } else { ?>
                                                <span class="status-badge status-<?= strtolower($list['status']); ?>"><?= $list['status']; ?></span>
                                            <?php } ?>
                                        </td>

                                        <td class="action-icons">
                                            <div id="action-icons">
                                                <?php if ($user_info['user_type'] == 'staff') { ?>
                                                    <?php if ($list['status'] != 'Approved') { ?>
                                                        <i class="fa fa-pencil-alt edit-icon" title="Edit" onclick="handleEdit(<?= $list['id']; ?>)"></i>
                                                        <i class="fa fa-trash" title="Delete" data-toggle="modal" data-target="#deleteModal" data-record-id="<?= $list['id']; ?>"></i>
                                                    <?php } else { ?>
                                                        <i class="fa fa-pencil-alt edit-icon" title="Edit" style="color: #ccc; cursor: not-allowed;"></i>
                                                        <i class="fa fa-trash" title="Delete" style="color: #ccc; cursor: not-allowed;"></i>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </td>



                                    </tr>
                                <?php } ?>


                                <script>
                                    function handleEdit(id) {
                                        var row = document.getElementById('row-' + id);
                                        var cells = row.querySelectorAll('.editable');

                                        cells.forEach(cell => {
                                            var field = cell.getAttribute('data-field');
                                            var value = cell.innerText.trim();

                                            if (field === 'proof') {
                                                cell.innerHTML = `<input type="file" class="form-control" name="${field}[]" multiple>`;
                                            } else {
                                                cell.innerHTML = `<input type="text" class="form-control" name="${field}" value="${value}">`;
                                            }
                                        });

                                        var editIcon = row.querySelector('.edit-icon');
                                        editIcon.className = 'fa fa-save save-icon';
                                        editIcon.title = 'Save';
                                        editIcon.setAttribute('onclick', `handleSave(${id})`);
                                    }

                                    function handleSave(id) {
                                        var row = document.getElementById('row-' + id);
                                        var inputs = row.querySelectorAll('.editable input');
                                        var formData = new FormData();

                                        inputs.forEach(input => {
                                            var field = input.name;
                                            if (input.type === 'file' && input.files.length > 0) {
                                                for (let i = 0; i < input.files.length; i++) {
                                                    formData.append(field, input.files[i]);
                                                }
                                            } else {
                                                var value = input.value.trim();
                                                formData.append(field, value);
                                            }
                                        });

                                        formData.append('id', id);
                                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                                        $.ajax({
                                            url: '<?= base_url('erp/tax_updateItem') ?>',
                                            type: 'POST',
                                            dataType: 'json',
                                            processData: false,
                                            contentType: false,
                                            data: formData,
                                            success: function(response) { // Changed parameter name from JSON to response
                                                if (response.error) {
                                                    toastr.error(response.error);
                                                    Ladda.stopAll();
                                                } else if (response.result) {
                                                    toastr.success(response.result);
                                                    Ladda.stopAll();
                                                    // Optional: reload or update the table/data
                                                }
                                            },
                                            error: function(xhr, error, thrown) {
                                                console.log("AJAX Error: ", xhr.responseText);
                                                toastr.error("An error occurred while saving.");
                                                Ladda.stopAll();
                                            }
                                        });
                                    }
                                </script>

                                <?php if ($user_info['user_type'] == 'staff') { ?>
                                    <form id="declarationForm" enctype="multipart/form-data">
                                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                        <tr id="newDataRow">
                                            <td>
                                                <select id="section" name="section" class="form-control" required style="width: 150px;">
                                                    <option value="" disabled selected>Section</option>
                                                    <option value="80C">80C</option>
                                                    <option value="80D">80D</option>
                                                    <option value="80E">80E</option>
                                                    <option value="80G">80G</option>
                                                    <option value="80TTA">80TTA</option>
                                                    <option value="80GG">80GG</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select id="investment_name" name="name" class="form-control" required style="width: 200px;" disabled>
                                                    <option value="" disabled selected>Select Section First</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="newDeclaration" name="newDeclaration" class="form-control" placeholder="Enter Declaration" required>
                                            </td>
                                            <td> <input type="file" id="newProof" name="proof[]" class="form-control" multiple> </td>
                                            <td>
                                                <input type="text" id="status" name="status" class="form-control" value="Not Submitted">
                                            </td>
                                            <td>
                                                <button type="submit" class="btn" style="background-color:#7c7b7b;color:white;" id="addRowBtn">Add</button>
                                            </td>
                                        </tr>
                                    </form>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>




                </div>
            </div>

            <div class="tab-pane fade" id="doc-pending">
                <h5>Pending Documents</h5>
                <p class="text-muted">Below are the documents that need to be submitted:</p>
                <ul class="list-group">
                    <?php
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
                    ?>


                </ul>
            </div>

            <div class="tab-pane fade" id="forms-tab" role="tabpanel">
                <div class="card">
                    <div class=" panel_s" style="background-color: #d9d8d8;">
                        <div class="row m-3">
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Form 16</h5>
                                        <p class="card-text">
                                            Form 16 summarizes your salary, deductions, and tax paid and is
                                            needed for filing tax returns.
                                        </p>
                                        <div class="d-flex align-items-center">
                                            <!-- <select class="form-select me-3" style="width: 232px; height: 39px;margin-right: 25px;" aria-label="Select Financial Year">
                                                <option selected>Select Financial Year</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                            </select> -->
                                            <button class="btn px-4 py-2" style="margin-right: 25px;background-color: #6e6f71;color:white;" id="generatePdfButton">
                                                Form 16 Part A <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn px-4 py-2" style="background-color: #6e6f71;color:white;" id="generatePdf_partb">
                                                Form 16 Part B <i class="fas fa-download"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Card 2 -->
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title">Form 12 BB</h5>
                                        <p class="card-text">
                                            Form 12BB has details about your proposed investments & expenses
                                            that are tax deductible.
                                        </p>
                                        <div class="d-flex align-items-center">
                                            <select class="form-select me-2" style="width: 232px; height: 39px;margin-right: 25px;" aria-label="Select Financial Year">
                                                <option selected>APR 2024 - MAR 2025</option>
                                                <option value="2023">APR 2023 - MAR 2024</option>
                                                <option value="2025">APR 2025 - MAR 2026</option>
                                            </select>
                                            <button class="btn px-4 py-2" style="background-color: #6e6f71;color:white;">
                                                Download Form 12BB <i class="fas fa-download"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="tab-pane fade" id="tax-saving-investment" role="tabpanel">
                <div class="card">
                    <div class="panel_s">
                        <div class="panel-body" style="background-color :#f1f1f1; padding: 22px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-0" style="font-size: 24px;">Tax Saving Investment</p>
                                    <p class="text-muted">You can use cleartax (our partner) to do tax saving investment</p>
                                </div>
                                <div>
                                    <button class="btn" style="font-size: 16px; background-color: #6e6f71; color:white;">Invest & Save Tax</button>

                                </div>
                            </div>



                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6 class="mt-3">
                                        Wondering how to save taxes & grow your wealth at the same time?
                                    </h6>
                                    <ul style="padding-left: 27px;">
                                        <li class="mb-3">You can save up to ₹45,000 in taxes by investing in the best-performing mutual funds.</li>
                                        <li class="mb-3">Cleartax continuously monitors your investment portfolio & recommends periodic re-balancing to maximize your investment returns.</li>
                                        <li class="mb-3">Get the best investment advisory with the convenience of 100% online & paperless process – for Free!</li>
                                        <li class="mb-3">Cleartax enables you in the best tax-saving ELSS mutual funds in India under 5 minutes.</li>
                                        <li class="mb-3">ELSS mutual funds are better than PPF, ULIP/LIC Insurance & tax-saving FDs owing to much higher returns & lowest lock-in period.</li>

                                    </ul>
                                    <p class="mb-0">With all your investment in one place, have complete transparency of every transaction and always stay on your investment track.</p>
                                    <p>Happy Investing!</p>
                                </div>
                            </div>


                            <p class="text-muted">A service offering from our partner <strong>Enorvision</strong></p>



                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>




</div>

</div>


</div>

<!-- delete popup -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this record?
                This action will also delete all associated proof files.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src=" https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#declarationForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $('#addRowBtn').prop('disabled', true).html('Loading...');
            $.ajax({
                url: '<?= base_url('erp/save-declaration') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',

                success: function(response) {
                    $('#addRowBtn').prop('disabled', false).html('Add');
                    if (response.error) {
                        toastr.error(response.error);
                        Ladda.stopAll();
                    } else if (response.result) {
                        toastr.success(response.result);
                        Ladda.stopAll();
                        // Optional: reload or update the table/data
                    }
                },
                error: function(xhr, error, thrown) {
                    $('#addRowBtn').prop('disabled', false).html('Add');
                    console.log("AJAX Error: ", xhr.responseText);
                }
            });
        });

        $('#section').change(function() {
            var section = $(this).val();
            var investmentDropdown = $('#investment_name');

            if (section) {
                investmentDropdown.prop('disabled', false);

                $.ajax({
                    url: '<?= base_url('erp/get-investmentname') ?>',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        section: section,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    beforeSend: function() {
                        investmentDropdown.empty().append('<option value="">Loading...</option>');
                    },
                    success: function(response) {
                        investmentDropdown.empty();

                        if (response.length > 0) {
                            investmentDropdown.append('<option value="" disabled selected>Select Investment</option>');
                            $.each(response, function(index, investment) {
                                investmentDropdown.append('<option value="' + investment.investment_name + '">' + investment.investment_name + '</option>');
                            });
                        } else {
                            investmentDropdown.append('<option value="" disabled>No investments found</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        investmentDropdown.empty().append('<option value="" disabled>Error loading investments</option>');
                        toastr.error('Error fetching investment names: ' + error);
                    }
                });
            } else {
                investmentDropdown.empty().append('<option value="" disabled selected>Select Section First</option>');
                investmentDropdown.prop('disabled', true);
            }
        });
    });
</script>



<script>
    $(document).ready(function() {
        $(document).on('change', '.status-dropdown', function() {
            var status = this.value;
            var id = this.getAttribute('data-id');
            if (status) {
                $.ajax({
                    url: '<?= base_url('erp/tax-statusUpdate') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        status: status,
                        id: id,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Status updated successfully!', 'Success');
                        } else {
                            toastr.error(response.message || 'Failed to update status', 'Error');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert(error);
                    }
                });
            }
        });
    });
</script>


<script>
    document.getElementById('generatePdf_partb').addEventListener('click', function() {
        var employeeId = '<?= $employe_id ?>';
        window.location.href = '<?= base_url('erp/form16-partB') ?>' + '/' + employeeId;
    });
</script>
<script>
    $(document).ready(function() {
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
        $('#payroll').DataTable({
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
        $('#deleteModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var recordId = button.data('record-id');
            var modal = $(this);
            modal.find('#confirmDeleteBtn').data('record-id', recordId);
        });

        $('#confirmDeleteBtn').on('click', function() {
            var recordId = $(this).data('record-id');
            var URL = '<?= base_url('erp/delete-alltaxProof') ?>/' + recordId;
            $('#confirmDeleteBtn').prop('disabled', true);

            $.ajax({
                url: URL,
                type: 'DELETE',
                dataType: "json",
                data: {
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>', // Include the CSRF token
                },

                success: function(JSON) {
                    $('#confirmDeleteBtn').prop('disabled', false);
                    if (JSON.error != '') {
                        toastr.error(JSON.error);
                        $('input[name="csrf_token"]').val(JSON.csrf_hash);
                        Ladda.stopAll();
                    } else {
                        $('#deleteModal').modal('hide');
                        toastr.success(JSON.result);
                        window.location.href = JSON.redirect_url;
                        $('input[name="csrf_token"]').val(JSON.csrf_hash);

                        Ladda.stopAll();
                    }
                },
                error: function(xhr, error, thrown) {
                    $('#confirmDeleteBtn').prop('disabled', false); // Re-enable the button
                    toastr.error('An error occurred while deleting the record.');
                    console.error("Error deleting record: ", error);
                }
            });
        });
    });
</script>

<script>
    document.getElementById('generatePdfButton').addEventListener('click', function() {
        var employeeId = '<?= $employe_id ?>';
        window.location.href = '<?= base_url('erp/generate-Pdf') ?>' + '/' + employeeId;
    });
</script>
<script>
    const ctx = document.getElementById('financeChart').getContext('2d');
    const financeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May'],
            datasets: [{
                    label: 'Revenue',
                    data: [50000, 60000, 70000, 80000, 90000],
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Expenses',
                    data: [20000, 25000, 30000, 40000, 50000],
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>