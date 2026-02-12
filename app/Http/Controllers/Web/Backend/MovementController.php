<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Models\MovementResponseVideo;

class MovementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Movement::with(['category', 'user'])->latest();
            if (! empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where(function ($query) use ($searchTerm) {
                    $query->where('title', 'LIKE', "%$searchTerm%")
                        ->orWhere('category', 'LIKE', "%$searchTerm%");
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert('.$data->id.')" type="checkbox" class="form-check-input" id="customSwitch'.$data->id.'" getAreaid="'.$data->id.'" name="status"';
                    if ($data->status == 'active') {
                        $status .= 'checked';
                    }
                    $status .= '><label for="customSwitch'.$data->id.'" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })

                ->addColumn('created_at', function($row){
                    return $row->created_at ? $row->created_at->format('d M, Y h:i A') : '';
                })
                // Action buttons
                ->addColumn('action', function ($data) {
                    return '
                        <a href="'.route('admin.movements.document', $data->id).'" class="btn btn-info btn-sm" title="View"><i class="bi bi-eye"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm('.$data->id.')">
                        <i class="bi bi-trash"></i></button>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make();
        }

        return view('backend.layouts.movement.index');
    }

    public function show($id)
    {
        $data = MovementResponseVideo::with(['movement', 'user'])->findOrFail($id);
        return view('backend.layouts.movement.show', compact('data'));
    }

    public function status(int $id): JsonResponse
    {
        $movement = Movement::findOrFail($id);
        if ($movement->status == 'active') {
            $movement->status = 'closed';
            $movement->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $movement,
            ]);
        } else {
            $movement->status = 'active';
            $movement->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $movement,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $movement = Movement::findOrFail($id);
        $movement->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
