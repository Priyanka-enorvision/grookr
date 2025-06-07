<?php

use CodeIgniter\I18n\Time;

use App\Models\UsersModel;
use App\Models\TicketsModel;
use App\Models\ProjectsModel;
use App\Models\MembershipModel;
use App\Models\CompanymembershipModel;

$UsersModel = new UsersModel();
$TicketsModel = new TicketsModel();
$ProjectsModel = new ProjectsModel();
$MembershipModel = new MembershipModel();
$CompanymembershipModel = new CompanymembershipModel();

$session = \Config\Services::session();
$current_time = Time::now('Asia/Karachi');
$usession = $session->get('sup_username');
$xin_system = erp_company_settings();
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

// $cache = \Config\Services::cache();
// $cacheKey = 'dashboard_data_' . $user_info['user_id'];
// $ttl = 900;
// if (!$cache->get($cacheKey)) {


//   $cache->save($cacheKey, $dashboard_data, $ttl);
// } else {
//   $dashboard_data = $cache->get($cacheKey);
// }

$company_id = user_company_info();
$total_staff = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->countAllResults();
$total_projects = $ProjectsModel->where('company_id', $company_id)->countAllResults();
$total_tickets = $TicketsModel->where('company_id', $company_id)->countAllResults();
$open_tickets = $TicketsModel->where('company_id', $company_id)->where('ticket_status', 1)->countAllResults();
$closed_tickets = $TicketsModel->where('company_id', $company_id)->where('ticket_status', 2)->countAllResults();
$company_membership = $CompanymembershipModel->where('company_id', $usession['sup_user_id'])->first();
$subs_plan = $MembershipModel->where('membership_id', $company_membership['membership_id'] ?? null)->first();
$company_membership_details = company_membership_details();
$alert_bg = ($company_membership_details['diff_days'] < 8) ? 'alert-danger' : 'alert-warning';

$dashboard_data = [
  'xin_system' => $xin_system,
  'user_info' => $user_info,
  'total_staff' => $total_staff,
  'total_projects' => $total_projects,
  'total_tickets' => $total_tickets,
  'open_tickets' => $open_tickets,
  'closed_tickets' => $closed_tickets,
  'company_membership' => $company_membership,
  'subs_plan' => $subs_plan,
  'company_membership_details' => $company_membership_details,
  'alert_bg' => $alert_bg,
  'current_time' => $current_time->toDateTimeString(),
];

?>

