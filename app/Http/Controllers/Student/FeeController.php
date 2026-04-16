<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FeeInvoice;

class FeeController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;

        $invoices = FeeInvoice::where('student_id', $student->id)
            ->latest()
            ->paginate(15);

        $totalPaid    = FeeInvoice::where('student_id', $student->id)
            ->where('status', 'paid')->sum('net_amount');
        $totalPending = FeeInvoice::where('student_id', $student->id)
            ->where('status', 'unpaid')->sum('net_amount');

        return view('student.fees', compact(
            'invoices', 'totalPaid', 'totalPending'
        ));
    }
}