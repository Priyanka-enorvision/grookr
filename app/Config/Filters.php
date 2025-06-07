<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
	// Makes reading things below nicer,
	// and simpler to change out script that's used.
	public $aliases = [
		'csrf'     => \CodeIgniter\Filters\CSRF::class,
		'toolbar'  => \CodeIgniter\Filters\DebugToolbar::class,
		'honeypot' => \CodeIgniter\Filters\Honeypot::class,
		'checklogin' => \App\Filters\CheckLogin::class,
		'noauth' => \App\Filters\Noauth::class,
		'superauth' => \App\Filters\SuperAuth::class,
		'companyauth' => \App\Filters\CompanyAuth::class,
	];

	// Always applied before every request
	public $globals = [
		'before' => [
			'honeypot',
			'csrf' => ['except' => [
				'erp/monthly_planning_submit',
				'api/submit-form',
				'erp/invoices/getProjectsByClient',
				'erp/invoices/getProjectsByExpert',
				'erp/invoices/getTasksByProject',
				'erp/invoices/update_invoice',
				'Erp/Opportunity/save',
				'Erp/Lead_config/create_dynamic_table',
				'Erp/Clients/insertLead',
				'erp/clients/save_accountDetails',
				'erp/clients/update_followup/',
				'erp/Clients/filter_leads',
				'erp/clients/set_opportunity_session',
				'erp/projects/add_project',
				'erp/Milestones/save',
				'erp/Milestones/delete',
				'erp/projects/copy_project',
				'erp/Timelogs/save',
				'erp/Timelogs/getdata/',
				'erp/invoices/create_new_invoice',
				'erp/clients/add_followup',
				'erp/projects/update_project',
				'erp/tasks/add_task',
				'erp/settings/save_duration',
				'erp/types/add_competencies',
				'erp/Talent/filter_performance',
				'erp/projects/add_discussion',
				'erp/settings/add_other_logo',
				'erp/documents/save_document',
				'erp/settings/save_category',
				'erp/settings/updateStatus/',
				'erp/documents/documentfiles_updates',
				'erp/Finance/save_declaration',
				'erp/Finance/insert_investment',
				'investment/update-status/',
				'erp/Finance/update_investment/',
				'erp/finance/getInvestmentname',
				's3-images/upload',
				's3-images/update/*',
				'erp/finance/tax_statusUpdate',
				'erp/finance/tax_updateItem',
				'erp/projects/set_entity_id_session',
				'erp/projects/get_employe',
				'erp/profile/system_info',
				'invoices/month_plan_chart',
				'erp/profile/update_profile_photo',
				'erp/month-plan-chart',
				'erp/add-employee',
				'get-designation/',
				'erp/update-profile-photo',
				'erp/insert-investment',
				'erp/update-status/',
				'erp/milestones-save',
				'erp/milestones-update/',
				'erp/add-projectTask',
				'erp/update-projectTask',
				'erp/timelogs-save',
				'erp/timelogs-delete',
				'erp/timelogs-update/',
				'delete-project-file',
				'erp/save-document',
				'erp/documentfiles-updates',
				'erp/save-declaration',
				'erp/save-lead',
				'erp/create-dynamic-table'
			]],
		],
		'after'  => [
			'toolbar',
			'honeypot'
		],
	];

	// Works on all of a particular HTTP method
	// (GET, POST, etc) as BEFORE filters only
	//     like: 'post' => ['CSRF', 'throttle'],
	public $methods = [
		//'post' => ['honeypot'],
	];

	// List filter aliases and any before/after uri patterns
	// that they should run on, like:
	//    'isLoggedIn' => ['before' => ['account/*', 'profiles/*']],
	public $filters = [
		//'erpauth' => ['before' => ['dashboard/*']],
	];
}
