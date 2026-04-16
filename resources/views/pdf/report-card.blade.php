<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card — {{ $student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #1E293B;
            background: #fff;
            padding: 30px;
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #2563EB;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 22px;
            font-weight: 700;
            color: #2563EB;
            letter-spacing: -0.5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 4px;
        }
        .exam-name {
            font-size: 13px;
            color: #64748B;
            margin-top: 2px;
        }

        /* Student Info */
        .student-info {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 160px;
            color: #64748B;
            padding: 4px 0;
            font-size: 12px;
        }
        .info-value {
            display: table-cell;
            font-weight: 600;
            color: #1E293B;
            padding: 4px 0;
            font-size: 12px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        /* Result Summary */
        .result-summary {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-spacing: 8px;
        }
        .summary-box {
            display: table-cell;
            text-align: center;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px 8px;
            background: #F8FAFC;
        }
        .summary-number {
            font-size: 22px;
            font-weight: 800;
            color: #2563EB;
        }
        .summary-label {
            font-size: 11px;
            color: #64748B;
            margin-top: 2px;
        }
        .grade-box .summary-number {
            color: {{ $reportCard->percentage >= 80 ? '#059669' : ($reportCard->percentage >= 60 ? '#2563EB' : '#DC2626') }};
        }
        .position-box .summary-number { color: #7C3AED; }

        /* Marks Table */
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #E2E8F0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead tr {
            background: #2563EB;
            color: #fff;
        }
        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
        }
        tbody tr:nth-child(even) { background: #F8FAFC; }
        tbody tr:nth-child(odd)  { background: #fff; }
        tbody td {
            padding: 9px 12px;
            font-size: 12px;
            border-bottom: 1px solid #E2E8F0;
        }
        .grade-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: 11px;
        }
        .grade-a  { background: #D1FAE5; color: #065F46; }
        .grade-b  { background: #DBEAFE; color: #1E40AF; }
        .grade-c  { background: #FEF3C7; color: #92400E; }
        .grade-f  { background: #FEE2E2; color: #991B1B; }
        .grade-abs{ background: #F1F5F9; color: #64748B; }

        .pass  { color: #059669; font-weight: 600; }
        .fail  { color: #DC2626; font-weight: 600; }
        .absent-text { color: #64748B; }

        /* Progress bar */
        .progress-bar-wrap {
            background: #E2E8F0;
            border-radius: 4px;
            height: 7px;
            width: 80px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 6px;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 4px;
        }

        /* Footer */
        .footer {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid #E2E8F0;
            display: table;
            width: 100%;
        }
        .footer-left  { display: table-cell; font-size: 11px; color: #64748B; }
        .footer-right { display: table-cell; text-align: right; font-size: 11px; color: #64748B; }

        .signature-line {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        .sig-cell {
            display: table-cell;
            width: 33%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #94A3B8;
            margin: 0 20px;
            padding-top: 6px;
            font-size: 11px;
            color: #64748B;
        }

        .watermark-pass {
            color: #059669;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .watermark-fail {
            color: #DC2626;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <div class="school-name">{{ $school->name ?? 'EduCore School' }}</div>
        <div class="report-title">Academic Report Card</div>
        <div class="exam-name">{{ $exam->name }} · {{ $exam->start_date->format('M Y') }}</div>
    </div>

    {{-- Student Information --}}
    <div class="student-info">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-col">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Student Name</div>
                            <div class="info-value">{{ $student->user->name }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Admission No.</div>
                            <div class="info-value">{{ $student->admission_number }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Class</div>
                            <div class="info-value">
                                @if($enrollment)
                                    {{ $enrollment->section->schoolClass->name }}
                                    — Section {{ $enrollment->section->name }}
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-col">
                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-label">Academic Year</div>
                            <div class="info-value">{{ $enrollment?->academicYear->name ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Date of Birth</div>
                            <div class="info-value">
                                {{ $student->date_of_birth
                                    ? \Carbon\Carbon::parse($student->date_of_birth)->format('M d, Y')
                                    : 'N/A' }}
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Generated On</div>
                            <div class="info-value">{{ now()->format('M d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Result Summary --}}
    <div class="result-summary">
        <div class="summary-box">
            <div class="summary-number">{{ $reportCard->obtained_marks }}</div>
            <div class="summary-label">Marks Obtained</div>
        </div>
        <div class="summary-box">
            <div class="summary-number">{{ $reportCard->total_marks }}</div>
            <div class="summary-label">Total Marks</div>
        </div>
        <div class="summary-box grade-box">
            <div class="summary-number">{{ $reportCard->percentage }}%</div>
            <div class="summary-label">Percentage</div>
        </div>
        <div class="summary-box grade-box">
            <div class="summary-number">{{ $reportCard->grade }}</div>
            <div class="summary-label">Grade</div>
        </div>
        <div class="summary-box position-box">
            <div class="summary-number">#{{ $reportCard->position ?? '—' }}</div>
            <div class="summary-label">Class Position</div>
        </div>
        <div class="summary-box">
            <div class="summary-number
                {{ $reportCard->percentage >= 40 ? 'pass' : 'fail' }}">
                {{ $reportCard->percentage >= 40 ? 'PASS' : 'FAIL' }}
            </div>
            <div class="summary-label">Result</div>
        </div>
    </div>

    {{-- Subject-wise Marks --}}
    <div class="section-title">Subject-wise Performance</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Full Marks</th>
                <th>Pass Marks</th>
                <th>Obtained</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $i => $mark)
            @php
                $pct = $mark->is_absent ? 0
                     : round(($mark->marks_obtained / $mark->examSubject->full_marks) * 100);
                $gradeClass = match(true) {
                    $mark->grade === 'ABS'           => 'grade-abs',
                    in_array($mark->grade, ['A+','A']) => 'grade-a',
                    $mark->grade === 'B'             => 'grade-b',
                    in_array($mark->grade, ['C','D']) => 'grade-c',
                    default                          => 'grade-f',
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $mark->examSubject->subject->name }}</strong></td>
                <td>{{ $mark->examSubject->full_marks }}</td>
                <td>{{ $mark->examSubject->passing_marks }}</td>
                <td>{{ $mark->is_absent ? '—' : $mark->marks_obtained }}</td>
                <td>
                    @if(!$mark->is_absent)
                    <div class="progress-bar-wrap">
                        <div class="progress-bar-fill"
                             style="width:{{ $pct }}%;
                                    background:{{ $pct >= 60 ? '#059669' : '#DC2626' }}">
                        </div>
                    </div>
                    {{ $pct }}%
                    @else
                    —
                    @endif
                </td>
                <td>
                    <span class="grade-badge {{ $gradeClass }}">{{ $mark->grade }}</span>
                </td>
                <td>
                    @if($mark->is_absent)
                        <span class="absent-text">Absent</span>
                    @elseif($mark->marks_obtained >= $mark->examSubject->passing_marks)
                        <span class="pass">Pass</span>
                    @else
                        <span class="fail">Fail</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Grading Legend --}}
    <div style="margin-bottom:20px;font-size:11px;color:#64748B">
        <strong>Grading Scale:</strong>
        A+ (90–100%) &nbsp;·&nbsp;
        A (80–89%) &nbsp;·&nbsp;
        B (70–79%) &nbsp;·&nbsp;
        C (60–69%) &nbsp;·&nbsp;
        D (50–59%) &nbsp;·&nbsp;
        F (Below 50%)
    </div>

    {{-- Remarks --}}
    @if($reportCard->remarks)
    <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:6px;
                padding:10px 14px;margin-bottom:20px;font-size:12px">
        <strong>Remarks:</strong> {{ $reportCard->remarks }}
    </div>
    @endif

    {{-- Signature Lines --}}
    <div class="signature-line">
        <div class="sig-cell">
            <div class="sig-line">Class Teacher</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">Principal</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line">Parent / Guardian</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-left">
            {{ $school->name ?? 'EduCore School' }}
            @if(isset($school->address)) · {{ $school->address }} @endif
        </div>
        <div class="footer-right">
            Generated by EduCore · {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>

</body>
</html>