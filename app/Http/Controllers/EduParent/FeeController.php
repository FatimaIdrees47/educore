<?php

namespace App\Http\Controllers\EduParent;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $user     = auth()->user();
        $children = $user->children()->get();

        $selectedStudentId = $request->get('student_id',
            session('selected_child_id', $children->first()?->id));
        $selectedStudent   = $children->firstWhere('id', $selectedStudentId) ?? $children->first();

        $invoices = $selectedStudent
            ? FeeInvoice::where('student_id', $selectedStudent->id)
                ->latest()->paginate(15)
            : collect();

        $totalPaid    = $selectedStudent
            ? FeeInvoice::where('student_id', $selectedStudent->id)
                ->where('status', 'paid')->sum('net_amount')
            : 0;
        $totalPending = $selectedStudent
            ? FeeInvoice::where('student_id', $selectedStudent->id)
                ->where('status', 'unpaid')->sum('net_amount')
            : 0;

        return view('parent.fees', compact(
            'children', 'selectedStudent', 'invoices',
            'totalPaid', 'totalPending'
        ));
    }
}