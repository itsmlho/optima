<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpModel extends Model
{
    protected $table = 'user_otp';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;

    protected $allowedFields = [
        'user_id',
        'otp_code',
        'email',
        'ip_address',
        'attempts',
        'max_attempts',
        'is_verified',
        'expires_at',
        'verified_at',
        'created_at',
    ];

    protected $useTimestamps = false; // Manual timestamps
    protected $dateFormat = 'datetime';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'otp_code' => 'required|exact_length[6]',
        'email' => 'required|valid_email',
        'ip_address' => 'required|max_length[45]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Generate OTP untuk user
     */
    public function generateOtp(int $userId, string $email, string $ipAddress, int $expireMinutes = 5): ?array
    {
        // Clean expired OTPs untuk user ini
        $this->cleanExpiredOtps($userId);

        // Generate 6 digit OTP
        $otpCode = str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $data = [
            'user_id' => $userId,
            'otp_code' => $otpCode,
            'email' => $email,
            'ip_address' => $ipAddress,
            'attempts' => 0,
            'max_attempts' => 3,
            'is_verified' => 0,
            'expires_at' => date('Y-m-d H:i:s', strtotime("+{$expireMinutes} minutes")),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $otpId = $this->insert($data);

        if ($otpId) {
            $data['id'] = $otpId;
            return $data;
        }

        return null;
    }

    /**
     * Validate OTP code
     */
    public function validateOtp(string $otpCode, int $userId): ?array
    {
        $otp = $this->where('user_id', $userId)
                   ->where('otp_code', $otpCode)
                   ->where('is_verified', 0)
                   ->where('expires_at >', date('Y-m-d H:i:s'))
                   ->where('attempts <', $this->db->escapeString('max_attempts'))
                   ->orderBy('created_at', 'DESC')
                   ->first();

        if ($otp) {
            return $otp;
        }

        return null;
    }

    /**
     * Get active OTP untuk user
     */
    public function getActiveOtp(int $userId): ?array
    {
        return $this->where('user_id', $userId)
                   ->where('is_verified', 0)
                   ->where('expires_at >', date('Y-m-d H:i:s'))
                   ->orderBy('created_at', 'DESC')
                   ->first();
    }

    /**
     * Increment attempts untuk OTP
     */
    public function incrementAttempts(int $otpId): bool
    {
        return $this->set('attempts', 'attempts + 1', false)
                   ->where('id', $otpId)
                   ->update();
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(int $otpId): bool
    {
        return $this->update($otpId, [
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Clean expired OTPs untuk user tertentu atau semua
     */
    public function cleanExpiredOtps(?int $userId = null): int
    {
        $builder = $this->where('expires_at <', date('Y-m-d H:i:s'))
                       ->orWhere('is_verified', 1);

        if ($userId !== null) {
            $builder->where('user_id', $userId);
        }

        return $builder->delete();
    }

    /**
     * Check jika user punya active OTP
     */
    public function hasActiveOtp(int $userId): bool
    {
        $otp = $this->getActiveOtp($userId);
        return $otp !== null;
    }

    /**
     * Get OTP by ID
     */
    public function getOtpById(int $otpId): ?array
    {
        return $this->find($otpId);
    }
}

