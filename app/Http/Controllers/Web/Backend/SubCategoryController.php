<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\DataTables;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubCategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = SubCategory::with('category')->get();
            if (! empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('en_subcategory_name', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('en_category_name', function ($data) {
                    return $data->category->en_category_name;
                })
                ->addColumn('sub_category', function ($data) {
                    return $data->name;
                })
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert('.$data->id.')" type="checkbox" class="form-check-input" id="customSwitch'.$data->id.'" getAreaid="'.$data->id.'" name="status"';
                    if ($data->status == 'active') {
                        $status .= 'checked';
                    }
                    $status .= '><label for="customSwitch'.$data->id.'" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return ' <a href="'.route('admin.subcategory.edit', ['id' => $data->id]).'" type="button" class="btn btn-primary text-white btn-sm" title="Edit">
                              <i class="bi bi-pencil"></i>
                              </a>
                              <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm('.$data->id.')">
                              <i class="bi bi-trash"></i>
                              </button>';
                })
                ->rawColumns(['name',  'en_category_name', 'status', 'action'])
                ->make();
        }

        return view('backend.layouts.subcategory.index');
    }

    public function create()
    {
        $categories = Category::all();

        return view('backend.layouts.subcategory.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'en_subcategory_name' => 'required|string',
            ]);

            $data = new SubCategory;
            $data->category_id = $request->category_id;
            $data->en_subcategory_name = $request->en_subcategory_name;
            $data->sp_subcategory_name = $request->sp_subcategory_name;
            $data->fr_subcategory_name = $request->fr_subcategory_name;
            $data->ca_subcategory_name = $request->ca_subcategory_name;
            $data->save();

            return redirect()->back()->with('t-success', 'Product created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', 'Error: '.$e->getMessage());
        }
    }

    public function edit(int $id): View|RedirectResponse
    {
        $data = SubCategory::with('category')->findOrFail($id);
        $categories = Category::all();

        return view('backend.layouts.subcategory.edit', compact('data', 'categories'));

        return redirect()->route('admin.subcategory.index');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'en_subcategory_name' => 'required|string',
        ]);

        $data = SubCategory::find($id);

        try {
            $data = SubCategory::findOrFail($id);
            $data->category_id = $request->category_id;
            $data->en_subcategory_name = $request->en_subcategory_name;
            $data->sp_subcategory_name = $request->sp_subcategory_name;
            $data->fr_subcategory_name = $request->fr_subcategory_name;
            $data->ca_subcategory_name = $request->ca_subcategory_name;
            $data->save();

            return redirect()->back()->with('t-success', 'Product updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function status(int $id): JsonResponse
    {
        $data = SubCategory::findOrFail($id);
        if ($data->status == 'inactive') {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        }
    }

    // public function destroy(int $id): JsonResponse
    // {
    //     $page = SubCategory::findOrFail($id);
    //     $this->authorize('delete_content_permanently');
    //     $page->delete();
    //     return response()->json(['t-success' => true, 'message'   => 'Deleted successfully.',]);
    // }

    public function destroy(int $id): JsonResponse
    {
        try {
            $page = SubCategory::findOrFail($id);
            $this->authorize('delete_content_permanently');

            $page->delete();
            return response()->json(['t-success' => true, 'message' => 'Deleted successfully.',], 200);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                't-success' => false,
                'message' => 'You do not have permission to permanently delete this item.',
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                't-success' => false,
                'message' => 'Something went wrong. Please try again.',
            ], 500);
        }
    }
}
