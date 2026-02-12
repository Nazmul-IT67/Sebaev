<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovementResponseVideo;
use App\Models\MovementResponseVideoComment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovementResponseVideoCommentController extends Controller
{
    use ApiResponse;
    public function index(int $videoId)
    {
        $video = MovementResponseVideo::find($videoId);
        if (!$video) {
            return $this->error([], 'Video not found', 200);
        }

        $comments = MovementResponseVideoComment::where('m_r_video_id', $videoId)
            ->with([
                'user:id,name,avatar',
                'replies' => function ($query) {
                    $query->with('user:id,name,avatar')
                        ->latest();
                }
            ])
            ->whereNull('reply_id')
            ->latest()
            ->get();

        return $this->success($comments, 'Comments retrieved successfully');
    }

    public function store(Request $request, int $videoId)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $video = MovementResponseVideo::find($videoId);
        if (!$video) {
            return $this->error([], 'Video not found', 200);
        }

        $comment = MovementResponseVideoComment::create([
            'user_id' => auth()->id(),
            'movement_id' => $video->movement_id,
            'm_r_video_id' => $video->id,
            'comment' => $request->comment,
        ]);

        return $this->success($comment, 'Comment posted successfully', 201);
    }

    public function reply(Request $request, int $commentId)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $parentComment = MovementResponseVideoComment::find($commentId);
        if (!$parentComment) {
            return $this->error([], 'Comment not found', 200);
        }

        $reply = MovementResponseVideoComment::create([
            'user_id' => auth()->id(),
            'movement_id' => $parentComment->movement_id,
            'm_r_video_id' => $parentComment->m_r_video_id,
            'reply_id' => $parentComment->id,
            'comment' => $request->comment,
        ]);

        return $this->success($reply, 'Reply posted successfully', 201);
    }

    public function update(Request $request, int $commentId)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $comment = MovementResponseVideoComment::find($commentId);
        if (!$comment) {
            return $this->error([], 'Comment not found', 200);
        }

        if ($comment->user_id !== auth()->id()) {
            return $this->error([], 'Unauthorized to update this comment', 403);
        }

        $comment->update(['comment' => $request->comment]);

        return $this->success($comment, 'Comment updated successfully');
    }

    public function destroy(int $commentId)
    {
        $comment = MovementResponseVideoComment::find($commentId);
        if (!$comment) {
            return $this->error([], 'Comment not found', 200);
        }

        if ($comment->user_id !== auth()->id()) {
            return $this->error([], 'Unauthorized to delete this comment', 403);
        }

        $comment->delete();

        return $this->success([], 'Comment deleted successfully');
    }
}
