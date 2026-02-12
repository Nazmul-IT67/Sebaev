<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Banner::latest()->get();
            // if (!empty($request->input('search.value'))) {
            //     $searchTerm = $request->input('search.value');
            //     $data->where('status', 'LIKE', "%$searchTerm%");
            // }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    $url = asset($data->image);
                    $image       = "<img src='$url' width='50' height='50'>";
                    return $image;
                })
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                    if ($data->status == "active") {
                        $status .= "checked";
                    }
                    $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return ' <a href="' . route('admin.banner.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary text-white btn-sm" title="Edit">
                              <i class="bi bi-pencil"></i>
                              </a>
                              <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm(' . $data->id . ')">
                              <i class="bi bi-trash"></i>
                              </button>';
                })
                ->rawColumns(['status', 'action', 'image'])
                ->make();
        }
        return view('backend.layouts.banner.index');
    }

    public function create()
    {
        return view('backend.layouts.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:3080',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = uploadImage($image, 'banner');
        }

        try {
            $product = new Banner();
            $product->image = $imageName;
            $product->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin.banner.index')->with('t-success', 'Banner created successfully');
    }

    public function edit($id)
    {
        $data = Banner::findOrFail($id);
        return view('backend.layouts.banner.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:3080',
        ]);

        $data = Banner::find($id);

        if ($request->hasFile('image')) {
            if ($data->image) {
                $previousImagePath = public_path($data->image);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            $image = $request->file('image');
            $imageName = uploadImage($image, 'banner');
        } else {

            $imageName = $data->image;
        }

        try {
            $product = Banner::findOrFail($id);
            $product->image = $imageName;
            $product->save();

            return redirect()->route('admin.banner.index')->with('t-success', 'Banner updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $data = Banner::find($id);
        if ($data->image) {
            $previousImagePath = public_path($data->image);
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }
        $data->delete();
        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully!'
        ]);
    }

    public function status($id)
    {
        $data = Banner::findOrFail($id);
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
}