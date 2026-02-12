<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Comment;
use App\Models\Movement;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Enum\NotificationType;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    use ApiResponse;
    public function store(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $validator = Validator::make($request->all(), [
                'comment' => 'required|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }


            $post = Post::find($id);
            if (!$post) {
                return $this->error([], 'Your post not found', 200);
            }

            $movement = Movement::find($post->movement_id);

            $data = Comment::create([
                'user_id' => $user->id,
                'movement_id' => $movement->id,
                'post_id' => $id,
                'comment' => $request->comment,
            ]);

            $user->notify(new UserNotification(
                subject: 'New Comment',
                message: 'You have a new comment',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            $movement->user->notify(new UserNotification(
                subject: 'New Comment',
                message: 'Your post has a new comment',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            return $this->success($data, 'Comment successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
