<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\ExamMark;
use App\Models\ReportCard;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Notice;
use App\Models\TimetableSlot;
use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\ExamSubject;
use App\Models\Exam;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId     = 1;
        $sectionId    = 2;
        $adminId      = 2;
        $teacherId    = 3;
        $academicYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $subject      = Subject::where('school_id', $schoolId)->first();
        $subjects     = Subject::where('school_id', $schoolId)->get();
        $exam         = Exam::where('school_id', $schoolId)->first();
        $examSubject  = ExamSubject::where('exam_id', $exam->id)->first();
        $feeStructure = FeeStructure::where('school_id', $schoolId)->first();

        // ── 1. Students ───────────────────────────────────────────
        $studentsData = [
            ['name' => 'Ahmed Ali',    'email' => 'ahmed@demo.com',  'gender' => 'male',   'dob' => '2002-05-15', 'blood' => 'A+',  'adm' => 'ADM-2026-002'],
            ['name' => 'Fatima Malik', 'email' => 'fatima@demo.com', 'gender' => 'female', 'dob' => '2002-08-22', 'blood' => 'B+',  'adm' => 'ADM-2026-003'],
            ['name' => 'Usman Tariq',  'email' => 'usman@demo.com',  'gender' => 'male',   'dob' => '2001-11-10', 'blood' => 'O+',  'adm' => 'ADM-2026-004'],
            ['name' => 'Zara Hassan',  'email' => 'zara@demo.com',   'gender' => 'female', 'dob' => '2002-03-28', 'blood' => 'AB+', 'adm' => 'ADM-2026-005'],
            ['name' => 'Ali Raza',     'email' => 'ali@demo.com',    'gender' => 'male',   'dob' => '2001-07-19', 'blood' => 'A-',  'adm' => 'ADM-2026-006'],
            ['name' => 'Hina Baig',    'email' => 'hina@demo.com',   'gender' => 'female', 'dob' => '2002-01-05', 'blood' => 'B-',  'adm' => 'ADM-2026-007'],
            ['name' => 'Omar Sheikh',  'email' => 'omar@demo.com',   'gender' => 'male',   'dob' => '2001-09-30', 'blood' => 'O-',  'adm' => 'ADM-2026-008'],
            ['name' => 'Sana Qureshi', 'email' => 'sana@demo.com',   'gender' => 'female', 'dob' => '2002-12-14', 'blood' => 'A+',  'adm' => 'ADM-2026-009'],
            ['name' => 'Bilal Ahmed',  'email' => 'bilal@demo.com',  'gender' => 'male',   'dob' => '2001-06-08', 'blood' => 'B+',  'adm' => 'ADM-2026-010'],
            ['name' => 'Noor Fatima',  'email' => 'noor@demo.com',   'gender' => 'female', 'dob' => '2002-04-17', 'blood' => 'O+',  'adm' => 'ADM-2026-011'],
        ];

        $createdStudents = [];

        // Always include Ayesha (student id 1)
        $ayesha = Student::find(1);
        if ($ayesha) $createdStudents[] = $ayesha;

        foreach ($studentsData as $s) {
            if (User::where('email', $s['email'])->exists()) {
                echo "Skipping existing: {$s['name']}\n";
                $user    = User::where('email', $s['email'])->first();
                $student = Student::where('user_id', $user->id)->first();
                if ($student) $createdStudents[] = $student;
                continue;
            }

            $user = User::create([
                'school_id' => $schoolId,
                'name'      => $s['name'],
                'email'     => $s['email'],
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);
            $user->assignRole('student');

            $student = Student::create([
                'user_id'          => $user->id,
                'school_id'        => $schoolId,
                'admission_number' => $s['adm'],
                'date_of_birth'    => $s['dob'],
                'gender'           => $s['gender'],
                'blood_group'      => $s['blood'],
                'religion'         => 'Muslim',
                'address'          => 'Karachi, Pakistan',
                'admission_date'   => '2026-04-05',
                'status'           => 'active',
            ]);

            Enrollment::firstOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
                [
                    'section_id'  => $sectionId,
                    'roll_number' => rand(10, 99),
                    'status'      => 'active',
                ]
            );

            $createdStudents[] = $student;
            echo "Created student: {$s['name']}\n";
        }

        // ── 2. More Teachers ──────────────────────────────────────
        $teachersData = [
            ['name' => 'Mr. Khalid Mahmood', 'email' => 'khalid@democschool.com', 'emp' => 'EMP-002', 'qual' => 'M.Sc Physics'],
            ['name' => 'Ms. Amna Siddiqui',  'email' => 'amna@democschool.com',   'emp' => 'EMP-003', 'qual' => 'M.A English'],
            ['name' => 'Mr. Tariq Mehmood',  'email' => 'tariq@democschool.com',  'emp' => 'EMP-004', 'qual' => 'M.Sc Chemistry'],
        ];

        foreach ($teachersData as $t) {
            if (User::where('email', $t['email'])->exists()) {
                echo "Skipping existing teacher: {$t['name']}\n";
                continue;
            }
            $user = User::create([
                'school_id' => $schoolId,
                'name'      => $t['name'],
                'email'     => $t['email'],
                'password'  => Hash::make('password'),
                'is_active' => true,
            ]);
            $user->assignRole('teacher');
            Teacher::create([
                'user_id'        => $user->id,
                'school_id'      => $schoolId,
                'employee_id'    => $t['emp'],
                'qualifications' => $t['qual'],
                'joining_date'   => '2026-04-05',
                'salary'         => 5000000,
                'leave_balance'  => 21,
            ]);
            echo "Created teacher: {$t['name']}\n";
        }

        // ── 3. Attendance ─────────────────────────────────────────
        $statuses = ['present', 'present', 'present', 'present', 'absent', 'present', 'present', 'late', 'present', 'present'];

        for ($i = 30; $i >= 1; $i--) {
            $date      = now()->subDays($i)->format('Y-m-d');
            $dayOfWeek = now()->subDays($i)->dayOfWeek;
            if (in_array($dayOfWeek, [5, 6])) continue;

            foreach ($createdStudents as $index => $student) {
                if (!$student) continue;
                $status = $statuses[($index + $i) % count($statuses)];
                Attendance::firstOrCreate(
                    ['student_id' => $student->id, 'date' => $date],
                    [
                        'section_id'       => $sectionId,
                        'academic_year_id' => $academicYear->id,
                        'status'           => $status,
                        'marked_by'        => $adminId,
                    ]
                );
            }
        }
        echo "Attendance seeded\n";

        // ── 4. Fee Invoices ───────────────────────────────────────
        $months = [
            ['month' => 1, 'year' => 2026],
            ['month' => 2, 'year' => 2026],
            ['month' => 3, 'year' => 2026],
            ['month' => 4, 'year' => 2026],
        ];

        foreach ($createdStudents as $student) {
            if (!$student) continue;
            foreach ($months as $idx => $m) {
                $isPaid = $idx < 3;

                $invoice = FeeInvoice::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'month'      => $m['month'],
                        'year'       => $m['year'],
                    ],
                    [
                        'school_id'        => $schoolId,
                        'academic_year_id' => $academicYear->id,
                        'fee_structure_id' => $feeStructure->id,
                        'receipt_number'   => 'RCP-' . $student->id . '-' . $m['month'] . '-' . $m['year'],
                        'fee_type'         => 'Monthly Tuition',
                        'amount'           => 500000,
                        'fine_amount'      => 0,
                        'discount_amount'  => 0,
                        'net_amount'       => 500000,
                        'due_date'         => Carbon::createFromDate($m['year'], $m['month'], 10),
                        'status'           => $isPaid ? 'paid' : 'unpaid',
                    ]
                );

                if ($isPaid && $invoice->wasRecentlyCreated) {
                    FeePayment::create([
                        'fee_invoice_id' => $invoice->id,
                        'collected_by'   => $adminId,
                        'amount'         => 500000,
                        'payment_method' => 'cash',
                        'reference'      => 'REF-' . $student->id . '-' . $m['month'],
                        'paid_at'        => Carbon::createFromDate($m['year'], $m['month'], rand(1, 9)),
                    ]);
                }
            }
        }
        echo "Fee invoices seeded\n";

        // ── 5. Exam Marks ─────────────────────────────────────────
        $marksData = [85, 72, 91, 65, 78, 55, 88, 43, 95, 61, 70];

        foreach ($createdStudents as $index => $student) {
            if (!$student) continue;
            $marks = $marksData[$index % count($marksData)];
            $pct   = round(($marks / $examSubject->full_marks) * 100);
            $grade = match (true) {
                $pct >= 90 => 'A+',
                $pct >= 80 => 'A',
                $pct >= 70 => 'B',
                $pct >= 60 => 'C',
                $pct >= 50 => 'D',
                default    => 'F',
            };

            ExamMark::updateOrCreate(
                [
                    'exam_id'         => $exam->id,
                    'exam_subject_id' => $examSubject->id,
                    'student_id'      => $student->id,
                ],
                [
                    'marks_obtained' => $marks,
                    'grade'          => $grade,
                    'is_absent'      => false,
                    'entered_by'     => $adminId,
                ]
            );
        }
        echo "Exam marks seeded\n";

        // ── 6. Report Cards ───────────────────────────────────────
        $allMarks = ExamMark::where('exam_id', $exam->id)
            ->orderByDesc('marks_obtained')
            ->get();

        $position = 1;
        foreach ($allMarks as $mark) {
            $pct   = round(($mark->marks_obtained / $examSubject->full_marks) * 100);
            $grade = match (true) {
                $pct >= 90 => 'A+',
                $pct >= 80 => 'A',
                $pct >= 70 => 'B',
                $pct >= 60 => 'C',
                $pct >= 50 => 'D',
                default    => 'F',
            };

            ReportCard::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $mark->student_id],
                [
                    'total_marks'    => $examSubject->full_marks,
                    'obtained_marks' => $mark->marks_obtained,
                    'percentage'     => $pct,
                    'grade'          => $grade,
                    'position'       => $position++,
                    'published_at'   => now(),
                ]
            );
        }
        echo "Report cards seeded\n";

        // ── 7. Homework ───────────────────────────────────────────
        $homeworkList = [
            ['title' => 'Chapter 3 Exercises',     'desc' => 'Complete all exercises',       'due' => now()->addDays(3),  'marks' => 10],
            ['title' => 'Practice Problems Set 2',  'desc' => 'Solve problems 1-20 page 45', 'due' => now()->addDays(7),  'marks' => 20],
            ['title' => 'Revision Assignment',      'desc' => 'Revise chapters 1-5',          'due' => now()->subDays(2), 'marks' => 15],
        ];

        foreach ($homeworkList as $hw) {
            $homework = Homework::create([
                'school_id'   => $schoolId,
                'teacher_id'  => $teacherId,
                'section_id'  => $sectionId,
                'subject_id'  => $subject->id,
                'title'       => $hw['title'],
                'description' => $hw['desc'],
                'due_date'    => $hw['due'],
                'total_marks' => $hw['marks'],
            ]);

            foreach (collect($createdStudents)->take(7) as $student) {
                if (!$student) continue;
                HomeworkSubmission::firstOrCreate(
                    ['homework_id' => $homework->id, 'student_id' => $student->id],
                    [
                        'notes'          => 'Completed the assignment as instructed.',
                        'submitted_at'   => now()->subHours(rand(1, 48)),
                        'marks_obtained' => rand(6, $hw['marks']),
                        'feedback'       => 'Good work, keep it up!',
                        'graded_at'      => now()->subHours(rand(1, 24)),
                    ]
                );
            }
            echo "Homework: {$hw['title']}\n";
        }

        // ── 8. Notices ────────────────────────────────────────────
        $noticesList = [
            ['title' => 'Eid ul Fitr Holiday',     'body' => 'School closed April 28 to May 5 for Eid holidays. Classes resume May 6.', 'role' => 'all'],
            ['title' => 'Parent-Teacher Meeting',  'body' => 'PTM scheduled April 25 at 9:00 AM. All parents are requested to attend.', 'role' => 'parent'],
            ['title' => 'Staff Meeting Notice',    'body' => 'All staff must attend monthly meeting on April 22 at 2:00 PM.',           'role' => 'teacher'],
            ['title' => 'Exam Schedule Released',  'body' => 'Final term exam schedule is now available on the student portal.',        'role' => 'student'],
            ['title' => 'Fee Submission Deadline', 'body' => 'Last date for April fee submission is April 10. Late fee PKR 200.',       'role' => 'all'],
            ['title' => 'Annual Sports Day',       'body' => 'Sports day on May 15, 2026. Students encouraged to participate.',        'role' => 'all'],
        ];

        foreach ($noticesList as $n) {
            Notice::create([
                'school_id'    => $schoolId,
                'posted_by'    => $adminId,
                'title'        => $n['title'],
                'body'         => $n['body'],
                'target_role'  => $n['role'],
                'published_at' => now()->subDays(rand(1, 10)),
            ]);
        }
        echo "Notices seeded\n";

        // ── 9. Timetable ──────────────────────────────────────────
        $slots = [
            ['day' => 'monday',    'start' => '08:00', 'end' => '09:00', 'room' => 'Room 1'],
            ['day' => 'monday',    'start' => '09:00', 'end' => '10:00', 'room' => 'Room 1'],
            ['day' => 'tuesday',   'start' => '08:00', 'end' => '09:00', 'room' => 'Room 1'],
            ['day' => 'tuesday',   'start' => '09:00', 'end' => '10:00', 'room' => 'Lab 1'],
            ['day' => 'wednesday', 'start' => '08:00', 'end' => '09:00', 'room' => 'Room 1'],
            ['day' => 'wednesday', 'start' => '09:00', 'end' => '10:00', 'room' => 'Room 2'],
            ['day' => 'thursday',  'start' => '08:00', 'end' => '09:00', 'room' => 'Room 1'],
            ['day' => 'thursday',  'start' => '09:00', 'end' => '10:00', 'room' => 'Room 3'],
        ];

        foreach ($slots as $index => $slot) {
            $subj = $subjects[$index % $subjects->count()];
            try {
                TimetableSlot::create([
                    'school_id'        => $schoolId,
                    'section_id'       => $sectionId,
                    'subject_id'       => $subj->id,
                    'teacher_id'       => $teacherId,
                    'academic_year_id' => $academicYear->id,
                    'day_of_week'      => $slot['day'],
                    'start_time'       => $slot['start'],
                    'end_time'         => $slot['end'],
                    'room'             => $slot['room'],
                ]);
                echo "Slot: {$slot['day']} {$slot['start']}\n";
            } catch (\Exception $e) {
                echo "Skipped (conflict): {$slot['day']} {$slot['start']}\n";
            }
        }

        echo "\n✅ Demo data seeded successfully!\n";
    }
}