<meta name="csrf-token" content="<?= csrf_token() ?>">
<div class="row">
  <div class="col-xl-6 col-md-12">
    <div class="row">
      <div class="col-xl-12 col-md-12">
        <div class="row">
          <div class="col-sm-6">
            <div class="card prod-p-card bg-primary background-pattern-white">
              <div class="card-body">
                <div class="row align-items-center m-b-0">
                  <div class="col">
                    <h6 class="m-b-5 text-white">
                      <?= lang('Dashboard.xin_total_deposit'); ?>
                    </h6>
                    <h3 class="m-b-0 text-white">
                      <?= number_to_currency(total_deposit(), $xin_system['default_currency'], null, 2); ?>
                    </h3>
                  </div>
                  <div class="col-auto"> <i class="fas fa-database text-white"></i> </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="card prod-p-card background-pattern">
              <div class="card-body">
                <div class="row align-items-center m-b-0">
                  <div class="col">
                    <h6 class="m-b-5">
                      <?= lang('Projects.xin_total_projects'); ?>
                    </h6>
                    <h3 class="m-b-0">
                      <?= $total_projects; ?>
                    </h3>
                  </div>
                  <div class="col-auto"> <i class="fas fa-money-bill-alt text-primary"></i> </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-header">
            <h5>
              <?= lang('Dashboard.xin_acc_invoice_payments'); ?>
            </h5>
          </div>
          <div class="card-body">
            <div class="row pb-2">
              <div class="col-auto m-b-10">
                <h3 class="mb-1">
                  <?= number_to_currency(erp_total_paid_invoices(), $xin_system['default_currency'], null, 2); ?>
                </h3>
                <span>
                  <?= lang('Invoices.xin_total_paid'); ?>
                </span>
              </div>
              <div class="col-auto m-b-10">
                <h3 class="mb-1">
                  <?= number_to_currency(erp_total_unpaid_invoices(), $xin_system['default_currency'], null, 2); ?>
                </h3>
                <span>
                  <?= lang('Invoices.xin_total_unpaid'); ?>
                </span>
              </div>
            </div>
            <div id="paid-invoice-chart"></div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h6>
              <?= lang('Dashboard.xin_staff_department_wise'); ?>
            </h6>
            <div class="row d-flex justify-content-center align-items-center">
              <div class="col">
                <div id="department-wise-chart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xl-12 col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-auto">
                    <h6>
                      <?= lang('Dashboard.xin_staff_attendance'); ?>
                    </h6>
                  </div>
                  <div class="col">
                    <div class="dropdown float-right">
                      <?= date('d F, Y'); ?>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-6 pr-0">
                    <h6 class="my-3"><i class="feather icon-users f-20 mr-2 text-primary"></i>
                      <?= lang('Dashboard.xin_total_staff'); ?>
                    </h6>
                    <h6 class="my-3"><i class="feather icon-user f-20 mr-2 text-success"></i>
                      <?= lang('Attendance.attendance_present'); ?>
                      <span class="text-success ml-2 f-14"><i class="feather icon-arrow-up"></i></span>
                    </h6>
                    <h6 class="my-3"><i class="feather icon-user f-20 mr-2 text-danger"></i>
                      <?= lang('Attendance.attendance_absent'); ?>
                      <span class="text-danger ml-2 f-14"><i class="feather icon-arrow-down"></i></span>
                    </h6>
                  </div>
                  <div class="col-6">
                    <div id="staff-attendance-chart" class="chart-percent text-center"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card flat-card">
          <div class="row-table">
            <div class="col-sm-6 card-body br">
              <div class="row">
                <div class="col-sm-4"> <i class="fa fa-ticket-alt text-primary mb-1 d-block"></i> </div>
                <div class="col-sm-8 text-md-center">
                  <h5>
                    <?= $total_tickets; ?>
                  </h5>
                  <span>
                    <?= lang('Dashboard.left_tickets'); ?>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 d-none d-md-table-cell d-lg-table-cell d-xl-table-cell card-body br">
              <div class="row">
                <div class="col-sm-4"> <i class="fa fa-folder-open text-primary mb-1 d-block"></i> </div>
                <div class="col-sm-8 text-md-center">
                  <h5>
                    <?= $open_tickets; ?>
                  </h5>
                  <span>
                    <?= lang('Main.xin_open'); ?>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-sm-6 card-body">
              <div class="row">
                <div class="col-sm-4"> <i class="fa fa-folder text-primary mb-1 d-block"></i> </div>
                <div class="col-sm-8 text-md-center">
                  <h5>
                    <?= $closed_tickets; ?>
                  </h5>
                  <span>
                    <?= lang('Main.xin_closed'); ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-md-12">
    <div class="row">
      <div class="col-sm-6">
        <div class="card prod-p-card background-pattern">
          <div class="card-body">
            <div class="row align-items-center m-b-0">
              <div class="col">
                <h6 class="m-b-5">
                  <?= lang('Dashboard.xin_total_employees'); ?>
                </h6>
                <h3 class="m-b-0">
                  <?= $total_staff; ?>
                </h3>
              </div>
              <div class="col-auto"> <i class="fas fa-money-bill-alt text-primary"></i> </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="card prod-p-card bg-primary background-pattern-white">
          <div class="card-body">
            <div class="row align-items-center m-b-0">
              <div class="col">
                <h6 class="m-b-5 text-white">
                  <?= lang('Finance.xin_total_expense'); ?>
                </h6>
                <h3 class="m-b-0 text-white">
                  <?= number_to_currency(total_expense(), $xin_system['default_currency'], null, 2); ?>
                </h3>
              </div>
              <div class="col-auto"> <i class="fas fa-database text-white"></i> </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card">
      <div class="card-header">
        <h5>
          <?= lang('Payroll.xin_payroll_monthly_report'); ?>
        </h5>
      </div>
      <div class="card-body">
        <div class="row pb-2">
          <div class="col-auto m-b-10">
            <h3 class="mb-1">
              <?= number_to_currency(total_payroll(), $xin_system['default_currency'], null, 2); ?>
            </h3>
            <span>
              <?= lang('Main.xin_total'); ?>
            </span>
          </div>
          <div class="col-auto m-b-10">
            <h3 class="mb-1">
              <?= number_to_currency(payroll_this_month(), $xin_system['default_currency'], null, 2); ?>
            </h3>
            <span>
              <?= lang('Payroll.xin_payroll_this_month'); ?>
            </span>
          </div>
        </div>
        <div id="erp-payroll-chart"></div>
      </div>
    </div>
    <div class="card">
      <div class="card-body">
        <h6>
          <?= lang('Dashboard.xin_staff_designation_wise'); ?>
        </h6>
        <div class="row d-flex justify-content-center align-items-center">
          <div class="col">
            <div id="designation-wise-chart"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-xl-6 col-md-12">
        <div class="card">
          <div class="card-body">
            <h6>
              <?= lang('Projects.xin_projects_status'); ?>
            </h6>
            <div class="row d-flex justify-content-center align-items-center">
              <div class="col">
                <div id="project-status-chart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-md-12">
        <div class="card">
          <div class="card-body">
            <h6>
              <?= lang('Projects.xin_tasks_status'); ?>
            </h6>
            <div class="row d-flex justify-content-center align-items-center">
              <div class="col">
                <div id="task-status-chart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>