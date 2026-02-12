<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Models\UserInformation;
use App\Models\UserInterest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OnBodingController extends Controller
{

    use ApiResponse;

    public function store(Request $request)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $role = $user->role;

        // Common validation rules
        $rules = [
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . Auth::id(),
            'country'        => 'required|string|max:255',
            'agree_to_terms' => 'nullable|boolean',
            'language'       => 'nullable|in:english,spanish,french,catalan',
            'avatar'         => 'required|image|mimes:jpeg,png,jpg,svg|max:10240',
        ];

        // Role-based validation
        if ($role === 'individual') {
            $rules['birth']  = 'required|string';
            $rules['gender'] = 'required|in:male,female,other';
        } elseif ($role === 'organization') {
            $rules['cif']     = 'required|string|max:255';
            $rules['website'] = 'required|url|max:255';
        }

        // Validate request
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        // Handle file upload for avatar
        if ($request->hasFile('avatar')) {

            if ($user->avatar) {
                $previousImagePath = public_path($user->avatar);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            $user->avatar = uploadImage($request->file('avatar'), 'profile');
        }

        // Save or update onboarding data
        $onboarding = UserInformation::updateOrCreate(
            ['user_id' => $user->id],
            [
                'cif'     => $request->cif ?? null,
                'website' => $request->website ?? null,
                'birth'   => $request->birth ?? null,
                'gender'  => $request->gender ?? null,
                'country' => $request->country,
            ]
        );

        // Update user record
        $user->update([
            'name'           => $request->name,
            'avatar'         => $user->avatar,
            'language'       => $request->language,
            'agree_to_terms' => $request->agree_to_terms ?? 0,
        ]);

        return $this->success([
            'user'       => $user,
            'onboarding' => $onboarding,
        ], 'Onboarding data saved successfully');
    }

    public function interest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_category_id'   => 'required|array',
            'sub_category_id.*' => 'exists:sub_categories,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $savedInterests = [];

        if ($request->has('sub_category_id')) {
            foreach ($request->sub_category_id as $item) {
                $data = UserInterest::create([
                    'user_id'         => $user->id,
                    'sub_category_id' => $item,
                ]);

                $savedInterests[] = $data;
            }
        }

        return $this->success($savedInterests, 'Interests saved successfully');
    }
}
