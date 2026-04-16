@extends('layouts.teacher')
@section('title', 'Messages')

@section('content')

<div class="page-header">
    <h1 class="page-title">Messages</h1>
    <p class="page-subtitle">Communications from parents</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-3">
    {{-- Conversations List --}}
    <div class="col-md-4">
        <div class="educore-card" style="padding:0;overflow:hidden">
            <div class="px-4 py-3" style="border-bottom:1px solid var(--color-border)">
                <h2 class="card-title mb-0">Conversations</h2>
            </div>

            @if(isset($conversations) && $conversations->count() > 0)
            @foreach($conversations as $otherId => $msgs)
            @php
                $lastMsg  = $msgs->first();
                $other    = $lastMsg->sender_id === auth()->id()
                            ? $lastMsg->receiver
                            : $lastMsg->sender;
                $unread   = $msgs->where('receiver_id', auth()->id())
                                 ->whereNull('read_at')->count();
                $isActive = isset($parent) && $parent->id === $other->id;
            @endphp
            <a href="{{ route('teacher.messages.show', $other->id) }}"
               style="display:block;text-decoration:none;
                      background:{{ $isActive ? 'rgba(5,150,105,0.06)' : 'transparent' }};
                      border-left:3px solid {{ $isActive ? 'var(--color-success)' : 'transparent' }}">
                <div class="d-flex align-items-center gap-3 px-4 py-3"
                     style="border-bottom:1px solid var(--color-border)">
                    <div style="width:40px;height:40px;border-radius:10px;flex-shrink:0;
                                background:rgba(219,39,119,0.1);color:var(--color-rose);
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:14px">
                        {{ strtoupper(substr($other->name, 0, 2)) }}
                    </div>
                    <div class="flex-fill" style="min-width:0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-semibold" style="font-size:14px">{{ $other->name }}</div>
                            @if($unread > 0)
                            <span style="background:var(--color-success);color:#fff;
                                         border-radius:10px;font-size:11px;
                                         padding:2px 7px;font-weight:600">
                                {{ $unread }}
                            </span>
                            @endif
                        </div>
                        <div class="text-muted-sm" style="font-size:12px;
                             white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                            {{ Str::limit($lastMsg->body, 45) }}
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
            @else
            <div style="text-align:center;padding:40px 20px;color:var(--color-text-mid)">
                <p class="text-muted-sm">No messages yet.</p>
                <p class="text-muted-sm" style="font-size:12px">
                    Parents will appear here when they message you.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- Message Thread --}}
    <div class="col-md-8">
        @if(isset($parent) && isset($messages))
        <div class="educore-card" style="padding:0;overflow:hidden">
            {{-- Header --}}
            <div class="px-4 py-3" style="border-bottom:1px solid var(--color-border);
                         background:var(--color-bg)">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;
                                background:rgba(219,39,119,0.1);color:var(--color-rose);
                                display:flex;align-items:center;justify-content:center;
                                font-weight:700;font-size:14px">
                        {{ strtoupper(substr($parent->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $parent->name }}</div>
                        <div class="text-muted-sm" style="font-size:12px">Parent</div>
                    </div>
                </div>
            </div>

            {{-- Messages --}}
            <div style="padding:20px;min-height:300px;max-height:400px;overflow-y:auto"
                 id="messageThread">
                @forelse($messages as $msg)
                @php $isMine = $msg->sender_id === auth()->id(); @endphp
                <div class="d-flex {{ $isMine ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                    <div style="max-width:75%">
                        @if($msg->student)
                        <div class="text-muted-sm mb-1" style="font-size:11px;
                             text-align:{{ $isMine ? 'right' : 'left' }}">
                            Re: {{ $msg->student->user->name }}
                        </div>
                        @endif
                        <div style="padding:10px 14px;border-radius:12px;font-size:14px;
                                    background:{{ $isMine ? 'var(--color-success)' : 'var(--color-bg)' }};
                                    color:{{ $isMine ? '#fff' : 'var(--color-text-dark)' }};
                                    border:{{ $isMine ? 'none' : '1px solid var(--color-border)' }}">
                            {{ $msg->body }}
                        </div>
                        <div class="text-muted-sm mt-1" style="font-size:11px;
                             text-align:{{ $isMine ? 'right' : 'left' }}">
                            {{ $msg->created_at->format('M d, h:i A') }}
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align:center;padding:40px 0;color:var(--color-text-mid)">
                    <p class="text-muted-sm">No messages yet.</p>
                </div>
                @endforelse
            </div>

            {{-- Reply Form --}}
            <div style="padding:16px;border-top:1px solid var(--color-border);background:var(--color-bg)">
                <form method="POST" action="{{ route('teacher.messages.reply') }}" id="replyForm">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $parent->id }}">
                    <div class="d-flex gap-2">
                        <textarea name="body" id="replyBody" class="form-control" rows="2"
                                  placeholder="Type your reply..." required
                                  style="resize:none;font-size:14px"></textarea>
                        <button type="submit" class="btn btn-primary px-4">Reply</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        (function () {
            const thread   = document.getElementById('messageThread');
            const form     = document.getElementById('replyForm');
            const textarea = document.getElementById('replyBody');
            let lastId     = {{ $messages->last()?->id ?? 0 }};
            const pollUrl  = "{{ route('teacher.messages.poll', $parent->id) }}";
            const myColor  = 'var(--color-success)';

            function renderMessage(msg) {
                const wrapper = document.createElement('div');
                wrapper.className = 'd-flex mb-3 ' + (msg.is_mine ? 'justify-content-end' : 'justify-content-start');
                const studentHtml = msg.student
                    ? `<div class="text-muted-sm mb-1" style="font-size:11px;text-align:${msg.is_mine ? 'right' : 'left'}">Re: ${msg.student}</div>`
                    : '';
                wrapper.innerHTML = `
                    <div style="max-width:75%">
                        ${studentHtml}
                        <div style="padding:10px 14px;border-radius:12px;font-size:14px;
                                    background:${msg.is_mine ? myColor : 'var(--color-bg)'};
                                    color:${msg.is_mine ? '#fff' : 'var(--color-text-dark)'};
                                    border:${msg.is_mine ? 'none' : '1px solid var(--color-border)'}">
                            ${msg.body}
                        </div>
                        <div class="text-muted-sm mt-1" style="font-size:11px;text-align:${msg.is_mine ? 'right' : 'left'}">
                            ${msg.time}
                        </div>
                    </div>`;
                thread.appendChild(wrapper);
                thread.scrollTop = thread.scrollHeight;
                lastId = msg.id;
            }

            async function poll() {
                try {
                    const res  = await fetch(`${pollUrl}?since=${lastId}`);
                    const data = await res.json();
                    if (data.messages && data.messages.length > 0) {
                        data.messages.forEach(renderMessage);
                    }
                } catch (e) {}
            }

            if (thread) thread.scrollTop = thread.scrollHeight;
            setInterval(poll, 5000);

            if (form) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const data = new FormData(form);
                    await fetch(form.action, { method: 'POST', body: data });
                    textarea.value = '';
                    await poll();
                });
            }
        })();
        </script>

        @else
        <div class="educore-card" style="text-align:center;padding:80px 0;color:var(--color-text-mid)">
            <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                 style="margin-bottom:16px;opacity:0.35">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>
            <p class="fw-semibold">Select a conversation to view</p>
        </div>
        @endif
    </div>
</div>

@endsection