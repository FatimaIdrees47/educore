<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboard;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\EduParent\DashboardController as ParentDashboard;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\TimetableController;
use App\Http\Controllers\Admin\LeaveController as AdminLeave;
use App\Http\Controllers\Student\AttendanceController as StudentAttendance;
use App\Http\Controllers\Student\ResultController as StudentResult;
use App\Http\Controllers\Student\FeeController as StudentFee;
use App\Http\Controllers\Student\NoticeController as StudentNotice;
use App\Http\Controllers\Student\TimetableController as StudentTimetable;
use App\Http\Controllers\Student\HomeworkController as StudentHomework;
use App\Http\Controllers\Teacher\AttendanceController as TeacherAttendance;
use App\Http\Controllers\Teacher\NoticeController as TeacherNotice;
use App\Http\Controllers\Teacher\TimetableController as TeacherTimetable;
use App\Http\Controllers\Teacher\HomeworkController as TeacherHomework;
use App\Http\Controllers\Teacher\LeaveController as TeacherLeave;
use App\Http\Controllers\Teacher\MessageController as TeacherMessage;
use App\Http\Controllers\EduParent\AttendanceController as ParentAttendance;
use App\Http\Controllers\EduParent\ResultController as ParentResult;
use App\Http\Controllers\EduParent\FeeController as ParentFee;
use App\Http\Controllers\EduParent\NoticeController as ParentNotice;
use App\Http\Controllers\EduParent\MessageController as ParentMessage;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\SuperAdmin\SchoolController;
use App\Http\Controllers\SuperAdmin\SettingsController as SuperAdminSettings;
use App\Http\Controllers\Admin\LibraryController;

Route::get('/', fn() => redirect()->route('login'));

// ── Profile ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Super Admin ────────────────────────────────────────────────
Route::middleware(['auth', 'role:super-admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboard::class, 'index'])->name('dashboard');

        // Schools
        Route::get('/schools',             [SchoolController::class, 'index'])->name('schools.index');
        Route::post('/schools',            [SchoolController::class, 'store'])->name('schools.store');
        Route::get('/schools/{school}',    [SchoolController::class, 'show'])->name('schools.show');
        Route::post(
            '/schools/{school}/toggle-status',
            [SchoolController::class, 'toggleStatus']
        )->name('schools.toggle-status');
        Route::delete('/schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');

        // Settings
        Route::get('/settings',           [SuperAdminSettings::class, 'index'])->name('settings');
        Route::post('/settings/password', [SuperAdminSettings::class, 'updatePassword'])->name('settings.password');
    });

