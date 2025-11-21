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
            // Ensure created_at is set if not provided
            if (empty($data['created_at'])) {
                $jakartaTz = new \DateTimeZone('Asia/Jakarta');
                $now = new \DateTime('now', $jakartaTz);
                $data['created_at'] = $now->format('Y-m-d H:i:s');
            }
            
            // Ensure user_id is integer
            if (isset($data['user_id'])) {
                $data['user_id'] = (int)$data['user_id'];
            }
            
            log_message('debug', '[VerificationAuditLogModel] Inserting log: ' . json_encode($data));
            
            $result = (bool)$this->insert($data);
            
            if ($result) {
                log_message('info', '[VerificationAuditLogModel] ✓ Log inserted successfully. ID: ' . $this->insertID());
            } else {
                $errors = $this->errors();
                log_message('error', '[VerificationAuditLogModel] ✗ Failed to insert log. Errors: ' . json_encode($errors));
            }
            
            return $result;
        } catch (\Throwable $e) {
            log_message('error', '[VerificationAuditLogModel] Exception: '.$e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}
