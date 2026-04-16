<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ExamMark;
use App\Models\ReportCard;
use App\Models\Enrollment;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class ExamService
{
    public function createExam(array $data, int $schoolId): Exam
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->firstOrFail();

        return Exam::create([
            'school_id'        => $schoolId,
            'academic_year_id' => $activeYear->id,
            'name'             => $data['name'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'status'           => 'draft',
        ]);
    }

    public function addSubjectToExam(array $data): ExamSubject
    {
        return ExamSubject::create($data);
    }

    public function saveMarks(int $examSubjectId, array $marks): void
    {
        $examSubject = ExamSubject::findOrFail($examSubjectId);

        DB::transaction(function () use ($examSubject, $examSubjectId, $marks) {
            foreach ($marks as $studentId => $data) {
                $isAbsent      = isset($data['is_absent']) && $data['is_absent'];
                $marksObtained = $isAbsent ? 0 : (int)($data['marks'] ?? 0);
                $grade         = $isAbsent ? 'ABS' : $this->calculateGrade(
                    $marksObtained,
                    $examSubject->full_marks
                );

                ExamMark::updateOrCreate(
                    [
                        'exam_subject_id' => $examSubjectId,
                        'student_id'      => $studentId,
                    ],
                    [
                        'exam_id'        => $examSubject->exam_id,
                        'entered_by'     => auth()->id(),
                        'marks_obtained' => $marksObtained,
                        'grade'          => $grade,
                        'is_absent'      => $isAbsent,
                    ]
                );
            }
        });
    }

    public function generateReportCards(Exam $exam, int $classId): int
    {
        $activeYear = AcademicYear::where('school_id', $exam->school_id)
            ->where('is_active', true)
            ->firstOrFail();

        $enrollments = Enrollment::where('status', 'active')
            ->where('academic_year_id', $activeYear->id)
            ->whereHas('section', fn($q) => $q->where('class_id', $classId))
            ->get();

        $examSubjects = ExamSubject::where('exam_id', $exam->id)
            ->where('class_id', $classId)
            ->get();

        $count = 0;

        DB::transaction(function () use ($enrollments, $examSubjects, $exam, &$count) {
            foreach ($enrollments as $enrollment) {
                $totalMarks    = $examSubjects->sum('full_marks');
                $obtainedMarks = 0;

                foreach ($examSubjects as $examSubject) {
                    $mark = ExamMark::where('exam_subject_id', $examSubject->id)
                        ->where('student_id', $enrollment->student_id)
                        ->first();

                    if ($mark && !$mark->is_absent) {
                        $obtainedMarks += $mark->marks_obtained;
                    }
                }

                $percentage = $totalMarks > 0
                    ? round(($obtainedMarks / $totalMarks) * 100, 2)
                    : 0;

                $grade = $this->calculateGrade($obtainedMarks, $totalMarks);

                ReportCard::updateOrCreate(
                    [
                        'student_id' => $enrollment->student_id,
                        'exam_id'    => $exam->id,
                    ],
                    [
                        'total_marks'    => $totalMarks,
                        'obtained_marks' => $obtainedMarks,
                        'percentage'     => $percentage,
                        'grade'          => $grade,
                    ]
                );

                $count++;
            }

            // Calculate positions
            $reportCards = ReportCard::where('exam_id', $exam->id)
                ->orderByDesc('obtained_marks')
                ->get();

            foreach ($reportCards as $index => $card) {
                $card->update(['position' => $index + 1]);
            }
        });

        return $count;
    }

    public function publishResults(Exam $exam): void
    {
        $exam->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);

        ReportCard::where('exam_id', $exam->id)
            ->update(['published_at' => now()]);
    }

    public function calculateGrade(int $obtained, int $total): string
    {
        if ($total === 0) return 'N/A';
        $percentage = ($obtained / $total) * 100;

        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            $percentage >= 40 => 'E',
            default           => 'F',
        };
    }
}
