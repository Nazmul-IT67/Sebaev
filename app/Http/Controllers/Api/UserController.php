<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Fetch Login User Data
     *
     * @return \Illuminate\Http\JsonResponse  JSON response with success or error.
     */

    public function userData()
    {
        $user = User::with(['information'])->where('id', auth()->user()->id)->first();

        if (! $user) {
            return $this->error([], 'User Not Found', 200);
        }

        $user->setAttribute('is_any_movement_joined', $user->userMovements()->exists());
        $user->setAttribute('is_choose_interest', $user->interests()->exists());

        return $this->success($user, 'User data fetched successfully', 200);
    }

    /**
     * Update User Information
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request with the register query.
     * @return \Illuminate\Http\JsonResponse  JSON response with success or error.
     */

    public function userUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar'  => 'nullable|image|mimes:jpeg,png,jpg,svg|max:5120',
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:users,email,' . auth()->user()->id,
            'cif'     => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'birth'   => 'nullable|date|before_or_equal:today',
            'gender'  => 'nullable|in:male,female,other',
            'country' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        try {
            // Find the user by ID
            $user = auth()->user();

            // If user is not found, return an error response
            if (! $user) {
                return $this->error([], "User Not Found", 200);
            }

            if ($request->hasFile('avatar')) {

                if ($user->avatar) {
                    $previousImagePath = public_path($user->avatar);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }

                $image     = $request->file('avatar');
                $imageName = uploadImage($image, 'User/Avatar');
            } else {
                $imageName = $user->avatar;
            }

            if ($request->language) {
                $user->language = $request->language;
            }

            if ($request->name) {
                $user->name = $request->name;
            }

            if ($request->email) {
                $user->email = $request->email;
            }

            // Update user fields
            $user->avatar = $imageName;
            $user->save();

            // Ensure user information exists
            if (!$user->information) {
                $user->information()->create([
                    'cif'     => $request->cif,
                    'website' => $request->website,
                    'birth'   => $request->birth,
                    'gender'  => $request->gender,
                    'country' => $request->country,
                ]);
            } else {
                $user->information->update([
                    'cif'     => $request->cif,
                    'website' => $request->website,
                    'birth'   => $request->birth,
                    'gender'  => $request->gender,
                    'country' => $request->country,
                ]);
            }

            $user->load('information');
            return $this->success($user, 'User updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Change Login User Password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with success or error.
     */
    public function passwordChange(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is not found, return an error response
        if (! $user) {
            return $this->error([], "User Not Found", 200);
        }

        // Validate request inputs
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        // Check if the current password matches
        if (! Hash::check($request->current_password, $user->password)) {
            return $this->error([], "Current password is incorrect", 400);
        }

        // Update the password securely
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->success($user->fresh(), "Password changed successfully", 200);
    }

    /**
     * Logout the authenticated user's account
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with success or error.
     */
    public function logoutUser()
    {

        try {
            $auth = auth()->user();

            if (!$auth) {
                return $this->error([], "Unauthorized", 401);
            }

            if ($auth->firebaseTokens) {
                $auth->firebaseTokens->delete();
            }

            $auth->JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success([], 'Successfully logged out', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Delete the authenticated user's account
     *
     * @return \Illuminate\Http\JsonResponse JSON response with success or error.
     */
    public function deleteUser()
    {
        try {
            // Get the authenticated user
            $user = auth()->user();

            if (!$user) {
                return $this->error([], "Unauthorized", 401);
            }

            if ($user->firebaseTokens) {
                $user->firebaseTokens->delete();
            }

            // Delete the user's avatar if it exists
            if ($user->avatar) {
                $previousImagePath = public_path($user->avatar);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }

            // Delete the user
            $user->delete();

            return $this->success([], 'User deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    /**
     * Update User Language Preference
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with success or error.
     */
    public function updateLanguage(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();

        // If user is not found, return an error response
        if (! $user) {
            return $this->error([], "User Not Found", 200);
        }

        // Validate the request input
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:255|in:english,spanish,french,catalan',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        // Update the user's language preference
        $user->language = $request->language;
        $user->save();

        return $this->success($user, 'Language updated successfully', 200);
    }
}
