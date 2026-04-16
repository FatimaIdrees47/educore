<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $schoolId = auth()->user()->school_id;

        $settings = SchoolSetting::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'school_name'   => auth()->user()->school->name,
                'grading_scale' => SchoolSetting::defaultGradingScale(),
                'currency'      => 'PKR',
                'timezone'      => 'Asia/Karachi',
            ]
        );

        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderByDesc('start_date')
            ->get();

        return view('admin.settings.index', compact('settings', 'academicYears'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name'    => 'required|string|max:200',
            'school_email'   => 'nullable|email|max:200',
            'school_phone'   => 'nullable|string|max:30',
            'school_address' => 'nullable|string|max:500',
            'school_website' => 'nullable|url|max:200',
            'principal_name' => 'nullable|string|max:200',
            'currency'       => 'required|string|max:10',
            'timezone'       => 'required|string|max:60',
        ]);

        $schoolId = auth()->user()->school_id;

        SchoolSetting::updateOrCreate(
            ['school_id' => $schoolId],
            $request->only([
                'school_name', 'school_email', 'school_phone',
                'school_address', 'school_website', 'principal_name',
                'currency', 'timezone',
            ]) + [
                'allow_parent_messages' => $request->boolean('allow_parent_messages'),
                'show_positions'        => $request->boolean('show_positions'),
            ]
        );

        // Also update school name in schools table
        auth()->user()->school->update(['name' => $request->school_name]);

        return back()->with('success', 'Settings saved successfully.');
    }

    public function updateGrading(Request $request)
    {
        $request->validate([
            'grades'       => 'required|array',
            'grades.*.grade' => 'required|string|max:5',
            'grades.*.min'   => 'required|integer|min:0|max:100',
            'grades.*.max'   => 'required|integer|min:0|max:100',
        ]);

        $schoolId = auth()->user()->school_id;

        SchoolSetting::updateOrCreate(
            ['school_id' => $schoolId],
            ['grading_scale' => $request->grades]
        );

        return back()->with('success', 'Grading scale updated.');
    }

    public function storeAcademicYear(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after:start_date',
        ]);

        $schoolId = auth()->user()->school_id;

        // Deactivate all existing years if new one is active
        if ($request->boolean('is_active')) {
            AcademicYear::where('school_id', $schoolId)
                ->update(['is_active' => false]);
        }

        AcademicYear::create([
            'school_id'  => $schoolId,
            'name'       => $request->name,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Academic year added.');
    }

    public function activateYear(AcademicYear $academicYear)
    {
        $schoolId = auth()->user()->school_id;
        if ($academicYear->school_id !== $schoolId) abort(403);

        AcademicYear::where('school_id', $schoolId)->update(['is_active' => false]);
        $academicYear->update(['is_active' => true]);

        return back()->with('success', 'Academic year activated.');
    }
}