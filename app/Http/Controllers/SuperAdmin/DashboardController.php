<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\FeeInvoice;
use App\Models\LeaveApplication;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSchools    = School::count();
        $activeSchools   = School::where('status', 'active')->count();
        $inactiveSchools = School::where('status', 'inactive')->count();
        $totalStudents   = Student::count();
        $totalTeachers   = Teacher::count();
        $totalUsers      = User::count();
        $totalRevenue    = FeeInvoice::where('status', 'paid')->sum('net_amount');
        $pendingFees     = FeeInvoice::where('status', 'unpaid')->sum('net_amount');
        $pendingLeaves   = LeaveApplication::where('status', 'pending')->count();

        // Attendance today across all schools
        $todayAttendance = Attendance::whereDate('date', today())->count();

        // Schools with stats
        $schools = School::withCount(['students', 'teachers'])
            ->latest()
            ->take(8)
            ->get();

        // Recent schools
        $recentSchools = School::latest()->take(5)->get();

        // Monthly revenue data (last 6 months)
        $monthlyRevenue = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);
            return [
                'month'   => $month->format('M'),
                'revenue' => FeeInvoice::where('status', 'paid')
                    ->whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->sum('net_amount'),
            ];
        });

        return view('super-admin.dashboard', compact(
            'totalSchools', 'activeSchools', 'inactiveSchools',
            'totalStudents', 'totalTeachers', 'totalUsers',
            'totalRevenue', 'pendingFees', 'pendingLeaves',
            'todayAttendance', 'schools', 'recentSchools',
            'monthlyRevenue'
        ));
    }
}