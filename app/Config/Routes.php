<?php

namespace Config;

use App\Controllers\Erp\Finance;
use App\Models\UsersModel;


// Create a new instance of our RouteCollection class.
$routes = Services::routes();


// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */


$routes->get('/s3-images', 'S3ImageController::index');
$routes->post('/s3-images/upload', 'S3ImageController::upload');

$routes->get('/s3-images/edit/(:any)', 'S3ImageController::edit/$1');
$routes->post('/s3-images/update/(:any)', 'S3ImageController::update/$1');

$routes->get('/s3-images/download/(:any)', 'S3ImageController::download/$1');
$routes->get('/s3-images/delete/(:any)', 'S3ImageController::delete/$1');




$routes->get('/', 'Home::index', ['namespace' => 'App\Controllers']);

$routes->match(['get', 'post'], 'erp/auth/login/', 'Auth::login', ['namespace' => 'App\Controllers\Erp']);
$routes->get('erp/desk', 'Dashboard::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/forgot-password/', 'Auth::forgot_password', ['namespace' => 'App\Controllers\Erp']);
$routes->post('erp/check-password/', 'Auth::check_password', ['namespace' => 'App\Controllers\Erp']);
$routes->get('erp/verified-password/', 'Auth::verified_password', ['namespace' => 'App\Controllers\Erp']);
$routes->match(['get', 'post'], 'erp/auth/unlock/', 'Auth::unlock', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/my-profile/', 'Profile::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/system-logout/', 'Logout::index', ['namespace' => 'App\Controllers\Erp']);

$routes->get('erp/set-language/(:segment)', 'Dashboard::language/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->post('erp/set-clocking', 'Timesheet::set_clocking', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/staff-tickets-priority-chart', 'Tickets::staff_tickets_priority_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/staff-payroll-chart', 'Payroll::staff_payroll_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/staff-project-status-chart', 'Projects::staff_project_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('download', 'Download::index', ['namespace' => 'App\Controllers', 'filter' => 'checklogin']);
// planning Configuration
$routes->get('erp/planning_configuration', 'Dashboard::planning_configuration', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-planning-entities', 'Dashboard::add_planning_entities', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/planning-configuration-detail/(:any)', 'Dashboard::planning_configuration_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-planning-entity', 'Dashboard::update_planning_entity', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-planning-entity', 'Dashboard::delete_planning_entity', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


//year-plannning
$routes->get('erp/year-planning', 'Dashboard::year_planning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/year_planning_submit', 'Dashboard::year_planning_submit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/year-planning-detail/(:any)', 'Dashboard::year_planning_detail/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-year-planning-entity', 'Dashboard::update_year_planning_entity', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-year-planning', 'Dashboard::delete_year_planning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//monthly-planning
$routes->get('erp/monthly-planning-list', 'Dashboard::monthly_planning_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/monthly-plan-submit', 'Dashboard::monthly_plan_submit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/monthly-planning-detail/(:any)', 'Dashboard::monthly_planning_detail/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-monthly-planning-entity', 'Dashboard::update_monthly_planning_entity', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-monthly-planning', 'Dashboard::delete_monthly_planning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//monthly-planning-review
$routes->get('erp/monthly-planning-review/(:any)', 'Dashboard::monthly_planning_review/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/review-monthly-planning-entity', 'Dashboard::review_monthly_planning_entity', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//monthly-achived
$routes->get('erp/monthly-planning', 'Dashboard::monthly_planning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/monthly-achive-submit', 'Dashboard::monthly_achive_submit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


//Work-Management

// projects||Staff
$routes->get('erp/projects-list/', 'Projects::show_projects', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/create-project/', 'Projects::create_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-project', 'projects::add_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/edit-project/(:segment)', 'Projects::edit_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/project-detail/(:segment)', 'Projects::project_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/get-employelist', 'Projects::get_employe', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/copy-project', 'Projects::copy_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-project', 'Projects::update_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-project', 'projects::delete_project', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// Milestones
$routes->post('erp/milestones-save', 'Milestones::save', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/milestones-delete/(:any)', 'Milestones::delete/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/milestones-getdata/(:any)', 'Milestones::getData/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// add files
$routes->post('erp/projects-add-attachment', 'projects::add_attachment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-project-file/(:any)', 'projects::delete_project_file/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-project-discussion/(:any)', 'projects::delete_project_discussion/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-project-bug/(:any)', 'projects::delete_project_bug/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// discussion and Notes
$routes->post('erp/add-discussion', 'projects::add_discussion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-project-client-discussion', 'projects::add_project_client_discussion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-client-project-bug', 'projects::add_client_project_bug', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-note', 'projects::add_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/projects/add_attachment', 'projects::add_client_project_attachment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/projects/add_note', 'projects::add_client_project_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// Add Bug
$routes->post('erp/add-bug', 'projects::add_bug', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// project task
$routes->post('erp/add-projectTask', 'Tasks::add_task', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-projectTask', 'Tasks::delete_projecttask', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// project invoice
$routes->get('erp/project-invoice/(:any)', 'projects::project_invoice/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/create-new-invoice', 'invoices::create_new_invoice', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/edit-projectInvoice/(:segment)', 'projects::edit_projectInvoice', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-detail/(:any)', 'Invoices::invoice_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-invoice-data', 'Invoices::read_invoice_data', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/pay-invoice-record', 'Invoices::pay_invoice_record', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-invoice', 'Invoices::update_invoice', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-invoice-items', 'Invoices::delete_invoice_items', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


//not use route start
$routes->get('erp/projects-dashboard/', 'Projects::projects_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/projects-calendar/', 'Projects::projects_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/projects-scrum-board/', 'Projects::projects_scrum_board', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/projects-grid/', 'Projects::projects_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// projects||Clients
$routes->get('erp/my-projects-list/', 'Projects::projects_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-projects-list/', 'Projects::client_projects_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/project-details/(:segment)', 'Projects::client_project_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/project-tasks-list/(:segment)', 'Projects::project_tasks_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//not use route end


// tasks||Staff
$routes->get('erp/tasks-list/', 'Tasks::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tasks-data-Lists', 'Tasks::tasks_data_lists', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-tasks', 'Tasks::add_tasks', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-client-tasks', 'Tasks::add_task', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-tasks', 'Tasks::delete_tasks', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/task-detail/(:segment)', 'Tasks::task_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-task-progress', 'Tasks::update_task_progress', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-task', 'Tasks::update_task', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-task-discussion', 'Tasks::add_discussion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-task-discussion', 'Tasks::delete_task_discussion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-task-note', 'Tasks::add_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-task-note', 'Tasks::delete_task_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-attachment', 'tasks::add_attachment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-task-file', 'Tasks::delete_task_file', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/tasks-grid/', 'Tasks::tasks_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-grid-task', 'Tasks::add_gridTask', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-gridTask', 'Tasks/delete_gridtask', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/tasks-summary/', 'Tasks::tasks_summary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tasks-calendar/', 'Tasks::tasks_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tasks-scrum-board/', 'Tasks::tasks_scrum_board', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/edit-task/(:segment)', 'Tasks::edit_task', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-projectTask/(:num)', 'Tasks::taskUpdate/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-task-status-chart/', 'Tasks::client_task_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


// Documention
$routes->get('erp/documentation', 'Documents::upload_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/save-document', 'Documents::save_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/documentfiles-updates', 'Documents::documentfiles_updates', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('view/employe-document/(:any)', 'Documents::view_document/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//employee

$routes->get('erp/staff-list/', 'Employees::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/employees-data-list', 'Employees::employees_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/is-designation/(:any)', 'Employees::is_designation/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-staff', 'Employees::delete_staff', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-employee', 'Employees::add_employee', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/staff-grid/', 'Employees::staff_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/staff-dashboard/', 'Employees::staff_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/organization-chart/', 'Employees::staff_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/employee-details/(:segment)', 'Employees::staff_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-contract-info', 'Employees::update_contract_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-allowance', 'Employees::add_allowance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/allowance-list/(:any)', 'Employees::allowances_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/dialog-user-data', 'Employees::dialog_user_data', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-allowance', 'Employees::update_allowance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-commissions', 'Employees::add_commissions', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/commissions-list/(:any)', 'Employees::commissions_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-commission', 'Employees::update_commission', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-statutory', 'Employees::add_statutory', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/statutory-list/(:any)', 'Employees::statutory_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-statutory', 'Employees::update_statutory', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-otherpayment', 'Employees::add_otherpayment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/other-payments-list/(:any)', 'Employees::other_payments_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-other-payments', 'Employees::update_other_payments', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->post('erp/update-basic-info', 'Employees::update_basic_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-bio', 'Employees::update_bio', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-social', 'Employees::update_social', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-bankinfo', 'Employees::update_bankinfo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-contact-info', 'Employees::update_contact_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-profile-photo', 'Employees::update_profile_photo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-account-info', 'Employees::update_account_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-document', 'Employees::add_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/user-documents-list/(:any)', 'Employees::user_documents_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-password', 'Employees::update_password', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-lists/(:any)', 'Agenda::leave_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/expense-lists/(:any)', 'Agenda::expense_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/loan-list/(:any)', 'Agenda::loan_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/travel-list/(:any)', 'Agenda::travel_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/advance-salary-list/(:any)', 'Agenda::advance_salary_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/overtime-request-list/(:any)', 'Agenda::overtime_request_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/awards-list/(:any)', 'Agenda::awards_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/projects-record-list/(:any)', 'Agenda::projects_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tasks-record-list/(:any)', 'Agenda::tasks_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payslip-history-list/(:any)', 'Agenda::payslip_history_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->group('erp', ['filter' => 'checklogin', 'namespace' => 'App\Controllers\Erp'], function ($routes) {
	$routes->delete('delete_all_allowances', 'Employees::delete_all_allowances');
	$routes->delete('delete_all_commissions', 'Employees::delete_all_commissions');
	$routes->delete('delete_all_statutory_deductions', 'Employees::delete_all_statutory_deductions');
	$routes->delete('delete_all_other_payments', 'Employees::delete_all_other_payments');
	$routes->delete('delete_document', 'Employees::delete_document');
});


//client management
//custom lead config
$routes->get('erp/customization-lead', 'Lead_config::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/save-lead', 'Lead_config::saveLead', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/create-dynamic-table', 'Lead_config::create_dynamic_table', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('lead/getDetails/(:any)', 'Lead_config::getDetails/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('lead/delete-field/(:num)', 'Lead_config::delete_field/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/lead-update-status/(:any)/(:any)', 'Lead_config::updateStatus/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/Lead-config-updatelead/(:num)', 'Lead_config::updateLead/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('api/erp/customization-lead-field/(:segment)', 'Lead_config::fetchGlobalLeadFields/$1', ['namespace' => 'App\Controllers\Erp']);
$routes->get('api/erp/customization-lead-field', 'Lead_config::fetchGlobalLeadFields', ['namespace' => 'App\Controllers\Erp']);

$routes->get('erp/opportunity-list', 'Opportunity::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/opportunity-save', 'Opportunity::save', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('opportunity/update-status/(:any)/(:any)', 'Opportunity::updateStatus/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('Opportunity/getdata/(:any)', 'Opportunity::getData/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/opportunity-update/(:any)', 'Opportunity::update/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/opportunity-delete/(:any)', 'Opportunity::delete/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);

// leads
$routes->get('erp/leads-list/', 'Clients::leads_index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/clients-insert-lead/', 'Clients::insertLead', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/web-leads-list/', 'Clients::web_leads_index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/view-lead-info/(:any)', 'Clients::lead_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-lead', 'Clients::update_lead', ['namespace' => 'App\Controllers\Erp']);
$routes->post('erp/save_client-account-details', 'Clients::save_accountDetails', ['namespace' => 'App\Controllers\Erp']);
$routes->get('erp/account-view-details/(:any)', 'Clients::account_view_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-client-account/(:any)', 'Clients::update_account/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-client-account-record/(:any)', 'Clients::delete_accountRecord/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-client-followup/', 'Clients::add_followup', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-follow-up/(:any)', 'Clients::update_followup/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/follow-up-view/(:any)', 'Clients::follow_up_view/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-follow/(:any)', 'Clients::delete_follow/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/clients-read-lead', 'Clients::read_lead', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/convert-lead', 'Clients::convert_lead', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-leads/(:any)', 'Clients::delete_leads/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/filter-leads', 'Clients::filter_leads', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-bulk-lead', 'Clients::add_bulk_lead', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-client', 'Clients::delete_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->post('api/insert-lead', 'Clients::insertLeadApi', ['namespace' => 'App\Controllers\Erp']);
// Items
$routes->get('erp/milestones-list', 'Milestones::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/milestones-update/(:num)', 'Milestones::update/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/edit-timelogs/(:segment)', 'Timelogs::edit_timelogs', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/timelogs-save', 'Timelogs::save', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/timelogs-delete/(:num)', 'Timelogs::delete/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/timelogs-update/(:num)', 'Timelogs::update/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


// 
$routes->get('erp/view-performances/(:any)', 'Talent::view_performance/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// Finance
$routes->get('erp/tax-verification', 'finance::tax_verification', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/tax-declaration/(:any)', 'finance::tax_declaration/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/tax_updateItem', 'finance::tax_updateItem', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/save-declaration', 'finance::save_declaration', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-alltaxProof/(:any)', 'finance::delete_alltaxProof/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/tax-statusUpdate', 'finance::tax_statusUpdate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/generate-Pdf/(:any)', 'finance::generatePdf/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/form16-partB/(:any)', 'finance::form16_partB/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/get-investmentname', 'finance::getInvestmentname', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/investment-type', 'finance::investment_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->post('erp/insert-investment', 'Finance::insert_investment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/update-status/(:num)/(:num)', 'Finance::updateStatus/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-investment/(:num)', 'Finance::delete_investment/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/get-data/(:num)', 'Finance::getData/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-investment/(:num)', 'Finance::update_investment/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/apply-declaration', 'finance::investment_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tax-update-status', 'finance::tax_statusUpdate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// global api

$routes->get('api/get-form', 'FormController::get_form', ['namespace' => 'App\Controllers']);
$routes->post('api/submit-form', 'FormController::submit_form', ['namespace' => 'App\Controllers']);


$routes->get('erp/web-leads', 'Dashboard::web_leads', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/web-lead-detail/(:segment)', 'Dashboard::web_lead_detail', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


//Super User Modules
//Companies
$routes->get('erp/companies-list', 'Company::companies_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-company', 'Company::add_company', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/companies-grid', 'Company::companies_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/company-detail/(:segment)', 'Company::company_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-company', 'Company::update_company', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-company', 'Company::delete_company', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/company-monthly-planning/(:segment)', 'Company::monthly_planning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/company-month-plan-chart', 'Invoices::company_month_plan_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'company/update-status/(:any)/(:any)', 'company::updateStatus/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//Subscriptions
$routes->get('erp/subscriptions', 'SubscriptionController::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/subscription-detail/(:segment)', 'SubscriptionController::subscription_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//4: Languages
$routes->get('erp/all-languages/', 'Languages::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/languages-datalist', 'Languages::languages_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/languages/add_language/', 'Languages::add_language', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->delete('erp/delete-language/(:any)', 'Languages::delete_language/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/language-status/(:any)/(:any)', 'Languages::language_status/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
//7: System||Settings|| STD
$routes->get('erp/currency-converter/', 'Settings::currency_converter', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
//STD
$routes->get('erp/system-settings/', 'Settings::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/planning-configuration', 'Settings::planning_configuration', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-planning-configuration', 'Settings::delete_planning_configuration', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/settings/save_category', 'Settings::save_category', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/settings/update_status/(:any)/(:any)', 'Settings::update_status/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/settings/edit_category/(:any)', 'Settings::save_category/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/settings/delete_category/(:any)', 'Settings::delete_category/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/settings/save_taxduration', 'Settings::save_taxduration', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/settings/system_info/', 'Settings::system_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/add_logo/', 'Settings::add_logo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/add_favicon/', 'Settings::add_favicon', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/add_other_logo', 'Settings::add_other_logo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/add_singin_logo/', 'Settings::add_singin_logo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/update_payment_gateway/', 'Settings::update_payment_gateway', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/notification_position_info/', 'Settings::notification_position_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/email_info/', 'Settings::email_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
//8: System||Constants
$routes->get('erp/system-constants/', 'Settings::constants', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/religion-data-list', 'Settings::religion_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/currency-type-list', 'Settings::currency_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/company-type-datalist', 'Settings::company_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/payment-method-list', 'Settings::payment_method_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/settings/company_type_info/', 'Settings::company_type_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/settings/update_company_type', 'Settings::update_company_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->delete('erp/settings/delete_company_type/', 'Settings::delete_company_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/currency_type_info/', 'Settings::currency_type_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/update_currency_type/', 'Settings::update_currency_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/delete_currency_type/', 'Settings::delete_currency_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/constants-read', 'Settings::constants_read', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/add-religion-info', 'Settings::add_religion_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->post('erp/update-religion', 'Settings::update_religion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->delete('erp/settings/delete_religion', 'Settings::delete_religion', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);


//9: System||Database Backup
$routes->get('erp/system-backup/', 'Settings::database_backup', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/create_database_backup/', 'Settings::create_database_backup', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/delete_db_backup/', 'Settings::delete_db_backup', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->match(['get', 'post'], 'erp/settings/delete_dbsingle_backup/', 'Settings::delete_dbsingle_backup', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
//10: System||Email Templates
$routes->get('erp/email-templates/', 'Settings::email_templates', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/email-template-list', 'Settings::email_template_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);
$routes->get('erp/read-emailTempalte', 'Settings::read_tempalte', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);

$routes->match(['get', 'post'], 'erp/settings/update_template/', 'Settings::update_template', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin',]);


/***************************************************************************************************************/
/***************************************************************************************************************/
/***************************************************************************************************************/

/////Company|Staff Modules
//1: Staff Roles

$routes->get('erp/set-roles/', 'Roles::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/staff-roles-list', 'Roles::staff_roles_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->post('erp/add-role', 'Roles::add_role', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/read-role', 'Roles::read_role', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->post('erp/update-role', 'Roles::update_role', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->delete('erp/delete-role/(:any)', 'Roles::delete_role/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);

//2: Assets
$routes->get('erp/assets-list/', 'Assets::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/assets-Datalist', 'assets::assets_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->post('erp/add-asset', 'Assets::add_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/asset-view/(:segment)', 'Assets::asset_view', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->post('erp/update-asset', 'Assets::update_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->delete('erp/delete-asset', 'Assets::delete_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->match(['get', 'post'], 'erp/assets/assets_list/', 'Assets::assets_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->match(['get', 'post'], 'erp/assets/read_asset/', 'Assets::read_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
// $routes->match(['get', 'post'], 'erp/assets/add_asset/', 'Assets::add_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->match(['get', 'post'], 'erp/assets/update_asset/', 'Assets::update_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->delete('erp/assets/delete_asset/', 'Assets::delete_asset', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);

// module types
$routes->get('erp/assets-category/', 'Types::asset_category', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/assets-category-list', 'types::assets_category_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-category', 'types::add_category', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-asset-category', 'types::read_asset_category', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/assets-brand/', 'Types::asset_brand', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin', 'filter' => 'companyauth']);
$routes->get('erp/assets-brand-list', 'types::assets_brand_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-brand', 'types::add_brand', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-asset-brand', 'types::read_asset_brand', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/leave-type/', 'Types::leave_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-type-dataList', 'types::leave_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-leave-type', 'types::add_leave_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-leave-type', 'types::read_leave_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-leave-type', 'types::update_leave_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);



$routes->get('erp/award-type/', 'Types::award_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/award-type-Datalist', 'Types::award_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-award-type', 'Types::add_award_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-award-type', 'Types::read_award_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/arrangement-type/', 'Types::arrangement_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/arrangement-type-dataList', 'types::arrangement_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-arrangement-type', 'types::add_arrangement_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-arrangement-type', 'types::read_arrangement_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/exit-type/', 'Types::exit_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/exit-type-list', 'Types::exit_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-exit-type', 'Types::add_exit_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-exit-type', 'Types::read_exit_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// Income 
$routes->get('erp/income-type/', 'Types::income_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/income-type-list', 'types::income_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-income-type', 'types::add_income_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-income-type', 'types::read_income_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/expense-type/', 'Types::expense_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/expenseType_datalist', 'types::expense_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-expense-type', 'types::add_expense_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-expense-type', 'types::read_expense_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/competencies/', 'Types::competencies', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/competencies-Datalist', 'Types::competencies_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-competencies', 'Types::add_competencies', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-competencies', 'Types::read_competencies', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/goal-type/', 'Types::goal_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/goal-type-list', 'Types::goal_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-goal-type', 'Types::add_goal_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-goal-type', 'Types::read_goal_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/training-skills/', 'Types::training_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/training-type-list/', 'Types::training_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-training-type/', 'Types::add_training_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-training-type/', 'Types::read_training_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-constants-type', 'Types::update_constants_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/case-type/', 'Types::case_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tax-type/', 'Types::tax_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tax-type-list', 'Types::tax_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-tax-type', 'Types::add_tax_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-tax-type', 'Types::read_tax_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-tax-type', 'Types::update_tax_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-type/(:any)', 'Types::delete_type/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobs-categories/', 'Types::jobs_categories', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/customers-group/', 'Types::customers_group', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/apply_performance/', 'Types::apply_performance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// core hr dashboard
$routes->get('erp/corehr-dashboard/', 'Department::corehr_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// department
$routes->get('erp/departments-list/', 'Department::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/departments-data-list', 'Department::departments_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add_department', 'Department::add_department', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read_department', 'Department::read_department', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update_department', 'Department::update_department', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete_department', 'Department::delete_department', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// designation
$routes->get('erp/designation-list/', 'Designation::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/designation_data_list', 'Designation::designation_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-designation', 'designation::add_designation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-designation', 'designation::delete_designation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-designation', 'designation::read_designation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update_designation', 'designation::update_designation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// announcements
$routes->get('erp/news-list/', 'Announcements::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/announcement-view/(:any)', 'Announcements::announcement_view/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/announcement-data-list', 'announcements::announcement_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-announcements', 'announcements::add_announcement', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-announcements', 'announcements::delete_announcement', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-announcements', 'announcements::read_announcement', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-announcements', 'announcements::update_announcement', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// policies
$routes->get('erp/policies-list/', 'Policies::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/all-policies/', 'Policies::staff_policies_all', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/policies-data-list/', 'policies::policies_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-policy', 'policies::add_policy', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-policy', 'policies::delete_policy', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-policy', 'policies::read_policy', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-policy', 'policies::update_policy', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//hire-expert
$routes->get('erp/hire-experts/', 'HireExpert::hire_expert_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/expert-details/(:num)', 'HireExpert::expert_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/expert-apply/(:num)', 'HireExpert::expert_apply/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/applied-experts', 'HireExpert::applied_experts', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/applied-expert-data/(:num)', 'HireExpert::applied_expert_data/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


// awards
$routes->get('erp/awards-list/', 'Awards::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/awards-dataList', 'Awards::awards_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/award-view/(:segment)', 'Awards::award_view', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-awards', 'Awards::add_awards', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-award', 'Awards::delete_award', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-award', 'Awards::update_award', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// travel
$routes->get('erp/business-travel/', 'Travel::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/travel-dataList', 'Travel::travel_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-travel', 'travel::add_travel', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-travel', 'travel::delete_travel', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-travel', 'travel::read_travel', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-travel', 'travel::update_travel', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-travel-status', 'travel::update_travel_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/travel-calendar/', 'Travel::travel_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/view-travel-info/(:segment)', 'Travel::travel_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// complaints
$routes->get('erp/complaints-list/', 'Complaints::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/complaints-datalist/', 'Complaints::complaints_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-complaint', 'Complaints::add_complaint', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-complaints/', 'Complaints::read_complaints', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-complaint', 'Complaints::update_complaint', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-complaint', 'Complaints::delete_complaint', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// resignation
$routes->get('erp/resignation-list/', 'Resignation::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/resignation-datalist/', 'Resignation::resignation_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-resignation/', 'Resignation::add_resignation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-resignation/', 'Resignation::read_resignation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-resignation/', 'Resignation::update_resignation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-resignation', 'Resignation::delete_resignation', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// transfer
$routes->get('erp/transfers-list/', 'Transfers::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/transfers-dataList', 'transfers::transfers_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-transfer', 'transfers::add_transfer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/is-department/(:num)', 'transfers::is_department/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/is-designation/(:num)', 'transfers::is_designation/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-transfer', 'transfers::delete_transfer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-transfer', 'transfers::read_transfer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-transfer', 'transfers::update_transfer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// employee exit
$routes->get('erp/employee-exit/', 'Leaving::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/employee-off-list', 'Leaving::employee_off_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-exit', 'Leaving::add_exit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-employee-exit', 'Leaving::read_employee_exit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-exit', 'Leaving::update_exit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-employee-exit', 'Leaving::read_employee_exit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-employee-exit', 'Leaving::delete_employee_exit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// documents || upload files, official and expired documents
$routes->get('erp/upload-files/', 'Documents::upload_files', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-document', 'documents::add_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/system-documents-list/did/(:any)', 'Documents::system_documents_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-document', 'documents::delete_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-document', 'documents::read_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-document', 'documents::update_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/official-documents/', 'Documents::official_documents', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/official-documents-dataList', 'documents::official_documents_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-official-document', 'documents::add_official_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-official-document', 'documents::delete_official_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-official-document', 'documents::read_official_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-official-document', 'documents::update_official_document', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/expired-documents/', 'Documents::expired_documents', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// warning
$routes->get('erp/disciplinary-cases/', 'Warning::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/warning-list', 'Warning::warning_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-warning', 'Warning::add_warning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-warning', 'Warning::delete_warning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-warning', 'Warning::read_warning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-warning', 'Warning::update_warning', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// tickets
$routes->get('erp/support-tickets/', 'Tickets::tickets_page', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tickets-priority-chart', 'Tickets::tickets_priority_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/tickets-status-chart', 'Tickets::tickets_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-ticket', 'Tickets::add_ticket', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/get-employeeBy-department/(:num)', 'Tickets::is_department/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/create-ticket/', 'Tickets::create_ticket', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/helpdesk-dashboard/', 'Tickets::helpdesk_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/ticket-view/(:segment)', 'Tickets::ticket_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-ticket-status', 'Tickets::update_ticket_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-ticket-reply', 'Tickets::add_ticket_reply', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/tickets-add-note', 'Tickets::add_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-ticket-note', 'Tickets::delete_ticket_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/tickets-add-attachment', 'Tickets::add_attachment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-ticket-file', 'Tickets::delete_ticket_file', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-ticket', 'Tickets::delete_ticket', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-ticket', 'Tickets::read_ticket', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-ticket', 'Tickets::update_ticket', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-ticket-reply', 'Tickets::delete_ticket_reply', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// training
$routes->get('erp/training-sessions/', 'Training::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/training-list/', 'Training::training_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-training', 'Training::add_training', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-training/', 'Training::read_training', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-training', 'Training::update_training', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/training-details/(:any)', 'Training::training_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-training-status', 'Training::update_training_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/training-add-note', 'Training::add_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-training-note', 'Training::delete_training_note', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/training-calendar/', 'Training::training_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-training', 'Training::delete_training', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// trainers
$routes->get('erp/trainers-list/', 'Trainers::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/trainer-list/', 'Trainers::trainer_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-trainer/', 'Trainers::add_trainer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-trainer/', 'Trainers::read_trainer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-trainer/', 'Trainers::update_trainer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-trainer/', 'Trainers::delete_trainer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// events
$routes->get('erp/events-list/', 'Events::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/events-dataList', 'Events::events_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-event', 'Events::add_event', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-event-record', 'Events::read_event_record', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-event', 'Events::delete_event', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-event', 'Events::update_event', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/events-calendar/', 'Events::events_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// meetings
$routes->get('erp/meeting-list/', 'Conference::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/meetings-calendar/', 'Conference::meetings_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/meetings-data-list', 'conference::meetings_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-meeting', 'conference::add_meeting', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-meeting', 'conference::delete_meeting', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-meeting-record', 'conference::read_meeting_record', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-meeting', 'conference::update_meeting', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// leave
$routes->get('erp/leave-list/', 'Leave::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-dataList', 'leave::leave_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-status-chart', 'leave::leave_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-type-chart', 'leave::leave_type_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-leave', 'leave::add_leave', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-leave', 'leave::delete_leave', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-leave-status', 'leave::update_leave_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-leave', 'leave::read_leave', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-leave', 'leave::update_leave', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/leave-status/', 'Leave::leave_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/view-leave-info/(:segment)', 'Leave::view_leave', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-calendar/', 'Leave::leave_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// holidays
$routes->get('erp/holidays-list/', 'Holidays::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/holidays-dataList', 'Holidays::holidays_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-holiday', 'holidays::add_holiday', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-holiday', 'holidays::delete_holiday', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-holiday', 'holidays::read_holiday', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-holiday', 'holidays::update_holiday', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/holidays-calendar/', 'Holidays::holidays_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// officeshifts
$routes->get('erp/office-shifts/', 'Officeshifts::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/office-shifts-list', 'Officeshifts::office_shifts_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-office-shift', 'Officeshifts::add_office_shift', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-shift', 'Officeshifts::read_shift', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-office-shift', 'Officeshifts::update_office_shift', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-office-shift', 'Officeshifts::delete_office_shift', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

//mom admin
$routes->get('erp/moms-list/', 'Moms::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/moms-calendar/', 'Moms::moms_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/moms-grid/', 'Moms::moms_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-mom', 'Moms::add_mom', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/mom-detail/(:any)', 'Moms::mom_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-mom', 'Moms::update_mom', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/moms-delete', 'Moms::moms_delete', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// user mom
$routes->post('erp/user/add-mom', 'Moms::add_mom', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


// tasks||Clients
$routes->get('erp/my-tasks-list/', 'Tasks::task_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-tasks-list/', 'Tasks::client_tasks_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/task-details/(:segment)', 'Tasks::client_task_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// clients
$routes->get('erp/clients-list/', 'Clients::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/clients-datalist/', 'Clients::clients_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-client', 'Clients::add_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/clients-grid/', 'Clients::clients_grid', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/view-client-info/(:any)', 'Clients::client_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/clients-overview', 'Clients::overview', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-client', 'Clients::update_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-profile-photo', 'Clients::update_profile_photo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/client-project-status-chart', 'Projects::client_project_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-profile-projects-list/(:num)', 'Projects::client_profile_projects_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-profile-tasks-list/(:num)', 'Tasks::client_profile_tasks_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-profile-invoices-list/(:num)', 'Invoices::client_profile_invoices_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-invoice-amount-chart', 'Invoices::client_invoice_amount_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// performance
$routes->get('erp/performance-indicator-list', 'Talent::performance_indicator', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-indicator', 'Talent::add_indicator', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-indicator', 'Talent::update_indicator', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/kpi-details/(:segment)', 'Talent::indicator_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/performance-appraisal-list', 'Talent::performance_appraisal', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/kpa-details/(:segment)', 'Talent::appraisal_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/track-goals', 'Trackgoals::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/goals-datalist', 'Trackgoals::goals_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-tracking', 'Trackgoals::add_tracking', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-goal', 'Trackgoals::delete_goal', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/goal-details/(:segment)', 'Trackgoals::goal_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/goals-calendar/', 'Trackgoals::goals_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// filter 
$routes->get('erp/filter-performance', 'Talent::filter_performance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// visitors
$routes->get('erp/visitors-list/', 'Visitors::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/visitors-data-list/', 'Visitors::visitors_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// todo
$routes->get('erp/todo-list/', 'Todo::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-todo', 'Todo::add_todo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-todo', 'Todo::delete_todo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// organization chart
$routes->get('erp/chart/', 'Application::org_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// subscription
$routes->get('erp/my-subscription/', 'Subscription::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/subscription-expired/', 'Subscription::subscription_expired', ['namespace' => 'App\Controllers\Erp']);
$routes->get('erp/upgrade-subscription/(:segment)', 'Subscription::upgrade_subscription', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/subscription-list/', 'Subscription::more_subscriptions', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// payment history
$routes->get('erp/my-payment-history/', 'Paymenthistory::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payment-details/(:segment)', 'Paymenthistory::billing_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// contact support
$routes->get('erp/contact-support/', 'Contact::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// payroll
$routes->get('erp/payroll-list/', 'Payroll::index', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payroll/payslip_list/(:any)/(:any)', 'Payroll::payslip_list/$1/$2', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payroll-view/(:segment)', 'Payroll::payroll_view', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-payroll', 'Payroll::read_payroll', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/delete-payslip/(:any)', 'Payroll::delete_payslip', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-pay-monthly', 'Payroll::add_pay_monthly', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/payslip-history/', 'Payroll::payroll_history', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payslip-history-dataList', 'payroll::payslip_history_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/advance-salary/', 'Payroll::advance_salary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/advance-salary-data-list', 'payroll::advance_salary_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-advance-salary', 'payroll::add_advance_salary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-advance-salary', 'payroll::read_advance_salary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/edit-advance-salary', 'payroll::edit_advance_salary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-advance-salary', 'payroll::delete_advance_salary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/loan-request/', 'Payroll::request_loan', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/loan_dataList', 'payroll::loan_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-loan', 'payroll::add_loan', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-loan', 'payroll::read_loan', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/edit-loan', 'payroll::edit_loan', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-loan', 'payroll::delete_loan', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// invoices || Staff
$routes->get('erp/invoices-list', 'Invoices::project_invoices', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-status-chart', 'Invoices::invoice_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-dashboard/', 'Invoices::invoice_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-payments-list/', 'Invoices::project_invoice_payment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/create-invoice/', 'Invoices::create_invoice', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-invoice/(:any)',  'Invoices::delete_invoice/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-calendar/', 'Invoices::invoice_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/edit-invoice/(:any)', 'Invoices::edit_invoice/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/print-invoice/(:any)', 'Invoices::view_project_invoice/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/invoices/getProjectsByClient', 'Invoices::getProjectsByClient', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/invoices/getProjectsByExpert', 'Invoices::getProjectsByExpert', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/invoices/getTasksByProject', 'Invoices::getTasksByProject', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/project-billing-list/', 'Invoices::project_billing_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// estimates || Staff
$routes->get('erp/estimates-list/', 'Estimates::project_estimates', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/create-new-estimate/', 'Estimates::create_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/save-estimate', 'Estimates::create_new_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-estimate', 'Estimates::delete_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/estimates-calendar/', 'Estimates::estimates_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/estimate-detail/(:any)', 'Estimates::estimate_details/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/edit-estimate/(:segment)', 'Estimates::edit_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-estimate', 'Estimates::update_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/print-estimate/(:segment)', 'Estimates::view_project_estimate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/estimate-to-invoice', 'Estimates::read_estimate_data', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// invoices || Clients
$routes->get('erp/my-invoices-list/', 'Invoices::invoices_client', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-invoices-list/', 'Invoices::client_invoices_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/my-invoice-payments-list/', 'Invoices::client_invoice_payment', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/client-project-billing-list/', 'Invoices::client_project_billing_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/my-invoices-calendar/', 'Invoices::client_invoice_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// staff attendance
$routes->get('erp/timesheet-dashboard/', 'Timesheet::timesheet_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/attendance-list/', 'Timesheet::attendance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/get-attendanceList', 'Timesheet::attendance_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/attendance-info/(:segment)/(:segment)', 'Timesheet::attendance_view', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/manual-attendance/', 'Timesheet::update_attendance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/attendance-datalist', 'Timesheet::update_attendance_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/update-attendance-list', 'Timesheet::update_attendance_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-attendance', 'Timesheet::delete_attendance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/update-attendance-add', 'Timesheet::update_attendance_add', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-attendance', 'Timesheet::add_attendance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-attendance-record', 'Timesheet::update_attendance_record', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/monthly-attendance-view/', 'Timesheet::monthly_timesheet', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/monthly-attendance/', 'Timesheet::monthly_timesheet_filter', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/timesheet-calendar/', 'Timesheet::timesheet_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// overtime request
$routes->get('erp/overtime-request/', 'Timesheet::overtime_request', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/overtime-request-datalist', 'Timesheet::overtime_request_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-overtime-request', 'timesheet::read_overtime_request', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-overtime', 'timesheet::add_overtime', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-overtime-record', 'timesheet::update_overtime_record', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-overtime', 'timesheet::delete_overtime', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


// Finance
$routes->get('erp/finance-dashboard/', 'Finance::finance_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payees-list/', 'Finance::payees', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payers-list/', 'Finance::payers', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/accounts-list/', 'Finance::bank_cash', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/accounts-datalist', 'finance::accounts_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-account', 'finance::add_account', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/account-ledger/(:segment)', 'Finance::account_ledger', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-accounts', 'finance::read_accounts', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-account', 'finance::delete_account', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-account', 'finance::update_account', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

// Finance
$routes->get('erp/deposit-list/', 'Finance::deposit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/deposit-dataList', 'Finance::deposit_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-deposit', 'Finance::add_deposit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-transaction', 'finance::delete_transaction', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-transactions', 'finance::read_transactions', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-deposit', 'finance::update_deposit', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/expense-list/', 'Finance::expense', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/expense_dataList', 'finance::expense_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-expense', 'finance::add_expense', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-expense', 'finance::update_expense', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/transfer-list/', 'Finance::transfer', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/transactions-list/', 'Finance::transactions', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/transaction-dataList', 'finance::transaction_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/transaction-details/(:segment)', 'Finance::transaction_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// HR System
$routes->get('erp/system-reports/', 'Application::reports', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/attendance-report/', 'Reports::attendance_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/payroll-report/', 'Reports::payroll_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/project-report/', 'Reports::project_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/task-report/', 'Reports::task_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/invoice-report/', 'Reports::invoice_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/leave-report/', 'Reports::leave_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/training-report/', 'Reports::training_report', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/account-statement/', 'Reports::account_statement', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/system-import/', 'Application::import', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/system-calendar/', 'Application::erp_calendar', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/company-settings/', 'Application::company_settings', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/company-constants/', 'Application::company_constants', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// recruitment
$routes->get('erp/jobs-dashboard/', 'Recruitment::recruitment_dashboard', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobs-list/', 'Recruitment::jobs', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/view-job/(:segment)', 'Recruitment::job_details', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/apply-job', 'Recruitment::apply_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->get('erp/candidates-list/', 'Recruitment::candidates', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/candidates-DataList', 'Recruitment::candidates_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-candidate', 'Recruitment::read_candidate', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-candidate-status', 'Recruitment::update_candidate_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-interview-status', 'Recruitment::update_interview_status', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/jobs-interviews/', 'Recruitment::interviews', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/interview-Datalist', 'Recruitment::interview_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/rejected-list', 'Recruitment::rejected', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/reject-candidates-list', 'Recruitment::reject_candidates_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/promotion-list/', 'Recruitment::promotions', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/promotion-dataList', 'Recruitment::promotion_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/create-new-job/', 'Recruitment::create_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-job', 'Recruitment::add_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/edit-a-job/(:segment)', 'Recruitment::edit_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-job', 'Recruitment::update_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->delete('erp/delete-job', 'Recruitment::delete_job', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobs-data-list', 'Recruitment::jobs_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobs-status-chart', 'Recruitment::jobs_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobs-type-chart', 'Recruitment::jobs_type_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/jobBy-designation-chart', 'Recruitment::job_by_designation_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->post('erp/save-summary', 'UserDetails::save_summary', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->match(['get', 'post'], 'erp/department-wise-chart', 'Department::department_wise_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->match(['get', 'post'], 'erp/designation-wise-chart', 'designation::designation_wise_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/payroll-chart', 'payroll::payroll_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/staff-working-statusChart', 'timesheet::staff_working_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/planing-monthly-chart', 'invoices::planing_monthly_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/invoice-amount-chart', 'invoices::invoice_amount_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/task-status-chart', 'tasks::task_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/project-status-chart', 'projects::project_status_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->match(['get', 'post'], 'erp/month-plan-chart', 'Invoices::month_plan_chart', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


$routes->post('erp/set-entityid-session', 'projects::set_entity_id_session', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
// $routes->post('erp/Milestones/update/(:any)', 'Milestones::update/$1');
$routes->get('erp/employee-list', 'employees::employees_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-employee', 'employees::add_employee', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/get-designation/(:num)', 'employees::is_designation/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->delete('erp/delete-staff/(:any)', 'employees::delete_staff/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->post('erp/system-info', 'profile::system_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-profile', 'profile::update_profile', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-profile-photo', 'profile::update_profile_photo', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-company-info', 'profile::update_company_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-password', 'profile::update_password', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-contract-info', 'employees::update_contract_info', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/allowances-list/(:num)', 'employees::allowances_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-allowance', 'employees::add_allowance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);



// $routes->get('erp/dialog-user-data', 'employees::dialog_user_data', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/update-allowance', 'employees::update_allowance', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/commissions-list/(:num)', 'employees::commissions_list/$1', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);

$routes->get('erp/caseType-data-list', 'types::case_type_list', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->post('erp/add-case-type', 'types::add_case_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('erp/read-case-type', 'types::read_case_type', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);
$routes->get('start-pattern', 'Employees::print_pattern', ['namespace' => 'App\Controllers\Erp', 'filter' => 'checklogin']);


/***************************************************************************************************************/
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
