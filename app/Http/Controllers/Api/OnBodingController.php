<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInformation;
use App\Models\UserInterest;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OnBodingController extends Controller
{
    use ApiResponse;

    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $role = $user->role;

        // Common validation rules
        $rules = [
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . Auth::id(),
            'country'         => 'required|string|max:255',
            'sub_category_id' => 'nullable|array',
            'sub_category_id.*' => 'exists:sub_categories,id',
            'agree_to_terms' => 'nullable|boolean',
            'language'        => 'nullable|in:english,spanish,french,catalan',
            'avatar'          => 'required|image|mimes:jpeg,png,jpg,svg|max:10240',
        ];

        // Role-based validation
        if ($role === 'individual') {
            $rules['birth']  = 'required|string|before_or_equal:today';
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
            'name'   => $request->name,
            'avatar' => $user->avatar,
            'language' => $request->language,
            'agree_to_terms' => $request->agree_to_terms,
        ]);

        if ($request->has('sub_category_id')) {
            foreach ($request->sub_category_id as $item) {
                UserInterest::create([
                    'user_id'        => $user->id,
                    'sub_category_id' => $item,
                ]);
            }
        }

        $user->load('information', 'interests');

        return $this->success($user, 'Data saved successfully', 200);
    }
}
