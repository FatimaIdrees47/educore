<?php

namespace App\Http\View\Composers;

use App\Models\Message;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UnreadMessageComposer
{
    public function compose(View $view): void
    {
        if (!Auth::check()) return;

        $unreadMessages = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        $view->with('unreadMessages', $unreadMessages);
    }
}