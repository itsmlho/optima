<?php

namespace App\Config;

/**
 * Unit Workflow Status
 * Status unit dalam proses TARIK dan TUKAR
 */
class UnitWorkflowStatus
{
    // Status untuk unit yang sedang dalam proses TARIK
    const UNIT_AKAN_DITARIK = 'UNIT_AKAN_DITARIK';
    const UNIT_SEDANG_DITARIK = 'UNIT_SEDANG_DITARIK';
    const UNIT_PULANG = 'UNIT_PULANG';
    const STOCK_ASET = 'STOCK_ASET';

    // Status untuk unit yang sedang dalam proses TUKAR
    const UNIT_AKAN_DITUKAR = 'UNIT_AKAN_DITUKAR';
    const UNIT_SEDANG_DITUKAR = 'UNIT_SEDANG_DITUKAR';
    const UNIT_TUKAR_SELESAI = 'UNIT_TUKAR_SELESAI';

    // Status operasional normal
    const TERSEDIA = 'TERSEDIA';
    const DISEWA = 'DISEWA';
    const BEROPERASI = 'BEROPERASI';
    const MAINTENANCE = 'MAINTENANCE';

    /**
     * Get status workflow untuk TARIK
     */
    public static function getTarikWorkflow()
    {
        return [
            self::DISEWA => 'Unit sedang disewa/beroperasi',
            self::UNIT_AKAN_DITARIK => 'Unit akan ditarik (DI disetujui)',
            self::UNIT_SEDANG_DITARIK => 'Unit dalam perjalanan pulang',
            self::UNIT_PULANG => 'Unit sudah sampai kantor/workshop',
            self::STOCK_ASET => 'Unit siap untuk kontrak baru'
        ];
    }

    /**
     * Get status workflow untuk TUKAR
     */
    public static function getTukarWorkflow()
    {
        return [
            self::DISEWA => 'Unit lama sedang beroperasi',
            self::UNIT_AKAN_DITUKAR => 'Unit akan ditukar (DI disetujui)',
            self::UNIT_SEDANG_DITUKAR => 'Proses penukaran berlangsung',
            self::UNIT_TUKAR_SELESAI => 'Unit lama sudah ditukar',
            self::STOCK_ASET => 'Unit lama kembali ke stock'
        ];
    }

    /**
     * Get next status in workflow
     */
    public static function getNextStatus($currentStatus, $jenisPerintah)
    {
        $workflows = [
            'TARIK' => [
                self::DISEWA => self::UNIT_AKAN_DITARIK,
                self::BEROPERASI => self::UNIT_AKAN_DITARIK,
                self::UNIT_AKAN_DITARIK => self::UNIT_SEDANG_DITARIK,
                self::UNIT_SEDANG_DITARIK => self::UNIT_PULANG,
                self::UNIT_PULANG => self::STOCK_ASET
            ],
            'TUKAR' => [
                self::DISEWA => self::UNIT_AKAN_DITUKAR,
                self::BEROPERASI => self::UNIT_AKAN_DITUKAR,
                self::UNIT_AKAN_DITUKAR => self::UNIT_SEDANG_DITUKAR,
                self::UNIT_SEDANG_DITUKAR => self::UNIT_TUKAR_SELESAI,
                self::UNIT_TUKAR_SELESAI => self::STOCK_ASET
            ]
        ];

        return $workflows[$jenisPerintah][$currentStatus] ?? null;
    }

    /**
     * Check if status requires contract disconnection
     */
    public static function requiresContractDisconnection($status)
    {
        return in_array($status, [
            self::UNIT_PULANG,
            self::STOCK_ASET,
            self::UNIT_TUKAR_SELESAI
        ]);
    }

    /**
     * Check if unit is available for new contract
     */
    public static function isAvailableForNewContract($status)
    {
        return in_array($status, [
            self::STOCK_ASET,
            self::TERSEDIA
        ]);
    }

    /**
     * Get status color for UI
     */
    public static function getStatusColor($status)
    {
        $colors = [
            self::TERSEDIA => 'success',
            self::DISEWA => 'primary',
            self::BEROPERASI => 'info',
            self::UNIT_AKAN_DITARIK => 'warning',
            self::UNIT_SEDANG_DITARIK => 'warning',
            self::UNIT_PULANG => 'secondary',
            self::STOCK_ASET => 'success',
            self::UNIT_AKAN_DITUKAR => 'warning',
            self::UNIT_SEDANG_DITUKAR => 'warning',
            self::UNIT_TUKAR_SELESAI => 'secondary',
            self::MAINTENANCE => 'danger'
        ];

        return $colors[$status] ?? 'secondary';
    }

    /**
     * Get workflow approval stages
     */
    public static function getApprovalStages($jenisPerintah)
    {
        $stages = [
            'TARIK' => [
                'DIAJUKAN' => 'DI diajukan untuk penarikan unit',
                'DISETUJUI' => 'DI disetujui, unit siap ditarik',
                'PERSIAPAN_UNIT' => 'Persiapan tim dan transportasi',
                'DALAM_PERJALANAN' => 'Tim menuju lokasi pelanggan',
                'UNIT_DITARIK' => 'Unit berhasil ditarik dari lokasi',
                'UNIT_PULANG' => 'Unit dalam perjalanan kembali',
                'SAMPAI_KANTOR' => 'Unit sampai di kantor/workshop',
                'SELESAI' => 'Proses penarikan selesai'
            ],
            'TUKAR' => [
                'DIAJUKAN' => 'DI diajukan untuk penukaran unit',
                'DISETUJUI' => 'DI disetujui, unit siap ditukar',
                'PERSIAPAN_UNIT' => 'Persiapan unit baru dan tim',
                'DALAM_PERJALANAN' => 'Tim menuju lokasi pelanggan',
                'UNIT_DITUKAR' => 'Unit berhasil ditukar',
                'UNIT_LAMA_PULANG' => 'Unit lama dalam perjalanan kembali',
                'SAMPAI_KANTOR' => 'Unit lama sampai di kantor',
                'SELESAI' => 'Proses penukaran selesai'
            ]
        ];

        return $stages[$jenisPerintah] ?? [];
    }

    /**
     * Get required actions for each stage
     */
    public static function getStageActions($stage, $jenisPerintah)
    {
        $actions = [
            'TARIK' => [
                'DISETUJUI' => [
                    'update_unit_status' => self::UNIT_AKAN_DITARIK,
                    'notify_customer' => true,
                    'prepare_transport' => true
                ],
                'UNIT_DITARIK' => [
                    'update_unit_status' => self::UNIT_SEDANG_DITARIK,
                    'disconnect_partial_contract' => true
                ],
                'SAMPAI_KANTOR' => [
                    'update_unit_status' => self::STOCK_ASET,
                    'disconnect_contract_fully' => true,
                    'quality_check' => true
                ]
            ],
            'TUKAR' => [
                'DISETUJUI' => [
                    'update_unit_status' => self::UNIT_AKAN_DITUKAR,
                    'prepare_replacement_unit' => true,
                    'notify_customer' => true
                ],
                'UNIT_DITUKAR' => [
                    'update_old_unit_status' => self::UNIT_TUKAR_SELESAI,
                    'update_new_unit_status' => self::DISEWA,
                    'transfer_contract_to_new_unit' => true
                ],
                'SAMPAI_KANTOR' => [
                    'update_old_unit_status' => self::STOCK_ASET,
                    'disconnect_old_unit_contract' => true,
                    'quality_check_old_unit' => true
                ]
            ]
        ];

        return $actions[$jenisPerintah][$stage] ?? [];
    }
}