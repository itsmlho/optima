<?php
namespace App\Models;
use CodeIgniter\Model;

class VerificationAuditLogModel extends Model
{
    protected $table = 'verification_audit_log';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $protectFields = true;
    protected $allowedFields = [
        'po_type','source_id','po_id','action','status_before','status_after','user_id','notes','payload','created_at'
    ];
    protected $useTimestamps = false; // created_at handled by DB default

    public function log(array $data): bool
    {
        try {
            return (bool)$this->insert($data);
        } catch (\Throwable $e) {
            log_message('error', '[VerificationAuditLogModel] Failed to insert log: '.$e->getMessage());
            return false;
        }
    }
}
