<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatbotHistoryModel extends Model
{
    protected $table            = 'chatbot_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'role',
        'message',
        'intent',
        'metadata_json',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';

    public function logMessage(int $userId, string $role, string $message, ?string $intent = null, array $metadata = []): bool
    {
        return (bool) $this->insert([
            'user_id'       => $userId,
            'role'          => $role,
            'message'       => $message,
            'intent'        => $intent,
            'metadata_json' => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRecentHistory(int $userId, int $turns = 20): array
    {
        $rows = $this->where('user_id', $userId)
            ->orderBy('id', 'DESC')
            ->findAll($turns * 2);

        $rows = array_reverse($rows);

        return array_map(static function (array $row): array {
            return [
                'role' => $row['role'] === 'assistant' ? 'model' : 'user',
                'text' => (string) ($row['message'] ?? ''),
            ];
        }, $rows);
    }

    public function clearUserHistory(int $userId): bool
    {
        return (bool) $this->where('user_id', $userId)->delete();
    }
}
