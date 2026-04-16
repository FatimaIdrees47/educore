<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        $conversations = Message::where(function ($q) use ($user) {
            $q->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
            ->where('school_id', $schoolId)
            ->with(['sender', 'receiver', 'student'])
            ->latest()
            ->get()
            ->groupBy(function ($msg) use ($user) {
                $otherId = $msg->sender_id === $user->id
                    ? $msg->receiver_id
                    : $msg->sender_id;
                return $otherId;
            });

        $unreadCount = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('teacher.messages', compact('conversations', 'unreadCount'));
    }

    public function show($parentId)
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        $parent = User::findOrFail($parentId);

        // Mark as read
        Message::where('sender_id', $parentId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('school_id', $schoolId)
            ->where(function ($q) use ($user, $parentId) {
                $q->where(function ($q) use ($user, $parentId) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $parentId);
                })->orWhere(function ($q) use ($user, $parentId) {
                    $q->where('sender_id', $parentId)
                        ->where('receiver_id', $user->id);
                });
            })
            ->with(['sender', 'student'])
            ->oldest()
            ->get();

        return view('teacher.messages', compact('messages', 'parent'));
    }

    public function reply(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body'        => 'required|string|max:1000',
        ]);

        Message::create([
            'school_id'   => auth()->user()->school_id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'body'        => $request->body,
        ]);

        return redirect()->route('teacher.messages.show', $request->receiver_id)
            ->with('success', 'Reply sent.');
    }

    public function poll(Request $request, $parentId)
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;
        $since    = $request->get('since', 0);

        Message::where('sender_id', $parentId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('school_id', $schoolId)
            ->where(function ($q) use ($user, $parentId) {
                $q->where(function ($q) use ($user, $parentId) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $parentId);
                })->orWhere(function ($q) use ($user, $parentId) {
                    $q->where('sender_id', $parentId)
                        ->where('receiver_id', $user->id);
                });
            })
            ->where('id', '>', $since)
            ->with(['sender', 'student'])
            ->oldest()
            ->get()
            ->map(fn($msg) => [
                'id'      => $msg->id,
                'body'    => $msg->body,
                'is_mine' => $msg->sender_id === $user->id,
                'time'    => $msg->created_at->format('M d, h:i A'),
                'student' => $msg->student?->user->name,
            ]);

        return response()->json(['messages' => $messages]);
    }
}
