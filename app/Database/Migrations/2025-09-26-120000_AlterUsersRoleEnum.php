<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUsersRoleEnum extends Migration
{
    public function up()
    {
        // Drop the existing role column
        $this->forge->dropColumn('users', 'role');
        
        // Add the new role column with updated ENUM
        $this->forge->addColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['student', 'teacher', 'admin'],
                'default'    => 'student',
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
        // Drop the current role column
        $this->forge->dropColumn('users', 'role');
        
        // Revert to original ENUM
        $this->forge->addColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['student', 'instructor', 'admin'],
                'default'    => 'student',
                'null'       => false,
            ],
        ]);
    }
}
