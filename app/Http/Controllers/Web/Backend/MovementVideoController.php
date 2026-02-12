<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\MovementResponseVideo;

class MovementVideoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MovementResponseVideo::with(['movement', 'user'])->latest();
            if (! empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where(function ($query) use ($searchTerm) {
                    $query->whereHas('user', function ($q) use ($searchTerm) {
                        $q->where('user', 'LIKE', "%{$searchTerm}%");
                    })
                        ->orWhereHas('movement', function ($q) use ($searchTerm) {
                            $q->where('title', 'LIKE', "%{$searchTerm}%");
                        });
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '';
                })
                ->make(true);
        }

        return view('backend.layouts.movement_video.index');
    }

    public function status(int $id): JsonResponse
    {
        $video = MovementResponseVideo::findOrFail($id);
        if ($video->status == 'active') {
            $video->status = 'closed';
            $video->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $video,
            ]);
        } else {
            $video->status = 'active';
            $video->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $video,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $video = MovementResponseVideo::findOrFail($id);
        $video->delete();

        return response()->json([
            't-success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }
}
