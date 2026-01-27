 <?php

namespace App\Models;

use CodeIgniter\Model;

class DeliveryInstructionModel extends Model
{
    protected $table = 'delivery_instructions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'spk_id','jenis_spk','nomor_di','po_kontrak_nomor','pelanggan','lokasi','tanggal_kirim','status_di','catatan','dibuat_oleh','dibuat_pada','diperbarui_pada',
        'jenis_perintah_kerja_id','tujuan_perintah_kerja_id','status_eksekusi_workflow_id',
        'perencanaan_tanggal_approve','estimasi_sampai','nama_supir','no_hp_supir','no_sim_supir','kendaraan','no_polisi_kendaraan',
        'berangkat_tanggal_approve','catatan_berangkat',
        'sampai_tanggal_approve','catatan_sampai'
    ];
    
    // Explicitly disable automatic timestamps since our table uses custom field names
    protected $useTimestamps = false;
    protected $createdField = '';  // Disable automatic created_at
    protected $updatedField = '';  // Disable automatic updated_at

    /** Generate next DI number with prefix DI/YYYYMM/NNN */
    public function generateNextNumber(): string
    {
        $prefix = 'DI/'.date('Ym').'/';
        $row = $this->db->table($this->table)->like('nomor_di', $prefix)->orderBy('id','DESC')->get()->getRowArray();
        $seq = 1;
        if ($row && isset($row['nomor_di'])) {
            $parts = explode('/', $row['nomor_di']);
            $seq = isset($parts[2]) ? ((int)$parts[2] + 1) : 1;
        }
        return $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
    }
}
