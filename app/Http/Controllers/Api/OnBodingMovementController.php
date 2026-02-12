<?php

namespace App\Http\Controllers\Api;

use App\Models\Movement;
use App\Traits\ApiResponse;
use App\Models\UserInterest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OnBodingMovementController extends Controller
{
    use ApiResponse;
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->query(), [
            'sub_category_id' => 'required|array',
            'sub_category_id.*' => 'nullable|exists:sub_categories,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }
        $userId = $user->id;

        // $userCountry = $user->information->country ?? null;


        $movements = Movement::withCount('userMovements')->with('user:id,name,avatar')->whereIn('sub_category_id', $request->sub_category_id)->get()->makeHidden(['video', 'avatar', 'description', 'pdf']);


        if ($movements->isEmpty()) {
            $movements = Movement::withCount('userMovements')->with('user:id,name,avatar')->latest()->get()->makeHidden(['video', 'avatar', 'description', 'pdf']);
            
            $movements = $movements->map(function ($movement) use ($userId) {
                $movement->is_joined = $movement->userMovements->contains($userId); // Add bookmark flag
                unset($movement->userMovements);
                return $movement;
            });
    
            return $this->success($movements, 'Movements fetched successfully!', 200);
        }

        $movements = $movements->map(function ($movement) use ($userId) {
            $movement->is_joined = $movement->userMovements->contains($userId); // Add bookmark flag
            unset($movement->userMovements);
            return $movement;
        });

        return $this->success($movements, 'Movements fetched successfully!', 200);
    }
}
