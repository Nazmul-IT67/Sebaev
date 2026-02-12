<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\MovementResponseVideo;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MovementResponseVideoController extends Controller
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

        $user = auth()->user();
        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        if ($request->hasFile('file_url')) {
            $file      = $request->file('file_url');
            $file_name = time() . '_' . $file->getClientOriginalName();
            $storeFile = $file->storeAs("movements/response", $file_name, "gcs");
            $bucket    = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";
        }

        $data = [
            'user_id'     => $user->id,
            'movement_id' => $id,
            'file_url'    => $url,
        ];

        $response = MovementResponseVideo::create($data);

        return $this->success($response, 'Response Video uploaded successfully', 200);
    }

    public function getVideo($id)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = MovementResponseVideo::with('user:id,name,avatar')->where('movement_id', $id)->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Response Video not found', 200);
        }

        return $this->success($data, 'Response Video fetch Successful!', 200);
    }

    public function delete($id)
    {
        $user = auth()->user();
        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = MovementResponseVideo::where('id', $id)->first();

        if (! $data) {
            return $this->error([], 'Response Video not found', 200);
        }

        // if ($data->user_id != $user->id) {
        //     return $this->error([], 'Unauthorized', 401);
        // }

        if ($data->file_url) {
            $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
            $prefix = "https://storage.googleapis.com/{$bucket}/";
            $path   = str_replace($prefix, '', $data->file_url);

            // Delete video file from GCS
            if (Storage::disk('gcs')->exists($path)) {
                Storage::disk('gcs')->delete($path);
            }
        }

        $data->delete();

        return $this->success([], 'Response Video deleted successfully', 200);
    }

}
