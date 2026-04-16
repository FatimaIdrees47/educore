<div>
    {{-- ── Filters Row ──────────────────────────────────────────── --}}
    <div class="educore-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" wire:model.live="date"
                    class="form-control"
                    max="{{ today()->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Class</label>
                <select wire:model.live="selectedClassId" class="form-select">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Section</label>
                <select wire:model.live="selectedSectionId" class="form-select"
                    {{ empty($sections) ? 'disabled' : '' }}>
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                    <option value="{{ $section['id'] }}">{{ $section['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                @if($alreadyMarked)
                <div class="d-flex align-items-center gap-2"
                    style="background:rgba(5,150,105,0.08);border:1px solid rgba(5,150,105,0.2);
                                border-radius:8px;padding:10px 14px">
                    <svg width="16" height="16" fill="none" stroke="var(--color-success)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span style="font-size:13px;color:var(--color-success);font-weight:500">
                        Already marked
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Success Message ──────────────────────────────────────── --}}
    @if($saved)
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Attendance Table ─────────────────────────────────────── --}}
    @if(count($students) > 0)
    <div class="educore-card">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="card-title">Mark Attendance</h2>
                <p class="text-muted-sm">
                    {{ count($students) }} students
                    · {{ \Carbon\Carbon::parse($date)->format('l, M d Y') }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="markAll('present')"
                    class="btn btn-sm"
                    style="background:rgba(5,150,105,0.08);color:var(--color-success);border:1px solid rgba(5,150,105,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:6px 12px">
                    ✓ All Present
                </button>
                <button wire:click="markAll('absent')"
                    class="btn btn-sm"
                    style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;font-weight:500;padding:6px 12px">
                    ✗ All Absent
                </button>
            </div>
        </div>

        <table class="educore-table">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th>Student</th>
                    <th style="width:320px">Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td class="text-muted-sm">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initials-sm" style="font-size:11px">
                                {{ strtoupper(substr($student['name'], 0, 2)) }}
                            </div>
                            <span class="fw-semibold" style="font-size:14px">{{ $student['name'] }}</span>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            @foreach([
                            'present' => ['label' => 'P', 'bg' => '5,150,105'],
                            'absent' => ['label' => 'A', 'bg' => '220,38,38'],
                            'late' => ['label' => 'L', 'bg' => '217,119,6'],
                            'half-day' => ['label' => 'H', 'bg' => '37,99,235'],
                            'excused' => ['label' => 'E', 'bg' => '100,116,139'],
                            ] as $statusKey => $statusMeta)
                            @php
                            $isActive = isset($attendance[$student['id']]['status']) &&
                            $attendance[$student['id']]['status'] === $statusKey;
                            @endphp
                            <button wire:click="$set('attendance.{{ $student['id'] }}.status', '{{ $statusKey }}')"
                                class="btn btn-sm"
                                style="width:36px;height:32px;padding:0;font-size:12px;font-weight:700;border-radius:6px;
                                           {{ $isActive
                                               ? "background:rgba({$statusMeta['bg']},1);color:#fff;border:1px solid rgba({$statusMeta['bg']},1)"
                                               : "background:rgba({$statusMeta['bg']},0.08);color:rgba({$statusMeta['bg']},1);border:1px solid rgba({$statusMeta['bg']},0.2)" }}"
                                title="{{ ucfirst($statusKey) }}">
                                {{ $statusMeta['label'] }}
                            </button>
                            @endforeach
                        </div>
                    </td>
                    <td>
                        <input type="text"
                            wire:model.defer="attendance.{{ $student['id'] }}.remarks"
                            class="form-control form-control-sm"
                            placeholder="Optional remark"
                            style="font-size:13px">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex align-items-center justify-content-between mt-4 pt-3"
            style="border-top:1px solid var(--color-border)">
            <div class="d-flex gap-3" style="font-size:13px">
                <span style="color:var(--color-success)">
                    ● Present: {{ collect($attendance)->where('status', 'present')->count() }}
                </span>
                <span style="color:var(--color-danger)">
                    ● Absent: {{ collect($attendance)->where('status', 'absent')->count() }}
                </span>
                <span style="color:var(--color-amber)">
                    ● Late: {{ collect($attendance)->where('status', 'late')->count() }}
                </span>
            </div>
            <button wire:click="save"
                class="btn btn-primary"
                wire:loading.attr="disabled">
                <span wire:loading.remove>Save Attendance</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>

    @elseif($selectedSectionId)
    <div class="educore-card" style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        <p class="fw-semibold">No students enrolled in this section</p>
        <p class="text-muted-sm">Enroll students first from the Students module.</p>
    </div>

    @elseif($selectedClassId)
    <div class="educore-card" style="text-align:center;padding:40px 0;color:var(--color-text-mid)">
        <p>Select a section to load students.</p>
    </div>

    @else
    <div class="educore-card" style="text-align:center;padding:40px 0;color:var(--color-text-mid)">
        <p>Select a class and section above to start marking attendance.</p>
    </div>
    @endif
</div>