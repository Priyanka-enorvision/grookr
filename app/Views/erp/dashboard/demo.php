<?php

   use CodeIgniter\I18n\Time;
   use App\Models\RolesModel;
   use App\Models\UsersModel;
   use App\Models\SystemModel;
   use App\Models\LeaveModel;
   use App\Models\TasksModel;
   use App\Models\TicketsModel;
   use App\Models\ProjectsModel;
   use App\Models\MembershipModel;
   use App\Models\CompanymembershipModel;

   //$encrypter = \Config\Services::encrypter();
   $SystemModel = new SystemModel();
   $RolesModel = new RolesModel();
   $UsersModel = new UsersModel();
   $LeaveModel = new LeaveModel();
   $TasksModel = new TasksModel();
   $TicketsModel = new TicketsModel();
   $ProjectsModel = new ProjectsModel();
   $MembershipModel = new MembershipModel();
   $CompanymembershipModel = new CompanymembershipModel();


   $session = \Config\Services::session();
   $usession = $session->get('sup_username');
   $request = \Config\Services::request();
   $xin_system = erp_company_settings();
   $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
 
   $all_projects = $ProjectsModel->where('company_id', $user_info['company_id'])->findAll();

   $all_tasks = $TasksModel->where('company_id', $user_info['company_id'])->findAll();
   $company_id = user_company_info();
   $total_staff = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->countAllResults();
   $total_projects = $ProjectsModel->where('company_id', $company_id)->countAllResults();
   $total_tickets = $TicketsModel->where('company_id', $company_id)->countAllResults();
   $open = $TicketsModel->where('company_id', $company_id)->where('ticket_status', 1)->countAllResults();
   $closed = $TicketsModel->where('company_id', $company_id)->where('ticket_status', 2)->countAllResults();


   $company_membership = $CompanymembershipModel->where('company_id', $usession['sup_user_id'])->first();
   $subs_plan = $MembershipModel->where('membership_id', $company_membership['membership_id'])->first();
   $current_time = Time::now('Asia/Karachi');
   $company_membership_details = company_membership_details();
   if ($company_membership_details['diff_days'] < 8) {
      $alert_bg = 'alert-danger';
   } 
   else 
   {
      $alert_bg = 'alert-warning';
   }

?>
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
                              <?= $open; ?>
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
                              <?= $closed; ?>
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


