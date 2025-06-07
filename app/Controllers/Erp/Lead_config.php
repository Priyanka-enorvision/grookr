<?php

namespace App\Controllers\Erp;

use App\Controllers\BaseController;

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\LeadConfigModel;
use App\Models\LeadOptions;
use App\Models\OpportunityModel;

class Lead_config extends BaseController
{
    public function __construct()
    {
        // Load language
        helper('Language');
        // $this->lang = \Config\Services::language();
        // $this->db = \Config\Database::connect();
    }


    public function index()
    {
        $session = \Config\Services::session();
        $SystemModel = new SystemModel();
        $UsersModel = new UsersModel();
        $LeadConfig = new LeadConfigModel();

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $data['title'] = "Setting / Customization Leads";
        $data['path_url'] = 'lead';
        $data['breadcrumbs'] = 'Setting / Customization Leads';
        $data['result'] = $LeadConfig->groupStart()
            ->where('company_id', $user_info['company_id'])
            ->orWhere('company_id', null)
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->findAll();

        $data['subview'] = view('erp/lead/custom_list', $data);
        return view('erp/layout/layout_main', $data); //page load

    }

    public function fetchGlobalLeadFields($username = null)
    {
        $LeadConfig = new LeadConfigModel();
        $UsersModel = new UsersModel();
        $OpportunityModel = new OpportunityModel();

        // Check if username is missing
        if (empty($username)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Username parameter is required'
            ])->setStatusCode(400);
        }

        try {
            // Find the user with the given username
            $user = $UsersModel->where(['username' => $username, 'user_type' => 'company'])->first();
            if (!$user) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Company not found'
                ])->setStatusCode(404);
            }

            $company_id = $user['company_id'] ?? null;

            // Validate company_id
            if ($company_id && !is_numeric($company_id)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid company ID format'
                ])->setStatusCode(400);
            }

            // Fetch lead configuration fields
            $configFields = $LeadConfig->groupStart()
                ->where('company_id', null)
                ->orWhere('company_id', $company_id)
                ->groupEnd()
                ->where('status', 1)
                ->findAll();

            if (empty($configFields)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'No configuration fields found',
                    'config_fields' => []
                ]);
            }

            $opportunities = $OpportunityModel
                ->select('id, 	opportunity_name')
                ->where('company_id', $company_id)
                ->where('status', 1)
                ->findAll();

            $opportunityList = array_map(function ($opportunity) {
                return [
                    'id' => (int) $opportunity['id'],
                    'name' => $opportunity['opportunity_name']
                ];
            }, $opportunities);


            // Format the fields
            $formattedFields = array_map(function ($field) {
                return [
                    'id' => (int)$field['id'],
                    'field_name' => $field['column_name'],
                    'field_label' => $field['column_name'],
                    'field_type' => $field['type'],
                    'is_required' => (bool)$field['is_required'],
                    'options' => !empty($field['field_options']) ? json_decode($field['field_options'], true) : null
                ];
            }, $configFields);

            // Success response
            return $this->response
                ->setJSON([
                    'status' => 'success',
                    'username' => $username,
                    'company_id' => $company_id,
                    'count' => count($formattedFields),
                    'config_fields' => $formattedFields,
                    'opportunity_count' => count($opportunityList),
                    'opportunities' => $opportunityList,
                ])
                ->setHeader('Cache-Control', 'public, max-age=3600');
        } catch (\Exception $e) {
            log_message('error', 'Lead Config API Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Internal server error'
            ])->setStatusCode(500);
        }
    }


    public function saveLead()
    {
        $UsersModel = new UsersModel();
        $LeadConfigModel = new \App\Models\LeadConfigModel();
        $LeadOptionsModel = new \App\Models\LeadOptions();
        $validation = \Config\Services::validation();
        $session = \Config\Services::session();
        $usession = $session->get('sup_username');

        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $validation->setRules([
            'field_name' => 'required|min_length[3]',
            'input_Type' => 'required',
            'is_required' => 'required|in_list[0,1]',
            'options' => 'permit_empty', // Options are not mandatory
        ]);

        if ($this->request->getMethod()) {

            $fieldName = $this->request->getPost('field_name');
            $fieldExists = $LeadConfigModel->where(['column_name' => $fieldName, 'company_id' => $user_info['company_id']])->first();

            if ($fieldExists) {
                session()->setFlashdata('error', lang('Language.xin_error_exists_label'));
                return redirect()->back()->withInput();
            }

            $data = [
                'company_id' => $user_info['company_id'],
                'column_name' => $this->request->getPost('field_name'),
                'type' => $this->request->getPost('input_Type'),
                'is_required' => $this->request->getPost('is_required'),
                'status' => 1,
                'valid' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            log_message('debug', 'Data to be inserted: ' . json_encode($data));

            try {
                $LeadConfigModel->insert($data);
                $lastInsertId = $LeadConfigModel->insertID();

                $options = $this->request->getPost('options'); // Retrieving options from the form
                if (!empty($options)) {
                    $optionsData = [
                        'lead_config_id' => $lastInsertId, // Use the inserted ID
                        'options' => $options,
                        'valid' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    log_message('debug', 'Options data to be inserted: ' . json_encode($optionsData));

                    $LeadOptionsModel->insert($optionsData);
                }

                session()->setFlashdata('message', lang('Language.xin_success_lead_add'));
            } catch (\Exception $e) {
                session()->setFlashdata('error', 'Exception occurred: ' . $e->getMessage());
            }
        } else {
            $validationErrors = $validation->getErrors();
            if (!empty($validationErrors)) {
                session()->setFlashdata('error', implode(", ", $validationErrors));
            } else {
                session()->setFlashdata('error', 'Something went wrong, please check your form data or refresh the page.');
            }
        }

        return redirect()->back()->withInput(); // Keep form input values
    }


    public function delete_field($id)
    {
        // $id = base64_decode($enc_id);
        $session = \Config\Services::session();
        $request = \Config\Services::request();

        $Return = array('result' => '', 'error' => '', 'csrf_hash' => csrf_hash());

        $LeadConfig = new LeadConfigModel();

        $result = $LeadConfig->delete($id);

        if ($result) {
            session()->setFlashdata('message', lang('Language.xin_lead_delete'));
        } else {
            $session->setFlashdata('error', lang('Membership.xin_error_msg')); // Store error message in session
        }

        return redirect()->back();
    }


    // Validate and update info in database
    public function updateStatus($lead_id, $status)
    {
        $session = \Config\Services::session();
        $LeadConfig = new LeadConfigModel();

        $data = ['status' => $status];

        if ($LeadConfig->update($lead_id, $data)) {
            $session->setFlashdata('message', lang('Language.xin_success_update_status'));
        } else {
            $session->setFlashdata('error', lang('Main.xin_error_msg'));
        }

        return redirect()->back();
    }

    public function getDetails($id)
    {
        $LeadConfig = new LeadConfigModel();
        $result = $LeadConfig->where('id', $id)->first();

        if ($result) {
            return view('erp/lead/custom_editlead', ['result' => $result]);
        } else {
            return redirect()->back()->with('error', 'No data found for the given ID');
        }
    }

    public function updateLead($lead_id)
    {
        // $lead_id = base64_decode($enc_id);
        $session = session();

        // Validate the lead ID
        if (empty($lead_id)) {
            $session->setFlashdata('error', lang('Main.xin_invalid_lead_id'));
            return redirect()->to(base_url('erp/customization-lead'));
        }

        // Prepare the data to be updated
        $data = [
            'column_name' => $this->request->getPost('field_name'),
            'type' => $this->request->getPost('input_Type'),
            'is_required' => $this->request->getPost('is_required'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $LeadConfig = new LeadConfigModel();
        $result = $LeadConfig->update($lead_id, $data);

        // Handle options if they are provided
        $optionsInput = $this->request->getPost('options');

        if (!empty($optionsInput)) {

            $optionsArray = explode(',', $optionsInput);
            $formattedOptions = array_map('trim', $optionsArray);

            $optionsData = [
                'options' => json_encode(array_map(function ($value) {
                    return ['value' => $value];
                }, $formattedOptions)),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            log_message('debug', 'Form Data: ' . json_encode($this->request->getPost()));

            $LeadOptionsModel = new LeadOptions();
            $LeadOptionsModel->where('lead_config_id', $lead_id)->set($optionsData)->update();
        }

        // Set flashdata messages based on the result
        if ($result) {
            $session->setFlashdata('message', lang('Language.xin_update_leadfield'));
        } else {
            $session->setFlashdata('error', lang('Main.xin_error_msg'));
        }

        return redirect()->to(base_url('erp/customization-lead'));
    }

    public function create_dynamic_table()
    {

        $session = \Config\Services::session();
        $UsersModel = new UsersModel();
        $LeadConfig = new LeadConfigModel();

        if (!$session->has('sup_username')) {
            return $this->response->setJSON(['error' => 'You are not logged in.']);
        }

        $usession = $session->get('sup_username');
        $user_info = $UsersModel->find($usession['sup_user_id']);

        if (!$user_info) {
            return $this->response->setJSON(['error' => 'User information not found.']);
        }

        $company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
        $company_id = $user_info['company_id'];
        $table_name = 'leads_' . $company_name;


        $db = \Config\Database::connect();

        $existingFields = [];
        if ($db->tableExists($table_name)) {
            $existingFields = $db->getFieldNames($table_name);
        }

        $leadFields = $LeadConfig->groupStart()
            ->where('company_id', $company_id)
            ->orWhere('company_id', null)
            ->groupEnd()
            ->orderBy('id', 'ASC')
            ->findAll();

        if (empty($leadFields)) {
            return $this->response->setJSON(['error' => 'No valid fields found for this company.']);
        }

        try {
            $newColumns = [];
            if (empty($existingFields)) {
                // Create new table
                $query = "CREATE TABLE `{$table_name}` (
                `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `opportunity_id` INT,
                `status` INT DEFAULT 1,
                `lead_status` VARCHAR(255), 
                `sources_name` VARCHAR(255)";

                foreach ($leadFields as $field) {
                    $column_name = $this->sanitizeColumnName($field['column_name']);
                    $query .= ", `{$column_name}` VARCHAR(255)";
                    $newColumns[] = $field['column_name']; // Track new columns
                }

                $query .= ");";

                $db->query($query);
                $isNewTable = true;
            } else {
                // Add new columns if not exists
                foreach ($leadFields as $field) {
                    $column_name = $this->sanitizeColumnName($field['column_name']);
                    if (!in_array($column_name, $existingFields)) {
                        $db->query("ALTER TABLE `{$table_name}` ADD COLUMN `{$column_name}` VARCHAR(255)");
                        $newColumns[] = $field['column_name']; // Track new columns
                    }
                }

                // Ensure required columns exist
                $fieldsToAdd = ['lead_status', 'sources_name', 'status'];
                foreach ($fieldsToAdd as $field) {
                    if (!in_array($field, $existingFields)) {
                        $db->query("ALTER TABLE `{$table_name}` ADD COLUMN `{$field}` VARCHAR(255)");
                        $newColumns[] = $field; // Track new columns
                    }
                }

                // Drop unnecessary columns
                foreach ($existingFields as $existingField) {
                    if (!in_array($existingField, ['id', 'opportunity_id', 'status', 'lead_status', 'sources_name'])) {
                        $dropFieldName = $existingField;
                        $found = false;
                        foreach ($leadFields as $field) {
                            if ($dropFieldName === $this->sanitizeColumnName($field['column_name'])) {
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $db->query("ALTER TABLE `{$table_name}` DROP COLUMN `{$dropFieldName}`");
                        }
                    }
                }
                $isNewTable = false;
            }

            foreach ($newColumns as $column) {
                $this->logMigration($table_name, $column);
            }

            $this->createDynamicModelAndMigration($table_name, $leadFields, $isNewTable);

            return $this->response->setJSON(['success' => 'Table and model created/updated successfully.']);
        } catch (\Exception $e) {
            log_message('error', 'Table creation/update failed: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Table creation/update failed: ' . $e->getMessage()]);
        }
    }


    protected function sanitizeColumnName($column_name)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', trim($column_name)));
    }

    protected function createDynamicModelAndMigration($table_name, $leadFields, $isNewTable)
    {
        $modelName = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name))) . '_Model';
        $modelContent = "<?php\n\n"
            . "namespace App\Models;\n\n"
            . "use CodeIgniter\Model;\n\n"
            . "class $modelName extends Model\n"
            . "{\n"
            . "    protected \$table = '$table_name';\n"
            . "    protected \$primaryKey = 'id';\n"
            . "    protected \$allowedFields = ['opportunity_id', ";

        foreach ($leadFields as $field) {
            $column_name = $this->sanitizeColumnName($field['column_name']);
            $modelContent .= "'" . $column_name . "', ";
        }

        $modelContent = rtrim($modelContent, ', ') . "];\n";
        $modelContent .= "}\n";

        $modelFilePath = APPPATH . 'Models/' . $modelName . '.php';

        try {
            if (file_put_contents($modelFilePath, $modelContent) === false) {
                throw new \RuntimeException('Failed to create model file.');
            } else {
                log_message('info', "Model file created successfully at $modelFilePath");
            }
        } catch (\Exception $e) {
            log_message('error', 'Model creation failed: ' . $e->getMessage());
        }

        if ($isNewTable) {
            $this->createMigrationFile($table_name, $leadFields);
            $this->runMigration();
        } else {
            // Run the incremental migration
            $this->createIncrementalMigrationFile($table_name, $leadFields);
            $this->runMigration();
        }
    }
    protected function createMigrationFile($table_name, $leadFields)
    {
        // Include timestamp to make the class name unique
        $className = 'Create' . ucfirst($table_name) . 'Table' . date('YmdHis');

        $migrationContent = "<?php\n\n"
            . "namespace App\Database\Migrations;\n\n"
            . "use CodeIgniter\Database\Migration;\n\n"
            . "class $className extends Migration\n"
            . "{\n"
            . "    public function up()\n"
            . "    {\n"
            . "        \$this->forge->addField([\n"
            . "            'id' => [\n"
            . "                'type' => 'INT',\n"
            . "                'constraint' => 11,\n"
            . "                'unsigned' => true,\n"
            . "                'auto_increment' => true\n"
            . "            ],\n"
            . "            'opportunity_id' => [\n"
            . "                'type' => 'INT',\n"
            . "                'constraint' => 11,\n"
            . "            ],\n";

        // Add dynamic fields
        foreach ($leadFields as $field) {
            $migrationContent .= "            '" . $field['column_name'] . "' => [\n"
                . "                'type' => 'VARCHAR',\n"
                . "                'constraint' => '255',\n"
                . "            ],\n";
        }

        $migrationContent .= "        ]);\n";
        $migrationContent .= "        \$this->forge->addKey('id', true);\n";
        $migrationContent .= "        \$this->forge->createTable('$table_name');\n";
        $migrationContent .= "    }\n\n";

        $migrationContent .= "    public function down()\n"
            . "    {\n"
            . "        \$this->forge->dropTable('$table_name');\n"
            . "    }\n";
        $migrationContent .= "}\n";

        $migrationFileName = date('YmdHis') . '_create_' . $table_name . '_table.php';
        $migrationFilePath = APPPATH . 'Database/Migrations/' . $migrationFileName;

        try {
            if (file_put_contents($migrationFilePath, $migrationContent) === false) {
                throw new \RuntimeException('Failed to create migration file.');
            } else {
                log_message('info', "Migration file created successfully at $migrationFilePath");
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration creation failed: ' . $e->getMessage());
        }
    }

    protected function createIncrementalMigrationFile($table_name, $newFields)
    {
        // Include timestamp to make the class name unique
        $className = 'Update' . ucfirst($table_name) . 'Table' . date('YmdHis');

        $migrationContent = "<?php\n\n"
            . "namespace App\Database\Migrations;\n\n"
            . "use CodeIgniter\Database\Migration;\n\n"
            . "class $className extends Migration\n"
            . "{\n"
            . "    public function up()\n"
            . "    {\n"
            . "        \$this->forge->addField([\n";

        foreach ($newFields as $field) {
            $migrationContent .= "            '" . $field['column_name'] . "' => [\n"
                . "                'type' => 'VARCHAR',\n"
                . "                'constraint' => '255',\n"
                . "            ],\n";
        }

        $migrationContent .= "        ]);\n";
        $migrationContent .= "        \$this->forge->createTable('$table_name');\n";
        $migrationContent .= "    }\n\n";

        $migrationContent .= "    public function down()\n"
            . "    {\n"
            . "        \$this->forge->dropTable('$table_name');\n"
            . "    }\n";
        $migrationContent .= "}\n";

        $migrationFileName = date('YmdHis') . '_update_' . $table_name . '_table.php';
        $migrationFilePath = APPPATH . 'Database/Migrations/' . $migrationFileName;

        try {
            if (file_put_contents($migrationFilePath, $migrationContent) === false) {
                throw new \RuntimeException('Failed to create migration file.');
            } else {
                log_message('info', "Migration file created successfully at $migrationFilePath");
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration creation failed: ' . $e->getMessage());
        }
    }
    protected function logMigration($table_name, $column_name)
    {
        $db = \Config\Database::connect();
        $db->table('migration_log')->insert([
            'table_name' => $table_name,
            'column_name' => $column_name
        ]);
    }
    protected function runMigration()
    {
        try {
            $migrate = \Config\Services::migrations();
            if ($migrate->latest() === false) {
                throw new \RuntimeException('Migration failed.');
            } else {
                log_message('info', 'Migration ran successfully.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration execution failed: ' . $e->getMessage());
        }
    }
}
