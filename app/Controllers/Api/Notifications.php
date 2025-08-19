<?php
namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;

class Notifications extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $user = session('user');
        $role = $user['role'] ?? null;
        $division = $user['division'] ?? null;
        $userId = $user['id'] ?? null;

        $model = new NotificationModel();
        $builder = $model->where('is_read', 0)
            ->groupStart()
                ->where('user_id', $userId)
                ->orWhere('role', $role)
                ->orWhere('division', $division)
            ->groupEnd()
            ->orderBy('created_at', 'DESC');
        $notifications = $builder->findAll(10);
        return $this->respond($notifications);
    }

    public function markAsRead($id)
    {
        $model = new NotificationModel();
        $model->update($id, ['is_read' => 1]);
        return $this->respond(['status' => 'success']);
    }
}