<div class="row">
   <div class="col-md-8" data-container="left-8">
      <div class="widget" id="widget-finance_overview" data-name="Finance Overview">
         <div class="finance-summary">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="widget-dragger"></div>
                  <div class="row home-summary">
                     <div class="col-md-6 col-lg-4 col-sm-6">
                        <div class="row">
                           <div class="col-md-12">
                              <p class="text-dark text-uppercase">Invoice overview</p>
                              <hr class="mtop15" />
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?status=6" class="text-muted mbot15 inline-block">
                                 <span class="_total bold">0</span> Draft </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="0.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?filter=not_sent" class="text-muted inline-block mbot15">
                                 <span class="_total bold">17</span> Not Sent </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              89.47%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="89.47" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="89.47">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?status=1" class="text-danger mbot15 inline-block">
                                 <span class="_total bold">9</span> Unpaid </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              47.37%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="47.37">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?status=3" class="text-warning mbot15 inline-block">
                                 <span class="_total bold">0</span> Partially Paid </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="0.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?status=4" class="text-warning mbot15 inline-block">
                                 <span class="_total bold">9</span> Overdue </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              47.37%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="47.37" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="47.37">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/invoices/list_invoices?status=2" class="text-success mbot15 inline-block">
                                 <span class="_total bold">1</span> Paid </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              5.26%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="5.26" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="5.26">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-lg-4 col-sm-6">
                        <div class="row">
                           <div class="col-md-12 text-stats-wrapper">
                              <p class="text-dark text-uppercase">Estimate overview</p>
                              <hr class="mtop15" />
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?status=1" class="text-muted mbot15 inline-block estimate-status-dashboard-muted">
                                 <span class="_total bold">0</span>
                                 Draft </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?filter=not_sent" class="text-muted mbot15 inline-block estimate-status-dashboard-muted">
                                 <span class="_total bold">0</span>
                                 Not Sent </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?status=2" class="text-info mbot15 inline-block estimate-status-dashboard-info">
                                 <span class="_total bold">0</span>
                                 Sent </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?status=5" class="text-warning mbot15 inline-block estimate-status-dashboard-warning">
                                 <span class="_total bold">0</span>
                                 Expired </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?status=3" class="text-danger mbot15 inline-block estimate-status-dashboard-danger">
                                 <span class="_total bold">0</span>
                                 Declined </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/estimates/list_estimates?status=4" class="text-success mbot15 inline-block estimate-status-dashboard-success">
                                 <span class="_total bold">0</span>
                                 Accepted </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 col-sm-6 col-lg-4">
                        <div class="row">
                           <div class="col-md-12 text-stats-wrapper">
                              <p class="text-dark text-uppercase">Proposal overview</p>
                              <hr class="mtop15" />
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=6" class="text-muted mbot15 inline-block">
                                 <span class="_total bold">0</span> Draft </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="0.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=4" class="text-info mbot15 inline-block">
                                 <span class="_total bold">3</span> Sent </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              60.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="60.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="60.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=1" class="text-muted mbot15 inline-block">
                                 <span class="_total bold">1</span> Open </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              20.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="20.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="20.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=5" class="text-info mbot15 inline-block">
                                 <span class="_total bold">0</span> Revised </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="0.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=2" class="text-danger mbot15 inline-block">
                                 <span class="_total bold">0</span> Declined </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              0.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="0.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="0.00">
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-12 text-stats-wrapper">
                              <a href="https://connect195.com/crm/admin/proposals/list_proposals?status=3" class="text-success mbot15 inline-block">
                                 <span class="_total bold">1</span> Accepted </a>
                           </div>
                           <div class="col-md-12 text-right progress-finance-status">
                              20.00%
                              <div class="progress no-margin progress-bar-mini">
                                 <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="20.00" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="20.00">
                                 </div>
                              </div>
                           </div>
                           <div class="clearfix"></div>
                        </div>
                     </div>
                  </div>
                  <hr />
                  <a href="#" class="hide invoices-total initialized"></a>
                  <div id="invoices_total" class="invoices-total-inline">
                     <div class="row">
                        <div class="col-md-12 simple-bootstrap-select mbot5">
                           <select data-none-selected-text="2024" data-width="auto" class="selectpicker" name="invoices_total_years" onchange="init_invoices_total();" multiple="true" id="invoices_total_years">
                              <option value="2024" selected>2024</option>
                              <option value="2023">2023</option>
                              <option value="2022">2022</option>
                              <option value="2021">2021</option>
                              <option value="2020">2020</option>
                           </select>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total">
                                    ₹0.00 </h3>
                                 <span class="text-warning">Outstanding Invoices</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total">
                                    ₹0.00 </h3>
                                 <span class="text-danger">Past Due Invoices</span>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                           <div class="panel_s">
                              <div class="panel-body">
                                 <h3 class="text-muted _total">
                                    ₹0.00 </h3>
                                 <span class="text-success">Paid Invoices</span>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <script>
                        (function() {
                           if (typeof(init_selectpicker) == 'function') {
                              init_selectpicker();
                           }
                        })();
                     </script>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="widget d-flex" id="widget-user_data" data-name="User Widget">
         <div class="panel_s user-data">
            <div class="panel-body home-activity">
               <div class="widget-dragger"></div>
               <div class="horizontal-scrollable-tabs">
                  <!-- <div class="scroller scroller-left arrow-left"><i class="fa fa-angle-left"></i></div>
                <div class="scroller scroller-right arrow-right"><i class="fa fa-angle-right"></i></div> -->
                  <div class="horizontal-tabs ">
                     <ul class="nav nav-tabs nav-tabs-horizontal nav-justified" role="tablist">
                        <li role="presentation" class="active">
                           <a href="#home_tab_tasks" aria-controls="home_tab_tasks" role="tab" data-toggle="tab">
                              <i class="fa fa-tasks menu-icon"></i> My Tasks </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_my_projects" onclick="init_table_staff_projects(true);" aria-controls="home_my_projects" role="tab" data-toggle="tab">
                              <i class="fa fa-bars menu-icon"></i> My Projects </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_my_reminders" onclick="initDataTable('.table-my-reminders', admin_url + 'misc/my_reminders', undefined, undefined,undefined,[2,'asc']);" aria-controls="home_my_reminders" role="tab" data-toggle="tab">
                              <i class="fa fa-bell menu-icon"></i> My Reminders </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_tab_tickets" onclick="init_table_tickets(true);" aria-controls="home_tab_tickets" role="tab" data-toggle="tab">
                              <i class="fa fa-ticket-alt menu-icon"></i> Tickets </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_announcements" onclick="init_table_announcements(true);" aria-controls="home_announcements" role="tab" data-toggle="tab">
                              <i class="fa fa-bullhorn menu-icon"></i> Announcements </a>
                        </li>
                        <li role="presentation">
                           <a href="#home_tab_activity" aria-controls="home_tab_activity" role="tab" data-toggle="tab">
                              <i class="fa fa-window-maximize menu-icon"></i> Latest Activity </a>
                        </li>
                     </ul>
                     <hr class="hr-panel-heading hr-user-data-tabs" />
                     <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home_tab_tasks">
                           <div class="clearfix"></div>
                           <div class="">
                              <table class="datatables-demo table table-striped table-bordered" id="select_all_tasks" style="width:100%;">
                                 <thead>
                                    <tr>
                                       <th><?php echo lang('Dashboard.xin_title'); ?></th>
                                       <th><?php echo lang('Projects.xin_project_users'); ?></th>
                                       <th><?php echo lang('Projects.xin_start_date'); ?></th>
                                       <th><?php echo lang('Projects.xin_end_date'); ?></th>
                                       <th><?php echo lang('Projects.dashboard_xin_progress'); ?></th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php foreach ($all_tasks as $task) {
                                       // Get the progress class for styling
                                       $progressClass = getProgressClass($task['task_progress']);

                                       // Format the start and end dates
                                       $formatted_start_date = set_date_format($task['start_date']);
                                       $formatted_end_date = set_date_format($task['end_date']);

                                       // Handle multiple assigned users
                                       $assigned_to = explode(',', $task['assigned_to']);
                                       $multi_users = multi_user_profile_photo($assigned_to);
                                    ?>
                                       <tr>
                                          <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                          <td><?php echo $multi_users; ?></td>
                                          <td><?php echo $formatted_start_date; ?></td>
                                          <td><?php echo $formatted_end_date; ?></td>
                                          <td class="<?php echo $progressClass; ?>">
                                             <?php echo htmlspecialchars($task['task_progress']) . '%'; ?>
                                          </td>
                                       </tr>
                                    <?php } ?>
                                 </tbody>

                                 <?php
                                 function getProgressClass($progress)
                                 {
                                    if ($progress <= 20) {
                                       return 'bg-danger';
                                    } elseif ($progress <= 50) {
                                       return 'bg-warning';
                                    } elseif ($progress <= 75) {
                                       return 'bg-info';
                                    } else {
                                       return 'bg-success';
                                    }
                                 }
                                 ?>


                              </table>

                           </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home_tab_tickets">

                           <div class="clearfix"></div>
                           <table class="table customizable-table dt-table-loading tickets-table table-tickets" id="table-tickets" data-last-order-identifier="tickets" data-default-order="">
                              <thead>
                                 <tr>
                                    <th class="not_visible"><span class="hide"> - </span>
                                       <div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tickets"><label></label></div>
                                    </th>
                                    <th class="toggleable" id="th-number">#</th>
                                    <th class="toggleable" id="th-subject">Subject</th>
                                    <th class="toggleable" id="th-tags">Tags</th>
                                    <th class="toggleable" id="th-department">Department</th>
                                    <th>Service</th>
                                    <th class="toggleable" id="th-submitter">Contact</th>
                                    <th class="toggleable" id="th-status">Status</th>
                                    <th class="toggleable" id="th-priority">Priority</th>
                                    <th class="toggleable" id="th-last-reply">Last Reply</th>
                                    <th class="toggleable ticket_created_column" id="th-created">Created</th>
                                 </tr>
                              </thead>
                              <tbody></tbody>
                           </table>
                           <script id="hidden-columns-table-tickets" type="text/json"></script>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home_my_projects">

                           <div class="clearfix"></div>
                           <div class="">

                              <table class="datatables-demo table table-striped table-bordered" id="select_all_projects" style="width:100%;">
                                 <thead>
                                    <tr>
                                       <th>Project Name</th>
                                       <th>Start Date</th>
                                       <th>Deadline</th>
                                       <th>Status</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <?php
                                    foreach ($all_projects as $project) {
                                       // Extract project details
                                       $project_name = htmlspecialchars($project['title']);
                                       $formatted_start_date = set_date_format($project['start_date']);
                                       $formatted_end_date = set_date_format($project['end_date']);

                                       switch ($project['status']) {
                                          case 0:
                                             $status = 'Not Started';
                                             $status_class = 'text-danger';
                                             break;
                                          case 1:
                                             $status = 'In Progress';
                                             $status_class = 'text-warning';
                                             break;
                                          case 2:
                                             $status = 'Completed';
                                             $status_class = 'text-success';
                                             break;
                                          case 3:
                                             $status = 'Cancelled';
                                             $status_class = 'text-secondary';
                                             break;
                                          case 4:
                                             $status = 'On Hold';
                                             $status_class = 'text-info';
                                             break;
                                          default:
                                             $status = 'Unknown'; // Handle any unexpected status values
                                             $status_class = 'text-muted'; // Optional: default styling
                                             break;
                                       }
                                    ?>
                                       <tr>
                                          <td><?php echo $project_name; ?></td>
                                          <td><?php echo $formatted_start_date; ?></td>
                                          <td><?php echo $formatted_end_date; ?></td>
                                          <td class="<?php echo $status_class; ?>">
                                             <?php echo htmlspecialchars($status); ?>
                                          </td>
                                       </tr>
                                    <?php } ?>

                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home_my_reminders">
                           <a href="https://connect195.com/crm/admin/misc/reminders" class="mbot20 inline-block full-width">
                              View All </a>
                           <div class="">
                              <table class="dt-table-loading table table-my-reminders ">
                                 <thead>
                                    <tr>
                                       <th>Related to</th>
                                       <th>Description</th>
                                       <th>Date</th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                              </table>
                           </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home_announcements">
                           <a href="https://connect195.com/crm/admin/announcements" class="mbot20 inline-block full-width">View All</a>
                           <div class="clearfix"></div>
                           <div class="">
                              <table class="dt-table-loading table table-announcements ">
                                 <thead>
                                    <tr>
                                       <th>Subject</th>
                                       <th>Date</th>
                                    </tr>
                                 </thead>
                                 <tbody></tbody>
                              </table>
                           </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="home_tab_activity">
                           <a href="https://connect195.com/crm/admin/utilities/activity_log" class="mbot20 inline-block full-width">View All</a>
                           <div class="clearfix"></div>
                           <div class="activity-feed">
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-11-2024 11:56:56">
                                       5 hrs ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d2a1b3b1babbbc92bab0b4b6bba0b7b1a6fcb1bdbf">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.71.186.189]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-11-2024 11:56:56">
                                       5 hrs ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="e49785878c8d8aa48c8682808d96818790ca878b89">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.71.186.188]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="29-10-2024 11:59:35">
                                       6 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Customer Status Changed [ID: 48 Status(Active/Inactive): 0] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="29-10-2024 11:59:03">
                                       6 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Customer Status Changed [ID: 61 Status(Active/Inactive): 0] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="29-10-2024 11:58:55">
                                       6 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Customer Status Changed [ID: 56 Status(Active/Inactive): 0] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="29-10-2024 11:42:10">
                                       6 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6c1f0d0f0405022c040e0a08051e090f18420f0301">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.71.186.133]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 12:26:15">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Project Deleted [ID: 96, Name: priyanka Gupta] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 11:14:41">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Project Copied [ID: 96, NewID: 97] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 11:13:53">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    New Project Created [ID: 96] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:53:37">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="93e0f2f0fbfafdd3fbf1f5f7fae1f6f0e7bdf0fcfe">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.68.234.74]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:46:58">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d5a6b4b6bdbcbb95bdb7b3b1bca7b0b6a1fbb6bab8">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.68.234.44]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:36:06">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    sachin kumar<br />
                                    Staff Member Updated [ID: 1, sachin kumar] </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:34:37">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Failed Login Attempt [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="186b797b70717658707a7e7c716a7d7b6c367b7775">[email&#160;protected]</a>, Is Staff Member: Yes, IP: 172.68.234.6]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:33:58">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1063717378797e507872767479627573643e737f7d">[email&#160;protected]</a>, Is Staff Member: No, IP: 162.158.22.91]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 10:16:33">
                                       7 days ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Failed Login Attempt [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5930373f36193c37362b2f302a303637773a3634">[email&#160;protected]</a>, Is Staff Member: No, IP: 172.68.234.7]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="21-10-2024 13:56:20">
                                       2 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="4d253f0d252f2b29243f282e39632e2220">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="15-10-2024 14:41:45">
                                       3 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="177e797178577f7571737e657274633974787a">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="14-10-2024 11:29:29">
                                       3 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6d04030b022d050f0b09041f080e19430e0200">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="07-10-2024 12:13:16">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Non Existing User Tried to Login [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1b767a75726873232d2e2a2b5b7c767a727735787476">[email&#160;protected]</a>, Is Staff Member: No, IP: 162.158.13.131]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="05-10-2024 08:17:10">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="365e44765e5450525f445355421855595b">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="05-10-2024 08:13:21">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="d3bba193bbb1b5b7baa1b6b0a7fdb0bcbe">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-10-2024 14:49:32">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="345c46745c5652505d465157401a575b59">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-10-2024 13:57:56">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="117963517973777578637472653f727e7c">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-10-2024 09:59:49">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="fd958fbd959f9b99948f989e89d39e9290">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="04-10-2024 09:58:51">
                                       4 weeks ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3b53497b53595d5f52495e584f15585456">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="30-09-2024 09:42:25">
                                       a month ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="a7cfd5e7cfc5c1c3ced5c2c4d389c4c8ca">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="24-09-2024 10:22:54">
                                       a month ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f29b9c949db29a9094969b80979186dc919d9f">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="22-09-2024 12:04:11">
                                       a month ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="076f75476f6561636e756264732964686a">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="20-09-2024 15:36:20">
                                       a month ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="bcd4cefcd4dedad8d5ced9dfc892dfd3d1">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
                                 </div>
                              </div>
                              <div class="feed-item">
                                 <div class="date">
                                    <span class="text-has-action" data-toggle="tooltip" data-title="20-09-2024 10:17:05">
                                       2 months ago </span>
                                 </div>
                                 <div class="text">
                                    <br />
                                    Email Send To [Email: <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="157d67557d7773717c677076613b767a78">[email&#160;protected]</a>, Template: New Lead Assigned to Staff Member]
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

      <div class="widget" id="widget-calendar" data-name="Calendar">
         <div class="clearfix"></div>
         <div class="panel_s">
            <div class="panel-body">
               <div class="widget-dragger ui-sortable-handle"></div>
               <div class="dt-loader hide"></div>
               <div id="calendar_filters" style="display:none;">
                  <form action="https://connect195.com/crm/admin" method="post" accept-charset="utf-8">
                     <input type="hidden" name="csrf_token_name" value="8a9fc080ce5dfb6ee1882b13cc0c20e5">
                     <input type="hidden" name="calendar_filters" value="1">
                     <div class="row">
                        <div class="col-md-3">
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="events" id="cf_events">
                              <label for="cf_events">Events</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="tasks" id="cf_tasks">
                              <label for="cf_tasks">Tasks</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="projects" id="cf_projects">
                              <label for="cf_projects">Projects</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="invoices" id="cf_invoices">
                              <label for="cf_invoices">Invoices</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="estimates" id="cf_estimates">
                              <label for="cf_estimates">Estimates</label>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="proposals" id="cf_proposals">
                              <label for="cf_proposals">Proposals</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="contracts" id="cf_contracts">
                              <label for="cf_contracts">Contracts</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="customer_reminders" id="cf_customers_reminders">
                              <label for="cf_customers_reminders">Customer Reminders</label>
                           </div>

                           <div class="checkbox">
                              <input type="checkbox" value="1" name="expense_reminders" id="cf_expenses_reminders">
                              <label for="cf_expenses_reminders">Expense Reminders</label>
                           </div>

                           <div class="checkbox">
                              <input type="checkbox" value="1" name="lead_reminders" id="cf_leads_reminders">
                              <label for="cf_leads_reminders">Lead Reminders</label>
                           </div>
                        </div>
                        <div class="col-md-3">

                           <div class="checkbox">
                              <input type="checkbox" value="1" name="estimate_reminders" id="cf_estimates_reminders">
                              <label for="cf_estimates_reminders">Estimate Reminders</label>
                           </div>

                           <div class="checkbox">
                              <input type="checkbox" value="1" name="invoice_reminders" id="cf_invoices_reminders">
                              <label for="cf_invoices_reminders">Invoice Reminders</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="credit_note_reminders" id="cf_credit_note_reminders">
                              <label for="cf_credit_note_reminders">Credit Note Reminders</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="proposal_reminders" id="cf_proposal_reminders">
                              <label for="cf_proposal_reminders">Proposal Reminders</label>
                           </div>
                           <div class="checkbox">
                              <input type="checkbox" value="1" name="ticket_reminders" id="cf_ticket_reminders">
                              <label for="cf_ticket_reminders">Ticket Reminders</label>
                           </div>
                        </div>
                        <div class="col-md-3 text-right">
                           <a class="btn btn-default" href="https://connect195.com/crm/admin">Clear</a>
                           <button class="btn btn-success" type="submit">Apply</button>
                        </div>
                     </div>
                     <hr class="mbot15">
                     <div class="clearfix"></div>
                  </form>
               </div>
               <div id="calendar" class="fc fc-bootstrap3 fc-ltr">
                  <div class="fc-toolbar fc-header-toolbar">
                     <div class="fc-left">
                        <div class="btn-group">
                           <button type="button" class="fc-prev-button btn btn-info" aria-label="prev" style="display: block;">
                              <span class="glyphicon glyphicon-chevron-left"></span>
                           </button>
                           <button type="button" class="fc-next-button btn btn-info" aria-label="next" style="display: block;">
                              <span class="glyphicon glyphicon-chevron-right"></span>
                           </button>
                        </div>
                        <button type="button" class="fc-today-button btn btn-info disabled" disabled="" style="display: block;">today</button>
                     </div>
                     <div class="fc-right">
                        <div class="btn-group">
                           <button type="button" class="fc-month-button btn active btn-info" style="display: block;">month</button>
                           <button type="button" class="fc-agendaWeek-button btn btn-info" style="display: block;">week</button>
                           <button type="button" class="fc-agendaDay-button btn btn-info" style="display: block;">day</button>
                           <button type="button" class="fc-viewFullCalendar-button btn btn-info" style="display: block;">expand</button>
                           <button type="button" class="fc-calendarFilter-button btn btn-info" style="display: block;">filter by</button>
                        </div>
                     </div>
                     <div class="fc-center">
                        <h2>November 2024</h2>
                     </div>
                     <div class="fc-clear">
                     </div>
                  </div>
                  <div class="fc-view-container" style="">
                     <div class="fc-view fc-month-view fc-basic-view" style="">
                        <table class="table-bordered">
                           <thead class="fc-head">
                              <tr>
                                 <td class="fc-head-container ">
                                    <div class="fc-row panel-default">
                                       <table class="table-bordered">
                                          <thead>
                                             <tr>
                                                <th class="fc-day-header  fc-sun">
                                                   <span>Sun</span>
                                                </th>
                                                <th class="fc-day-header  fc-mon">
                                                   <span>Mon</span>
                                                </th>
                                                <th class="fc-day-header  fc-tue">
                                                   <span>Tue</span>
                                                </th>
                                                <th class="fc-day-header  fc-wed">
                                                   <span>Wed</span>
                                                </th>
                                                <th class="fc-day-header  fc-thu">
                                                   <span>Thu</span>
                                                </th>
                                                <th class="fc-day-header  fc-fri">
                                                   <span>Fri</span>
                                                </th>
                                                <th class="fc-day-header  fc-sat">
                                                   <span>Sat</span>
                                                </th>
                                             </tr>
                                          </thead>
                                       </table>
                                    </div>
                                 </td>
                              </tr>
                           </thead>
                           <tbody class="fc-body">
                              <tr>
                                 <td class="">
                                    <div class="fc-scroller fc-day-grid-container" style="overflow: hidden; height: 471.038px;">
                                       <div class="fc-day-grid fc-unselectable">
                                          <div class="fc-row fc-week panel-default" style="">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-other-month fc-past" data-date="2024-10-27">

                                                         </td>
                                                         <td class="fc-day  fc-mon fc-other-month fc-past" data-date="2024-10-28">

                                                         </td>
                                                         <td class="fc-day  fc-tue fc-other-month fc-past" data-date="2024-10-29">

                                                         </td>
                                                         <td class="fc-day  fc-wed fc-other-month fc-past" data-date="2024-10-30">

                                                         </td>
                                                         <td class="fc-day  fc-thu fc-other-month fc-past" data-date="2024-10-31">

                                                         </td>
                                                         <td class="fc-day  fc-fri fc-past" data-date="2024-11-01">

                                                         </td>
                                                         <td class="fc-day  fc-sat fc-past" data-date="2024-11-02">

                                                         </td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-other-month fc-past" data-date="2024-10-27">
                                                            <span class="fc-day-number">27</span>
                                                         </td>
                                                         <td class="fc-day-top fc-mon fc-other-month fc-past" data-date="2024-10-28">
                                                            <span class="fc-day-number">28</span>
                                                         </td>
                                                         <td class="fc-day-top fc-tue fc-other-month fc-past" data-date="2024-10-29">
                                                            <span class="fc-day-number">29</span>
                                                         </td>
                                                         <td class="fc-day-top fc-wed fc-other-month fc-past" data-date="2024-10-30">
                                                            <span class="fc-day-number">30</span>
                                                         </td>
                                                         <td class="fc-day-top fc-thu fc-other-month fc-past" data-date="2024-10-31">
                                                            <span class="fc-day-number">31</span>
                                                         </td>
                                                         <td class="fc-day-top fc-fri fc-past" data-date="2024-11-01">
                                                            <span class="fc-day-number">1</span>
                                                         </td>
                                                         <td class="fc-day-top fc-sat fc-past" data-date="2024-11-02">
                                                            <span class="fc-day-number">2</span>
                                                         </td>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td rowspan="3"></td>
                                                         <td rowspan="3"></td>
                                                         <td rowspan="3"></td>
                                                         <td rowspan="3"></td>
                                                         <td class="fc-event-container"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end" href="#" style="background-color:#03A9F4;border-color:#03A9F4" title="" onclick="init_task_modal(2555); return false" data-toggle="tooltip" data-original-title="Task - CEO Personal Review ... (#73 - CEO JOB  - HBF DIRECT LIMITED )">
                                                               <div class="fc-content"> <span class="fc-title">CEO Personal Review ...</span></div>
                                                            </a></td>
                                                         <td rowspan="3"></td>
                                                         <td rowspan="3"></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="fc-event-container"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end" href="#" style="background-color:#03A9F4;border-color:#03A9F4" title="" onclick="init_task_modal(2556); return false" data-toggle="tooltip" data-original-title="Task - Company Monthly Review... (#73 - CEO JOB  - HBF DIRECT LIMITED )">
                                                               <div class="fc-content"> <span class="fc-title">Company Monthly Review...</span></div>
                                                            </a></td>
                                                      </tr>
                                                      <tr>
                                                         <td class="fc-event-container"><a class="fc-day-grid-event fc-h-event fc-event fc-start fc-end" href="https://connect195.com/crm/admin/projects/view/97" style="background-color:#B72974;border-color:#B72974" title="Project - priyanka Gupta (Vishal Bhatia )" data-toggle="tooltip">
                                                               <div class="fc-content"> <span class="fc-title">priyanka Gupta</span></div>
                                                            </a></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <div class="fc-row fc-week panel-default" style="height: 65px;">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-past" data-date="2024-11-03"></td>
                                                         <td class="fc-day  fc-mon fc-past" data-date="2024-11-04"></td>
                                                         <td class="fc-day  fc-tue fc-past" data-date="2024-11-05"></td>
                                                         <td class="fc-day  fc-wed fc-past" data-date="2024-11-06"></td>
                                                         <td class="fc-day  fc-thu fc-past" data-date="2024-11-07"></td>
                                                         <td class="fc-day  fc-fri fc-past" data-date="2024-11-08"></td>

                                                         <td class="fc-day  fc-sat fc-today alert alert-info" data-date="2024-11-09"></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-past" data-date="2024-11-03"><span class="fc-day-number">3</span></td>
                                                         <td class="fc-day-top fc-mon fc-past" data-date="2024-11-04"><span class="fc-day-number">4</span></td>
                                                         <td class="fc-day-top fc-tue fc-past" data-date="2024-11-05"><span class="fc-day-number">5</span></td>
                                                         <td class="fc-day-top fc-wed fc-past" data-date="2024-11-06"><span class="fc-day-number">6</span></td>
                                                         <td class="fc-day-top fc-thu fc-past" data-date="2024-11-07"><span class="fc-day-number">7</span></td>
                                                         <td class="fc-day-top fc-fri fc-past" data-date="2024-11-08"><span class="fc-day-number">8</span></td>
                                                         <td class="fc-day-top fc-sat fc-today alert alert-info" data-date="2024-11-09"><span class="fc-day-number">9</span></td>
                                                      </tr>
                                                   </thead>
                                                </table>
                                             </div>
                                          </div>
                                          <div class="fc-row fc-week panel-default" style="height: 65px;">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-future" data-date="2024-11-10"></td>
                                                         <td class="fc-day  fc-mon fc-future" data-date="2024-11-11"></td>
                                                         <td class="fc-day  fc-tue fc-future" data-date="2024-11-12"></td>
                                                         <td class="fc-day  fc-wed fc-future" data-date="2024-11-13"></td>
                                                         <td class="fc-day  fc-thu fc-future" data-date="2024-11-14"></td>
                                                         <td class="fc-day  fc-fri fc-future" data-date="2024-11-15"></td>
                                                         <td class="fc-day  fc-sat fc-future" data-date="2024-11-16"></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-future" data-date="2024-11-10"><span class="fc-day-number">10</span></td>
                                                         <td class="fc-day-top fc-mon fc-future" data-date="2024-11-11"><span class="fc-day-number">11</span></td>
                                                         <td class="fc-day-top fc-tue fc-future" data-date="2024-11-12"><span class="fc-day-number">12</span></td>
                                                         <td class="fc-day-top fc-wed fc-future" data-date="2024-11-13"><span class="fc-day-number">13</span></td>
                                                         <td class="fc-day-top fc-thu fc-future" data-date="2024-11-14"><span class="fc-day-number">14</span></td>
                                                         <td class="fc-day-top fc-fri fc-future" data-date="2024-11-15"><span class="fc-day-number">15</span></td>
                                                         <td class="fc-day-top fc-sat fc-future" data-date="2024-11-16"><span class="fc-day-number">16</span></td>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <div class="fc-row fc-week panel-default" style="height: 65px;">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-future" data-date="2024-11-17"></td>
                                                         <td class="fc-day  fc-mon fc-future" data-date="2024-11-18"></td>
                                                         <td class="fc-day  fc-tue fc-future" data-date="2024-11-19"></td>
                                                         <td class="fc-day  fc-wed fc-future" data-date="2024-11-20"></td>
                                                         <td class="fc-day  fc-thu fc-future" data-date="2024-11-21"></td>
                                                         <td class="fc-day  fc-fri fc-future" data-date="2024-11-22"></td>
                                                         <td class="fc-day  fc-sat fc-future" data-date="2024-11-23"></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-future" data-date="2024-11-17"><span class="fc-day-number">17</span></td>
                                                         <td class="fc-day-top fc-mon fc-future" data-date="2024-11-18"><span class="fc-day-number">18</span></td>
                                                         <td class="fc-day-top fc-tue fc-future" data-date="2024-11-19"><span class="fc-day-number">19</span></td>
                                                         <td class="fc-day-top fc-wed fc-future" data-date="2024-11-20"><span class="fc-day-number">20</span></td>
                                                         <td class="fc-day-top fc-thu fc-future" data-date="2024-11-21"><span class="fc-day-number">21</span></td>
                                                         <td class="fc-day-top fc-fri fc-future" data-date="2024-11-22"><span class="fc-day-number">22</span></td>
                                                         <td class="fc-day-top fc-sat fc-future" data-date="2024-11-23"><span class="fc-day-number">23</span></td>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <div class="fc-row fc-week panel-default" style="height: 65px;">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-future" data-date="2024-11-24"></td>
                                                         <td class="fc-day  fc-mon fc-future" data-date="2024-11-25"></td>
                                                         <td class="fc-day  fc-tue fc-future" data-date="2024-11-26"></td>
                                                         <td class="fc-day  fc-wed fc-future" data-date="2024-11-27"></td>
                                                         <td class="fc-day  fc-thu fc-future" data-date="2024-11-28"></td>
                                                         <td class="fc-day  fc-fri fc-future" data-date="2024-11-29"></td>
                                                         <td class="fc-day  fc-sat fc-future" data-date="2024-11-30"></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-future" data-date="2024-11-24"><span class="fc-day-number">24</span></td>
                                                         <td class="fc-day-top fc-mon fc-future" data-date="2024-11-25"><span class="fc-day-number">25</span></td>
                                                         <td class="fc-day-top fc-tue fc-future" data-date="2024-11-26"><span class="fc-day-number">26</span></td>
                                                         <td class="fc-day-top fc-wed fc-future" data-date="2024-11-27"><span class="fc-day-number">27</span></td>
                                                         <td class="fc-day-top fc-thu fc-future" data-date="2024-11-28"><span class="fc-day-number">28</span></td>
                                                         <td class="fc-day-top fc-fri fc-future" data-date="2024-11-29"><span class="fc-day-number">29</span></td>
                                                         <td class="fc-day-top fc-sat fc-future" data-date="2024-11-30"><span class="fc-day-number">30</span></td>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                          <div class="fc-row fc-week panel-default" style="height: 65px;">
                                             <div class="fc-bg">
                                                <table class="table-bordered">
                                                   <tbody>
                                                      <tr>
                                                         <td class="fc-day  fc-sun fc-other-month fc-future" data-date="2024-12-01"></td>
                                                         <td class="fc-day  fc-mon fc-other-month fc-future" data-date="2024-12-02"></td>
                                                         <td class="fc-day  fc-tue fc-other-month fc-future" data-date="2024-12-03"></td>
                                                         <td class="fc-day  fc-wed fc-other-month fc-future" data-date="2024-12-04"></td>
                                                         <td class="fc-day  fc-thu fc-other-month fc-future" data-date="2024-12-05"></td>
                                                         <td class="fc-day  fc-fri fc-other-month fc-future" data-date="2024-12-06"></td>
                                                         <td class="fc-day  fc-sat fc-other-month fc-future" data-date="2024-12-07"></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                             <div class="fc-content-skeleton">
                                                <table>
                                                   <thead>
                                                      <tr>
                                                         <td class="fc-day-top fc-sun fc-other-month fc-future" data-date="2024-12-01"><span class="fc-day-number">1</span></td>
                                                         <td class="fc-day-top fc-mon fc-other-month fc-future" data-date="2024-12-02"><span class="fc-day-number">2</span></td>
                                                         <td class="fc-day-top fc-tue fc-other-month fc-future" data-date="2024-12-03"><span class="fc-day-number">3</span></td>
                                                         <td class="fc-day-top fc-wed fc-other-month fc-future" data-date="2024-12-04"><span class="fc-day-number">4</span></td>
                                                         <td class="fc-day-top fc-thu fc-other-month fc-future" data-date="2024-12-05"><span class="fc-day-number">5</span></td>
                                                         <td class="fc-day-top fc-fri fc-other-month fc-future" data-date="2024-12-06"><span class="fc-day-number">6</span></td>
                                                         <td class="fc-day-top fc-sat fc-other-month fc-future" data-date="2024-12-07"><span class="fc-day-number">7</span></td>
                                                      </tr>
                                                   </thead>
                                                   <tbody>
                                                      <tr>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                         <td></td>
                                                      </tr>
                                                   </tbody>
                                                </table>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <div class="widget" id="widget-weekly_payments_chart" data-name="Weekly Payment Records">
         <div class="row" id="weekly_payments">
            <div class="col-md-12">
               <div class="panel_s">
                  <div class="panel-body padding-10">
                     <div class="widget-dragger"></div>
                     <div class="col-md-12">
                        <p class="pull-left mtop5">Weekly Payment Records</p>
                        <a href="https://connect195.com/crm/admin/reports/sales" class="pull-right mtop5">Full Report</a>
                        <div class="clearfix"></div>
                        <div class="clearfix"></div>
                        <div class="row mtop5">
                           <hr class="hr-panel-heading-dashboard">
                        </div>
                        <canvas height="130" class="weekly-payments-chart-dashboard" id="weekly-payment-statistics"></canvas>
                        <div class="clearfix"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script>
         document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('weekly-payment-statistics').getContext('2d');

            // Example data - replace this with your actual weekly payment data
            const labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            const data = {
               labels: labels,
               datasets: [{
                  label: 'Payments',
                  data: [1200, 1500, 800, 1700, 2200, 1300, 1900], // Sample data for each day
                  backgroundColor: 'rgba(54, 162, 235, 0.2)', // Light blue background
                  borderColor: 'rgba(54, 162, 235, 1)', // Darker blue border
                  borderWidth: 1
               }]
            };

            // Chart configuration
            const config = {
               type: 'bar', // Choose 'line' or 'bar' depending on your preference
               data: data,
               options: {
                  responsive: true,
                  scales: {
                     y: {
                        beginAtZero: true,
                        title: {
                           display: true,
                           text: 'Amount (in USD)' // Replace with appropriate currency/label
                        }
                     }
                  },
                  plugins: {
                     legend: {
                        display: true,
                        position: 'top'
                     }
                  }
               }
            };

            // Render the chart
            new Chart(ctx, config);
         });
      </script>
   </div>
   <div class="col-md-4" data-container="right-4">
      <div class="widget" id="widget-todos" data-name="My To Do Items">
         <div class="panel_s todo-panel">
            <div class="panel-body padding-10">
               <div class="widget-dragger"></div>
               <p class="pull-left padding-5">
                  My To Do Items </p>
               <a href="#__todo" data-toggle="modal" class="pull-right padding-5" style="padding-right:0px;">
                  New To Do </a>
               <div class="clearfix"></div>
               <hr class="hr-panel-heading-dashboard">
               <h4 class="todo-title text-warning"><i class="fa fa-warning"></i> Latest to do's</h4>
               <ul class="list-unstyled todo unfinished-todos todos-sortable sortable">
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="0" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" name="todo_id" value="170">
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more no-padding-left" data-todo-description="170">
                              Proposal to jasmeet<br /> </p>
                           <a href="#" onclick="delete_todo_item(this,170); return false;" class="pull-right text-muted">
                              <i class="fa fa-remove"></i>
                           </a>
                           <a href="#" onclick="edit_todo_item(170); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date">08-12-2022 09:36:25</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="0" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" name="todo_id" value="171">
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more no-padding-left" data-todo-description="171">
                              Private Equity Project Report <br />
                              Pitch Deck </p>
                           <a href="#" onclick="delete_todo_item(this,171); return false;" class="pull-right text-muted">
                              <i class="fa fa-remove"></i>
                           </a>
                           <a href="#" onclick="edit_todo_item(171); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date">08-12-2022 09:37:18</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="0" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" name="todo_id" value="173">
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more no-padding-left" data-todo-description="173">
                              Paramjeet jasmeet </p>
                           <a href="#" onclick="delete_todo_item(this,173); return false;" class="pull-right text-muted">
                              <i class="fa fa-remove"></i>
                           </a>
                           <a href="#" onclick="edit_todo_item(173); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date">09-12-2022 01:35:12</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="0" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" name="todo_id" value="174">
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more no-padding-left" data-todo-description="174">
                              Proposal to Franciscan solutions </p>
                           <a href="#" onclick="delete_todo_item(this,174); return false;" class="pull-right text-muted">
                              <i class="fa fa-remove"></i>
                           </a>
                           <a href="#" onclick="edit_todo_item(174); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date">21-12-2022 10:04:23</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="0" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" name="todo_id" value="176">
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more no-padding-left" data-todo-description="176">
                              BOB SINGH<br />
                              <br />
                              1. Shashwat share return<br />
                              2. Resignation<br />
                              3. kanisk case <br />
                              4. Baggage Porter case<br />
                              5.
                           </p>
                           <a href="#" onclick="delete_todo_item(this,176); return false;" class="pull-right text-muted">
                              <i class="fa fa-remove"></i>
                           </a>
                           <a href="#" onclick="edit_todo_item(176); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date">27-01-2023 10:09:14</small>
                        </div>
                     </div>
                  </li>
                  <li class="padding no-todos ui-state-disabled hide">No todos found</li>
               </ul>
               <h4 class="todo-title text-success"><i class="fa fa-check"></i> Latest finished to do's</h4>
               <ul class="list-unstyled todo finished-todos todos-sortable sortable">
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="1" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" value="107" name="todo_id" checked>
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more line-throught no-padding-left">
                              List of Meetings <br />
                              <br />
                              1. Amit ref 7493186801 - Vivek Singh<br />
                              2. vineet<br />
                              3. shweta<br />
                              4. sonu<br />
                              5.
                           </p>
                           <a href="#" onclick="delete_todo_item(this,107); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                           <a href="#" onclick="edit_todo_item(107); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date todo-date-finished">06-12-2022 16:39:32</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="" />

                     <input type="hidden" name="finished" value="1" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" value="113" name="todo_id" checked>
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more line-throught no-padding-left">
                              NSDL Process </p>
                           <a href="#" onclick="delete_todo_item(this,113); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                           <a href="#" onclick="edit_todo_item(113); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date todo-date-finished">06-12-2022 16:39:37</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="1" />

                     <input type="hidden" name="finished" value="1" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" value="5" name="todo_id" checked>
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more line-throught no-padding-left">
                              Track - Sunil Case<br />
                              <br />
                              Draft of complaint
                           </p>
                           <a href="#" onclick="delete_todo_item(this,5); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                           <a href="#" onclick="edit_todo_item(5); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date todo-date-finished">05-08-2022 10:46:11</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="1" />

                     <input type="hidden" name="finished" value="1" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" value="80" name="todo_id" checked>
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more line-throught no-padding-left">
                              Make secure crm id </p>
                           <a href="#" onclick="delete_todo_item(this,80); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                           <a href="#" onclick="edit_todo_item(80); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date todo-date-finished">06-12-2022 16:39:43</small>
                        </div>
                     </div>
                  </li>
                  <li>

                     <input type="hidden" name="todo_order" value="2" />

                     <input type="hidden" name="finished" value="1" />
                     <div class="media">
                        <div class="media-left no-padding-right">
                           <div class="dragger todo-dragger"></div>
                           <div class="checkbox checkbox-default todo-checkbox">
                              <input type="checkbox" value="6" name="todo_id" checked>
                              <label></label>
                           </div>
                        </div>
                        <div class="media-body">
                           <p class="todo-description read-more line-throught no-padding-left">
                              brijesh case to discuss with pujari <br /> </p>
                           <a href="#" onclick="delete_todo_item(this,6); return false;" class="pull-right text-muted"><i class="fa fa-remove"></i></a>
                           <a href="#" onclick="edit_todo_item(6); return false;" class="pull-right text-muted mright5">
                              <i class="fa fa-pencil"></i>
                           </a>
                           <small class="todo-date todo-date-finished">05-08-2022 10:46:11</small>
                        </div>
                     </div>
                  </li>
                  <li class="padding no-todos ui-state-disabled hide">No finished todos found</li>
               </ul>
            </div>
         </div>
         <div class="modal fade" id="__todo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                     <h4 class="modal-title" id="myModalLabel">
                        <!-- <span class="edit-title hide">Edit todo item</span> -->
                        <span class="add-title hide">Add New Todo</span>
                     </h4>
                  </div>
                  <form action="<?php echo $main . 'erp/todo/add_todo_list'; ?>" id="add_new_todo_item" method="post" accept-charset="utf-8">
                     <input type="hidden" name="csrf_token_name" value="<?php echo $csrf_token; ?>" />

                     <div class="modal-body">
                        <div class="row">
                           <input type="hidden" name="todoid" value="" />

                           <div class="col-md-12">
                              <div class="form-group" app-field-wrapper="description">
                                 <label for="description" class="control-label">Description</label>
                                 <textarea id="description" name="task_insert" class="form-control" rows="4" placeholder="Enter task description here..."></textarea>
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Save</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>

      <div class="widget" id="widget-projects_chart" data-name="Projects Chart">
         <div class="row">
            <div class="col-md-12">
               <div class="panel_s">
                  <div class="panel-body padding-10">
                     <div class="widget-dragger"></div>
                     <p class="padding-5">Statistics by Project Status</p>
                     <hr class="hr-panel-heading-dashboard">
                     <div class="relative" style="height:250px">
                        <div id="projects_status_stats" style="height: 250px;"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <div class="widget" id="widget-tasks_chart" data-name="Tasks Chart">
         <div class="row">
            <div class="col-md-12">
               <div class="panel_s">
                  <div class="panel-body padding-10">
                     <div class="widget-dragger"></div>
                     <p class="padding-5">Statistics by Tasks Status</p>
                     <hr class="hr-panel-heading-dashboard">
                     <div class="relative" style="height:250px">
                        <div id="tasks_status_stats" style="height: 250px;"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- <div class="widget" id="widget-tickets_chart">
                  <div class="panel_s">
                     <div class="panel-body padding-10">
                        <div class="widget-dragger"></div>
                        <div class="row">
                           <div class="col-md-12 mbot10">
                              <p class="padding-5"> Tickets Awaiting Reply by Status</p>
                              <hr class="hr-panel-heading-dashboard">
                              <canvas height="170" id="tickets-awaiting-reply-by-status"></canvas>
                           </div>
                           <div class="clearfix"></div>
                           <hr class="no-margin" />
                           <div class="clearfix mtop10"></div>
                           <div class="col-md-12">
                              <p class="padding-5">Tickets Awaiting Reply by Department</p>
                              <hr class="hr-panel-heading-dashboard">
                              <canvas height="170" id="tickets-awaiting-reply-by-department"></canvas>
                           </div>
                        </div>
                     </div>
                  </div>
               </div> -->

      <div class="widget" id="widget-projects_activity" data-name="Latest Project Activity">
         <div class="panel_s projects-activity">
            <div class="panel-body padding-10">
               <div class="widget-dragger"></div>
               <p class="padding-5">Latest Project Activity</p>
               <hr class="hr-panel-heading-dashboard">
               <div class="activity-feed">
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 11:14:41">
                           7 days ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Added new team member
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/97">priyanka Gupta</a>
                     </div>
                     <p class="text-muted mtop5">sachin kumar</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="28-10-2024 11:14:41">
                           7 days ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Created the project
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/97">priyanka Gupta</a>
                     </div>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="05-05-2024 17:07:25">
                           6 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Project completed
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/79">Mizoram Client</a>
                     </div>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="05-05-2024 17:07:25">
                           6 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Marked all tasks as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/79">Mizoram Client</a>
                     </div>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 13:01:50">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Task marked as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Black Coffee Cafe</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 13:00:57">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Task marked as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Nehal Mehta - Dubai (Kethana)</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 13:00:52">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Nehal Mehta - Dubai (Kethana)</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 13:00:38">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">HVR Solar</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 13:00:25">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Task marked as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">HVR Solar</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 12:59:54">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Created new milestone
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">MANTR AI</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 12:58:20">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Created new milestone
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">My Lyf Care</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 12:57:39">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Task marked as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Tvaster Genkalp</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 12:57:22">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Tvaster Genkalp</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 10:58:38">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Created new milestone
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">I am Here </p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 10:51:49">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Task marked as complete
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">AMIT LEATHERS </p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="30-03-2024 10:51:22">
                           7 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Deleted milestone
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">JSS Dainer</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="18-03-2024 15:56:02">
                           8 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/119">Vedansh Tyagi</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/76">Vedansh Tyagi</a>
                     </div>
                     <p class="text-muted mtop5">I am Here</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="09-03-2024 14:12:44">
                           8 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Monthly Review and plan</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="09-03-2024 14:11:23">
                           8 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Uploaded attachment on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Monthly Review and plan</p>
                  </div>
                  <div class="feed-item">
                     <div class="date"><span class="text-has-action" data-toggle="tooltip" data-title="09-03-2024 14:06:40">
                           8 months ago </span>
                     </div>
                     <div class="text">
                        <p class="bold no-mbot">
                           <a href="https://connect195.com/crm/admin/profile/1">sachin kumar</a> -
                           Commented on task
                        </p>
                        Project Name: <a href="https://connect195.com/crm/admin/projects/view/73">CEO JOB </a>
                     </div>
                     <p class="text-muted mtop5">Monthly Review and plan</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<link rel="stylesheet" type="text/css" id="roboto-css" href="https://connect195.com/gpsadvanced/public/assets/pluginss/roboto/robotocss">
<link rel="stylesheet" type="text/css" id="app-css" href="https://connect195.com/gpsadvanced/public/assets/csss/style.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">


<style>
   .admin #side-menu,
   .admin #setup-menu {
      background: #fff;
   }

   body {
      background: #fff;
   }

   #setup-menu-wrapper {
      background: #fff;
   }

   .admin #header {
      background: #fff;
   }
