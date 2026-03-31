<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotificationStyleToNotifications extends Migration
{
    public function up()
    {
        // Safety: only add column if table & column state require it
        $table = 'notifications';

        if (! $this->db->tableExists($table)) {
            return;
        }

        $fields = $this->db->getFieldNames($table);
        if (in_array('notification_style', $fields, true)) {
            return;
        }

        $this->forge->addColumn($table, [
            'notification_style' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'info_only',
                'comment'    => 'Display style for notification (info_only, action_required, etc.)',
            ],
        ]);
    }

    public function down()
    {
        $table = 'notifications';

        if (! $this->db->tableExists($table)) {
            return;
        }

        $fields = $this->db->getFieldNames($table);
        if (! in_array('notification_style', $fields, true)) {
            return;
        }

        $this->forge->dropColumn($table, 'notification_style');
    }
}

