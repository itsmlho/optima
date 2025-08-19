<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PurchasingManagementModel adalah model serbaguna yang digunakan untuk
 * berbagai operasi terkait data purchasing, terutama untuk mengambil
 * statistik dan data agregat dari tabel purchase_orders.
 */
class PurchasingManagementModel extends Model
{
    /**
     * Tabel utama yang digunakan oleh model ini.
     * @var string
     */
    protected $table            = 'purchase_orders';
    
    /**
     * Primary key dari tabel.
     * @var string
     */
    protected $primaryKey       = 'id_po';

    /**
     * Tipe data yang dikembalikan.
     * @var string
     */
    protected $returnType       = 'array';

    /**
     * Kolom yang diizinkan untuk mass assignment.
     * Sesuaikan jika Anda menggunakan model ini untuk insert/update.
     * @var array
     */
    protected $allowedFields    = [
        'no_po', 
        'tanggal_po', 
        'supplier_id', 
        'tipe_po', 
        'status',
        'keterangan_po', 
        'invoice_no', 
        'invoice_date', 
        'bl_date'
    ];

    /**
     * Mengaktifkan penggunaan timestamps.
     * @var bool
     */
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /**
     * Menghitung statistik PO berdasarkan tipe dan status.
     * @param string $field Kolom yang akan digunakan untuk filter (misal: 'tipe_po').
     * @param string $value Nilai yang dicari di dalam kolom (misal: 'Sparepart').
     * @return array
     */
    public function getPOStats(string $field, string $value): array
    {
        // Pastikan tabel yang digunakan adalah purchase_orders
        $this->table = 'purchase_orders';

        // PERBAIKAN: Logika diubah agar setiap penghitungan independen dan akurat
        $stats = [
            'total'                   => $this->where($field, $value)->countAllResults(),
            'pending'                 => $this->where($field, $value)->where('status', 'pending')->countAllResults(),
            'approved'                => $this->where($field, $value)->where('status', 'approved')->countAllResults(),
            'completed'               => $this->where($field, $value)->where('status', 'completed')->countAllResults(),
            'Selesai dengan Catatan'  => $this->where($field, $value)->where('status', 'Selesai dengan Catatan')->countAllResults(),
            'cancelled'               => $this->where($field, $value)->where('status', 'cancelled')->countAllResults(),
        ];

        // Reset query builder untuk pemanggilan selanjutnya agar tidak saling mempengaruhi
        $this->resetQuery();
        return $stats;
    }

    /**
     * Mengambil data untuk server-side datatable.
     * (Ini adalah contoh dasar, Anda mungkin perlu menyesuaikannya lebih lanjut)
     */
    public function getDataTable(string $tipe, int $start, int $length, string $orderColumn, string $orderDir, string $searchValue)
    {
        // Tentukan tabel item dan kolom ID berdasarkan tipe PO
        $itemTable = '';
        $itemIdColumn = '';
        if ($tipe === 'Sparepart') {
            $itemTable = 'po_sparepart_items';
            $itemIdColumn = 'id';
        } elseif ($tipe === 'Attachment & Battery') {
            $itemTable = 'po_items';
            $itemIdColumn = 'id_po_item';
        } elseif ($tipe === 'Unit') {
            $itemTable = 'po_units';
            $itemIdColumn = 'id_po_unit';
        } else {
            // Jika tipe tidak dikenali, kembalikan data kosong
            return [];
        }

        // Subquery dinamis berdasarkan tabel item
        $totalItemsSubquery = "(SELECT COUNT({$itemIdColumn}) FROM {$itemTable} WHERE po_id = po.id_po)";
        $sesuaiItemsSubquery = "(SELECT COUNT({$itemIdColumn}) FROM {$itemTable} WHERE po_id = po.id_po AND status_verifikasi = 'Sesuai')";
        $processedItemsSubquery = "(SELECT COUNT({$itemIdColumn}) FROM {$itemTable} WHERE po_id = po.id_po AND status_verifikasi != 'Belum Dicek')";
        $rejectedItemsSubquery = "(SELECT COUNT({$itemIdColumn}) FROM {$itemTable} WHERE po_id = po.id_po AND status_verifikasi = 'Tidak Sesuai')";

        $builder = $this->db->table('purchase_orders as po')
            ->select("
                po.id_po, 
                po.no_po, 
                po.tanggal_po, 
                s.nama_supplier, 
                po.status,
                {$totalItemsSubquery} as total_items,
                {$sesuaiItemsSubquery} as sesuai_items,
                {$processedItemsSubquery} as processed_items,
                {$rejectedItemsSubquery} as rejected_items
            ")
            ->join('suppliers as s', 's.id_supplier = po.supplier_id', 'left')
            ->where('po.tipe_po', $tipe);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('po.no_po', $searchValue)
                ->orLike('s.nama_supplier', $searchValue)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)
                ->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    /**
     * Menghitung semua data berdasarkan tipe untuk pagination datatable.
     */
    public function countAllTipe(string $tipe): int
    {
        return $this->db->table('purchase_orders')->where('tipe_po', $tipe)->countAllResults();
    }

    /**
     * Menghitung data yang terfilter untuk pagination datatable.
     */
    public function countFiltered(string $tipe, string $searchValue): int
    {
        $builder = $this->db->table('purchase_orders as po')
            ->join('suppliers as s', 's.id_supplier = po.supplier_id', 'left')
            ->where('po.tipe_po', $tipe);

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('po.no_po', $searchValue)
                ->orLike('s.nama_supplier', $searchValue)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Mengambil daftar supplier.
     */
    public function getSuppliers()
    {
        return $this->db->table('suppliers')->get()->getResultArray();
    }
    
    /**
     * Placeholder untuk statistik notifikasi.
     * Anda bisa kembangkan logika ini sesuai kebutuhan.
     * @param string $division
     * @return array
     */
    public function getNotificationStats(string $division): array
    {
        // Contoh logika:
        // $this->table = 'notifications'; // Ganti tabel ke notifikasi
        // $unread = $this->where('division', $division)->where('is_read', 0)->countAllResults();
        // return ['unread_count' => $unread];
        
        // Untuk saat ini, kembalikan array kosong agar tidak error
        return ['unread_count' => 0];
    }

    
}
