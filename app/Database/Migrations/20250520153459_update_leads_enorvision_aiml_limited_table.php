<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateLeads_enorvision_aiml_limitedTable20250520153459 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'Lead Owner ' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Re assigned date ' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Contact' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Lead Type' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Profile Image' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Description' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'Test' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->createTable('leads_enorvision_aiml_limited');
    }

    public function down()
    {
        $this->forge->dropTable('leads_enorvision_aiml_limited');
    }
}
