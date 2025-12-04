<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToUsers extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Only add the column if it doesn't already exist (make migration safe to re-run)
        if (! $db->fieldExists('deleted_at', 'users')) {
            $fields = [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'default' => null,
                ],
            ];

            $this->forge->addColumn('users', $fields);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        if ($db->fieldExists('deleted_at', 'users')) {
            $this->forge->dropColumn('users', 'deleted_at');
        }
    }
}
