<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Comment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Comment::with(['movement', 'user', 'post'])->latest();
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

        return view('backend.layouts.comment.index');
    }

}
