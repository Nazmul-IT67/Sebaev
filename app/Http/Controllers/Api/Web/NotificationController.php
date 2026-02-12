<?php
namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class NotificationController extends Controller
{

    use ApiResponse;

    public function notification()
    {
        $user = auth()->user();

        $data = $user->notifications()
            ->with('notifiable')
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id'          => $notification->id,
                    'data'        => $notification->data,
                    'read_at'     => $notification->read_at,
                    'created_at'  => $notification->created_at,
                    'user_name'   => $notification->notifiable?->name,
                    'user_avatar' => $notification->notifiable?->avatar,
                ];
            });

        if ($data->isEmpty()) {
            return $this->error([], 'No Notifications Found', 200);
        }

        return $this->success($data, 'Notifications fetched successfully', 200);
        
    }
}
