<?php


use App\Models\SystemModel;
use App\Models\UsersModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$session = \Config\Services::session();
$router = service('router');
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

$xin_system = $SystemModel->where('setting_id', 1)->first();
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
            <?= lang('Dashboard.dashboard_your_company'); ?>
        </label>
    </li>


    <!-- Dashboard|Monthly Planning -->
    <li class="pc-item"><a href="<?= site_url('erp/monthly-planning'); ?>" class="pc-link "><span class="pc-micon"><i
                    data-feather="file"></i></span><span class="pc-mtext">
                Annual Planning
            </span></a></li>

    <!-- Dashboard|Home -->
    <li class="pc-item"><a href="<?= site_url('erp/desk'); ?>" class="pc-link "><span class="pc-micon"><i
                    data-feather="home"></i></span>
            <span class="pc-mtext">
                <?= lang('Dashboard.dashboard_title'); ?>
            </span></a>
    </li>

    <!-- Employees -->
    <li class="pc-item <?php if (!empty($arr_mod['employee_open']))
                            echo $arr_mod['employee_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="users"></i></span>
            <?= lang('Dashboard.dashboard_employees'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['employee_style_ul']))
                                    echo $arr_mod['employee_style_ul']; ?>>
            <li class="pc-item <?php if (!empty($arr_mod['employee_active']))
                                    echo $arr_mod['employee_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/staff-list'); ?>">
                    <?= lang('Dashboard.dashboard_employees'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['set_role_active']))
                                    echo $arr_mod['set_role_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/set-roles'); ?>">
                    <?= lang('Dashboard.left_set_roles'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['office_shift_active']))
                                    echo $arr_mod['office_shift_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/office-shifts'); ?>">
                    <?= lang('Dashboard.xin_manage_shifts'); ?>
                </a>
            </li>

            <li class="pc-item <?php if (!empty($arr_mod['employee_exit_active']))
                                    echo $arr_mod['employee_exit_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/employee-exit'); ?>">
                    <?= lang('Dashboard.left_employees_exit'); ?>
                </a>
            </li>

            <li class="pc-item <?php if (!empty($arr_mod['exit_type_active']))
                                    echo $arr_mod['exit_type_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/exit-type'); ?>">
                    <?php echo "Exit Type"; ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['left_complaints_active']))
                                    echo $arr_mod['left_complaints_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/complaints-list'); ?>">
                    <?= lang('Dashboard.left_complaints'); ?>
                </a>
            </li>

            <li class="pc-item <?php if (!empty($arr_mod['left_resignations_active']))
                                    echo $arr_mod['left_resignations_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/resignation-list'); ?>">
                    <?= lang('Dashboard.left_resignations'); ?>

                </a>
            </li>

        </ul>
    </li>



    <!-- CoreHR -->
    <li class="pc-item <?php if (!empty($arr_mod['corehr_open']))
                            echo $arr_mod['corehr_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="crosshair"></i></span>
            <?= lang('Dashboard.dashboard_core_hr'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['core_style_ul']))
                                    echo $arr_mod['core_style_ul']; ?>>
            <li class="pc-item <?php if (!empty($arr_mod['department_active']))
                                    echo $arr_mod['department_active']; ?>"> <a class="pc-link"
                    href="<?= site_url('erp/departments-list'); ?>">
                    <?= lang('Dashboard.left_department'); ?>
                </a> </li>
            <li class="pc-item <?php if (!empty($arr_mod['designation_active']))
                                    echo $arr_mod['designation_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/designation-list'); ?>">
                    <?= lang('Dashboard.left_designation'); ?>
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['policies_active']))
                                    echo $arr_mod['policies_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/policies-list'); ?>">
                    <?= lang('Dashboard.header_policies'); ?>
                </a> </li>
            <li class="pc-item <?php if (!empty($arr_mod['announcements_active']))
                                    echo $arr_mod['announcements_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/news-list'); ?>">
                    <?= lang('Dashboard.left_announcement_make'); ?>
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['announcements_active']))
                                    echo $arr_mod['announcements_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/chart'); ?>">
                    <?= lang('Dashboard.xin_org_chart_title'); ?>
                </a>
            </li>

            <!-- Disciplinary -->
            <!-- <i data-feather="alert-circle"></i> -->
            <li class="pc-item <?php if (!empty($arr_mod['left_warning_active']))
                                    echo $arr_mod['left_warning_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/disciplinary-cases'); ?>">
                    <?= lang('Dashboard.left_warnings'); ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['conference1_active']))
                                    echo $arr_mod['conference1_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/meeting-list'); ?>">
                    <?= lang('Dashboard.xin_hr_meetings'); ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['file1_active']))
                                    echo $arr_mod['file1_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/upload-files'); ?>">
                    <?= lang('Dashboard.xin_upload_files'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['transfers1_active']))
                                    echo $arr_mod['transfers1_active']; ?>"> <a class="pc-link"
                    href="<?= site_url('erp/transfers-list'); ?>">
                    <?= lang('Dashboard.left_transfers'); ?>

                </a> </li>

        </ul>
    </li>


    <!-- Attendance -->
    <li class="pc-item pc-hasmenu <?php if (!empty($arr_mod['attendance_open']))
                                        echo $arr_mod['attendance_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"><span class="pc-micon"><i
                    data-feather="clock"></i></span><span class="pc-mtext">
                <?= lang('Dashboard.left_attendance'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['attendance_style_ul']))
                                    echo $arr_mod['attendance_style_ul']; ?>>
            <li class="pc-item <?php if (!empty($arr_mod['attnd_active']))
                                    echo $arr_mod['attnd_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/attendance-list'); ?>">
                    <?= lang('Dashboard.left_attendance'); ?>
                </a> </li>
            <li class="pc-item <?php if (!empty($arr_mod['upd_attnd_active']))
                                    echo $arr_mod['upd_attnd_active']; ?>"> <a class="pc-link"
                    href="<?= site_url('erp/manual-attendance'); ?>">
                    <?= lang('Dashboard.left_update_attendance'); ?>
                </a> </li>


            <li class="pc-item <?php if (!empty($arr_mod['timesheet_active']))
                                    echo $arr_mod['timesheet_active']; ?>"> <a class="pc-link"
                    href="<?= site_url('erp/monthly-attendance'); ?>">
                    <?= lang('Dashboard.xin_month_timesheet_title'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['holiday1_active']))
                                    echo $arr_mod['holiday1_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/holidays-list'); ?>">
                    <?= lang('Dashboard.left_holidays'); ?>

                </a> </li>

            <!-- Leave -->
            <!-- <i data-feather="plus-square"></i> -->

            <li class="pc-item <?php if (!empty($arr_mod['leave_active']))
                                    echo $arr_mod['leave_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/leave-list'); ?>">
                    <?= lang('Leave.left_leave_request'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['travel_active']))
                                    echo $arr_mod['travel_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/business-travel'); ?>">
                    <?= lang('Dashboard.dashboard_travel_request'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['timesheet_active']))
                                    echo $arr_mod['timesheet_active']; ?>"> <a class="pc-link"
                    href="<?= site_url('erp/overtime-request'); ?>">
                    <?= lang('Dashboard.xin_overtime_request'); ?>

                </a> </li>
        </ul>
    </li>

    <!-- Finance -->
    <li class="pc-item <?php if (!empty($arr_mod['finance_open']))
                            echo $arr_mod['finance_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="credit-card"></i></span>
            <?= lang('Dashboard.xin_hr_finance'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>

        <ul class="pc-submenu" <?php if (!empty($arr_mod['finance_style_ul']))
                                    echo $arr_mod['finance_style_ul']; ?>>

            <li class="pc-item <?php if (!empty($arr_mod['finance_active']))
                                    echo $arr_mod['finance_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/accounts-list'); ?>">
                    <?= lang('Dashboard.xin_hr_finance'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['account_deposit_active']))
                                    echo $arr_mod['account_deposit_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/deposit-list'); ?>">
                    <?= lang('Dashboard.xin_acc_deposit'); ?>
                </a>
            </li>

            <li class="pc-item <?php if (!empty($arr_mod['expense_active']))
                                    echo $arr_mod['expense_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/expense-list'); ?>">
                    <?= lang('Dashboard.xin_acc_expense'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['transactions_active']))
                                    echo $arr_mod['transactions_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/transactions-list'); ?>">
                    <?= lang('Dashboard.xin_acc_transactions'); ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['asset1_active']))
                                    echo $arr_mod['asset1_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/assets-list'); ?>">
                    <?= lang('Dashboard.xin_assets'); ?>
                </a>
            </li>
        </ul>
    </li>



    <li class="pc-item"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="edit"></i></span>
            Tax Managment
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu">
            <!-- 
            <li class="pc-item"> <a href="<?= site_url('erp/tax-declaration'); ?>" class="pc-link"><span
                        class="pc-micon"></i></span><span class="pc-mtext">
                        Tax Verify
                    </span> </a>
            </li> -->

            <li class="pc-item"> <a href="<?= site_url('erp/tax-verification'); ?>" class="pc-link"><span
                        class="pc-micon"></i></span><span class="pc-mtext">
                        Tax Verification
                    </span> </a>
            </li>


            <li class="pc-item"> <a href="<?= site_url('erp/investment-type'); ?>" class="pc-link"><span
                        class="pc-micon"></span><span class="pc-mtext">
                        Investment Type
                    </span> </a>
            </li>

        </ul>
    </li>

    <!-- Payroll -->

    <li class="pc-item <?php if (!empty($arr_mod['payroll_open']))
                            echo $arr_mod['payroll_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="speaker"></i></span>
            <?= lang('Dashboard.left_payroll'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['payroll_style_ul']))
                                    echo $arr_mod['payroll_style_ul']; ?>>
            <li class="pc-item <?php if (!empty($arr_mod['payroll_active']))
                                    echo $arr_mod['payroll_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/payroll-list'); ?>">
                    <?= lang('Dashboard.left_payroll'); ?>
                </a> </li>

            <li class="pc-item <?php if (!empty($arr_mod['payroll_history_active']))
                                    echo $arr_mod['payroll_history_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/payslip-history'); ?>">
                    <?= lang('Payroll.xin_view_payroll_history'); ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['advance_salary_active']))
                                    echo $arr_mod['advance_salary_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/advance-salary'); ?>">
                    <?= lang('Main.xin_request_advance_salary'); ?>
                </a>
            </li>


            <li class="pc-item <?php if (!empty($arr_mod['request_loan_active']))
                                    echo $arr_mod['request_loan_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/loan-request'); ?>">
                    <?= lang('Main.xin_request_loan'); ?>

                </a>
            </li>
        </ul>
    </li>


    <!-- Work Management -->
    <li class="pc-item <?php if (!empty($arr_mod['work_open']))
                            echo $arr_mod['work_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="edit"></i></span>
            <?= lang('Dashboard.work_management'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['work_style_ul']))
                                    echo $arr_mod['work_style_ul']; ?>>
            <!-- Projects -->
            <!-- <i data-feather="layers"> -->
            <li class="pc-item"> <a href="<?= site_url('erp/projects-list'); ?>" class="pc-link"><span
                        class="pc-micon"></i></span><span class="pc-mtext">
                        <?= lang('Dashboard.left_projects'); ?>
                    </span> </a> </li>


            <li class="pc-item"> <a href="<?= site_url('erp/tasks-list'); ?>" class="pc-link"><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.left_tasks'); ?>
                    </span> </a> </li>

        </ul>
    </li>

    <!-- Client Management -->

    <li class="pc-item <?php if (!empty($arr_mod['client_management_open']))
                            echo $arr_mod['client_management_open']; ?>">
        <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i data-feather="user-check"></i></span>
            <?= lang('Dashboard.client_management'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['client_management_style_ul']))
                                    echo $arr_mod['client_management_style_ul']; ?>>

            <!-- Clients -->
            <!-- <i data-feather="user-check"></i> -->
            <li class="pc-item"><a href="<?= site_url('erp/clients-overview'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        Overview
                    </span></a></li>
            <li class="pc-item"><a href="<?= site_url('erp/clients-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Projects.xin_manage_clients'); ?>
                    </span></a></li>

            <li class="pc-item"><a href="<?= site_url('erp/opportunity-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.xin_opportunity'); ?>
                    </span></a></li>

            <!-- Leads -->
            <li class="pc-item"><a href="<?= site_url('erp/leads-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.xin_leads'); ?>
                    </span></a></li>
            <!-- Web Leads -->
            <li class="pc-item"><a href="<?= site_url('erp/web-leads-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?php echo "Web Leads"; ?>
                    </span></a></li>

            <!-- Invoices -->
            <!-- <i data-feather="calendar"></i> -->
            <li class="pc-item"><a href="<?= site_url('erp/invoices-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.xin_invoices_title'); ?>
                    </span></a></li>
        </ul>
    </li>


    <!-- Performance -->
    <li class="<?php if (!empty($arr_mod['talent_open']))
                    echo $arr_mod['talent_open']; ?> pc-item"> <a href="#" class="pc-link sidenav-toggle"> <span
                class="pc-micon"><i data-feather="aperture"></i></span>
            Performance Management
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['talent_style_ul']))
                                    echo $arr_mod['talent_style_ul']; ?>>


            <li class="pc-item <?php if (!empty($arr_mod['indicator_active']))
                                    echo $arr_mod['indicator_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/performance-indicator-list'); ?>">
                    Performances
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['competencies_active']))
                                    echo $arr_mod['competencies_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/competencies'); ?>">
                    <?= lang('Performance.xin_competencies'); ?>
                </a>
            </li>
            <!-- <li class="pc-item <?php if (!empty($arr_mod['appraisal_active']))
                                        echo $arr_mod['appraisal_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/performance-appraisal-list'); ?>">
                    <?= lang('Dashboard.left_performance_appraisal'); ?>
                </a>
            </li> -->

            <li class="pc-item <?php if (!empty($arr_mod['goal_track_active']))
                                    echo $arr_mod['goal_track_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/track-goals'); ?>">
                    <?= lang('Dashboard.xin_hr_goal_tracking'); ?>
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['tracking_type_active']))
                                    echo $arr_mod['tracking_type_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/goal-type'); ?>">
                    <?= lang('Dashboard.xin_hr_goal_tracking_type'); ?>
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['goals_calendar_active']))
                                    echo $arr_mod['goals_calendar_active']; ?>">
                <a class="pc-link" href="<?= site_url('erp/goals-calendar'); ?>">
                    <?= lang('Performance.xin_goals_calendar'); ?>
                </a>
            </li>
            <li class="pc-item <?php if (!empty($arr_mod['award_active']))
                                    echo $arr_mod['award_active']; ?>"> <a class="pc-link" href="<?= site_url('erp/awards-list'); ?>">
                    <?= lang('Dashboard.left_awards'); ?>
                </a> </li>
        </ul>
    </li>

    <!-- Recruitment -->

    <li class="pc-item <?php if (!empty($arr_mod['recruitment_open']))
                            echo $arr_mod['recruitment_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="gitlab"></i></span>
            <?= lang('Recruitment.xin_recruitment_ats'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['recruitment_style_ul']))
                                    echo $arr_mod['recruitment_style_ul']; ?>>

            <li class="pc-item"><a href="<?= site_url('erp/jobs-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Recruitment.xin_add_new_jobs'); ?>
                    </span></a></li>

            <li class="pc-item"><a href="<?= site_url('erp/candidates-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.left_job_candidates'); ?>
                    </span></a></li>

            <li class="pc-item"><a href="<?= site_url('erp/jobs-interviews'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Recruitment.xin_interviews'); ?>
                    </span></a></li>

            <li class="pc-item"><a href="<?= site_url('erp/rejected-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?php echo "Rejected"; ?>
                    </span></a></li>

            <li class="pc-item"><a href="<?= site_url('erp/promotion-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Dashboard.left_promotions'); ?>
                    </span></a></li>
        </ul>
    </li>


    <!-- Calender  -->
    <li class="pc-item <?php if (!empty($arr_mod['calender_open']))
                            echo $arr_mod['calender_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i
                    data-feather="calendar"></i></span>
            <?= lang('Dashboard.xin_acc_calendar'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['calender_style_ul']))
                                    echo $arr_mod['calender_style_ul']; ?>>
            <!-- Estimates -->
            <!-- <i data-feather="calendar"></i> -->
            <li class="pc-item">
                <a class="pc-link" data-toggle="tooltip" data-placement="top"
                    title="<?= lang('Dashboard.xin_estimates'); ?>" href="<?= site_url('erp/estimates-list'); ?>">
                    <span><?= lang('Dashboard.xin_estimates'); ?><span>
                </a>
            </li>
            <!-- <i data-feather="calendar"></i>&nbsp;&nbsp;&nbsp;&nbsp; -->
            <?php if (in_array('system_calendar', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
                <li class="pc-item">
                    <a class="pc-link" data-toggle="tooltip" data-placement="top"
                        title="<?= lang('Dashboard.xin_system_calendar'); ?>"
                        href="<?= site_url('erp/system-calendar'); ?>">
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

    <!-- Training Session -->

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

    <!-- Other Services  -->

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

    <!-- Report  -->

    <?php if (in_array('system_reports', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
        <li class="pc-item">
            <a class="pc-link" data-toggle="tooltip" data-placement="top"
                title="<?= lang('Dashboard.xin_system_reports'); ?>" href="<?= site_url('erp/system-reports'); ?>">
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
    <li class="pc-item <?php if (!empty($arr_mod['planning_configuration_open']))
                            echo $arr_mod['planning_configuration_open']; ?>">
        <a href="#" class="pc-link sidenav-toggle"> <span class="pc-micon"><i data-feather="search"></i></span>
            <?= lang('Main.xin_planning_configuration'); ?>
            </span><span class="pc-arrow"><i data-feather="chevron-right"></i></span> </a>
        <ul class="pc-submenu" <?php if (!empty($arr_mod['planning_configuration_style_ul']))
                                    echo $arr_mod['planning_configuration_style_ul']; ?>>

            <li class="pc-item"><a href="<?= site_url('erp/planning_configuration'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        <?= lang('Main.xin_planning_configuration'); ?>
                    </span></a>
            </li>
            <li class="pc-item"><a href="<?= site_url('erp/year-planning'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        Year Planning
                    </span></a>
            </li>

            <li class="pc-item"><a href="<?= site_url('erp/monthly-planning-list'); ?>" class="pc-link "><span
                        class="pc-micon"></span><span class="pc-mtext">
                        Monthly Planning
                    </span></a>
            </li>

        </ul>
    </li>

    <!-- Setting -->
    <?php if ($user_info['user_type'] == 'super_user' || $user_info['user_type'] == 'company' || $user_info['user_type'] == 'customer' || $user_info['user_type'] == 'staff') { ?>

        <?php if (in_array('settings1', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>
            <li class="pc-item <?php if (!empty($arr_mod['setting_open']))
                                    echo $arr_mod['setting_open']; ?>"> <a href="#" class="pc-link sidenav-toggle"> <span
                        class="pc-micon"><i data-feather="settings"></i></span>
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

        <!-- Document Management -->
        <li class="pc-item <?php if (!empty($arr_mod['work_open'])) echo $arr_mod['work_open']; ?>">
            <a href="<?= site_url('erp/documentation'); ?>" class="pc-link sidenav-toggle">
                <span class="pc-micon">
                    <i data-feather="file-text"></i> <!-- Feather icon for document -->
                </span>
                Documentation
                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            </a>
        </li>



        <!-- Tickets -->
        <li class="pc-item"> <a href="<?= site_url('erp/support-tickets'); ?>" class="pc-link"> <span class="pc-micon"><i
                        data-feather="help-circle"></i></span><span class="pc-mtext">
                    <?= lang('Dashboard.dashboard_helpdesk'); ?>
                </span> </a> </li>

    <?php }
    if ($user_info['user_type'] == 'customer') { ?>
        <li class="pc-item">
            <a class="pc-link active" data-toggle="tooltip" data-placement="top"
                title="<?= lang('Dashboard.xin_acc_calendar'); ?>" href="<?= site_url('erp/my-invoices-calendar'); ?>">
                <i data-feather="calendar"></i><span>&nbsp;&nbsp;&nbsp;&nbsp;<span><?= lang('Dashboard.xin_acc_calendar'); ?></span>
            </a>
        </li>
    <?php }
    if ($user_info['user_type'] == 'super_user') { ?>
        <li class="pc-item">
            <a class="pc-link active" data-toggle="tooltip" data-placement="top"
                title="<?= lang('Dashboard.xin_my_account'); ?>" href="<?= site_url('erp/my-profile'); ?>">
                <i data-feather="user"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span><?= lang('Dashboard.xin_my_account'); ?></span>
            </a>
        </li>
        <li class="pc-item">
            <a class="pc-link active" data-toggle="tooltip" data-placement="top"
                title="<?= lang('Main.xin_frontend_landing'); ?>" href="<?= site_url(''); ?>" target="_blank">
                <i
                    data-feather="layout"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span><?= lang('Dashboard.xin_frontend_landing'); ?></span>
            </a>
        </li>
    <?php } ?>

</ul>