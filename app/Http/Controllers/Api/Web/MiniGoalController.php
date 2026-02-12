<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MiniGoalController extends Controller
{
    use ApiResponse;
    
    public function getTodayMiniGoal(Request $request)
    {
        $user = auth()->user();

        if(!$user){
            return $this->error([], 'User not found', 200);
        }
        
        $query = Post::withCount(['videoComments', 'postShare'])->where('user_id', $user->id);

        if($request->query('date') == 'today'){
            $query->whereDate('created_at', now()->toDateString());
        }

        if($request->query('date') == 'weekly'){
            $query->whereBetween('created_at', [now()->subDays(7)->toDateString(), now()->toDateString()]);
        }

        if($request->query('date') == 'month'){
            $query->whereBetween('created_at', [now()->subDays(30)->toDateString(), now()->toDateString()]);
        }

        $data = $query->get();

        if($data->isEmpty()){
            return $this->error([], 'No posts found', 200);
        }

        return $this->success($data, 'Posts found', 200);
    }

    public function shareableLink(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        
        $shareUrl = url("/post/{$post->id}");
        
        return $this->success($shareUrl, 'Shareable link', 200);
    }

}
