<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    protected $helpers = [
        'form', 'html', 'inflector', 'number', 'security',
        'text', 'url', 'string', 'main', 'filesystem', 'encrypt', 'timehr'
    ];

    protected $session;
    protected $language;
    protected $UsersModel;
    protected $SystemModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session  = \Config\Services::session();
        $this->language = \Config\Services::language();

        $this->UsersModel  = new \App\Models\UsersModel();
        $this->SystemModel = new \App\Models\SystemModel();

        $defaultSystem = $this->SystemModel->where('setting_id', 1)->first();

        if ($this->session->has('sup_username')) {
            $usession = $this->session->get('sup_username');

            if (!empty($usession['sup_user_id'])) {
                $user_info = $this->UsersModel->find($usession['sup_user_id']);
				

                if (!empty($user_info)) {
                    
                    if ($user_info['user_type'] === 'super_user') {
                        $this->setSystemConfig($defaultSystem);
                    } else {
                        $companySystem = erp_company_settings();
                        $this->setSystemConfig($companySystem);
                    }
                }
            }
        } else {
            
            $this->setSystemConfig($defaultSystem);
        }

        if ($this->session->has('lang')) {
            $this->language->setLocale($this->session->get('lang'));
        }
    }

    /**
     * Set system locale and timezone
     */
    protected function setSystemConfig(array $config)
    {
        if (!empty($config['default_language'])) {
            $this->language->setLocale($config['default_language']);
        }

        if (!empty($config['system_timezone'])) {
            date_default_timezone_set($config['system_timezone']);
        }
    }

    /**
     * Send JSON output
     */
    public function output(array $data = [])
    {
        // Set response headers
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setJSON($data);
    }
}
