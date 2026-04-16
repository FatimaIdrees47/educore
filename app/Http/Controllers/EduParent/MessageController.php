<?php

namespace App\Http\Controllers\EduParent;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        // Get all unique conversations
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

        // Get teachers for new message
        $teachers = Teacher::where('school_id', $schoolId)
            ->with('user')
            ->get();

        // Get children
        $children = $user->children()->get();

        $unreadCount = Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('parent.messages', compact(
            'conversations',
            'teachers',
            'children',
            'unreadCount'
        ));
    }

    public function show($teacherId)
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;

        $teacher = User::findOrFail($teacherId);

        // Mark messages as read
        Message::where('sender_id', $teacherId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('school_id', $schoolId)
            ->where(function ($q) use ($user, $teacherId) {
                $q->where(function ($q) use ($user, $teacherId) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $teacherId);
                })->orWhere(function ($q) use ($user, $teacherId) {
                    $q->where('sender_id', $teacherId)
                        ->where('receiver_id', $user->id);
                });
            })
            ->with(['sender', 'student'])
            ->oldest()
            ->get();

        $teachers = Teacher::where('school_id', $schoolId)->with('user')->get();
        $children = $user->children()->get();

        return view('parent.messages', compact(
            'messages',
            'teacher',
            'teachers',
            'children'
        ));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body'        => 'required|string|max:1000',
            'student_id'  => 'nullable|exists:students,id',
        ]);

        Message::create([
            'school_id'   => auth()->user()->school_id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'student_id'  => $request->student_id,
            'body'        => $request->body,
        ]);

        return redirect()->route('parent.messages.show', $request->receiver_id)
            ->with('success', 'Message sent.');
    }
    public function poll(Request $request, $teacherId)
    {
        $user     = auth()->user();
        $schoolId = $user->school_id;
        $since    = $request->get('since', 0);

        // Mark as read
        Message::where('sender_id', $teacherId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = Message::where('school_id', $schoolId)
            ->where(function ($q) use ($user, $teacherId) {
                $q->where(function ($q) use ($user, $teacherId) {
                    $q->where('sender_id', $user->id)
                        ->where('receiver_id', $teacherId);
                })->orWhere(function ($q) use ($user, $teacherId) {
                    $q->where('sender_id', $teacherId)
                        ->where('receiver_id', $user->id);
                });
            })
            ->where('id', '>', $since)
            ->with(['sender', 'student'])
            ->oldest()
            ->get()
            ->map(fn($msg) => [
                'id'         => $msg->id,
                'body'       => $msg->body,
                'is_mine'    => $msg->sender_id === $user->id,
                'time'       => $msg->created_at->format('M d, h:i A'),
                'student'    => $msg->student?->user->name,
            ]);

        return response()->json(['messages' => $messages]);
    }
}
