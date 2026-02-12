<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsFeedMovementController extends Controller
{
    use ApiResponse;

    // Retrieve Posts From My Joined Movements with pagination
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('title');

        if (!$user) {
            $data = Post::withCount(['miniGoalViews', 'videoComments','postShare'])
                ->with(['user:id,name,avatar,role'])
                ->withExists([
                    'movement as is_my_movement' => function ($query) use ($user) {
                        $query->where('user_id', Auth::id());
                    }
                ])
                ->with(['movement' => function ($query) use ($user) {
                    $query->select(['id', 'user_id', 'title']) // always select necessary fields only
                        ->withExists([
                            'userMovements as is_joined' => function ($query) use ($user) {
                                $query->where('user_id', Auth::id());
                            }
                        ]);
                }])
                ->when($search, function ($query) use ($search) {
                    return $query->where('title', 'like', '%' . $search . '%');
                })
                ->latest()
                ->paginate(10);

            if ($data->isEmpty()) {
                return $this->error([], 'No posts found', 200);
            }

            return $this->success($data, 'Posts fetch Successful!', 200);
        }

        $movements = Movement::whereHas('userMovements', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->pluck('id');

        if ($movements->isEmpty()) {
            $data = Post::withCount(['miniGoalViews', 'videoComments'])
                ->with(['user:id,name,avatar,role'])
                ->withExists([
                    'movement as is_my_movement' => function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    }
                ])
                ->with(['movement' => function ($query) use ($user) {
                    $query->select(['id', 'user_id', 'title']) // always select necessary fields only
                        ->withExists([
                            'userMovements as is_joined' => function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            }
                        ]);
                }])
                ->when($search, function ($query) use ($search) {
                    return $query->where('title', 'like', '%' . $search . '%');
                })
                ->latest()
                ->paginate(15);

            if ($data->isEmpty()) {
                return $this->error([], 'No posts found', 200);
            }

            return $this->success($data, 'Posts fetch Successful!', 200);
        }

        $data = Post::withCount(['miniGoalViews', 'videoComments'])
            ->with(['user:id,name,avatar,role'])
            ->withExists([
                'movement as is_my_movement' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ])
            ->with(['movement' => function ($query) use ($user) {
                $query->select(['id', 'user_id', 'title']) // always select necessary fields only
                    ->withExists([
                        'userMovements as is_joined' => function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        }
                    ]);
            }])
            ->whereIn('movement_id', $movements)
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', '%' . $search . '%');
            })
            ->latest()
            ->paginate(15);

        if ($data->isEmpty()) {
            $data = Post::withCount(['miniGoalViews', 'videoComments'])
                ->with(['user:id,name,avatar,role'])
                ->withExists([
                    'movement as is_my_movement' => function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    }
                ])
                ->with(['movement' => function ($query) use ($user) {
                    $query->select(['id', 'user_id', 'title']) // always select necessary fields only
                        ->withExists([
                            'userMovements as is_joined' => function ($query) use ($user) {
                                $query->where('user_id', $user->id);
                            }
                        ]);
                }])
                ->when($search, function ($query) use ($search) {
                    return $query->where('title', 'like', '%' . $search . '%');
                })
                ->latest()
                ->paginate(15);

            if ($data->isEmpty()) {
                return $this->error([], 'No posts found', 200);
            }

            return $this->success($data, 'Posts fetch Successful!', 200);
        }

        return $this->success($data, 'Posts fetch Successful!', 200);
    }
}
