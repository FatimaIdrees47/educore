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
    public $sections  = [];
    public $students  = [];

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

        if ($value) {
            $class = $this->classes->find($value);
            $this->sections = $class ? $class->sections->toArray() : [];
        } else {
            $this->sections = [];
        }
    }

    public function updatedSelectedSectionId($value): void
    {
        $this->loadStudents();
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

        // Get enrolled students for this section
        $enrollments = Enrollment::where('section_id', $this->selectedSectionId)
            ->where('status', 'active')
            ->with('student.user')
            ->get();

        $this->students = $enrollments->map(fn($e) => [
            'id'   => $e->student->id,
            'name' => $e->student->user->name,
        ])->toArray();

        // Check if already marked
        $this->alreadyMarked = $this->attendanceService
            ->isAlreadyMarked($this->selectedSectionId, $this->date);

        // Pre-fill existing records or default to present
        $existing = $this->attendanceService
            ->getAttendanceForSection($this->selectedSectionId, $this->date);

        $this->attendance = [];
        foreach ($this->students as $student) {
            $this->attendance[$student['id']] = [
                'status'  => $existing[$student['id']]['status'] ?? 'present',
                'remarks' => $existing[$student['id']]['remarks'] ?? '',
            ];
        }

        $this->saved = false;
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
        $this->message       = 'Attendance saved successfully for ' . count($this->students) . ' students.';
    }

    public function render()
    {
        return view('livewire.admin.attendance-marker');
    }
}