<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\Post;
use App\Models\ReportPost;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ReportController extends Controller {
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ReportPost::latest()->get();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('reason', 'LIKE', "%$searchTerm%");
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user_name', function ($data) {
                    return $data->user->name;
                })
                ->addColumn('email', function ($data) {
                    return $data->user->email;
                })
                ->addColumn('post_name', function ($data) {
                    return $data->post->title;
                })
                ->addColumn('action', function ($data) {
                    return '
                     <a href="' . route('admin.report.show', ['id' => $data->id]) . '" type="button" class="btn btn-primary text-white btn-sm" title="Edit">
                              <i class="bi bi-eye"></i>

                              </a>
                    <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm(' . $data->id . ')">
                    <i class="bi bi-trash"></i>
                    </button>';
                })
                ->rawColumns(['action'])
                ->make();
        }
        return view('backend.layouts.report.index');
    }




    public function destroy($id)
    {
        try {
            ReportPost::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!'
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }


    public function show($id){
        $report = ReportPost::with(['user:id,name,role,email,language','post','post.category','post.subCategory','post.user','movement','movement.user','movement.category','movement.subCategory'])->findOrFail($id);
        // return $report;
        return view('backend.layouts.report.show', compact('report'));
    }

    public function destroy_movement($id){
        $movement=Movement::findOrFail($id);
        try {
            $movement->delete();
            return to_route('admin.report.index')->with(
                't-success' , 'Movement deleted successfully!'
            );
        } catch (\Exception $exception) {
            return response()->json(['t-error' => $exception->getMessage()], 500);
        }

    }
    public function destroy_post($id){
        $post=Post::findOrFail($id);
        try {
            $post->delete();
            return to_route('admin.report.index')->with(
                't-success', 'Post deleted successfully!'
            );
        } catch (\Exception $exception) {
            return response()->json(['t-error' => $exception->getMessage()], 500);
        }

    }

}
