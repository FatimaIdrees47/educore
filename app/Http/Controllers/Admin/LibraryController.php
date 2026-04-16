<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibraryBook;
use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $search   = $request->get('search');
        $category = $request->get('category');

        $books = LibraryBook::forSchool($schoolId)
            ->when($search, fn($q) => $q->where('title', 'like', "%$search%")
                ->orWhere('author', 'like', "%$search%")
                ->orWhere('isbn', 'like', "%$search%"))
            ->when($category, fn($q) => $q->where('category', $category))
            ->latest()
            ->paginate(15);

        $categories = LibraryBook::forSchool($schoolId)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        $totalBooks     = LibraryBook::forSchool($schoolId)->sum('total_copies');
        $availableBooks = LibraryBook::forSchool($schoolId)->sum('available_copies');
        $issuedBooks    = BookIssue::forSchool($schoolId)
            ->whereNull('return_date')->count();
        $overdueBooks   = BookIssue::forSchool($schoolId)
            ->whereNull('return_date')
            ->where('due_date', '<', today())
            ->count();

        return view('admin.library.index', compact(
            'books', 'categories', 'totalBooks',
            'availableBooks', 'issuedBooks', 'overdueBooks',
            'search', 'category'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:300',
            'author'         => 'nullable|string|max:200',
            'isbn'           => 'nullable|string|max:20',
            'publisher'      => 'nullable|string|max:200',
            'published_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'category'       => 'nullable|string|max:100',
            'shelf_location' => 'nullable|string|max:50',
            'total_copies'   => 'required|integer|min:1',
            'description'    => 'nullable|string',
        ]);

        LibraryBook::create([
            'school_id'        => auth()->user()->school_id,
            'title'            => $request->title,
            'author'           => $request->author,
            'isbn'             => $request->isbn,
            'publisher'        => $request->publisher,
            'published_year'   => $request->published_year,
            'category'         => $request->category,
            'shelf_location'   => $request->shelf_location,
            'total_copies'     => $request->total_copies,
            'available_copies' => $request->total_copies,
            'description'      => $request->description,
        ]);

        return back()->with('success', 'Book added successfully.');
    }

    public function destroy(LibraryBook $libraryBook)
    {
        $this->authorizeSchool($libraryBook->school_id);
        $libraryBook->delete();
        return back()->with('success', 'Book removed.');
    }

    public function issues(Request $request)
    {
        $schoolId = auth()->user()->school_id;
        $filter   = $request->get('filter', 'all');

        $issues = BookIssue::forSchool($schoolId)
            ->with(['book', 'issuedTo', 'issuedBy'])
            ->when($filter === 'active',  fn($q) => $q->whereNull('return_date'))
            ->when($filter === 'overdue', fn($q) => $q->whereNull('return_date')
                ->where('due_date', '<', today()))
            ->when($filter === 'returned', fn($q) => $q->whereNotNull('return_date'))
            ->latest()
            ->paginate(20);

        // Update overdue status
        BookIssue::forSchool($schoolId)
            ->whereNull('return_date')
            ->where('due_date', '<', today())
            ->where('status', '!=', 'overdue')
            ->update(['status' => 'overdue']);

        $books = LibraryBook::forSchool($schoolId)
            ->where('available_copies', '>', 0)
            ->orderBy('title')
            ->get();

        $members = User::where('school_id', $schoolId)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['student', 'teacher']))
            ->orderBy('name')
            ->get();

        return view('admin.library.issues', compact(
            'issues', 'books', 'members', 'filter'
        ));
    }

    public function issueBook(Request $request)
    {
        $request->validate([
            'book_id'    => 'required|exists:library_books,id',
            'issued_to'  => 'required|exists:users,id',
            'due_date'   => 'required|date|after:today',
            'notes'      => 'nullable|string|max:300',
        ]);

        $book = LibraryBook::findOrFail($request->book_id);

        if ($book->available_copies < 1) {
            return back()->with('error', 'No copies available for this book.');
        }

        // Check if user already has this book
        $alreadyIssued = BookIssue::where('book_id', $book->id)
            ->where('issued_to', $request->issued_to)
            ->whereNull('return_date')
            ->exists();

        if ($alreadyIssued) {
            return back()->with('error', 'This member already has a copy of this book.');
        }

        BookIssue::create([
            'school_id'  => auth()->user()->school_id,
            'book_id'    => $book->id,
            'issued_to'  => $request->issued_to,
            'issued_by'  => auth()->id(),
            'issue_date' => today(),
            'due_date'   => $request->due_date,
            'status'     => 'issued',
            'notes'      => $request->notes,
        ]);

        $book->decrement('available_copies');

        return back()->with('success', 'Book issued successfully.');
    }

    public function returnBook(Request $request, BookIssue $bookIssue)
    {
        $this->authorizeSchool($bookIssue->school_id);

        if ($bookIssue->return_date) {
            return back()->with('error', 'Book already returned.');
        }

        $fineAmount = 0;
        if ($bookIssue->is_overdue) {
            $fineAmount = $bookIssue->days_overdue * 500; // 5 PKR per day in paisas
        }

        $bookIssue->update([
            'return_date' => today(),
            'status'      => 'returned',
            'fine_amount' => $fineAmount,
        ]);

        $bookIssue->book->increment('available_copies');

        return back()->with('success',
            'Book returned.' . ($fineAmount > 0
                ? ' Fine: PKR ' . number_format($fineAmount / 100, 0)
                : '')
        );
    }

    private function authorizeSchool(int $schoolId): void
    {
        if ($schoolId !== auth()->user()->school_id) abort(403);
    }
}