<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestCategoryMovementController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request)
    {
        $take = $request->input('take');
        $user = Auth::id();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = Category::with([
            'movements' => function ($query) use ($take, $user) {
                $query->select(['id', 'user_id', 'category_id', 'sub_category_id', 'title', 'description', 'country'])
                    ->withCount(['userMovements', 'comments'])
                    ->whereDoesntHave('userMovements', function ($q) use ($user) {
                        $q->where('user_id', $user);
                    })
                    ->with([
                        'userMovements' => function ($q) {
                            $q->with('user:id,avatar')->latest()->take(3);
                        },
                        'user:id,name,avatar'
                    ]);

                // Only apply limit if 'take' is provided
                if (!empty($take)) {
                    $query->take($take);
                }
            }
        ])
            ->where('category_status', 'active')
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Movement not found', 200);
        }

        return $this->success($data, 'Movement fetch successful!', 200);
    }
}
