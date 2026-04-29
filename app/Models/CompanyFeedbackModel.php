<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyFeedbackModel extends Model
{
    protected $table            = 'company_feedback';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type',
        'message',
        'contact_email',
        'contact_phone',
        'is_anonymous',
        'created_at',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'type'    => 'required|in_list[masukan,keluh_kesah]',
        'message' => 'required|min_length[10]|max_length[10000]',
    ];

    protected $validationMessages = [
        'type' => [
            'required'   => 'Jenis masukan wajib dipilih.',
            'in_list'    => 'Jenis tidak valid.',
        ],
        'message' => [
            'required'   => 'Isi pesan wajib diisi.',
            'min_length' => 'Pesan minimal 10 karakter.',
            'max_length' => 'Pesan terlalu panjang.',
        ],
    ];

    /**
     * Ringkasan untuk dashboard HR (kartu statistik).
     */
    public function getListStats(): array
    {
        $t = $this->table;
        $monthStart = date('Y-m-01 00:00:00');

        return [
            'total'       => $this->countAllResults(),
            'this_month'  => (int) $this->db->table($t)->where('created_at >=', $monthStart)->countAllResults(),
            'masukan'     => (int) $this->db->table($t)->where('type', 'masukan')->countAllResults(),
            'keluh_kesah' => (int) $this->db->table($t)->where('type', 'keluh_kesah')->countAllResults(),
        ];
    }
}
