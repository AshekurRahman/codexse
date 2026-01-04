<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\Seller;
use App\Models\Product;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    public function index()
    {
        $conversations = Conversation::where('buyer_id', auth()->id())
            ->with(['seller.user', 'product', 'latestMessage'])
            ->latest('last_message_at')
            ->paginate(20);

        return view('pages.conversations.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $conversation->load(['seller.user', 'buyer', 'product', 'messages.sender', 'messages.attachments']);
        $conversation->markAsReadFor(auth()->user());

        return view('pages.conversations.show', compact('conversation'));
    }

    /**
     * Fetch messages for AJAX polling.
     */
    public function messages(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $afterId = $request->input('after');

        $query = $conversation->messages()->with(['sender', 'attachments'])->orderBy('id', 'asc');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->get();

        // Mark as read
        $conversation->markAsReadFor(auth()->user());

        return response()->json([
            'messages' => $messages->map(fn($msg) => $this->formatMessage($msg)),
            'last_id' => $messages->isNotEmpty() ? $messages->last()->id : $afterId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'product_id' => 'nullable|exists:products,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $conversation = Conversation::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $validated['seller_id'],
            'product_id' => $validated['product_id'] ?? null,
            'subject' => $validated['subject'],
            'last_message_at' => now(),
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['message'],
        ]);

        // Send push notification to seller
        $this->sendMessageNotification($conversation, $message);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('conversations.show', $conversation),
            ]);
        }

        return redirect()->route('conversations.show', $conversation)
            ->with('success', 'Message sent successfully!');
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $this->authorizeConversation($conversation);

        $validated = $request->validate([
            'message' => 'nullable|string|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,zip,txt',
        ]);

        // Require either message or attachments
        if (empty($validated['message']) && empty($request->file('attachments'))) {
            return response()->json(['message' => 'Please provide a message or attachment.'], 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'body' => $validated['message'] ?? '',
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('message-attachments/' . $conversation->id, $fileName, 'public');

                MessageAttachment::create([
                    'message_id' => $message->id,
                    'file_name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => MessageAttachment::determineFileType($file->getMimeType()),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        $conversation->update(['last_message_at' => now()]);

        // Send push notification
        $this->sendMessageNotification($conversation, $message);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->formatMessage($message->load(['sender', 'attachments'])),
            ]);
        }

        return back()->with('success', 'Reply sent!');
    }

    /**
     * Format a message for JSON response.
     */
    protected function formatMessage(Message $message): array
    {
        $sender = $message->sender;

        return [
            'id' => $message->id,
            'body' => $message->body,
            'sender_id' => $message->sender_id,
            'sender_name' => $sender?->name ?? 'Unknown',
            'sender_avatar' => $sender?->avatar_url ?? asset('images/default-avatar.webp'),
            'is_mine' => (int) $message->sender_id === (int) auth()->id(),
            'created_at' => $message->created_at->toIso8601String(),
            'time_ago' => $message->created_at->diffForHumans(),
            'formatted_time' => $message->created_at->format('g:i A'),
            'formatted_date' => $message->created_at->format('M d, Y'),
            'attachments' => $message->attachments->map(fn($att) => [
                'id' => $att->id,
                'original_name' => $att->original_name ?? $att->file_name,
                'file_type' => $att->file_type,
                'mime_type' => $att->mime_type,
                'file_size' => $att->file_size,
                'formatted_size' => $att->formatted_size,
                'url' => $att->url,
                'is_image' => $att->isImage(),
                'is_video' => $att->isVideo(),
                'is_document' => $att->isDocument(),
            ])->toArray(),
        ];
    }

    /**
     * Authorize access to a conversation.
     */
    protected function authorizeConversation(Conversation $conversation): void
    {
        $isBuyer = (int) $conversation->buyer_id === (int) auth()->id();
        $isSeller = auth()->user()->seller && (int) auth()->user()->seller->id === (int) $conversation->seller_id;

        if (!$isBuyer && !$isSeller) {
            abort(403);
        }
    }

    protected function sendMessageNotification(Conversation $conversation, Message $message): void
    {
        try {
            $pushService = app(PushNotificationService::class);
            $sender = auth()->user();

            // Determine the recipient (the other party in the conversation)
            $conversation->load('seller.user', 'buyer');

            // If sender is buyer, notify seller
            if ((int) $sender->id === (int) $conversation->buyer_id && $conversation->seller && $conversation->seller->user) {
                $recipient = $conversation->seller->user;
            }
            // If sender is seller, notify buyer
            elseif ($conversation->buyer && (int) $sender->id !== (int) $conversation->buyer_id) {
                $recipient = $conversation->buyer;
            } else {
                return;
            }

            $pushService->notifyNewMessage($recipient, [
                'conversation_id' => $conversation->id,
                'sender_name' => $sender->name,
                'sender_avatar' => $sender->avatar_url ?? null,
                'content' => $message->body,
                'url' => route('conversations.show', $conversation),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send message notification: ' . $e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $seller = null;
        $product = null;

        if ($request->has('seller')) {
            $seller = Seller::findOrFail($request->seller);
        }

        if ($request->has('product')) {
            $product = Product::findOrFail($request->product);
            $seller = $product->seller;
        }

        return view('pages.conversations.create', compact('seller', 'product'));
    }
}
