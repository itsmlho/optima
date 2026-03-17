<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContractLinkFieldsToDeliveryInstructions extends Migration
{
    public function up()
    {
        // Add contract linkage columns to delivery_instructions
        $this->forge->addColumn('delivery_instructions', [
            'contract_id' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
                'after'   => 'jenis_spk',
                'comment' => 'FK to kontrak.id',
            ],
            'pelanggan_id' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
                'after'   => 'pelanggan',
                'comment' => 'FK to customers.id',
            ],
            'bast_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'tanggal_kirim',
                'comment' => 'BAST tanggal serah terima unit',
            ],
            'billing_start_date' => [
                'type'    => 'DATE',
                'null'    => true,
                'default' => null,
                'after'   => 'bast_date',
                'comment' => 'Start date for billing calculation',
            ],
            'contract_linked_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Timestamp when contract was linked',
            ],
            'contract_linked_by' => [
                'type'    => 'INT',
                'constraint' => 11,
                'null'    => true,
                'default' => null,
                'comment' => 'User ID who performed the contract linking',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('delivery_instructions', [
            'contract_id',
            'pelanggan_id',
            'bast_date',
            'billing_start_date',
            'contract_linked_at',
            'contract_linked_by',
        ]);
    }
}
