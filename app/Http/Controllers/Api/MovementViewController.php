<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\MovementView;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovementViewController extends Controller
{
    use ApiResponse;
    public function store(Request $request, int $movementId)
    {

        $userId = $request->user()->id;

        if (!$userId) {
            return $this->error([], 'User not authenticated', 401);
        }

        $movement = Movement::findOrFail($movementId);

        if (!$movement) {
            return $this->error([], 'Movement not found', 200);
        }

        $ipAddress = $request->ip();

        $existingView = MovementView::where('user_id', $userId)
            ->where('movement_id', $movementId)
            ->where('ip_address', $ipAddress)
            ->first();

        if (!$existingView) {
            MovementView::create([
                'user_id' => $userId,
                'movement_id' => $movementId,
                'ip_address' => $ipAddress,
                'viewed_at' => now(),
            ]);
            $message = 'View recorded successfully';
        } else {
            $existingView->update([
                'viewed_at' => now(),
            ]);
            $message = 'View updated successfully';
        }
        return $this->success($movement, $message, 200);
    }
}
