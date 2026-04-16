@extends('layouts.admin')
@section('title', 'Library')

@section('content')

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h1 class="page-title">Library</h1>
        <p class="page-subtitle">Manage books and track inventory</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.library.issues') }}"
           class="btn btn-outline-primary">
            Issue / Return Books
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
            + Add Book
        </button>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-blue">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $totalBooks }}</div>
                <div class="stat-label">Total Books</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-green">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $availableBooks }}</div>
                <div class="stat-label">Available</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-purple">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $issuedBooks }}</div>
                <div class="stat-label">Currently Issued</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-orange">
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="stat-number">{{ $overdueBooks }}</div>
                <div class="stat-label">Overdue</div>
            </div>
        </div>
    </div>
</div>

{{-- Search & Filter --}}
<div class="educore-card mb-4">
    <form method="GET" class="d-flex gap-3 align-items-end flex-wrap">
        <div style="flex:1;min-width:200px">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control"
                   placeholder="Title, author, ISBN..."
                   value="{{ $search }}">
        </div>
        <div style="min-width:180px">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>
                    {{ $cat }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        @if($search || $category)
        <a href="{{ route('admin.library.index') }}" class="btn btn-outline-primary">Clear</a>
        @endif
    </form>
</div>

{{-- Books Table --}}
<div class="educore-card">
    @if($books->count() > 0)
    <table class="educore-table">
        <thead>
            <tr>
                <th>Book</th>
                <th>Author</th>
                <th>Category</th>
                <th>Shelf</th>
                <th>Copies</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($books as $book)
            <tr>
                <td>
                    <div class="fw-semibold" style="font-size:14px">{{ $book->title }}</div>
                    @if($book->isbn)
                    <div class="font-mono text-muted-sm" style="font-size:11px">
                        ISBN: {{ $book->isbn }}
                    </div>
                    @endif
                </td>
                <td class="text-muted-sm">{{ $book->author ?? '—' }}</td>
                <td>
                    @if($book->category)
                    <span class="status-badge badge-draft">{{ $book->category }}</span>
                    @else
                    <span class="text-muted-sm">—</span>
                    @endif
                </td>
                <td class="text-muted-sm">{{ $book->shelf_location ?? '—' }}</td>
                <td class="fw-semibold">{{ $book->total_copies }}</td>
                <td>
                    <span class="fw-semibold
                        {{ $book->available_copies === 0 ? 'text-danger' :
                           ($book->available_copies <= 2 ? 'text-warning' : '') }}">
                        {{ $book->available_copies }}
                    </span>
                    @if($book->available_copies === 0)
                    <span class="status-badge badge-absent ms-1" style="font-size:10px">
                        All Issued
                    </span>
                    @endif
                </td>
                <td>
                    <form method="POST"
                          action="{{ route('admin.library.destroy', $book) }}"
                          onsubmit="return confirm('Remove {{ $book->title }}?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm"
                                style="background:rgba(220,38,38,0.08);color:var(--color-danger);border:1px solid rgba(220,38,38,0.2);border-radius:7px;font-size:12px;padding:5px 8px">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-3">{{ $books->links() }}</div>
    @else
    <div style="text-align:center;padding:60px 0;color:var(--color-text-mid)">
        <svg width="56" height="56" fill="none" stroke="currentColor" viewBox="0 0 24 24"
             style="margin-bottom:16px;opacity:0.35">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="fw-semibold">No books in library yet</p>
        <p class="text-muted-sm mb-3">Start building your library collection.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
            + Add First Book
        </button>
    </div>
    @endif
</div>

{{-- Add Book Modal --}}
<div class="modal fade" id="addBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.library.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control"
                                   placeholder="Book title" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Copies <span class="text-danger">*</span></label>
                            <input type="number" name="total_copies" class="form-control"
                                   value="1" min="1" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control"
                                   placeholder="Author name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ISBN</label>
                            <input type="text" name="isbn" class="form-control"
                                   placeholder="e.g. 978-3-16-148410-0">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Publisher</label>
                            <input type="text" name="publisher" class="form-control"
                                   placeholder="Publisher name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Published Year</label>
                            <input type="number" name="published_year" class="form-control"
                                   placeholder="{{ date('Y') }}" min="1800" max="{{ date('Y') }}">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control"
                                   placeholder="e.g. Mathematics, Science, Fiction"
                                   list="categoryList">
                            <datalist id="categoryList">
                                @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                                @endforeach
                                <option value="Mathematics">
                                <option value="Science">
                                <option value="English">
                                <option value="History">
                                <option value="Geography">
                                <option value="Fiction">
                                <option value="Reference">
                                <option value="Islamic Studies">
                            </datalist>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Shelf Location</label>
                            <input type="text" name="shelf_location" class="form-control"
                                   placeholder="e.g. A-12, Row 3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2"
                                  placeholder="Brief description..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection