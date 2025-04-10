<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Dotenv\Util\Str;
use App\Notifications\MessageNotification;

class MessageController extends Controller
{
    public function compose()
    {
        $users = User::all();
        return view('content.dashboard.messages.compose', compact('users'));
    }

    public function index()
    {
        $users = User::all();
        $user_id = auth()->id();
        $totalMessages = Message::where('sender_id', $user_id)
            ->orWhereHas('conversation', function ($query) use ($user_id) {
                $query->where('user1_id', $user_id)->orWhere('user2_id', $user_id);
            })
            ->count();
        $unreadMessagesCount = Message::whereIn('conversation_id', Conversation::where('user1_id', $user_id)->orWhere('user2_id', $user_id)->pluck('id'))
            ->where('sender_id', '!=', $user_id)
            ->where('read', false)
            ->count();
        $sentMessagesCount = Message::where('sender_id', $user_id)->count();
        $conversations = Conversation::where('user1_id', $user_id)->orWhere('user2_id', $user_id)->pluck('id');
        $unreadMessages = Message::whereIn('conversation_id', $conversations)
            ->where('sender_id', '!=', $user_id)
            ->where('read', false)
            ->latest()
            ->get();
        foreach ($unreadMessages as $message) {
            $message->read = true;
            $message->save();
        }
        $messages = Message::whereIn('conversation_id', $conversations)
            ->where('sender_id', '!=', $user_id)
            ->latest()
            ->get();
        return view('content.dashboard.messages.index', compact('users','unreadMessages', 'messages', 'totalMessages', 'unreadMessagesCount', 'sentMessagesCount'));
    }

    public function createMessage()
    {
        $users = User::where('id', '!=', auth()->id())->get();
        return view('content.messages.create', compact('users'));
    }

    public function store(Request $request)
    {
        $message = new Message();
        $message->body = $request->body;
        $message->sender_id = auth()->id();
        $message->conversation_id = $request->conversation_id;
        $message->save();
        return redirect()->back();
    }

    public function send(Request $request)
    {
        \Log::info('Request data:', $request->all());

        $validated = $request->validate([
            'recipient' => 'required|exists:users,id',
            'subject' => 'required|string',
            'message' => 'required|string'
        ]);

        \Log::info('Validated data:', $validated);

        $sender_id = auth()->id();
        $recipient_id = $request->recipient;

        $conversation = Conversation::firstOrCreate(
            ['user1_id' => min($sender_id, $recipient_id), 'user2_id' => max($sender_id, $recipient_id)]
        );

        \Log::info('Conversation created:', ['id' => $conversation->id]);

        $message = new Message();
        $message->sender_id = $sender_id;
        $message->conversation_id = $conversation->id;
        $message->subject = $request->subject;
        $message->body = $request->message;
        $message->read = false;
        $message->save();

        \Log::info('Message saved:', ['id' => $message->id]);

        $recipient = User::find($recipient_id);
        $recipient->notify(new MessageNotification($message));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Message sent successfully!')
            ]);
        }

        return redirect()->route('dashboard.messages.index')->with('success', __('Message sent successfully!'));
    }

    public function sent()
    {
        $user_id = auth()->id();
        $messages = Message::where('sender_id', $user_id)->latest()->get();
        $sentMessages = Message::where('sender_id', $user_id)->latest()->get();
        $unreadMessagesCount = Message::whereIn('conversation_id', Conversation::where('user1_id', $user_id)->orWhere('user2_id', $user_id)->pluck('id'))
            ->where('sender_id', '!=', $user_id)
            ->where('read', false)
            ->count();
        $sentMessagesCount = Message::where('sender_id', $user_id)->count();
        return view('content.dashboard.messages.sent', compact('messages', 'sentMessages', 'unreadMessagesCount', 'sentMessagesCount'));
    }

    public function received()
    {
        $user_id = auth()->id();
        $conversations = Conversation::where('user1_id', $user_id)->orWhere('user2_id', $user_id)->pluck('id');
        $messages = Message::whereIn('conversation_id', $conversations)
            ->where('sender_id', '!=', $user_id)
            ->latest()
            ->get();
        return view('content.dashboard.messages.received', compact('messages'));
    }

    public function important()
    {
        return view('content.dashboard.messages.important', ['messages' => Message::where('is_important', true)->get()]);
    }

    public function drafts()
    {
        $user_id = auth()->id(); // احصل على معرف المستخدم الحالي

        // جلب الرسائل المحفوظة كمسودات
        $draftMessages = Message::where('sender_id', $user_id)
                                 ->where('is_draft', true)
                                 ->latest()
                                 ->get();

        // تمرير الرسائل إلى صفحة العرض
        return view('content.dashboard.messages.drafts', compact('draftMessages'));
    }


    public function trash(Request $request)
{
    if ($request->isMethod('delete')) {
        $messageIds = $request->input('selected_messages');

        if ($messageIds) {
            $messageIdsArray = explode(',', $messageIds); 
            Message::whereIn('id', $messageIdsArray)->delete();
            return redirect()->back()->with('success', 'Selected messages deleted successfully.');
        }
    }

    $user_id = auth()->id();
    $sentMessages = Message::where('sender_id', $user_id)->latest()->get();
    $conversations = Conversation::where('user1_id', $user_id)->orWhere('user2_id', $user_id)->pluck('id');
    $totalMessages = Message::where('sender_id', $user_id)
        ->orWhereHas('conversation', function ($query) use ($user_id) {
            $query->where('user1_id', $user_id)->orWhere('user2_id', $user_id);
        })
        ->count();
    $unreadMessages = Message::whereIn('conversation_id', $conversations)
        ->where('sender_id', '!=', $user_id)
        ->where('read', false)
        ->latest()
        ->get();
    return view('content.dashboard.messages.trash', compact('sentMessages', 'unreadMessages'));
}

