@extends('layouts.parent')
@section('title', 'Notices')

@section('content')

<div class="page-header">
    <h1 class="page-title">Notices</h1>
    <p class="page-subtitle">School announcements for parents</p>
</div>

<div class="educore-card">
    @if($notices->count() > 0)
    @foreach($notices as $notice)
    <div class="py-3" style="border-bottom:1px solid var(--color-border)">
        <div class="d-flex align-items-start gap-3">
            <div style="width:40px;height:40px;border-radius:10px;flex-shrink:0;
                        background:rgba(219,39,119,0.1);color:var(--color-rose);
                        display:flex;align-items:center;justify-content:center">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <div class="flex-fill">
                <div class="fw-semibold" style="font-size:15px">{{ $notice->title }}</div>
                <div class="text-muted-sm" style="font-size:12px;margin-bottom:8px">
                    {{ $notice->created_at->format('M d, Y') }} · {{ $notice->created_at->diffForHumans() }}
                </div>
                <p style="font-size:14px;color:var(--color-text-sub);margin:0">
                    {{ $notice->body }}
                </p>
            </div>
        </div>
    </div>
    @endforeach
    <div class="mt-3">{{ $notices->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <p class="fw-semibold">No notices at the moment.</p>
    </div>
    @endif
</div>

@endsection