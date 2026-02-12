<?php

namespace App\Http\Controllers\Api;

use App\Enum\NotificationType;
use App\Events\MessageCreated;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserNotification;
use App\Services\FCMService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Namu\WireChat\Events\NotifyParticipant;
use Namu\WireChat\Models\Conversation;

class SingleChatController extends Controller
{
    use ApiResponse;

    /**
     * Get all active users except the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeUsers()
    {
        $users = User::query()
            ->select('id', 'name', 'avatar', 'role', 'active_at')
            ->where('role', '!=', 'admin')
            ->where('active_at', '!=', null)
            ->where('id', '!=', auth()->id())
            ->where('active_at', '>=', now()->subMinutes(5))
            ->get();

        if ($users->isEmpty()) {
            return $this->error([], "No active users found", 200);
        }

        return $this->success($users, "Active users fetched successfully", 200);
    }

    // Get all conversations (chats)
    /**
     * Fetch all chats for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chats(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], "Unauthorized", 401);
        }

        $validator = Validator::make($request->all(), [
            'search' => "nullable|string|max:255",
            'per_page' => "nullable|integer|min:1|max:1000",
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $search = $request->get('search');

        $query = $user->conversations()
            ->where('type', '!=', 'group') // Exclude group chats
            ->with([
                'participants' => function ($query) {
                    $query->with('participantable:id,name,avatar,active_at')->where('participantable_id', '!=', auth()->id());
                },
                'lastMessage',
            ])
            ->latest('updated_at');

        if (!empty($search)) {
            $query->whereHas('participants.participantable', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $chats = $query->paginate($request->get('per_page', 10));

        return $this->success($chats, "Chat list fetched successfully", 200);
    }

    /**
     * Fetch all group chats for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupChats(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return $this->error([], "Unauthorized", 401);
        }

        $validator = Validator::make($request->all(), [
            'search' => "nullable|string|max:255",
            'per_page' => "nullable|integer|min:1|max:1000",
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $search = $request->get('search');

        $query = $user->conversations()
            ->where('type', 'group')
            ->with([
                'group',
                'lastMessage',
            ])
            ->latest('updated_at');

        if (!empty($search)) {
            $query->whereHas('group', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $chats = $query->paginate($request->get('per_page', 10));

        return $this->success($chats, "Chat list fetched successfully", 200);
    }

    /**
     * Fetch or create a chat with a specific user.
     *
     * @param int $user_id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => [
                'nullable',
                'integer',
                'required_without:conversation_id',
            ],
            'conversation_id' => [
                'nullable',
                'integer',
                'required_without:user_id',
            ],
            'per_page' => 'nullable|integer|min:1|max:1000',
            'page' => 'nullable|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $authUser = auth()->user();
        $perPage = $request->get('per_page', 1000);
        $page = $request->get('page', 1);
        $user_id = $request->get('user_id');
        $conversation_id = $request->get('conversation_id');
        // Fetch conversation if it exists
        if ($conversation_id) {
            $conversation = Conversation::find($conversation_id);

            if (!$conversation) {
                return $this->error([], "Conversation not found", 404);
            }

            // If the authenticated user is not a participant, add them
            if (!$conversation->participants()->where('participantable_id', $authUser->id)->exists()) {
                $conversation->addParticipant($authUser);
            }
        } else {
            $user = User::find($user_id);

            if (!$user) {
                return $this->error([], "User Not Found", 404);
            }

            $conversation = $authUser->conversations()
                ->where('type', '!=', 'group') // Exclude group chats
                ->whereHas('participants', function ($query) use ($user) {
                    $query->where('participantable_id', $user->id);
                })
                ->first();

            if (!$conversation) {
                $conversation = $authUser->createConversationWith($user);

                // Create initial message
                // $message = $authUser->sendMessageTo($conversation, 'RRR');

                // broadcast(new NotifyParticipant($user, $message));
            }
        }

        if (!$conversation_id) {
            // Load participants
            $conversation->load([
                'participants' => function ($query) {
                    $query->with('participantable:id,name,avatar')->where('participantable_id', '!=', auth()->id());
                }
            ]);
        } else {
            $conversation->load(['group']);
        }

        // Paginate messages separately
        $messages = $conversation->messages()->with(['sendable:id,name,avatar'])->oldest()->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => $conversation->wasRecentlyCreated ? "New conversation created" : "Chat fetched successfully",
            'conversation_id' => $conversation->id,
            'data' => [
                'conversation' => $conversation,
                'messages' => $messages
            ],
            'code' => $conversation->wasRecentlyCreated ? 201 : 200
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    /**
     * Send a message to a specific user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'exists:users,id', 'required_without:conversation_id'],
            'conversation_id' => ['nullable', 'required_without:user_id'],
            'message' => ['string'],
            'device' => ['nullable', 'string', 'in:web,app'],
        ]);
        $user_id = $request->get('user_id');
        $conversation_id = $request->get('conversation_id');

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $formUser = auth()->user();

            if ($conversation_id) {
                $conversation = Conversation::find($conversation_id);
                $participants = $conversation->participants()
                    ->where(function ($query) use ($formUser) {
                        $query->where('participantable_id', '!=', $formUser->id)
                            ->orWhere('participantable_type', '!=', get_class($formUser));
                    })
                    ->get();
                foreach ($participants as $participant) {
                    $user = User::find($participant->participantable_id);

                    Log::info([
                        "ID" => $user->id,
                        "Name" => $user->name,
                        "FCM_TOKEN" => $user->firebaseTokens->token ?? "N/A",
                        "Device_ID" => $user->firebaseTokens->device_id ?? "N/A",
                        "RealUser" => $formUser->name,
                        "RealUser_FCM_TOKEN" => $formUser->firebaseTokens->token ?? "N/A",
                        "RealUser_Device_ID" => $formUser->firebaseTokens->device_id ?? "N/A",
                        "br" => "-----------------",
                    ]);

                    if ($user->firebaseTokens && $user->firebaseTokens->token !== null) {
                        $fcmService = new FCMService();
                        $fcmService->sendMessage(
                            $user->firebaseTokens->token,
                            $formUser->name . ' sent a message',
                            $request->message,
                            [
                                'type' => 'single_chat',
                                'conversation_id' => $conversation->id
                            ]
                        );
                    }
                }
                if (!$conversation) {
                    return $this->error([], "Conversation not found", 404);
                }

                $chat = $formUser->sendMessageTo($conversation, $request->message);
            } else {
                $toUser = User::find($request->user_id);

                if (!$toUser || $formUser->id === $toUser->id) {
                    return $this->error([], "Invalid recipient", 404);
                }

                $conversation = $formUser->conversations()
                    ->whereHas('participants', function ($query) use ($toUser) {
                        $query->where('participantable_id', $toUser->id);
                    })
                    ->first();

                if (!$conversation) {
                    $conversation = $formUser->createConversationWith($toUser);
                    $conversation->save();
                }

                $messages = $conversation->messages()->with(['sendable:id,name,avatar'])->first();
                if (!$messages) {
                    $toUser->notify(new UserNotification(
                        subject: 'New Message from ' . $formUser->name,
                        message: $formUser->name . ' has sent you a message.',
                        channels: ['database'],
                        type: NotificationType::SUCCESS,
                    ));
                }

                $chat = $formUser->sendMessageTo($toUser, $request->message);

                $participant = $chat->conversation->participant($toUser);
                if ($participant) {
                    broadcast(new NotifyParticipant($participant, $chat));
                }

                if ($toUser->firebaseTokens && $toUser->firebaseTokens->token !== null) {
                    $fcmService = new FCMService();
                    $fcmService->sendMessage(
                        $toUser->firebaseTokens->token,
                        $formUser->name . ' sent you a message',
                        $request->message,
                        [
                            'type' => 'single_chat',
                            'conversation_id' => $conversation->id
                        ]
                    );
                }
            }

            broadcast(new MessageCreated($chat));

            DB::commit();
            return $this->success($chat, "Message sent successfully", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 500);
        }
    }
}
