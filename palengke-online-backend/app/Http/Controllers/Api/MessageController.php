<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\SellerRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversations = Conversation::where('seller_registration_id', $seller->id)
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($c) => $this->formatConversation($c));

        return response()->json(['conversations' => $conversations]);
    }

    public function show(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => $this->formatMessage($m));

        return response()->json([
            'conversation' => $this->formatConversation($conversation),
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'message' => 'required_without:image|string',
            'image' => 'nullable|string',
        ]);

        $seller = SellerRegistration::where('email', $validated['email'])->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'seller',
            'message' => $validated['message'] ?? null,
            'image' => $validated['image'] ?? null,
            'is_read' => false,
        ]);

        $conversation->last_message = $message->message ?: 'Sent an image';
        $conversation->last_message_at = now();
        $conversation->save();

        return response()->json([
            'message' => 'Message sent successfully.',
            'data' => $this->formatMessage($message),
        ], 201);
    }

    public function uploadImage(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
            'image' => 'required|file|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $path = $request->file('image')->store('chat-images', 'public');

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'seller',
            'message' => null,
            'image' => url('storage/' . $path),
            'is_read' => false,
        ]);

        $conversation->last_message = 'Sent an image';
        $conversation->last_message_at = now();
        $conversation->save();

        return response()->json([
            'message' => 'Image sent successfully.',
            'data' => $this->formatMessage($message),
        ], 201);
    }

    public function markRead(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('sender_type', 'participant')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $conversation->unread_count = 0;
        $conversation->save();

        return response()->json(['message' => 'Conversation marked as read.']);
    }

    public function toggleMute(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $conversation->muted = !$conversation->muted;
        $conversation->save();

        return response()->json([
            'message' => $conversation->muted ? 'Conversation muted.' : 'Conversation unmuted.',
            'muted' => $conversation->muted,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $conversation = Conversation::where('seller_registration_id', $seller->id)
            ->where('id', $id)->first();
        if (!$conversation) {
            return response()->json(['message' => 'Conversation not found.'], 404);
        }

        $messages = Message::where('conversation_id', $conversation->id)->get();
        foreach ($messages as $msg) {
            if ($msg->image) {
                $relative = str_replace(url('storage/'), '', $msg->image);
                if ($relative && Storage::disk('public')->exists($relative)) {
                    Storage::disk('public')->delete($relative);
                }
            }
        }

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted successfully.']);
    }

    public function destroyMessage(Request $request, $id)
    {
        $request->validate(['email' => 'required|email']);

        $seller = SellerRegistration::where('email', $request->email)->first();
        if (!$seller) {
            return response()->json(['message' => 'Seller not found.'], 404);
        }

        $message = Message::with('conversation')->find($id);
        if (!$message) {
            return response()->json(['message' => 'Message not found.'], 404);
        }

        if ($message->conversation->seller_registration_id !== $seller->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($message->image) {
            $relative = str_replace(url('storage/'), '', $message->image);
            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        }

        $message->delete();

        return response()->json(['message' => 'Message deleted successfully.']);
    }

    private function formatConversation($c)
    {
        $time = $c->last_message_at
            ? $c->last_message_at->diffForHumans()
            : $c->created_at->diffForHumans();

        return [
            'id' => $c->id,
            'name' => $c->participant_name,
            'type' => $c->participant_type,
            'avatar' => $c->participant_avatar,
            'lastMessage' => $c->last_message ?? '',
            'time' => $time,
            'unread' => $c->unread_count,
            'orderId' => $c->order_id,
            'muted' => $c->muted,
        ];
    }

    private function formatMessage($m)
    {
        return [
            'id' => $m->id,
            'text' => $m->message,
            'sent' => $m->sender_type === 'seller',
            'time' => $m->created_at->format('h:i A'),
            'status' => $m->is_read ? 'read' : 'delivered',
            'image' => $m->image,
        ];
    }
}
