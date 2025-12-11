<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add new columns to enrollments table
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
                'after' => 'course_id'
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'after' => 'status'
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['1st', '2nd', 'summer'],
                'after' => 'school_year'
            ],
            'schedule' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'e.g., MWF 9:00-10:30',
                'after' => 'semester'
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'enrollment_date'
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by'
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_at'
            ]
        ];

        $this->forge->addColumn('enrollments', $fields);

        // Add foreign key for approved_by
        $this->db->query('ALTER TABLE `enrollments` 
            ADD CONSTRAINT `fk_enrollments_approved_by` 
            FOREIGN KEY (`approved_by`) 
            REFERENCES `users`(`id`) 
            ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        if ($this->db->tableExists('enrollments')) {
            $this->db->query('ALTER TABLE `enrollments` DROP FOREIGN KEY `fk_enrollments_approved_by`');
        }
        
        // Drop columns
        $this->forge->dropColumn('enrollments', [
            'status',
            'school_year',
            'semester',
            'schedule',
            'approved_by',
            'approved_at',
            'rejection_reason'
        ]);
    }
}
