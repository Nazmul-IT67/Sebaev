<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MiniGoalView;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class MiniGoalViewController extends Controller
{
    use ApiResponse;
    public function store(Request $request, int $postId)
    {
        $user = $request->user();

        if (!$user) {
            return $this->error([], 'User not authenticated', 401);
        }

        $post = Post::find($postId);

        if (!$post) {
            return $this->error([], 'Post (Mini Goal) not found', 200);
        }

        $ipAddress = $request->ip();

        $existingView = MiniGoalView::where('user_id', $user->id)
            ->where('post_id', $postId)
            ->where('ip_address', $ipAddress)
            ->first();

        if (!$existingView) {
            MiniGoalView::create([
                'user_id' => $user->id,
                'post_id' => $postId,
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

        return $this->success([], $message, 200);
    }
}
