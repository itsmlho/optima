<?php

namespace App\Models;

use CodeIgniter\Model;

class SpkModel extends Model
{
    protected $table            = 'spk';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_spk','jenis_spk','kontrak_id','kontrak_spesifikasi_id','jumlah_unit','po_kontrak_nomor','pelanggan','pic','kontak','lokasi',
        'delivery_plan','spesifikasi','catatan','status',
        'dibuat_oleh','dibuat_pada','diperbarui_pada',
        'persiapan_unit_mekanik','persiapan_unit_estimasi_mulai','persiapan_unit_estimasi_selesai','persiapan_unit_tanggal_approve',
        'persiapan_unit_id','persiapan_aksesoris_tersedia',
        'fabrikasi_mekanik','fabrikasi_estimasi_mulai','fabrikasi_estimasi_selesai','fabrikasi_tanggal_approve',
        'fabrikasi_attachment_id',
        'painting_mekanik','painting_estimasi_mulai','painting_estimasi_selesai','painting_tanggal_approve',
        'pdi_mekanik','pdi_estimasi_mulai','pdi_estimasi_selesai','pdi_tanggal_approve','pdi_catatan'
    ];

    protected $useTimestamps = false;

    /** Generate next SPK number with prefix SPK/YYYYMM/NNN */
    public function generateNextNumber(): string
    {
        $prefix = 'SPK/'.date('Ym').'/';
        $row = $this->db->table($this->table)->like('nomor_spk', $prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($row && isset($row['nomor_spk'])) {
            $parts = explode('/', $row['nomor_spk']);
            $seq = isset($parts[2]) ? ((int)$parts[2] + 1) : 1;
        }
        return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
    }

    /** Update status and record history (best-effort). Requires SpkStatusHistoryModel table available. */
    public function setStatusWithHistory(int $id, string $newStatus, ?int $userId = null, ?string $note = null): bool
    {
        $prev = $this->select('status')->find($id);
        $ok = $this->update($id, ['status'=>$newStatus, 'diperbarui_pada'=>date('Y-m-d H:i:s')]);
        if ($ok && $prev && isset($prev['status'])) {
            try {
                $hist = new \App\Models\SpkStatusHistoryModel();
                $hist->insert([
                    'spk_id' => $id,
                    'status_from' => $prev['status'],
                    'status_to' => $newStatus,
                    'changed_by' => $userId ?: 0,
                    'note' => $note,
                    'changed_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $e) { /* ignore */ }
        }
        return (bool)$ok;
    }

    /** Persist selected unit/attachment into spesifikasi JSON and move to READY with history. */
    public function assignSelectionsAndReady(int $spkId, array $selection, ?int $userId = null): bool
    {
        $spk = $this->find($spkId);
        if (!$spk) return false;
        $spec = [];
        if (!empty($spk['spesifikasi'])) {
            $decoded = json_decode($spk['spesifikasi'], true);
            if (is_array($decoded)) $spec = $decoded;
        }
        $spec['selected'] = $selection;
        $ok = $this->update($spkId, [
            'spesifikasi' => json_encode($spec, JSON_UNESCAPED_UNICODE),
            'diperbarui_pada' => date('Y-m-d H:i:s'),
        ]);
        if (!$ok) return false;
        return $this->setStatusWithHistory($spkId, 'READY', $userId, 'Unit & attachment ditetapkan oleh Service');
    }
}
