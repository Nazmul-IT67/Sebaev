<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\VideoComment;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class VideoCommentController extends Controller
{
    use ApiResponse;

    public function store(Request $request, $id)
    {
        $size = Cms::latest('id')->first();

        $validator = Validator::make($request->all(), [
            'file_url' => 'required|mimes:mp4,mov,ogg|max:' . $size->size,
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        if ($request->duration > $size->duration) {
            return $this->error([], 'Video duration must be less than ' . $size->duration . ' seconds', 422);
        }

        $user = auth()->user();
        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $file      = $request->file('file_url');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $storeFile = $file->storeAs("comments", $file_name, "gcs");
        $bucket    = env('GOOGLE_CLOUD_STORAGE_BUCKET');
        $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";
        $data      = [
            'user_id'  => $user->id,
            'post_id'  => $id,
            'file_url' => $url,
        ];

        $comment = VideoComment::create($data);

        if (! $comment) {
            return $this->error([], 'Video Comment not created', 200);
        }

        return $this->success($comment, 'Video Comment successfully', 200);
    }

    public function allVideoComment($id)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = VideoComment::with('user')->where('post_id', $id)->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Data not found', 200);
        }

        return $this->success($data, 'Video Comment successfully', 200);
    }

    public function deleteVideoComment($id)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $comment = VideoComment::find($id);

        if (! $comment) {
            return $this->error([], 'Data not found', 200);
        }

        if ($comment->file_url) {
            $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            $prefix = "https://storage.googleapis.com/{$bucket}/";
            $path   = str_replace($prefix, '', $comment->file_url);

            // Delete the file from GCS
            Storage::disk('gcs')->delete($path);
        }

        $comment->delete();

        return $this->success([], 'Video Comment deleted successfully', 200);
    }
}
