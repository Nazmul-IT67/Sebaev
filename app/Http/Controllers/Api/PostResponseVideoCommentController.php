<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostResponseVideoComment;
use App\Models\VideoComment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostResponseVideoCommentController extends Controller
{
    use ApiResponse;

    public function index(int $videoId)
    {
        $video = VideoComment::find($videoId);
        if (!$video) {
            return $this->error([], 'Video not found', 200);
        }

        $comments = PostResponseVideoComment::where('video_comment_id', $videoId)
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

        return $this->success($comments, 'Comments retrieved successfully', 200);
    }

    public function store(Request $request, int $videoId)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $video = VideoComment::find($videoId);
        if (!$video) {
            return $this->error([], 'Video not found', 200);
        }

        $comment = $video->parentComment()->create([
            'user_id' => auth()->id(),
            'post_id' => $video->post_id,
            'video_comment_id' => $video->id,
            'reply_id' => null,
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

        $comment = PostResponseVideoComment::find($commentId);
        if (!$comment) {
            return $this->error([], 'Comment not found', 200);
        }

        // Validate that the comment is a reply to a video comment
        if (!$comment->videoComment) {
            return $this->error([], 'Cannot reply to this comment', 400);
        }

        // Create the reply
        $reply = $comment->replies()->create([
            'user_id' => auth()->id(),
            'post_id' => $comment->post_id,
            'video_comment_id' => $comment->video_comment_id,
            'reply_id' => $comment->id,
            'comment' => $request->comment,
        ]);

        return $this->success($reply, 'Reply posted successfully', 201);
    }

    public function destroy(int $commentId)
    {
        $comment = PostResponseVideoComment::find($commentId);
        if (!$comment) {
            return $this->error([], 'Comment not found', 200);
        }

        if ($comment->user_id !== auth()->id()) {
            return $this->error([], 'Unauthorized to delete this comment', 403);
        }

        $comment->delete();

        return $this->success([], 'Comment deleted successfully', 200);
    }

    public function update(Request $request, int $commentId)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $comment = PostResponseVideoComment::find($commentId);
        if (!$comment) {
            return $this->error([], 'Comment not found', 200);
        }

        if ($comment->user_id !== auth()->id()) {
            return $this->error([], 'Unauthorized to update this comment', 403);
        }

        $comment->update(['comment' => $request->comment]);

        return $this->success($comment, 'Comment updated successfully', 200);
    }
}
