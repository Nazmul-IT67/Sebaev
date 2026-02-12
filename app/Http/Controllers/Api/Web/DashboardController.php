<?php
namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\MiniGoalView;
use App\Models\Post;
use App\Models\VideoComment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    use ApiResponse;

    public function Engagement(Request $request)
    {
        $user       = auth()->user();
        $dateFilter = $request->query('date');

        // Determine date range
        $startDate = null;
        $endDate   = now();

        switch ($dateFilter) {
            case 'today':
                $startDate = now()->toDateString();
                break;
            case 'weekly':
                $startDate = now()->subDays(7)->toDateString();
                break;
            case 'month':
                $startDate = now()->subDays(30)->toDateString();
                break;
        }

        // Base post query
        $basePostQuery = Post::where('user_id', $user->id);

        // Filtered post query
        $filteredPostQuery = clone $basePostQuery;
        if ($startDate) {
            $filteredPostQuery = $filteredPostQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        // // Check if any post exists
        // if (! $basePostQuery->exists()) {
        //     return $this->error([], 'No posts found', 200);
        // }

        // Posts
        $totalPost = $filteredPostQuery->count();

        $currentPeriodPost  = (clone $basePostQuery)->whereDate('created_at', now()->toDateString())->count();
        $previousPeriodPost = (clone $basePostQuery)->whereDate('created_at', now()->subDay()->toDateString())->count();

        $growth = $currentPeriodPost - $previousPeriodPost;
        $status = $growth > 0 ? 'increase' : ($growth < 0 ? 'decrease' : 'no change');

        // Post IDs
        $postIds = $basePostQuery->pluck('id');

        // Comments
        $commentQuery = VideoComment::whereIn('post_id', $postIds);
        if ($startDate) {
            $commentQuery = $commentQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalComment = $commentQuery->count();

        $currentPeriodComment = VideoComment::whereIn('post_id', $postIds)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $previousPeriodComment = VideoComment::whereIn('post_id', $postIds)
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->count();

        $growth_comment = $currentPeriodComment - $previousPeriodComment;
        $status_comment = $growth_comment > 0 ? 'increase' : ($growth_comment < 0 ? 'decrease' : 'no change');

        // Views
        $viewQuery = MiniGoalView::whereIn('post_id', $postIds);
        if ($startDate) {
            $viewQuery = $viewQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalpostviews = $viewQuery->count();

        $currentPeriodView = MiniGoalView::whereIn('post_id', $postIds)
            ->whereDate('created_at', now()->toDateString())
            ->count();
        $previousPeriodView = MiniGoalView::whereIn('post_id', $postIds)
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->count();

        $growth_view = $currentPeriodView - $previousPeriodView;
        $status_view = $growth_view > 0 ? 'increase' : ($growth_view < 0 ? 'decrease' : 'no change');

        // Final response
        $post = [
            "post"    => [
                'total_mini_goals' => $totalPost,
                'growth'           => $growth,
                'status'           => $status,
            ],
            "comment" => [
                'total_comment'  => $totalComment,
                'growth_comment' => $growth_comment,
                'status_comment' => $status_comment,
            ],
            "view"    => [
                'total_postviews' => $totalpostviews,
                'growth_view'     => $growth_view,
                'status_view'     => $status_view,
            ],
        ];

        return $this->success($post, 'Your posts fetched successfully!', 200);
    }

}
