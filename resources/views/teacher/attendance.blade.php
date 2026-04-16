@extends('layouts.teacher')
@section('title', 'Mark Attendance')

@section('content')

<div class="page-header">
    <h1 class="page-title">Mark Attendance</h1>
    <p class="page-subtitle">Mark daily attendance for your students</p>
</div>

@livewire('admin.attendance-marker', ['schoolId' => auth()->user()->school_id])

@endsection