<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Post;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    use ApiResponse;
    public function create(Request $request, $id)
    {

        try {
            $user = Auth::user();

            if (! $user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $size = Cms::latest('id')->first();

            $validator = Validator::make($request->all(), [
                'title'       => 'required|string',
                'description' => 'required|string',
                'video'       => 'required|mimes:mp4,mov,ogg|max:' . $size->size,
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }

            if ($request->duration > $size->duration) {
                return $this->error([], 'Video duration must be less than ' . $size->duration . ' seconds', 422);
            }

            $file      = $request->file('video');
            $file_name = time() . '_' . $file->getClientOriginalName();
            $storeFile = $file->storeAs("mini_goal", $file_name, "gcs");
            $bucket    = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";

            $post = Post::create([
                'user_id'         => $user->id,
                'category_id'     => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'movement_id'     => $id,
                'title'           => $request->title,
                'description'     => $request->description,
                'video'           => $url,
            ]);

            return $this->success(
                $post,
                'Data saved successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function EditPost(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (! $user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $post = Post::find($id);
            if (! $post) {
                return $this->error([], 'Post not found', 200);
            }
            $size      = Cms::latest('id')->first();
            $validator = Validator::make($request->all(), [
                'title'       => 'required|string',
                'description' => 'required|string',
                'video'       => 'required|mimes:mp4,mov,ogg|max:' . $size->size,
            ]);

            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }

            if ($user->id !== $post->user_id) {
                return $this->error([], "You are not authenticate", 401);
            }

            if ($request->duration > $size->duration) {
                return $this->error([], 'Video duration must be less than ' . $size->duration . ' seconds', 422);
            }

            if ($request->video) {
                if ($post->video) {
                    $bucket  = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                    $prefix  = "https://storage.googleapis.com/{$bucket}/";
                    $oldPath = str_replace($prefix, '', $post->video);
                    if (Storage::disk('gcs')->exists($oldPath)) {
                        Storage::disk('gcs')->delete($oldPath);
                    }
                }

                // Upload new video
                $file      = $request->file('video');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $storeFile = $file->storeAs("mini_goal", $file_name, "gcs");
                $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";
            } else {
                $url = $post->video;
            }

            $post->update([
                'user_id'         => $user->id,
                'category_id'     => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'movement_id'     => $request->movement_id,
                'title'           => $request->title,
                'description'     => $request->description,
                'video'           => $url,
            ]);

            return $this->success($post, 'Data updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function DeletePost(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $data = Post::find($id);

            if (! $data) {
                return $this->error([], 'Data not found', 200);
            }

            if ($user->id !== $data->user_id) {
                return $this->error([], "You are not authenticate", 401);
            }

            if ($data->video) {
                $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $prefix = "https://storage.googleapis.com/{$bucket}/";
                $path   = str_replace($prefix, '', $data->video);

                // Delete video file from GCS
                if (Storage::disk('gcs')->exists($path)) {
                    Storage::disk('gcs')->delete($path);
                }
            }

            $data->delete();

            return $this->success([], 'Data deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function allPost(Request $request, $id)
    {
        $user = Auth::user();
        $data = Post::where('movement_id', $id)->withCount('comments', 'miniGoalViews')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'your post not found', 200);
        }

        return $this->success($data, 'your post fetch Successful!', 200);
    }

    public function singlePost(Request $request, $id)
    {
        $user = Auth::user();

        $data = Post::with('user', 'movement:id,title', 'movement.userMovements.user:id,name,avatar')->withCount('video_comments', 'miniGoalViews')->find($id);

        if (! $data) {
            return $this->error([], 'Mini Goal Not Found', 200);
        }

        // Log the client visit
        $ipAddress = $request->ip();
        $date      = now()->toDateString();

        // Check if the entry already exists
        $existingVisit = DB::table('post_views')
            ->where('post_id', $data->id)
            ->where('ip', ip2long($ipAddress))
            ->exists();

        if (! $existingVisit) {
            // Insert into client_visits table
            DB::table('post_views')->insert([
                'post_id'    => $data->id,
                'ip'         => ip2long($ipAddress),
                'date'       => $date,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($user) {
            if ($data->user_id == $user->id) {
                $data->setAttribute('is_my_movement', true);
            } else {
                $data->setAttribute('is_my_movement', false);
            }
        } else {
            $data->setAttribute('is_my_movement', false);
        }

        return $this->success($data, 'Mini Goal fetch Successful!', 200);
    }
}
