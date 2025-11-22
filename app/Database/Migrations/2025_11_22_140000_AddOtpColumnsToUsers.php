<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtpColumnsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'otp_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Enable OTP untuk login',
                'after'      => 'remember_token',
            ],
            'otp_enabled_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'Kapan OTP diaktifkan',
                'after'   => 'otp_enabled',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['otp_enabled', 'otp_enabled_at']);
    }
}

