<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add payment due fields for PO_ONLY and DAILY_SPOT rental types.
 * - payment_due_day: 1-31 for monthly billing
 * - payment_due_alert_days: days before due to send alert
 * - first_payment_due_date: optional override for first period
 */
class AddPaymentDueFieldsToKontrak extends Migration
{
    public function up()
    {
        $fields = [
            'payment_due_day' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Day of month (1-31) for payment due; used for PO_ONLY/DAILY_SPOT',
                'after'      => 'tanggal_berakhir',
            ],
            'payment_due_alert_days' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Days before due date to send payment reminder alert',
                'after'      => 'payment_due_day',
            ],
            'first_payment_due_date' => [
                'type'       => 'DATE',
                'null'       => true,
                'comment'    => 'Override first payment due date; if null, use tanggal_mulai + 1 month + payment_due_day',
                'after'      => 'payment_due_alert_days',
            ],
        ];
        $this->forge->addColumn('kontrak', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('kontrak', ['payment_due_day', 'payment_due_alert_days', 'first_payment_due_date']);
    }
}