// ── School Admin ───────────────────────────────────────────────
Route::middleware(['auth', 'role:school-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Classes
        Route::get('/classes',             [ClassController::class, 'index'])->name('classes.index');
        Route::post('/classes',            [ClassController::class, 'storeClass'])->name('classes.store');
        Route::put('/classes/{class}',     [ClassController::class, 'updateClass'])->name('classes.update');
        Route::delete('/classes/{class}',  [ClassController::class, 'destroyClass'])->name('classes.destroy');

        // Sections
        Route::post('/sections',             [ClassController::class, 'storeSection'])->name('sections.store');
        Route::put('/sections/{section}',    [ClassController::class, 'updateSection'])->name('sections.update');
        Route::delete('/sections/{section}', [ClassController::class, 'destroySection'])->name('sections.destroy');

        // Subjects
        Route::post('/subjects',             [ClassController::class, 'storeSubject'])->name('subjects.store');
        Route::delete('/subjects/{subject}', [ClassController::class, 'destroySubject'])->name('subjects.destroy');

        // Students
        Route::get('/students',              [StudentController::class, 'index'])->name('students.index');
        Route::post('/students',             [StudentController::class, 'store'])->name('students.store');
        Route::get('/students/{student}',    [StudentController::class, 'show'])->name('students.show');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

        // Teachers
        Route::get('/teachers',                [TeacherController::class, 'index'])->name('teachers.index');
        Route::post('/teachers',               [TeacherController::class, 'store'])->name('teachers.store');
        Route::get('/teachers/{teacher}',      [TeacherController::class, 'show'])->name('teachers.show');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
        Route::put('/teachers/{teacher}',      [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}',   [TeacherController::class, 'destroy'])->name('teachers.destroy');

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

        // Fees
        Route::get('/fees',                               [FeeController::class, 'index'])->name('fees.index');
        Route::post('/fees/structures',                   [FeeController::class, 'storeStructure'])->name('fees.structures.store');
        Route::delete('/fees/structures/{feeStructure}',  [FeeController::class, 'destroyStructure'])->name('fees.structures.destroy');
        Route::post('/fees/generate',                     [FeeController::class, 'generateInvoices'])->name('fees.generate');
        Route::post('/fees/{feeInvoice}/collect',         [FeeController::class, 'collectPayment'])->name('fees.collect');

        // Exams
        Route::get('/exams',                                      [ExamController::class, 'index'])->name('exams.index');
        Route::post('/exams',                                     [ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}',                               [ExamController::class, 'show'])->name('exams.show');
        Route::delete('/exams/{exam}',                            [ExamController::class, 'destroy'])->name('exams.destroy');
        Route::post('/exams/{exam}/subjects',                     [ExamController::class, 'addSubject'])->name('exams.subjects.store');
        Route::get('/exams/{exam}/subjects/{examSubject}/marks',  [ExamController::class, 'enterMarks'])->name('exams.marks.index');
        Route::post('/exams/{exam}/subjects/{examSubject}/marks', [ExamController::class, 'saveMarks'])->name('exams.marks.store');
        Route::post('/exams/{exam}/report-cards',                 [ExamController::class, 'generateReportCards'])->name('exams.report-cards.generate');
        Route::post('/exams/{exam}/publish',                      [ExamController::class, 'publishResults'])->name('exams.publish');
        Route::post('/exams/{exam}/complete',                     [ExamController::class, 'markCompleted'])->name('exams.complete');

        // Notices
        Route::get('/notices',               [NoticeController::class, 'index'])->name('notices.index');
        Route::post('/notices',              [NoticeController::class, 'store'])->name('notices.store');
        Route::get('/notices/{notice}/edit', [NoticeController::class, 'edit'])->name('notices.edit');
        Route::put('/notices/{notice}',      [NoticeController::class, 'update'])->name('notices.update');
        Route::delete('/notices/{notice}',   [NoticeController::class, 'destroy'])->name('notices.destroy');

        // Timetable
        Route::get('/timetable',                    [TimetableController::class, 'index'])->name('timetable.index');
        Route::post('/timetable',                   [TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('/timetable/{timetableSlot}', [TimetableController::class, 'destroy'])->name('timetable.destroy');

        // Leave Management
        Route::get('/leave',                              [AdminLeave::class, 'index'])->name('leave.index');
        Route::post('/leave/{leaveApplication}/approve',  [AdminLeave::class, 'approve'])->name('leave.approve');
        Route::post('/leave/{leaveApplication}/reject',   [AdminLeave::class, 'reject'])->name('leave.reject');

        // Settings
        Route::get('/settings',                        [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings',                       [SettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/grading',               [SettingsController::class, 'updateGrading'])->name('settings.grading');
        Route::post('/settings/academic-years',        [SettingsController::class, 'storeAcademicYear'])->name('settings.academic-years.store');
        Route::post('/settings/academic-years/{academicYear}/activate', [SettingsController::class, 'activateYear'])->name('settings.academic-years.activate');

        // PDF
        Route::get('/exams/{exam}/report-cards/{student}/pdf', [ExamController::class, 'downloadReportCard'])->name('exams.report-cards.pdf');

        // Library
        Route::get('/library',                        [LibraryController::class, 'index'])->name('library.index');
        Route::post('/library',                       [LibraryController::class, 'store'])->name('library.store');
        Route::delete('/library/{libraryBook}',       [LibraryController::class, 'destroy'])->name('library.destroy');
        Route::get('/library/issues',                 [LibraryController::class, 'issues'])->name('library.issues');
        Route::post('/library/issues',                [LibraryController::class, 'issueBook'])->name('library.issues.store');
        Route::post('/library/issues/{bookIssue}/return', [LibraryController::class, 'returnBook'])->name('library.issues.return');
    });

// ── Teacher ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard',  [TeacherDashboard::class, 'index'])->name('dashboard');
        Route::get('/attendance', [TeacherAttendance::class, 'index'])->name('attendance.index');
        Route::get('/notices',    [TeacherNotice::class, 'index'])->name('notices.index');
        Route::get('/timetable',  [TeacherTimetable::class, 'index'])->name('timetable');

        // Homework
        Route::get('/homework',              [TeacherHomework::class, 'index'])->name('homework.index');
        Route::post('/homework',             [TeacherHomework::class, 'store'])->name('homework.store');
        Route::get('/homework/{homework}',   [TeacherHomework::class, 'show'])->name('homework.show');
        Route::post(
            '/homework/{homework}/submissions/{submission}/grade',
            [TeacherHomework::class, 'grade']
        )->name('homework.grade');
        Route::delete('/homework/{homework}', [TeacherHomework::class, 'destroy'])->name('homework.destroy');

        // Leave
        Route::get('/leave',                       [TeacherLeave::class, 'index'])->name('leave.index');
        Route::post('/leave',                      [TeacherLeave::class, 'store'])->name('leave.store');
        Route::delete('/leave/{leaveApplication}', [TeacherLeave::class, 'destroy'])->name('leave.destroy');

        // Messages
        Route::get('/messages',                [TeacherMessage::class, 'index'])->name('messages.index');
        Route::get('/messages/{parent}',       [TeacherMessage::class, 'show'])->name('messages.show');
        Route::post('/messages/reply',         [TeacherMessage::class, 'reply'])->name('messages.reply');
        Route::get('/messages/{parent}/poll',  [TeacherMessage::class, 'poll'])->name('messages.poll');
    });

// ── Student ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard',            [StudentDashboard::class, 'index'])->name('dashboard');
        Route::get('/attendance',           [StudentAttendance::class, 'index'])->name('attendance');
        Route::get('/results',              [StudentResult::class, 'index'])->name('results');
        Route::get('/results/{reportCard}', [StudentResult::class, 'show'])->name('results.show');
        Route::get('/fees',                 [StudentFee::class, 'index'])->name('fees');
        Route::get('/notices',              [StudentNotice::class, 'index'])->name('notices');
        Route::get('/timetable',            [StudentTimetable::class, 'index'])->name('timetable');
        Route::get('/homework',             [StudentHomework::class, 'index'])->name('homework');
        Route::post('/homework/{homework}/submit', [StudentHomework::class, 'submit'])->name('homework.submit');
        Route::get('/results/{reportCard}/pdf', [StudentResult::class, 'downloadPdf'])->name('results.pdf');
    });

// ── Parent ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:parent'])
    ->prefix('parent')
    ->name('parent.')
    ->group(function () {
        Route::get('/dashboard',              [ParentDashboard::class, 'index'])->name('dashboard');
        Route::get('/switch-child/{student}', [ParentDashboard::class, 'switchChild'])->name('switch-child');
        Route::get('/attendance',             [ParentAttendance::class, 'index'])->name('attendance');
        Route::get('/results',                [ParentResult::class, 'index'])->name('results');
        Route::get('/results/{reportCard}',   [ParentResult::class, 'show'])->name('results.show');
        Route::get('/fees',                   [ParentFee::class, 'index'])->name('fees');
        Route::get('/notices',                [ParentNotice::class, 'index'])->name('notices');

        // Messages
        Route::get('/messages',                 [ParentMessage::class, 'index'])->name('messages');
        Route::get('/messages/{teacher}',        [ParentMessage::class, 'show'])->name('messages.show');
        Route::post('/messages',                 [ParentMessage::class, 'send'])->name('messages.send');
        Route::get('/messages/{teacher}/poll',   [ParentMessage::class, 'poll'])->name('messages.poll');
    });

require __DIR__ . '/auth.php';
