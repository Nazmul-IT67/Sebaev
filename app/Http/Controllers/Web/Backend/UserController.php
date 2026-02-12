<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::with('country')->latest();
            if (! empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where(function ($query) use ($searchTerm) {
                    $query->where('name', 'LIKE', "%$searchTerm%")
                        ->orWhere('email', 'LIKE', "%$searchTerm%");
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

                // Action buttons
                ->addColumn('action', function ($data) {
                    return '
                        <a href="'.route('admin.users.edit', $data->id).'" class="btn btn-primary btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="'.route('admin.users.show', $data->id).'" class="btn btn-info btn-sm" title="View"><i class="bi bi-eye"></i></a>
                        <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="showDeleteConfirm('.$data->id.')">
                        <i class="bi bi-trash"></i></button>
                    ';
                })

                ->rawColumns(['status', 'action'])
                ->make();
        }

        return view('backend.layouts.user.index');
    }

    public function show($id)
    {
        $user = User::with('subCategories')->findOrFail($id);

        return view('backend.layouts.user.show', compact('user'));
    }

    public function create()
    {
        $roles = Role::all();
        $countries = Country::all();
        $interests = SubCategory::all();

        return view('backend.layouts.user.create', compact('countries', 'interests', 'roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->merge([
                'password_confirmation' => $request->password,
            ]);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'country_id' => 'required',
                'date_of_birth' => 'nullable|date',
                'company_vat_id' => 'nullable|string',
                'company_website' => 'nullable|url',
                'gender' => 'nullable|in:male,female,other',
                'user_type' => 'nullable|in:individual,company',
                'sub_category_ids.*' => 'exists:sub_categories,id',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'role' => 'nullable|in:super_admin,admin,moderator,support',
            ]);

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->password = bcrypt($request->input('password'));
            $user->country_id = $request->input('country_id');
            $user->role = $request->input('role');
            $user->gender = $request->input('gender');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->user_type = $request->input('user_type');
            $user->company_vat_id = $request->input('company_vat_id');
            $user->company_website = $request->input('company_website');
            $user->active_at = now();
            $user->agree_to_terms = $request->has('agree_to_terms');

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/users'), $fileName);
                $user->avatar = $fileName;
            }

            $user->save();

            if ($request->has('sub_category_ids')) {
                $user->subCategories()->sync($request->input('sub_category_ids'));
            }

            if ($request->role_id) {
                $role = Role::findById($request->role_id);
                $user->syncRoles($role);
            }

            return redirect()->route('admin.users.index')->with('t-success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', 'Error: '.$e->getMessage());
        }
    }

    public function edit($id)
    {
        $roles = Role::all();
        $countries = Country::all();
        $interests = SubCategory::all();
        $user = User::with('subCategories')->findOrFail($id);

        return view('backend.layouts.user.edit', compact('user', 'countries', 'interests', 'roles'));
    }

    public function update(Request $request)
    {
        try {
            $id = $request->input('id');
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
                'country_id' => 'required',
                'date_of_birth' => 'nullable|date',
                'company_website' => 'nullable|url',
                'company_vat_id' => 'nullable|string',
                'gender' => 'nullable|in:male,female,other',
                'user_type' => 'nullable|in:individual,organization',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'role' => 'nullable|in:super_admin,admin,moderator,support',
            ]);

            $user = User::find($id);
            if (! $user) {
                return redirect()->back()->with('error', 'User not found.');
            }
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->country_id = $request->input('country_id');
            $user->gender = $request->input('gender');
            $user->date_of_birth = $request->input('date_of_birth');
            $user->user_type = $request->input('user_type');
            $user->company_vat_id = $request->input('company_vat_id');
            $user->company_website = $request->input('company_website');
            $user->active_at = now();
            $user->agree_to_terms = $request->has('agree_to_terms');

            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    $oldAvatarPath = public_path($user->avatar);
                    if (file_exists($oldAvatarPath)) {
                        @unlink($oldAvatarPath);
                    }
                }
                $file = $request->file('avatar');
                $fileName = time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $file->move(public_path('uploads/users'), $fileName);
                $user->avatar = 'uploads/users/'.$fileName;
            }

            $user->save();

            if ($request->has('sub_category_ids')) {
                $user->subCategories()->sync($request->input('sub_category_ids'));
            }

            if ($request->role_id) {
                $role = Role::findById($request->role_id);
                $user->syncRoles($role);
            }

            return redirect()->route('admin.users.index')->with('t-success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('t-error', 'Error: '.$e->getMessage());
        }
    }

    public function status(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        if ($user->status == 'inactive') {
            $user->status = 'active';
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $user,
            ]);
        } else {
            $user->status = 'inactive';
            $user->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $user,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            't-success' => true,
            'message' => 'Deleted successfully.',
        ]);
    }
}
