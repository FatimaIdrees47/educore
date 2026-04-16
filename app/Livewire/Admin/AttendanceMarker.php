<?php

namespace App\Livewire\Admin;

use App\Models\Section;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Services\AttendanceService;
use Livewire\Component;

class AttendanceMarker extends Component
{
    public int    $schoolId;
    public string $date;
    public ?int   $selectedClassId   = null;
    public ?int   $selectedSectionId = null;
    public array  $attendance        = [];
    public bool   $alreadyMarked     = false;
    public bool   $saved             = false;
    public string $message           = '';

    public $classes;
    public array $sections = [];
    public array $students = [];

    protected AttendanceService $attendanceService;

    public function boot(AttendanceService $attendanceService): void
    {
        $this->attendanceService = $attendanceService;
    }

    public function mount(int $schoolId): void
    {
        $this->schoolId = $schoolId;
        $this->date     = today()->toDateString();
        $this->classes  = SchoolClass::forSchool($schoolId)
            ->with('sections')
            ->orderBy('numeric_order')
            ->get();
    }

    public function updatedSelectedClassId($value): void
    {
        $this->selectedSectionId = null;
        $this->students          = [];
        $this->attendance        = [];
        $this->alreadyMarked     = false;
        $this->saved             = false;

        if ($value) {
            $class = $this->classes->find($value);
            $this->sections = $class
                ? $class->sections->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray()
                : [];
        } else {
            $this->sections = [];
        }
    }

    public function updatedSelectedSectionId($value): void
    {
        $this->students      = [];
        $this->attendance    = [];
        $this->alreadyMarked = false;
        $this->saved         = false;

        if ($value) {
            $this->loadStudents();
        }
    }

    public function updatedDate(): void
    {
        if ($this->selectedSectionId) {
            $this->loadStudents();
        }
    }

    public function loadStudents(): void
    {
        if (!$this->selectedSectionId) return;

        $enrollments = Enrollment::where('section_id', $this->selectedSectionId)
            ->where('status', 'active')
            ->with('student.user')
            ->get();

        $this->students = $enrollments->map(fn($e) => [
            'id'   => $e->student->id,
            'name' => $e->student->user->name,
        ])->toArray();

        $this->alreadyMarked = $this->attendanceService
            ->isAlreadyMarked($this->selectedSectionId, $this->date);

        $existing = $this->attendanceService
            ->getAttendanceForSection($this->selectedSectionId, $this->date);

        $this->attendance = [];
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = [
                'status'  => $existing[$student['id']]['status'] ?? 'present',
                'remarks' => $existing[$student['id']]['remarks'] ?? '',
            ];
        }
    }

    public function markAll(string $status): void
    {
        foreach ($this->students as $student) {
            $this->attendance[$student['id']]['status'] = $status;
        }
    }

    public function save(): void
    {
        if (empty($this->students)) return;

        $this->attendanceService->markBulk(
            sectionId: $this->selectedSectionId,
            date:      $this->date,
            records:   $this->attendance,
            markedBy:  auth()->id(),
            schoolId:  $this->schoolId,
        );

        $this->alreadyMarked = true;
        $this->saved         = true;
        $this->message       = 'Attendance saved for ' . count($this->students) . ' students.';
    }

    public function render()
    {
        return view('livewire.admin.attendance-marker');
    }
}