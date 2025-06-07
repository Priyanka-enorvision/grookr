<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\SubscriptionModel;
use App\Models\SystemModel;
use App\Models\UsersModel;

class SubscriptionController extends BaseController
{
    protected $subscriptionModel;

    public function __construct()
    {
        $this->subscriptionModel = new SubscriptionModel();
    }

    public function index()
    {

        $SystemModel = new SystemModel();
        $UsersModel = new UsersModel();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');
        $xin_system = $SystemModel->where('setting_id', 1)->first();
        $data['title'] = 'Subscription' . ' | ' . $xin_system['application_name'];
        $data['path_url'] = 'subscription';
        $data['breadcrumbs'] = 'Subscription';
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        $data['subscriptions'] = $this->subscriptionModel->findAll();
        $data['subview'] = view('subscriptions/index', $data);
        return view('erp/layout/layout_main', $data);
    }

    public function is_company_email()
    {

        $session = \Config\Services::session();
        $request = \Config\Services::request();
        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('/'));
        }
        $id = $request->uri->getSegment(4);

        $data = array(
            'company_id' => $id
        );
        if ($session->has('sup_username')) {
            return view('subscriptions/get_email', $data);
        } else {
            return redirect()->to(site_url('erp/login'));
        }
    }

    public function add_subscription()
    {
        $session = \Config\Services::session();
        $request = service('request');
        $validation = \Config\Services::validation();

        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $validation->setRules(
            [
                'company_id' => 'required',
                'email' => 'required|valid_email',
                'plan' => 'required|in_list[basic,standard,premium]',
                'start_date' => 'required|valid_date',
                'end_date' => 'required|valid_date',
                'payment_status' => 'required|in_list[paid,pending]',
            ],
            [
                'company_id' => [
                    'required' => 'The company is required.',
                ],
                'email' => [
                    'required' => 'Email is required.',
                    'valid_email' => 'Please provide a valid email address.',
                ],
                'plan' => [
                    'required' => 'A subscription plan must be selected.',
                    'in_list' => 'Invalid subscription plan.',
                ],
                'start_date' => [
                    'required' => 'Start date is required.',
                    'valid_date' => 'Start date must be a valid date.',
                ],
                'end_date' => [
                    'required' => 'End date is required.',
                    'valid_date' => 'End date must be a valid date.',
                ],
                'payment_status' => [
                    'required' => 'Payment status is required.',
                    'in_list' => 'Invalid payment status.',
                ],

            ]
        );

        $validation->withRequest($this->request)->run();
        //check error
        if ($validation->hasError('company_id')) {
            $Return['error'] = $validation->getError('company_id');
        } elseif ($validation->hasError('email')) {
            $Return['error'] = $validation->getError('email');
        } elseif ($validation->hasError('plan')) {
            $Return['error'] = $validation->getError('plan');
        } elseif ($validation->hasError('start_date')) {
            $Return['error'] = $validation->getError('start_date');
        } elseif ($validation->hasError('end_date')) {
            $Return['error'] = $validation->getError('end_date');
        } elseif ($validation->hasError('payment_status')) {
            $Return['error'] = $validation->getError('payment_status');
        }

        if ($Return['error'] != '') {
            $this->output($Return);
        }


        $start_date = $request->getPost('start_date');
        $end_date = $request->getPost('end_date');

        if ($end_date < $start_date) {
            $session->setFlashdata('error', 'End date must be greater than start date.');
            return redirect()->back()->withInput();
        }

        $data = [
            'company_id' => $request->getPost('company_id'),
            'email' => $request->getPost('email'),
            'plan' => $request->getPost('plan'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => $request->getPost('payment_status'),
            'notes' => $request->getPost('notes'),
        ];

        try {
            if ($this->subscriptionModel->insert($data)) {
                $session->setFlashdata('success', 'Subscription added successfully.');
                return redirect()->to(site_url('erp/subscriptions'));
            } else {
                $session->setFlashdata('error', 'Failed to add subscription. Please try again.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to save subscription: ' . $e->getMessage());
            $session->setFlashdata('error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back()->withInput();
        }
    }


    public function subscription_details()
    {

        $UsersModel = new UsersModel();
        $SystemModel = new SystemModel();
        $request = \Config\Services::request();
        $session = \Config\Services::session();

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }
        $xin_system = $SystemModel->where('setting_id', 1)->first();

        $data['title'] = 'subscription_details' . ' | ' . $xin_system['application_name'];
        $data['path_url'] = 'subscription';
        $data['breadcrumbs'] = 'subscription';
        $data['subview'] = view('subscriptions/subscription_detail', $data);
        return view('erp/layout/layout_main', $data); //page load
    }


    public function update_subscription()
    {


        $session = \Config\Services::session();
        $request = service('request');
        $validation = \Config\Services::validation();

        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $validation->setRules([
            'company_id' => 'required',
            'plan' => 'required|in_list[basic,standard,premium]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date',
            'payment_status' => 'required|in_list[paid,pending]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            $session->setFlashdata('error', implode(", ", $validation->getErrors()));
            return redirect()->back()->withInput();
        }

        $start_date = $request->getPost('start_date');
        $end_date = $request->getPost('end_date');

        if (strtotime($end_date) < strtotime($start_date)) {
            $session->setFlashdata('error', 'End date must be greater than or equal to the start date.');
            return redirect()->back()->withInput();
        }

        $data = [
            'company_id' => $request->getPost('company_id'),
            'plan' => $request->getPost('plan'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'payment_status' => $request->getPost('payment_status'),
            'notes' => $request->getPost('notes'),
        ];

        try {
            if ($request->getPost('id')) {
                $this->subscriptionModel->update($request->getPost('id'), $data);
                $session->setFlashdata('success', 'Subscription updated successfully.');
            } else {
                $this->subscriptionModel->insert($data);
                $session->setFlashdata('success', 'Subscription added successfully.');
            }
            return redirect()->to(site_url('erp/subscriptions'));
        } catch (\Exception $e) {
            log_message('error', 'Failed to save subscription: ' . $e->getMessage());
            $session->setFlashdata('error', 'An unexpected error occurred. Please try again later.');
            return redirect()->back()->withInput();
        }
    }


    public function delete_subscription()
    {
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        // Decode the subscription ID from the POST request
        $id = udecode($this->request->getPost('_token', FILTER_SANITIZE_STRING));

        $SubscriptionModel = new SubscriptionModel();

        // Initialize response
        $response = [
            'result' => '',
            'error' => ''
        ];

        try {
            $subscription = $SubscriptionModel->find($id);
            if (!$subscription) {
                $session->setFlashdata('error', 'Subscription Not Found');
                return redirect()->to(site_url('erp/subscriptions'));
            }

            if ($SubscriptionModel->delete($id)) {
                $session->setFlashdata('success', 'Subscription Deleted Successfully');
            } else {
                $session->setFlashdata('error', 'Error Deleting Subscription');
            }
        } catch (\Exception $e) {
            log_message('error', 'Subscription Deletion Error: ' . $e->getMessage());
            $session->setFlashdata('error', lang('Main.xin_error_occurred') . ' ' . $e->getMessage());
        }

        return redirect()->to(site_url('erp/subscriptions'));
    }


}

