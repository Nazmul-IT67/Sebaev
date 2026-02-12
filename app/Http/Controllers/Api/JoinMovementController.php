<?php

namespace App\Http\Controllers\Api;

use App\Enum\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\UserMovement;
use App\Notifications\UserNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JoinMovementController extends Controller
{
    use ApiResponse;
    public function joinMovement(int $id)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $movement = Movement::find($id);

        if (!$movement) {
            return $this->error([], 'Movement not found', 200);
        }

        $userMovement = UserMovement::where('user_id', $user->id)
            ->where('movement_id', $id)
            ->first();

        if ($userMovement) {
            $userMovement->delete();
            return $this->success([], 'User removed from movement successfully!', 200);

            $userMovement->notify(new UserNotification(
                subject: 'Removed from movement',
                message: 'You have been removed from a movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));
        } else {
            $userMovement = UserMovement::create([
                'user_id' => $user->id,
                'movement_id' => $id,
            ]);

            return $this->success([
                'user' => $user,
                'movement' => $movement,
            ], 'User joined movement successfully!', 200);

            $userMovement->notify(new UserNotification(
                subject: 'Joined movement',
                message: 'You have joined a movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));
        }
    }

    public function leaveMovement(int $id)
    {
        try {

            $user = Auth::user();

            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $data = UserMovement::where('user_id', $user->id)->where('movement_id', $id)->first();

            if (!$data) {
                return $this->error([], 'Movement not found', 200);
            }

            $data->delete();

            $user->notify(new UserNotification(
                subject: 'Leave movement',
                message: 'You have Leave the movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            return $this->success($data, 'Leave movement successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function joinMultipleMovement(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return $this->error([], 'No movements are selected', 422);
        }

        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $movements = Movement::whereIn('id', $ids)->get();

        if ($movements->isEmpty()) {
            return $this->error([], 'Movements not found', 200);
        }

        $joinedMovements = [];

        foreach ($movements as $movement) {
            $userMovement = UserMovement::where('user_id', $user->id)
                ->where('movement_id', $movement->id)
                ->first();

            if (!$userMovement) {
                $userMovement = UserMovement::create([
                    'user_id' => $user->id,
                    'movement_id' => $movement->id,
                ]);

                $joinedMovements[] = $movement;
            }
        }

        if (count($joinedMovements) > 0) {
            foreach ($joinedMovements as $movement) {
                $user->notify(new UserNotification(
                    message: 'You have joined a movement: ' . $movement->name,
                    channels: ['database'],
                    type: NotificationType::SUCCESS,
                ));
            }

            return $this->success([
                'user' => $user,
                'movements' => $joinedMovements,
            ], 'User joined movements successfully!', 200);
        }

        return $this->success([], 'User is already part of these movements', 200);
    }
}