</style>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.0/perfect-scrollbar.min.js"></script>

<script>
   $(document).ready(function() {
      var main_url = "<?php echo base_url(); ?>";

      function fetchProjectsStatusChart() {
         var requestUrl = main_url + '/erp/projects/projects_status_stats_chart';
         console.log('Requesting URL:', requestUrl); // Check the final request URL

         $.ajax({
            url: requestUrl,
            method: "GET",
            dataType: "json",
            success: function(response) {
               console.log('Response:', response); // Log the response for debugging

               if (response) {
                  // Convert all values to floats to ensure they are numbers
                  const seriesData = [
                     parseFloat(response.not_started) || 0, // Handle invalid or empty data
                     parseFloat(response.in_progress) || 0,
                     parseFloat(response.completed) || 0,
                     parseFloat(response.cancelled) || 0,
                     parseFloat(response.hold) || 0
                  ];

                  const labels = [
                     response.not_started_lb,
                     response.in_progress_lb,
                     response.completed_lb,
                     response.cancelled_lb,
                     response.hold_lb
                  ];

                  const options = {
                     chart: {
                        type: 'donut',
                        height: '100%',
                        events: {
                           mounted: function() {
                              console.log('Chart Mounted');
                           },
                           updated: function() {
                              console.log('Chart Updated');
                           }
                        }
                     },
                     labels: labels,
                     series: seriesData,
                     colors: ["#989898", "#03a9f4", "#008000", "#FF0000", "#FFA500"],
                     plotOptions: {
                        pie: {
                           donut: {
                              size: '65%',
                              labels: {
                                 show: true,
                                 name: {
                                    show: true
                                 },
                                 value: {
                                    show: true
                                 }
                              }
                           }
                        }
                     },
                     tooltip: {
                        y: {
                           formatter: function(value) {
                              return value + '%';
                           }
                        }
                     },
                     title: {
                        text: response.total_label + ': ' + response.total,
                        align: 'center'
                     }
                  };

                  try {
                     var chart = new ApexCharts(document.querySelector("#projects_status_stats"), options);
                     chart.render().then(function() {
                        console.log('Chart rendered successfully');
                     });
                  } catch (error) {
                     console.error('Error rendering chart:', error);
                  }
               } else {
                  console.error('No data returned from server.');
               }
            },
            error: function(error) {
               console.error("Error fetching chart data:", error);
            }
         });
      }

      function fetchTaskssStatusChart() {
         var requestUrl = main_url + '/erp/tasks/tasks_status_stats_chart';
         console.log('Requesting URL:', requestUrl); // Check the final request URL

         $.ajax({
            url: requestUrl,
            method: "GET",
            dataType: "json",
            success: function(response) {
               console.log('Response:', response); // Log the response for debugging

               if (response) {
                  // Convert all values to floats to ensure they are numbers, handle invalid or empty data
                  const seriesData = [
                     parseFloat(response.not_started) || 0,
                     parseFloat(response.in_progress) || 0,
                     parseFloat(response.completed) || 0,
                     parseFloat(response.cancelled) || 0,
                     parseFloat(response.hold) || 0
                  ];

                  const labels = [
                     response.not_started_lb || 'Not Started',
                     response.in_progress_lb || 'In Progress',
                     response.completed_lb || 'Completed',
                     response.cancelled_lb || 'Cancelled',
                     response.hold_lb || 'Hold'
                  ];

                  const options = {
                     chart: {
                        type: 'donut',
                        height: '100%',
                        events: {
                           mounted: function() {
                              console.log('Chart Mounted');
                           },
                           updated: function() {
                              console.log('Chart Updated');
                           }
                        }
                     },
                     labels: labels,
                     series: seriesData,
                     colors: ["#989898", "#03a9f4", "#008000", "#FF0000", "#FFA500"],
                     plotOptions: {
                        pie: {
                           donut: {
                              size: '65%',
                              labels: {
                                 show: true,
                                 name: {
                                    show: true
                                 },
                                 value: {
                                    show: true
                                 }
                              }
                           }
                        }
                     },
                     tooltip: {
                        y: {
                           formatter: function(value) {
                              return value + '%';
                           }
                        }
                     },
                     title: {
                        text: response.total_label + ': ' + response.total,
                        align: 'center'
                     }
                  };

                  try {
                     var chart = new ApexCharts(document.querySelector("#tasks_status_stats"), options);
                     chart.render().then(function() {
                        console.log('Chart rendered successfully');
                     });
                  } catch (error) {
                     console.error('Error rendering chart:', error);
                  }
               } else {
                  console.error('No data returned from server.');
               }
            },
            error: function(error) {
               console.error("Error fetching chart data:", error);
            }
         });
      }

      fetchTaskssStatusChart();
      fetchProjectsStatusChart(); // Ensure this function is called

      // Initialize PerfectScrollbar
      setTimeout(function() {
         var px = new PerfectScrollbar('.feed-scroll', {
            wheelSpeed: .5,
            swipeEasing: 0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
         });
         var px = new PerfectScrollbar('.pro-scroll', {
            wheelSpeed: .5,
            swipeEasing: 0,
            wheelPropagation: 1,
            minScrollbarLength: 40,
         });
      }, 700);
   });
</script>
<script>
   $(document).ready(function() {
      $('#select_all_tasks').DataTable();
      $('#select_all_projects').DataTable();
   });
</script>