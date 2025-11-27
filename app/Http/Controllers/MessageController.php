<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\Order;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * Display chat interface with a specific user.
     */
    public function chat(User $user, ?Order $order = null, ?\App\Models\MaterialRequest $materialRequest = null, ?\App\Models\FactoryRequest $factoryRequest = null): View
    {
        // Get conversation messages
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })
        ->when($order, function($query) use ($order) {
            $query->where('order_id', $order->id);
        })
        ->when($materialRequest, function($query) use ($materialRequest) {
            $query->where('material_request_id', $materialRequest->uuid);
        })
        ->when($factoryRequest, function($query) use ($factoryRequest) {
            $query->where('factory_request_id', $factoryRequest->uuid);
        })
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.chat', compact('user', 'messages', 'order', 'materialRequest', 'factoryRequest'));
    }

    /**
     * Display list of conversations.
     */
    public function index(): View
    {
        // Get all conversations for the authenticated user
        $conversations = Message::selectRaw('
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as other_user_id,
                MAX(created_at) as last_message_at
            ', [Auth::id()])
            ->where(function($query) {
                $query->where('sender_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->groupBy('other_user_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Get user details and last message for each conversation
        $conversationsWithDetails = $conversations->map(function($conversation) {
            $otherUser = User::find($conversation->other_user_id);
            $lastMessage = Message::where(function($query) use ($otherUser) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $otherUser->id);
            })->orWhere(function($query) use ($otherUser) {
                $query->where('sender_id', $otherUser->id)
                      ->where('receiver_id', Auth::id());
            })
            ->latest()
            ->first();

            $unreadCount = Message::where('sender_id', $otherUser->id)
                ->where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return [
                'user' => $otherUser,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'last_message_at' => $conversation->last_message_at,
            ];
        });

        return view('messages.index', compact('conversationsWithDetails'));
    }

    /**
     * Store a new message.
     */
    public function store(StoreMessageRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        
        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('messages/attachments', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $data['receiver_id'],
            'order_id' => $data['order_id'] ?? null,
            'material_request_id' => $data['material_request_id'] ?? null,
            'factory_request_id' => $data['factory_request_id'] ?? null,
            'message' => $data['message'],
            'attachments' => !empty($attachments) ? $attachments : null,
            'is_read' => false,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        // Send notification to receiver
        $receiver = User::find($data['receiver_id']);
        if ($receiver) {
            $receiver->notify(new NewMessageNotification($message));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load(['sender', 'receiver']),
            ]);
        }

        return redirect()->back()->with('success', 'Pesan berhasil dikirim.');
    }

    /**
     * Mark messages as read.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
        ]);

        Message::whereIn('id', $request->message_ids)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Download attachment.
     */
    public function downloadAttachment(Message $message, int $index): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        // Verify user has access to this message
        if ($message->sender_id !== Auth::id() && $message->receiver_id !== Auth::id()) {
            abort(403);
        }

        $attachments = $message->attachments ?? [];
        if (!isset($attachments[$index])) {
            abort(404);
        }

        $attachment = $attachments[$index];
        $path = $attachment['path'];

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download($path, $attachment['name']);
    }
}

