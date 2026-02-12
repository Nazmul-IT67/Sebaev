<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationHistoryController extends Controller
{
    use ApiResponse;
    public function donationHistory()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $donationHistory = $user->donationHistories()
            ->with('movement:id,title,user_id')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->toDateString(); // Groups by 'YYYY-MM-DD'
            });

        if ($donationHistory->isEmpty()) {
            return $this->error([], 'No donation history found', 200);
        }

        return $this->success($donationHistory, 'Donation history retrieved successfully', 200);
    }
}