public function toggleImportant($id)
{
    $message = Message::findOrFail($id);
    $message->is_important = !$message->is_important;
    $message->save();

    return redirect()->back()->with('success', 'Message importance toggled.');
}


    public function show(Message $message)
    {
        // Check if user can view this message
        $conversation = $message->conversation;
        if ($conversation->user1_id !== auth()->id() && $conversation->user2_id !== auth()->id()) {
            abort(403);
        }

        // Mark message as read if it's unread
        if (!$message->read && $message->sender_id !== auth()->id()) {
            return $this->markAsRead($message);
        }

        return view('content.dashboard.messages.show', compact('message'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_body' => 'required|string',
        ]);

        $originalMessage = Message::findOrFail($id);

        $replyMessage = new Message();
        $replyMessage->body = $request->input('reply_body');
        $replyMessage->sender_id = auth()->id();
        $replyMessage->conversation_id = $originalMessage->conversation_id;
        $replyMessage->subject = 'Re: ' . $originalMessage->subject;
        $replyMessage->save();

        return redirect()->route('messages.show', $id)->with('success', 'Reply sent successfully.');
    }

    public function delete($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->route('dashboard.messages.index')->with('success', 'Message deleted successfully.');
    }

    public function destroy($id)
{
    $message = Message::find($id);

    if (!$message) {
        return redirect()->route('dashboard.messages.sent')->with('error', __('Message not found.'));
    }

    if ($message->user_id !== auth()->id()) {
        abort(403, __('You are not authorized to delete this message.'));
    }

    // حذف الرسائل التابعة إذا كانت موجودة
    $message->relatedMessages()->delete(); // مثال فقط

    $message->delete();

    return redirect()->route('dashboard.messages.sent')->with('success', __('Message deleted successfully.'));
}


    public function markAsRead(Message $message)
    {
        if (!$message->read && $message->conversation->hasUser(auth()->id())) {
            $message->read = true;
            $message->save();
        }
        
        return response()->json(['success' => true]);
    }
}
