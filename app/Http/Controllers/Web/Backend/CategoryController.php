<?php
namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest()->get();
            if (! empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('en_category_name', 'LIKE', "%$searchTerm%");
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                    if ($data->status == "active") {
                        $status .= "checked";
                    }
                    $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })

                ->addColumn('category_status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showCategoryStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="category_status"';
                    if ($data->category_status == "active") {
                        $status .= "checked";
                    }
                    $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })

                ->addColumn('action', function ($data) {
                    return ' <a href="' . route('admin.categories.edit', $data->id) . '" class="text-white edit btn btn-primary btn-sm" title="Edit"> <i class="bi bi-pencil"></i></a>
                    <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm(' . $data->id . ')">
                    <i class="bi bi-trash"></i>
                    </button>';
                })
                ->rawColumns(['status', 'category_status', 'action'])
                ->make();
        }
        return view('backend.layouts.category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'en_category_name' => 'required|string|max:255',
            'sp_category_name' => 'required|string|max:255',
            'fr_category_name' => 'required|string|max:255',
            'ca_category_name' => 'required|string|max:255',
        ]);

        try {
            Category::create([
                'en_category_name' => $request->en_category_name,
                'sp_category_name' => $request->sp_category_name,
                'fr_category_name' => $request->fr_category_name,
                'ca_category_name' => $request->ca_category_name,
                // 'slug' => generateUniqueSlug($request->name, 'categories'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = Category::find($id);

        return view('backend.layouts.category.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'en_category_name' => 'nullable|string|max:255',
            'sp_category_name' => 'nullable|string|max:255',
            'fr_category_name' => 'nullable|string|max:255',
            'ca_category_name' => 'nullable|string|max:255',
        ]);

        try {
            $category = Category::findOrFail($id);

            $category->update([
                'en_category_name' => $request->en_category_name,
                'sp_category_name' => $request->sp_category_name,
                'fr_category_name' => $request->fr_category_name,
                'ca_category_name' => $request->ca_category_name,
            ]);

            return redirect()->route('admin.categories.index')->with('t-success', 'Category updated successfully');
        } catch (\Exception $exception) {
            return redirect()->back()->with('t-error', 'Error: ' . $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Category::destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully!',
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function status(int $id)
    {
        $data = Category::findOrFail($id);
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        }
    }

    public function categoryStatus(Request $request, $id)
    {
        $data = Category::findOrFail($id);

        if ($data->category_status == 'active') {
            $data->category_status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        } else {
            Category::where('category_status', 'active')->update(['category_status' => 'inactive']);
            $data->category_status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        }
    }
}
