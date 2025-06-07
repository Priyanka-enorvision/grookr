<?php

use App\Models\SystemModel;
use App\Models\SuperroleModel;
use App\Models\UsersModel;
use App\Models\MembershipModel;
use App\Models\CompanymembershipModel;
use App\Models\VerifyEmployeDocModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$SuperroleModel = new SuperroleModel();
$MembershipModel = new MembershipModel();
$CompanymembershipModel = new CompanymembershipModel();
$documentModel = new VerifyEmployeDocModel();
$session = \Config\Services::session();
$router = service('router');
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = $SystemModel->where('setting_id', 1)->first();


$singledocument_data = $documentModel->where('user_id', $user_info['user_id'])->first();

?>
<?php $arr_mod = select_module_class($router->controllerName(), $router->methodName()); ?>


<style>
  .pc-sidebar ul {
    width: 300px !important;
  }
</style>
<ul class="pc-navbar">
  <li class="pc-item pc-caption">
    <label>
      <?= lang('Main.xin_your_apps'); ?>
    </label>
  </li>
  <!-- Dashboard|Home -->
  <li class="pc-item"><a href="<?= site_url('erp/desk'); ?>" class="pc-link "><span class="pc-micon"><i
          data-feather="home"></i></span><span class="pc-mtext">
        <?= lang('Dashboard.dashboard_title'); ?></span></a></li>


  <!-- Work Management -->
  <?php if (in_array('milestone1', staff_role_resource()) || (in_array('project1', staff_role_resource())) || (in_array('task1', staff_role_resource()))) { ?>

    <li class="pc-item <?php if (!empty($arr_mod['work_open']))
                          echo $arr_mod['work_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="edit"></i></span>
        <?= lang('Dashboard.work_management'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['work_style_ul']))
                                echo $arr_mod['work_style_ul']; ?>>
        <!-- Projects -->
        <?php if (in_array('project1', staff_role_resource())) { ?>
          <li class="pc-item"> <a href="<?= site_url('erp/projects-list'); ?>" class="pc-link"><span
                class="pc-micon"></i></span><span class="pc-mtext">
                <?= lang('Dashboard.left_projects'); ?>
              </span> </a> </li>
        <?php } ?>

        <!-- Tasks -->
        <?php if (in_array('task1', staff_role_resource())) { ?>
          <li class="pc-item"> <a href="<?= site_url('erp/tasks-list'); ?>" class="pc-link"><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Dashboard.left_tasks'); ?>
              </span> </a> </li>
        <?php } ?>


      </ul>
    </li>
  <?php } ?>


  <!-- Attendance -->
  <?php if (in_array('attendance', staff_role_resource())) { ?>
    <li class="pc-item"><a href="<?= site_url('erp/attendance-list'); ?>" class="pc-link "><span class="pc-micon"><i
            data-feather="clock"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.left_attendance'); ?>
        </span></a></li>
  <?php } ?>

  <!-- Manual Attendance -->
  <?php
  if (in_array('upattendance1', staff_role_resource())) {
    $active_class = $arr_mod['upd_attnd_active'] ?? '';
  ?>
    <li class="pc-item <?= htmlspecialchars($active_class, ENT_QUOTES, 'UTF-8'); ?>">
      <a class="pc-link" href="<?= site_url('erp/manual-attendance'); ?>">
        <span class="pc-micon"><i data-feather="edit"></i></span>
        <span class="pc-mtext">
          <?= lang('Dashboard.left_update_attendance'); ?>
        </span>
      </a>
    </li>
  <?php } ?>



  <!-- Monthly Attendance -->
  <?php if (in_array('monthly_time', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['timesheet_active']))
                          echo $arr_mod['timesheet_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/monthly-attendance'); ?>">
        <span class="pc-micon"><i data-feather="clipboard"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.xin_month_timesheet_title'); ?>
        </span>
      </a> </li>
  <?php } ?>

  <!-- Holiday -->
  <?php if (in_array('holiday1', staff_role_resource())): ?>
    <li class="pc-item <?= !empty($arr_mod['holiday1_active']) ? $arr_mod['holiday1_active'] : ''; ?>">
      <a class="pc-link" href="<?= site_url('erp/holidays-list'); ?>">
        <span class="pc-micon"><i data-feather="sun"></i></span>
        <?= lang('Dashboard.left_holidays'); ?>
      </a>
    </li>
  <?php endif; ?>


  <!-- Requests -->
  <?php if (in_array('leave1', staff_role_resource()) || in_array('expense1', staff_role_resource()) || in_array('overtime_req1', staff_role_resource()) || in_array('loan1', staff_role_resource()) || in_array('advance_salary1', staff_role_resource()) || in_array('travel1', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['core_request_active']))
                          echo $arr_mod['core_request_active']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="list"></i></span>
        <?= lang('Dashboard.dashboard_requests'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu">
        <?php if (in_array('leave2', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['leave_request_active']))
                                echo $arr_mod['leave_request_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/leave-list'); ?>">
              <?= lang('Leave.left_leave_request'); ?>
            </a> </li>
        <?php } ?>

        <?php if (in_array('travel1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['travel_active']))
                                echo $arr_mod['travel_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/business-travel'); ?>">
              <?= lang('Dashboard.dashboard_travel_request'); ?>
            </a> </li>
        <?php } ?>



        <?php if (in_array('overtime_req1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['overtime_active']))
                                echo $arr_mod['overtime_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/overtime-request'); ?>">
              <?= lang('Dashboard.xin_overtime_request'); ?>
            </a> </li>
        <?php } ?>

        <?php if (in_array('expense1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['expense_active']))
                                echo $arr_mod['expense_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/expense-list'); ?>">
              <?= lang('Dashboard.dashboard_expense_claim'); ?>
            </a> </li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>


  <!-- Finances -->

  <li class="pc-item"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
          data-feather="edit"></i></span>
      Tax Managment
      </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
    <ul class="pc-submenu">

      <li class="pc-item"> <a href="<?= site_url('erp/tax-declaration/' . base64_encode($user_info['user_id'])); ?>" class="pc-link"><span
            class="pc-micon"></i></span><span class="pc-mtext">
            Tax Proof
          </span> </a>
      </li>

    </ul>
  </li>


  <!-- Complaint -->
  <?php if (in_array('complaint1', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['left_complaints_active']))
                          echo $arr_mod['left_complaints_active']; ?>"> <a class="pc-link"
        href="<?= site_url('erp/complaints-list'); ?>"><span class="pc-micon"><i
            class="fa-solid fa-feather"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.left_complaints'); ?>
      </a> </li>
  <?php } ?>

  <!-- Resignation -->
  <?php if (in_array('resignation1', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['left_resignations_active']))
                          echo $arr_mod['left_resignations_active']; ?>">
      <a class="pc-link" href="<?= site_url('erp/resignation-list'); ?>">
        <span class="pc-micon"><i data-feather="watch"></i></span>
        <span class="pc-mtext"><?= lang('Dashboard.left_resignations'); ?></span>
      </a>
    </li>
  <?php } ?>

  <!-- Disciplinary Cases -->
  <?php if (in_array('disciplinary1', staff_role_resource()) || in_array('case_type1', staff_role_resource())) { ?>
    <!-- Disciplinary -->
    <li class="pc-item"> <a href="<?= site_url('erp/disciplinary-cases'); ?>" class="pc-link"> <span class="pc-micon"><i
            data-feather="alert-circle"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.left_warnings'); ?>
        </span> </a> </li>
  <?php } ?>
  <!-- Transfer -->
  <?php if (in_array('transfers1', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['transfers1_active']))
                          echo $arr_mod['transfers1_active']; ?>">
      <a class="pc-link" href="<?= site_url('erp/transfers-list'); ?>">
        <span class="pc-micon"><i data-feather="repeat"></i></span>
        <span class="pc-mtext"><?= lang('Dashboard.left_transfers'); ?></span>
      </a>
    </li>
  <?php } ?>

  <!-- Payroll -->
  <?php if (in_array('pay1', staff_role_resource()) || (in_array('pay_history', staff_role_resource())) || (in_array('advance_salary1', staff_role_resource())) || (in_array('loan1', staff_role_resource()))) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['payroll_open']))
                          echo $arr_mod['payroll_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="speaker"></i></span>
        <?= lang('Dashboard.left_payroll'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['payroll_style_ul']))
                                echo $arr_mod['payroll_style_ul']; ?>>
        <?php if (in_array('pay1', staff_role_resource())) { ?>

          <li class="pc-item <?php if (!empty($arr_mod['payroll_active']))
                                echo $arr_mod['payroll_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/payroll-list'); ?>">
              <?= lang('Dashboard.left_payroll'); ?>
            </a> </li>
        <?php } ?>
        <?php if (in_array('pay_history', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['payroll_history_active']))
                                echo $arr_mod['payroll_history_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/payslip-history'); ?>">
              <?= lang('Payroll.xin_view_payroll_history'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('advance_salary1', staff_role_resource())) { ?>

          <li class="pc-item <?php if (!empty($arr_mod['advance_salary_active']))
                                echo $arr_mod['advance_salary_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/advance-salary'); ?>">
              <?= lang('Main.xin_request_advance_salary'); ?>
            </a>
          </li>
        <?php } ?>
        <?php if (in_array('loan1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['request_loan_active']))
                                echo $arr_mod['request_loan_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/loan-request'); ?>">
              <?= lang('Main.xin_request_loan'); ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>

  <!-- Tickets -->
  <?php if (in_array('helpdesk1', staff_role_resource())) { ?>
    <li class="pc-item"><a href="<?= site_url('erp/support-tickets'); ?>" class="pc-link "><span class="pc-micon"><i
            data-feather="help-circle"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.dashboard_helpdesk'); ?>
        </span></a></li>
  <?php } ?>


  <!-- Training Session -->
  <?php if (in_array('training1', staff_role_resource()) || in_array('trainer1', staff_role_resource()) || in_array('training_skill1', staff_role_resource()) || in_array('training_calendar', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['training_open']))
                          echo $arr_mod['training_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="target"></i></span>
        <?= lang('Dashboard.left_training'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['training_style_ul']))
                                echo $arr_mod['training_style_ul']; ?>>

        <li class="pc-item"><a href="<?= site_url('erp/training-sessions'); ?>" class="pc-link "><span
              class="pc-micon"></span><span class="pc-mtext">
              <?= lang('Dashboard.left_training'); ?>
            </span></a></li>

        <li class="pc-item"><a href="<?= site_url('erp/trainers-list'); ?>" class="pc-link "><span
              class="pc-micon"></span><span class="pc-mtext">
              <?= lang('Dashboard.left_trainers'); ?>
            </span></a></li>

        <li class="pc-item"><a href="<?= site_url('erp/training-skills'); ?>" class="pc-link "><span
              class="pc-micon"></span><span class="pc-mtext">
              <?= lang('Dashboard.left_training_skills'); ?>
            </span></a></li>

        <li class="pc-item"><a href="<?= site_url('erp/training-calendar'); ?>" class="pc-link "><span
              class="pc-micon"></span><span class="pc-mtext">
              <?= lang('Dashboard.left_training_calendar'); ?>
            </span></a></li>

      </ul>
    </li>
  <?php } ?>

  <!-- Asset -->
  <?php if (in_array('asset1', staff_role_resource()) || in_array('asset1', staff_role_resource())) { ?>

    <li class="pc-item <?php if (!empty($arr_mod['asset1_active']))
                          echo $arr_mod['asset1_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/assets-list'); ?>">
        <span class="pc-micon"><i data-feather="box"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.xin_assets'); ?>
      </a> </li>

  <?php } ?>

  <!-- Award -->

  <?php if (in_array('award1', staff_role_resource()) || in_array('award1', staff_role_resource())) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['award_active']))
                          echo $arr_mod['award_active']; ?>"><a class="pc-link" href="<?= site_url('erp/awards-list'); ?>"><span
          class="pc-micon"><i data-feather="gift"></i></span><span class="pc-mtext">
          <?= lang('Dashboard.left_awards'); ?></span>
      </a> </li>

  <?php } ?>

  <?php if (in_array('staff2', staff_role_resource()) || in_array('shift1', staff_role_resource()) || in_array('staffexit1', staff_role_resource()) || in_array('news1', staff_role_resource()) || in_array('department1', staff_role_resource()) || in_array('designation1', staff_role_resource()) || in_array('accounts1', staff_role_resource()) || in_array('deposit1', staff_role_resource()) || in_array('expense1', staff_role_resource()) || in_array('dep_cat1', staff_role_resource()) || in_array('exp_cat1', staff_role_resource()) || in_array('indicator1', staff_role_resource()) || in_array('appraisal1', staff_role_resource()) || in_array('competency1', staff_role_resource()) || in_array('tracking1', staff_role_resource()) || in_array('track_type1', staff_role_resource()) || in_array('track_calendar', staff_role_resource()) || in_array('client1', staff_role_resource()) || in_array('invoice2', staff_role_resource()) || in_array('invoice_payments', staff_role_resource()) || in_array('invoice_calendar', staff_role_resource()) || in_array('tax_type1', staff_role_resource()) || in_array('training1', staff_role_resource()) || in_array('trainer1', staff_role_resource()) || in_array('training_skill1', staff_role_resource()) || in_array('training_calendar', staff_role_resource()) || in_array('disciplinary1', staff_role_resource()) || in_array('case_type1', staff_role_resource())) { ?>
    <li class="pc-item pc-caption">
      <label>
        <?= lang('Dashboard.dashboard_your_company'); ?>
      </label>
    </li>
  <?php } ?>


  <!-- Dashboard|Monthly Planning -->
  <?php if (in_array('annual_planning', staff_role_resource())) { ?>
    <li class="pc-item"><a href="<?= site_url('erp/monthly-planning'); ?>" class="pc-link "><span class="pc-micon"><i
            data-feather="file"></i></span><span class="pc-mtext">
          Annual Planning
        </span></a></li>
  <?php } ?>

  <?php
  $staffRoles = staff_role_resource();

  if (
    in_array('staff2', $staffRoles) ||
    in_array('shift1', $staffRoles) ||
    in_array('staffexit1', $staffRoles) ||
    in_array('exit_type1', $staffRoles)
  ) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['employee_open']))
                          echo $arr_mod['employee_open']; ?>">
      <a href="#" class="pc-link sidenav-toggle">
        <span class="pc-micon"><i data-feather="users"></i></span>
        <?= lang('Dashboard.dashboard_employees'); ?>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
      </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['employee_style_ul']))
                                echo $arr_mod['employee_style_ul']; ?>>

        <?php if (in_array('staff2', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['employee_active']))
                                echo $arr_mod['employee_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/staff-list'); ?>">
              <?= lang('Dashboard.dashboard_employees'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('shift1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['office_shift_active']))
                                echo $arr_mod['office_shift_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/office-shifts'); ?>">
              <?= lang('Dashboard.xin_manage_shifts'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('staffexit1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['employee_exit_active']))
                                echo $arr_mod['employee_exit_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/employee-exit'); ?>">
              <?= lang('Dashboard.left_employees_exit'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('exit_type1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['exit_type_active']))
                                echo $arr_mod['exit_type_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/exit-type'); ?>">
              <?= "Exit Type"; ?>
            </a>
          </li>
        <?php } ?>

      </ul>
    </li>
  <?php } ?>


  <?php

  // Check if the user has access to any CoreHR functionality
  if (
    in_array('core_hr', $staffRoles) ||
    in_array('department1', $staffRoles) ||
    in_array('designation1', $staffRoles) ||
    in_array('policy1', $staffRoles) ||
    in_array('news1', $staffRoles) ||
    in_array('org_chart', $staffRoles) ||
    in_array('booking', $staffRoles) ||
    in_array('manager', $staffRoles)
  ) { ?>
    <!-- CoreHR Menu -->
    <li class="pc-item <?php if (!empty($arr_mod['corehr_open']))
                          echo $arr_mod['corehr_open']; ?>">
      <a href="#" class="pc-link sidenav-toggle">
        <span class="pc-micon"><i data-feather="crosshair"></i></span>
        <?= lang('Dashboard.dashboard_core_hr'); ?>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
      </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['core_style_ul']))
                                echo $arr_mod['core_style_ul']; ?>>

        <?php if (in_array('department1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['department_active']))
                                echo $arr_mod['department_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/departments-list'); ?>">
              <?= lang('Dashboard.left_department'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('designation1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['designation_active']))
                                echo $arr_mod['designation_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/designation-list'); ?>">
              <?= lang('Dashboard.left_designation'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('policy1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['policies_active']))
                                echo $arr_mod['policies_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/policies-list'); ?>">
              <?= lang('Dashboard.header_policies'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('news1', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['announcements_active']))
                                echo $arr_mod['announcements_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/news-list'); ?>">
              <?= lang('Dashboard.left_announcement_make'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('org_chart', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['org_chart_active']))
                                echo $arr_mod['org_chart_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/chart'); ?>">
              <?= lang('Dashboard.xin_org_chart_title'); ?>
            </a>
          </li>
        <?php } ?>


        <?php if (in_array('booking', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['conference1_active']))
                                echo $arr_mod['conference1_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/meeting-list'); ?>">
              <?= lang('Dashboard.xin_hr_meetings'); ?>
            </a>
          </li>
        <?php } ?>

        <?php if (in_array('manager', $staffRoles)) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['file1_active']))
                                echo $arr_mod['file1_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/upload-files'); ?>">
              <?= lang('Dashboard.xin_upload_files'); ?>
            </a>
          </li>
        <?php } ?>

      </ul>
    </li>
  <?php } ?>

  <!-- Finance -->

  <?php
  // Check if the user has access to any CoreHR functionality
  if (
    in_array('accounts1', $staffRoles) ||
    in_array('deposit1', $staffRoles) ||
    in_array('expense1', $staffRoles) ||
    in_array('transaction1', $staffRoles)
  ) { ?>
    <li class="pc-item <?= !empty($arr_mod['finance_open']) ? $arr_mod['finance_open'] : ''; ?>">
      <a href="#" class="pc-link sidenav-toggle">
        <span class="pc-micon"><i data-feather="credit-card"></i></span>
        <?= lang('Dashboard.xin_hr_finance'); ?>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
      </a>

      <ul class="pc-submenu" <?= !empty($arr_mod['finance_style_ul']) ? $arr_mod['finance_style_ul'] : ''; ?>>

        <!-- Accounts Section -->
        <?php if (in_array('accounts1', $staffRoles)) { ?>
          <li class="pc-item <?= !empty($arr_mod['finance_active']) ? $arr_mod['finance_active'] : ''; ?>">
            <a class="pc-link" href="<?= site_url('erp/accounts-list'); ?>">
              <?= lang('Dashboard.xin_hr_finance'); ?>
            </a>
          </li>
        <?php } ?>

        <!-- Deposit Section -->
        <?php if (in_array('deposit1', $staffRoles)) { ?>
          <li class="pc-item <?= !empty($arr_mod['account_deposit_active']) ? $arr_mod['account_deposit_active'] : ''; ?>">
            <a class="pc-link" href="<?= site_url('erp/deposit-list'); ?>">
              <?= lang('Dashboard.xin_acc_deposit'); ?>
            </a>
          </li>
        <?php } ?>

        <!-- Expense Section -->
        <?php if (in_array('expense1', $staffRoles)) { ?>
          <li class="pc-item <?= !empty($arr_mod['expense_active']) ? $arr_mod['expense_active'] : ''; ?>">
            <a class="pc-link" href="<?= site_url('erp/expense-list'); ?>">
              <?= lang('Dashboard.xin_acc_expense'); ?>
            </a>
          </li>
        <?php } ?>

        <!-- Transactions Section -->
        <?php if (in_array('transaction1', $staffRoles)) { ?>
          <li class="pc-item <?= !empty($arr_mod['transactions_active']) ? $arr_mod['transactions_active'] : ''; ?>">
            <a class="pc-link" href="<?= site_url('erp/transactions-list'); ?>">
              <?= lang('Dashboard.xin_acc_transactions'); ?>
            </a>
          </li>
        <?php } ?>

      </ul>
    </li>
  <?php } ?>

  <?php
  if (
    in_array('ats2', staff_role_resource()) ||
    in_array('candidate', staff_role_resource()) ||
    in_array('interview', staff_role_resource()) ||
    in_array('promotion', staff_role_resource())
  ) { ?>
    <li class="pc-item <?= !empty($arr_mod['recruitment_open']) ? $arr_mod['recruitment_open'] : ''; ?>">
      <a href="#" class="pc-link sidenav-toggle">
        <span class="pc-micon"><i data-feather="gitlab"></i></span>
        <?= lang('Recruitment.xin_recruitment_ats'); ?>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
      </a>

      <ul class="pc-submenu" <?= !empty($arr_mod['recruitment_style_ul']) ? $arr_mod['recruitment_style_ul'] : ''; ?>>

        <!-- Job Listings -->
        <li class="pc-item">
          <a href="<?= site_url('erp/jobs-list'); ?>" class="pc-link">
            <span class="pc-mtext"><?= lang('Recruitment.xin_add_new_jobs'); ?></span>
          </a>
        </li>

        <!-- Job Candidates -->
        <li class="pc-item">
          <a href="<?= site_url('erp/candidates-list'); ?>" class="pc-link">
            <span class="pc-mtext"><?= lang('Dashboard.left_job_candidates'); ?></span>
          </a>
        </li>

        <!-- Interviews -->
        <li class="pc-item">
          <a href="<?= site_url('erp/jobs-interviews'); ?>" class="pc-link">
            <span class="pc-mtext"><?= lang('Recruitment.xin_interviews'); ?></span>
          </a>
        </li>

        <!-- Rejected Candidates -->
        <!-- <li class="pc-item">
          <a href="<?= site_url('erp/rejected-list'); ?>" class="pc-link">
            <span class="pc-mtext"><?php echo "Rejected"; ?></span>
          </a>
        </li> -->

        <!-- Promotions -->
        <li class="pc-item">
          <a href="<?= site_url('erp/promotion-list'); ?>" class="pc-link">
            <span class="pc-mtext"><?= lang('Dashboard.left_promotions'); ?></span>
          </a>
        </li>

      </ul>
    </li>
  <?php } ?>



  <!-- Performance -->
  <?php if (in_array('indicator1', staff_role_resource()) || in_array('appraisal1', staff_role_resource()) || in_array('competency1', staff_role_resource()) || in_array('tracking1', staff_role_resource()) || in_array('track_type1', staff_role_resource()) || in_array('track_calendar', staff_role_resource())) { ?>
    <li class="<?php if (!empty($arr_mod['talent_open']))
                  echo $arr_mod['talent_open']; ?> pc-item"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="aperture"></i></span> Performance Management
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['talent_style_ul']))
                                echo $arr_mod['talent_style_ul']; ?>>




        <?php if (in_array('indicator1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['indicator_active']))
                                echo $arr_mod['indicator_active']; ?>"> <a class="pc-link"
              href="<?= site_url('erp/performance-indicator-list'); ?>">
              Performance
            </a> </li>
        <?php } ?>

        <?php if (in_array('competency1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['competencies_active']))
                                echo $arr_mod['competencies_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/competencies'); ?>">
              <?= lang('Performance.xin_competencies'); ?>
            </a> </li>
        <?php } ?>
        <!-- <?php if (in_array('appraisal1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['appraisal_active']))
                                echo $arr_mod['appraisal_active']; ?>"> <a class="pc-link"
              href="<?= site_url('erp/performance-appraisal-list'); ?>">
              <?= lang('Dashboard.left_performance_appraisal'); ?>
            </a> </li>
        <?php } ?> -->

        <?php if (in_array('tracking1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['goal_track_active']))
                                echo $arr_mod['goal_track_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/track-goals'); ?>">
              <?= lang('Dashboard.xin_hr_goal_tracking'); ?>
            </a> </li>
        <?php } ?>
        <?php if (in_array('track_type1', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['tracking_type_active']))
                                echo $arr_mod['tracking_type_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/goal-type'); ?>">
              <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>
            </a> </li>
        <?php } ?>
        <?php if (in_array('track_calendar', staff_role_resource())) { ?>
          <li class="pc-item <?php if (!empty($arr_mod['goals_calendar_active']))
                                echo $arr_mod['goals_calendar_active']; ?>">
            <a class="pc-link" href="<?= site_url('erp/goals-calendar'); ?>">
              <?= lang('Performance.xin_goals_calendar'); ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>


  <!-- Client Management -->
  <?php if (in_array('client1', staff_role_resource()) || (in_array('opportunity1', staff_role_resource())) || (in_array('leads1', staff_role_resource())) || (in_array('invoice2', staff_role_resource()) || in_array('invoice_payments', staff_role_resource()) || in_array('invoice_calendar', staff_role_resource()) || in_array('tax_type1', staff_role_resource()))) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['client_management_open']))
                          echo $arr_mod['client_management_open']; ?>">
      <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i data-feather="user-check"></i></span>
        <?= lang('Dashboard.client_management'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['client_management_style_ul']))
                                echo $arr_mod['client_management_style_ul']; ?>>

        <!-- Clients -->
        <?php if (in_array('client1', staff_role_resource())) { ?>
          <!-- <i data-feather="user-check"></i> -->
          <li class="pc-item"><a href="<?= site_url('erp/clients-overview'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                Overview
              </span></a></li>
          <li class="pc-item"><a href="<?= site_url('erp/clients-list'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Projects.xin_manage_clients'); ?>
              </span></a></li>
        <?php } ?>

        <?php if (in_array('opportunity1', staff_role_resource())) { ?>

          <li class="pc-item"><a href="<?= site_url('erp/opportunity-list'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Dashboard.xin_opportunity'); ?>
              </span></a></li>

        <?php } ?>

        <!-- Leads -->
        <?php if (in_array('leads1', staff_role_resource())) { ?>
          <li class="pc-item"><a href="<?= site_url('erp/leads-list'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Dashboard.xin_leads'); ?>
              </span></a></li>
        <?php } ?>
        <!-- Invoices -->
        <?php if (in_array('invoice2', staff_role_resource()) || in_array('invoice_payments', staff_role_resource()) || in_array('invoice_calendar', staff_role_resource()) || in_array('tax_type1', staff_role_resource())) { ?>
          <!-- <i data-feather="calendar"></i> -->
          <li class="pc-item"><a href="<?= site_url('erp/invoices-list'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Dashboard.xin_invoices_title'); ?>
              </span></a></li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>
  <!-- Calender  -->
  <?php if (in_array('hr_event1', staff_role_resource()) || (in_array('estimate2', staff_role_resource())) || (in_array('system_calendar', staff_role_resource())) || $user_info['user_type'] == 'company') { ?>
    <li class="pc-item <?php if (!empty($arr_mod['calender_open']))
                          echo $arr_mod['calender_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="calendar"></i></span>
        <?= lang('Dashboard.xin_acc_calendar'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['calender_style_ul']))
                                echo $arr_mod['calender_style_ul']; ?>>
        <!-- Estimates -->
        <?php if (in_array('estimate2', staff_role_resource())) { ?>
          <!-- <i data-feather="calendar"></i> -->
          <li class="pc-item">
            <a class="pc-link" data-toggle="tooltip" data-placement="top" title="<?= lang('Dashboard.xin_estimates'); ?>"
              href="<?= site_url('erp/estimates-list'); ?>">
              <span><?= lang('Dashboard.xin_estimates'); ?><span>
            </a>
          </li>
        <?php } ?>
        <!-- <i data-feather="calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp; -->
        <?php if (in_array('system_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <li class="pc-item">
            <a class="pc-link" data-toggle="tooltip" data-placement="top"
              title="<?= lang('Dashboard.xin_system_calendar'); ?>" href="<?= site_url('erp/system-calendar'); ?>">
              <span><?= lang('Dashboard.xin_system_calendar'); ?><span>
            </a>
          </li>
        <?php } ?>

        <!-- <i data-feather="disc"></i> -->
        <?php if (in_array('hr_event1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
          <li class="pc-item">
            <a class="pc-link" data-toggle="tooltip" data-placement="top"
              title="<?= lang('Dashboard.xin_system_calendar'); ?>" href="<?= site_url('erp/events-list'); ?>">
              <span><?= lang('Dashboard.xin_hr_events'); ?><span>
            </a>
          </li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>
  <!-- Other Services  -->
  <?php if (in_array('visitor1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <li class="pc-item <?php if (!empty($arr_mod['other_service_open']))
                          echo $arr_mod['other_service_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
            data-feather="globe"></i></span>
        <?= lang('Dashboard.xin_other_services'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['other_service_style_ul']))
                                echo $arr_mod['other_service_style_ul']; ?>>
        <li class="pc-item"><a href="<?= site_url('erp/visitors-list'); ?>" class="pc-link "><span
              class="pc-micon"></span><span class="pc-mtext">
              <?= lang('Main.xin_visitor_book'); ?>
            </span></a></li>
      </ul>
    </li>
  <?php } ?>

  <!-- Report  -->
  <?php if (in_array('system_reports', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
    <li class="pc-item">
      <a class="pc-link" data-toggle="tooltip" data-placement="top" title="<?= lang('Dashboard.xin_system_reports'); ?>"
        href="<?= site_url('erp/system-reports'); ?>">
        <i data-feather="pie-chart"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span><?= lang('Dashboard.xin_system_reports'); ?><span>
      </a>
    </li>
  <?php } ?>
  <!-- Hire Expert -->
  <li class="pc-item"> <a href="<?= site_url('erp/hire-experts'); ?>" class="pc-link"> <span class="pc-micon"><i
          data-feather="user-check"></i></span><span class="pc-mtext">
        <?= lang('Dashboard.dashboard_hire_expert'); ?>
      </span> </a> </li>

  <!-- Planing Configuration  -->
  <?php if (in_array('planning_configuration1', staff_role_resource()) || (in_array('monthly_planning1', staff_role_resource())) || (in_array('year_planning1', staff_role_resource()))) { ?>
    <li class="pc-item <?php if (!empty($arr_mod['planning_configuration_open']))
                          echo $arr_mod['planning_configuration_open']; ?>">
      <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i data-feather="search"></i></span>
        <?= lang('Main.xin_planning_configuration'); ?>
        </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
      <ul class="pc-submenu" <?php if (!empty($arr_mod['planning_configuration_style_ul']))
                                echo $arr_mod['planning_configuration_style_ul']; ?>>

        <?php if (in_array('planning_configuration1', staff_role_resource())) { ?>

          <li class="pc-item"><a href="<?= site_url('erp/planning_configuration'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Main.xin_planning_configuration'); ?>
              </span></a>
          </li>
        <?php } ?>
        <?php if (in_array('year_planning1', staff_role_resource())) { ?>

          <li class="pc-item"><a href="<?= site_url('erp/year-planning'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                Year Planning
              </span></a>
          </li>
        <?php } ?>
        <?php if (in_array('monthly_planning1', staff_role_resource())) { ?>

          <li class="pc-item"><a href="<?= site_url('erp/monthly-planning-list'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                Monthly Planning
              </span></a>
          </li>
        <?php } ?>

      </ul>
    </li>
  <?php } ?>
  <?php if (in_array('mom_1', staff_role_resource())) { ?>
    <!-- MOM -->
    <li class="pc-item"> <a href="<?= site_url('erp/moms-grid'); ?>" class="pc-link"> <span class="pc-micon"><i
            data-feather="book-open"></i></span><span class="pc-mtext">
          MOM
        </span> </a> </li>
  <?php } ?>


  <?php if ($user_info['user_type'] == 'super_user' || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'customer' || $user_info['user_type'] == 'staff') { ?>
    <?php if (in_array('settings1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
      <li class="pc-item <?php if (!empty($arr_mod['setting_open']))
                            echo $arr_mod['setting_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
              data-feather="settings"></i></span>
          <?= lang('Main.xin_configuration_wizard'); ?>
          </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['setting_style_ul']))
                                  echo $arr_mod['setting_style_ul']; ?>>
          <li class="pc-item"><a href="<?= site_url('erp/system-settings'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Main.left_settings'); ?>
              </span></a></li>
          <li class="pc-item"><a href="<?= site_url('erp/system-constants'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Main.left_constants'); ?>
              </span></a></li>
          <li class="pc-item"><a href="<?= site_url('erp/email-templates'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Main.left_email_templates'); ?>
              </span></a></li>
          <li class="pc-item"><a href="<?= site_url('erp/all-languages'); ?>" class="pc-link "><span
                class="pc-micon"></span><span class="pc-mtext">
                <?= lang('Main.xin_multi_language'); ?>
              </span></a></li>

          <li class="pc-item">
            <!-- Main menu item without a link -->
            <a class="pc-link" style="cursor:pointer;">
              <span class="pc-micon"></span>
              <span class="pc-mtext"><?= lang('Main.xin_customization'); ?></span>
            </a>


            <!-- Nested submenu -->
            <ul class="pc-submenu">
              <li class="pc-item">
                <a href="<?= site_url('erp/customization-lead'); ?>" class="pc-link">
                  <span class="pc-micon"></span>
                  <span class="pc-mtext"><?= lang('Main.xin_lead'); ?></span>
                </a>
              </li>
            </ul>
          </li>

        </ul>
      </li>
    <?php } ?>
  <?php } ?>


  <!-- Document Management -->
  <li class="pc-item <?php if (!empty($arr_mod['work_open'])) echo $arr_mod['work_open']; ?>">
    <a href="<?= base_url('erp/documentation') ?>" class="pc-link sidenav-toggle">
      <span class="pc-micon">
        <i data-feather="file-text"></i> <!-- Feather icon for document -->
      </span>
      Documentation
      <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
  </li>

</ul>