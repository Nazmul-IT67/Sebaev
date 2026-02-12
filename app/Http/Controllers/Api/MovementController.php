<?php

namespace App\Http\Controllers\Api;

use App\Enum\NotificationType;
use App\Http\Controllers\Controller;
use App\Models\Cms;
use App\Models\Movement;
use App\Models\UserInterest;
use App\Models\UserMovement;
use App\Notifications\UserNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MovementController extends Controller
{
    use ApiResponse;

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $role = $user->role;

            $size = Cms::latest('id')->first();

            $rules = [
                'category_id'     => 'required|exists:categories,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'country'         => 'required|string',
                'title'           => 'required|string|max:255',
                'description'     => 'required|string|max:1000',
                'video'           => 'required|mimetypes:video/mp4,video/x-msvideo,video/x-matroska|max:' . $size->size,
            ];

            if ($role === 'organization') {
                $rules['pdf'] = 'nullable|file|mimes:pdf|max:2048';
            } elseif ($role === 'individual') {
                $rules['pdf'] = 'nullable|file|mimes:pdf|max:2048';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }

            if ($request->duration > $size->duration) {
                return $this->error([], 'Video duration must be less than ' . $size->duration . ' seconds', 422);
            }

            $pdfName = null;

            if ($request->hasFile('pdf')) {
                $pdf     = $request->file('pdf');
                $pdfName = uploadImage($pdf, 'movement');
            }

            if ($request->hasFile('video')) {
                $file      = $request->file('video');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $storeFile = $file->storeAs("movements", $file_name, "gcs");
                $bucket    = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";
            }

            $movement = Movement::create([
                'user_id'         => $user->id,
                'category_id'     => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'country'         => $request->country,
                'title'           => $request->title,
                'description'     => $request->description,
                'pdf'             => $pdfName,
                'video'           => $url,
            ]);

            if ($movement && $user->role == 'organization') {
                $conversation = $user->createGroup(
                    name: $movement->title,
                    description: $movement->description,
                    photo: null,
                );

                // $conversation->addParticipant($user);

                $message = $user->sendMessageTo($conversation, 'Your Group has been created successfully');

                $movement->update([
                    'conversation_id' => $conversation->id ?? null,
                ]);
            }

            $userMovement = UserMovement::create([
                'user_id'     => $user->id,
                'movement_id' => $movement->id,
            ]);

            $user->notify(new UserNotification(
                subject: 'New movement',
                message: 'You have created a movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            return $this->success($movement, 'Movement saved successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (! $user) {
                return $this->error([], 'Unauthorized', 401);
            }

            $movement = Movement::find($id);

            if (! $movement) {
                return $this->error([], 'Movement not found', 200);
            }

            $role = $user->role;

            $size = Cms::latest('id')->first();

            $rules = [
                'category_id'     => 'required|exists:categories,id',
                'sub_category_id' => 'required|exists:sub_categories,id',
                'country'         => 'required|string',
                'title'           => 'required|string',
                'description'     => 'required|string',
                'video'           => 'nullable|file|mimes:mp4,mov,ogg|max:' . $size->size,
                'duration'        => 'nullable|numeric',
            ];

            if ($request->duration > $size->duration) {
                return $this->error([], 'Video duration must be less than ' . $size->duration . ' seconds', 422);
            }

            if ($role === 'organization') {
                $rules['pdf'] = 'nullable|file|mimes:pdf|max:2048';
            } elseif ($role === 'individual') {
                $rules['pdf'] = 'nullable|file|mimes:pdf|max:2048';
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return $this->error($validator->errors(), $validator->errors()->first(), 422);
            }

            if ($user->id !== $movement->user_id) {
                return $this->error([], "You are not authenticate", 401);
            }

            // Handle PDF upload
            if ($request->hasFile('pdf')) {
                if ($movement->pdf) {
                    $previousImagePath = public_path($movement->pdf);
                    if (file_exists($previousImagePath)) {
                        unlink($previousImagePath);
                    }
                }
                $movement->pdf = uploadImage($request->file('pdf'), 'movement');
            }

            // Handle Video upload
            if ($request->hasFile('video')) {
                if ($movement->video) {
                    $bucket  = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                    $prefix  = "https://storage.googleapis.com/{$bucket}/";
                    $oldPath = str_replace($prefix, '', $movement->video);
                    if (Storage::disk('gcs')->exists($oldPath)) {
                        Storage::disk('gcs')->delete($oldPath);
                    }
                }

                // Upload new video
                $file      = $request->file('video');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $storeFile = $file->storeAs("movements", $file_name, "gcs");
                $url       = "https://storage.googleapis.com/{$bucket}/{$storeFile}";
            } else {
                $url = $movement->video;
            }

            // Update Movement with new data
            $movement->update([
                'category_id'     => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'country'         => $request->country,
                'title'           => $request->title,
                'description'     => $request->description,
                'video'           => $url,
            ]);

            $user->notify(new UserNotification(
                subject: 'Updated movement',
                message: 'You have updated a movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            return $this->success($movement, 'Movement updated successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function delete(int $id)
    {
        try {
            $user = Auth::user();
            $data = Movement::find($id);

            if (! $data) {
                return $this->error([], 'Movement not found', 200);
            }

            if ($user->id !== $data->user_id) {
                return $this->error([], "You are not authenticate", 401);
            }

            if ($data->pdf) {
                $previousImagePath = public_path($data->pdf);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }
            if ($data->video) {
                $bucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');
                $prefix = "https://storage.googleapis.com/{$bucket}/";
                $path   = str_replace($prefix, '', $data->video);

                // Delete video file from GCS
                if (Storage::disk('gcs')->exists($path)) {
                    Storage::disk('gcs')->delete($path);
                }
            }
            $data->delete();

            $user->notify(new UserNotification(
                subject: 'Deleted movement',
                message: 'You have deleted a movement',
                channels: ['database'],
                type: NotificationType::SUCCESS,
            ));

            return $this->success([], 'Movement deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }

    public function singleMovement(int $id)
    {
        $user = Auth::user();

        $data = Movement::with('user:id,name,role,avatar', 'userMovements.user:id,name,avatar')->find($id);

        if (! $data) {
            return $this->error([], 'Movement not found', 200);
        }

        if ($user) {
            if ($data->user_id == $user->id) {
                $data->setAttribute('is_my_movement', true);
                $data->setAttribute('is_my_mini_goal', true);
            } else {
                $data->setAttribute('is_my_movement', false);
                $data->setAttribute('is_my_mini_goal', false);
            }
            $data->setAttribute('has_joined', $data->userMovements->contains('user_id', $user->id));
            unset($data->userMovements);
        } else {
            $data->setAttribute('is_my_movement', false);
            $data->setAttribute('is_my_mini_goal', false);
        }

        return $this->success($data, 'Movement fetched successfully!', 200);
    }

    // public function guestSingleMovement(int $id)
    // {
    //     $data = Movement::with('user:id,name,role,avatar', 'userMovements.user:id,name,avatar')->find($id);

    //     if (! $data) {
    //         return $this->error([], 'Movement not found', 200);
    //     }

    //     $data->setAttribute('is_my_movement', false);

    //     return $this->success($data, 'Movement fetched successfully!', 200);
    // }

    public function myMovement()
    {
        $user = Auth::id();
        $data = Movement::with('user:id,name,avatar')->where('user_id', $user)->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Movement not found', 200);
        }

        return $this->success($data, 'Movement fetch Successful!', 200);
    }

    public function myJoinMovement()
    {
        $user = Auth::id();
        $data = Movement::withCount('userMovements', 'comments', 'movementShare')->with(['userMovements.user:id,avatar', 'user:id,name,role,avatar'])
            ->whereHas('userMovements', function ($query) use ($user) {
                $query->where('user_id', $user)->latest()->limit(3);
            })
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Movement not found', 200);
        }

        return $this->success($data, 'Movement fetch Successful!', 200);
    }

    public function suggestMovement(Request $request)
    {
        $search = $request->input('search');

        $user = Auth::id();

        if (! $user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $userInterestSubCategories = UserInterest::where('user_id', $user)->pluck('sub_category_id')->toArray();

        if (empty($userInterestSubCategories)) {
            return $this->error([], 'No interests found for the user', 200);
        }

        $query = Movement::withCount('userMovements', 'comments', 'movementShare')->with([
            'userMovements' => function ($query) {
                $query->latest()->take(3);
            },
            'userMovements.user:id,avatar',
            'user:id,name,role,avatar',
        ])
            ->whereIn('sub_category_id', $userInterestSubCategories)
            ->where('country', auth()->user()->information->country);

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'LIKE', '%' . $search . '%');
            });
        }

        $data = $query->get()
            ->map(function ($movement) use ($user) {
                $movement->has_joined = $movement->userMovements->contains('user_id', $user);
                return $movement;
            });

        if ($data->isEmpty()) {
            $query = Movement::withCount('userMovements', 'comments', 'movementShare')->with([
                'userMovements' => function ($query) {
                    $query->latest()->take(3);
                },
                'userMovements.user:id,avatar',
                'user:id,name,role,avatar',
            ]);

            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'LIKE', '%' . $search . '%');
                });
            }

            $data = $query->get()
                ->map(function ($movement) use ($user) {
                    $movement->has_joined = $movement->userMovements->contains('user_id', $user);
                    return $movement;
                });
        }

        return $this->success($data, 'Movement fetch Successful!', 200);
    }
}
