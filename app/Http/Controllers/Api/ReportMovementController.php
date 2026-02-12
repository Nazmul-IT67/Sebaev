<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportPost;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReportMovementController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'movement_id' => 'required|integer|exists:movements,id',
                'post_id'     => 'required|integer|exists:posts,id',
                'reason'      => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }

            $user = Auth::user();

            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $report = ReportPost::create([
                'user_id'     => $user->id,
                'post_id'     => $request->post_id,
                'movement_id' => $request->movement_id,
                'reason'      => $request->reason,
            ]);

            return $this->success($report, 'Report saved successfully', 200);
        } catch (\Throwable $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
